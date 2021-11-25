<?php

namespace App\Http\Controllers;

use App\Exceptions\CustomException;
use App\Models\IndustryAssociation;
use App\Services\IndustryAssociationService;
use Carbon\Carbon;
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

class IndustryAssociationController extends Controller
{

    protected IndustryAssociationService $industryAssociationService;
    private Carbon $startTime;


    public function __construct(IndustryAssociationService $industryAssociationService)
    {
        $this->industryAssociationService = $industryAssociationService;
        $this->startTime = Carbon::now();
    }


    /**
     * Display a listing of the resource.
     * @param Request $request
     */
    public function getList(Request $request): JsonResponse
    {

    }


    /**
     * Display a specified resource
     * @param Request $request
     * @param int $id
     * @return JsonResponse
     */
    public function read(Request $request, int $id): JsonResponse
    {

    }

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
        $industryAssociation = app(IndustryAssociation::class);
        $validated = $this->industryAssociationService->validator($request)->validate();

        DB::beginTransaction();
        try {
            $industryAssociation = $this->industryAssociationService->store($industryAssociation, $validated);
            if (!($industryAssociation && $industryAssociation->id)) {
                throw new CustomException('IndustryAssociation has not been properly saved to db.');
            }
            $validated['industry_association_id'] = $industryAssociation->id;
            $createdRegisterUser = $this->industryAssociationService->createUser($validated);




            if (!($createdRegisterUser && !empty($createdRegisterUser['_response_status']))) {
                throw new RuntimeException('Creating User during  IndustryAssociation Creation has been failed!', 500);
            }
            $response = [
                '_response_status' => [
                    "success" => true,
                    "code" => ResponseAlias::HTTP_CREATED,
                    "message" => "IndustryAssociation has been Created Successfully",
                    "query_time" => $this->startTime->diffInSeconds(\Illuminate\Support\Carbon::now()),
                ]
            ];

            if (isset($createdRegisterUser['_response_status']['success']) && $createdRegisterUser['_response_status']['success']) {
                $response['data'] = $industryAssociation;
                DB::commit();
                return Response::json($response, ResponseAlias::HTTP_CREATED);
            }

            DB::rollBack();

            $httpStatusCode = ResponseAlias::HTTP_BAD_REQUEST;
            if (!empty($createdRegisterUser['_response_status']['code'])) {
                $httpStatusCode = $createdRegisterUser['_response_status']['code'];
            }

            $response['_response_status'] = [
                "success" => false,
                "code" => $httpStatusCode,
                "message" => "Error Occurred. Please Contact.",
                "query_time" => $this->startTime->diffInSeconds(\Carbon\Carbon::now()),
            ];

            if (!empty($createdRegisterUser['errors'])) {
                $response['errors'] = $createdRegisterUser['errors'];
            }

            return Response::json($response, $httpStatusCode);
        } catch (Throwable $e) {
            DB::rollBack();
            throw $e;
        }

    }


    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return JsonResponse
     * @throws CustomException
     * @throws RequestException
     * @throws Throwable
     * @throws ValidationException
     */
    public function industryAssociationOpenRegistration(Request $request): JsonResponse
    {
        $industryAssociation = app(IndustryAssociation::class);
        $validated = $this->industryAssociationService->industryAssociationRegistrationValidator($request)->validate();

        DB::beginTransaction();
        try {
            $industryAssociation = $this->industryAssociationService->store($industryAssociation, $validated);
            if (!($industryAssociation && $industryAssociation->id)) {
                throw new CustomException('IndustryAssociation has not been properly saved to db.');
            }

            $validated['industry_association_id'] = $industryAssociation->id;
            $createdRegisterUser = $this->industryAssociationService->createUser($validated);

            if (!($createdRegisterUser && !empty($createdRegisterUser['_response_status']))) {
                throw new RuntimeException('Creating User during  IndustryAssociation Creation has been failed!', 500);
            }
            $response = [
                '_response_status' => [
                    "success" => true,
                    "code" => ResponseAlias::HTTP_CREATED,
                    "message" => "IndustryAssociation has been Created Successfully",
                    "query_time" => $this->startTime->diffInSeconds(\Illuminate\Support\Carbon::now()),
                ]
            ];

            if (isset($createdRegisterUser['_response_status']['success']) && $createdRegisterUser['_response_status']['success']) {
                $response['data'] = $industryAssociation;
                DB::commit();
                return Response::json($response, ResponseAlias::HTTP_CREATED);
            }

            DB::rollBack();

            $httpStatusCode = ResponseAlias::HTTP_BAD_REQUEST;
            if (!empty($createdRegisterUser['_response_status']['code'])) {
                $httpStatusCode = $createdRegisterUser['_response_status']['code'];
            }

            $response['_response_status'] = [
                "success" => false,
                "code" => $httpStatusCode,
                "message" => "Error Occurred. Please Contact.",
                "query_time" => $this->startTime->diffInSeconds(\Carbon\Carbon::now()),
            ];

            if (!empty($createdRegisterUser['errors'])) {
                $response['errors'] = $createdRegisterUser['errors'];
            }

            return Response::json($response, $httpStatusCode);
        } catch (Throwable $e) {
            DB::rollBack();
            throw $e;
        }

    }

    /**
     * Remove the specified resource from storage.
     * @param Request $request
     * @param int $id
     * @return JsonResponse
     */
    public function update(Request $request, int $id): JsonResponse
    {

    }


    /**
     * Remove the specified resource from storage.
     * @param int $id
     * @return JsonResponse
     * @throws Throwable
     */
    public function destroy(int $id): JsonResponse
    {

        $response = [
            '_response_status' => [
                "success" => true,
                "code" => ResponseAlias::HTTP_OK,
                "message" => "Organization deleted successfully.",
                "query_time" => $this->startTime->diffInSeconds(Carbon::now())
            ]
        ];
        return Response::json($response, ResponseAlias::HTTP_OK);
    }


    /**
     * @param int $id
     * @return JsonResponse
     */
    public function restore(int $id)
    {
        $response = [
            '_response_status' => [
                "success" => true,
                "code" => ResponseAlias::HTTP_OK,
                "message" => "Organization restored successfully",
                "query_time" => $this->startTime->diffInSeconds(Carbon::now())
            ]
        ];
        return Response::json($response, ResponseAlias::HTTP_OK);
    }

}
