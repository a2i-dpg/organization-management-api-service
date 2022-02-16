<?php

namespace App\Http\Controllers;

use App\Exceptions\CustomException;
use App\Models\BaseModel;
use App\Models\IndustryAssociation;
use App\Models\Organization;
use App\Models\NascibMember;
use App\Services\NascibMemberService;
use App\Services\OrganizationService;
use Carbon\Carbon;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\Client\RequestException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Response;
use Illuminate\Validation\ValidationException;
use RuntimeException;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;
use Throwable;

/**
 *
 */
class NascibMemberController extends Controller
{
    /**
     * @var NascibMemberService
     */
    protected NascibMemberService $nascibMemberService;
    /**
     * @var OrganizationService
     */
    protected OrganizationService $organizationService;
    private Carbon $startTime;


    public function __construct(NascibMemberService $nascibMemberService, OrganizationService $organizationService)
    {
        $this->nascibMemberService = $nascibMemberService;
        $this->organizationService = $organizationService;
        $this->startTime = Carbon::now();
    }


    /**
     * Display a listing of the resource.
     * @param Request $request
     * @return JsonResponse
     * @throws ValidationException
     * @throws AuthorizationException
     */
    public function getList(Request $request): JsonResponse
    {
        //$this->authorize('viewAny', IndustryAssociation::class);
        $filter = $this->nascibMemberService->filterValidator($request)->validate();
        $returnedData = $this->nascibMemberService->getNascibMemberList($filter, $this->startTime);

        $response = [
            'order' => $returnedData['order'],
            'data' => $returnedData['data'],
            '_response_status' => [
                "success" => true,
                "code" => ResponseAlias::HTTP_OK,
                'query_time' => $returnedData['query_time']
            ]

        ];
        if (isset($returnedData['total_page'])) {
            $response['total'] = $returnedData['total'];
            $response['current_page'] = $returnedData['current_page'];
            $response['total_page'] = $returnedData['total_page'];
            $response['page_size'] = $returnedData['page_size'];
        }
        return Response::json($response, ResponseAlias::HTTP_OK);

    }


    /**
     * Display a specified resource
     * @param Request $request
     * @param int $id
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function read(Request $request, int $id): JsonResponse
    {
        $industryAssociation = $this->industryAssociationService->getOneIndustryAssociation($id);

        $this->authorize('view', $industryAssociation);

        $response = [
            "data" => $industryAssociation,
            "_response_status" => [
                "success" => true,
                "code" => ResponseAlias::HTTP_OK,
                "query_time" => $this->startTime->diffInSeconds(Carbon::now())
            ]
        ];
        return Response::json($response, ResponseAlias::HTTP_OK);

    }


    /** This methods are not using now. Delete after checking */
    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return JsonResponse
     * @throws CustomException
     * @throws RequestException
     * @throws Throwable
     * @throws ValidationException
     */
    public function store(Request $request): JsonResponse
    {
        $organizationMember = app(NascibMember::class);
        $organization = app(Organization::class);
        //$this->authorize('create', $organizationMember);

        $validated = $this->nascibMemberService->validator($request)->validate();

        DB::beginTransaction();
        try {
            $organizationMember = $this->nascibMemberService->store($organization, $organizationMember, $validated);


            $validated['organization_id'] = $organizationMember->organization_id;
            $validated['password'] = BaseModel::ADMIN_CREATED_USER_DEFAULT_PASSWORD;


            $createdRegisterUser = $this->nascibMemberService->createUser($validated);

            Log::info('id_user_info:' . json_encode($createdRegisterUser));

            if (!($createdRegisterUser && !empty($createdRegisterUser['_response_status']))) {
                throw new RuntimeException('Creating User during  Organization/Industry Creation has been failed!', 500);
            }

            $response = [
                '_response_status' => [
                    "success" => true,
                    "code" => ResponseAlias::HTTP_CREATED,
                    "message" => "OrganizationMember has been Created Successfully",
                    "query_time" => $this->startTime->diffInSeconds(\Illuminate\Support\Carbon::now()),
                ]
            ];

            DB::commit();
            $httpStatusCode = ResponseAlias::HTTP_BAD_REQUEST;
            return Response::json($response, $httpStatusCode);
        } catch (Throwable $e) {
            DB::rollBack();
            throw $e;
        }

    }


    /**
     * Update the specified resource from storage.
     * @param Request $request
     * @param int $id
     * @return JsonResponse
     * @throws ValidationException
     * @throws AuthorizationException
     */
    public function update(Request $request, int $id): JsonResponse
    {
        $industryAssociation = IndustryAssociation::findOrFail($id);

        $this->authorize('update', $industryAssociation);

        $validated = $this->industryAssociationService->validator($request, $id)->validate();
        $data = $this->industryAssociationService->update($industryAssociation, $validated);

        $data = array_merge($data->toArray(), ["skills" => $data->skills()->get()]);

        $response = [
            'data' => $data,
            '_response_status' => [
                "success" => true,
                "code" => ResponseAlias::HTTP_OK,
                "message" => "IndustryAssociation updated successfully.",
                "query_time" => $this->startTime->diffInSeconds(Carbon::now())
            ]
        ];
        return Response::json($response, ResponseAlias::HTTP_CREATED);
    }


    /**
     * Remove the specified resource from storage.
     * @param int $id
     * @return JsonResponse
     * @throws Throwable
     */
    public function destroy(int $id): JsonResponse
    {
        $industryAssociation = IndustryAssociation::findOrFail($id);
        $this->authorize('delete', $industryAssociation);
        DB::beginTransaction();
        try {
            $this->industryAssociationService->destroy($industryAssociation);
            $this->industryAssociationService->userDestroy($industryAssociation);
            DB::commit();
            $response = [
                '_response_status' => [
                    "success" => true,
                    "code" => ResponseAlias::HTTP_OK,
                    "message" => "industryAssociation deleted successfully.",
                    "query_time" => $this->startTime->diffInSeconds(Carbon::now())
                ]
            ];
        } catch (Throwable $e) {
            DB::rollBack();
            throw $e;
        }

        $response = [
            '_response_status' => [
                "success" => true,
                "code" => ResponseAlias::HTTP_OK,
                "message" => "IndustryAssociation deleted successfully.",
                "query_time" => $this->startTime->diffInSeconds(Carbon::now())
            ]
        ];
        return Response::json($response, ResponseAlias::HTTP_OK);
    }

    /**
     * @param int $id
     * @return JsonResponse
     */
    public function restore(int $id): JsonResponse
    {

        $industryAssociation = IndustryAssociation::onlyTrashed()->findOrFail($id);
        $this->industryAssociationService->restore($industryAssociation);

        $response = [
            '_response_status' => [
                "success" => true,
                "code" => ResponseAlias::HTTP_OK,
                "message" => "IndustryAssociation restored successfully",
                "query_time" => $this->startTime->diffInSeconds(Carbon::now())
            ]
        ];
        return Response::json($response, ResponseAlias::HTTP_OK);
    }

}
