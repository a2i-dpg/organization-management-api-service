<?php

namespace App\Http\Controllers;

use App\Exceptions\CustomException;
use App\Services\OrganizationService;
use App\Models\Organization;
use Exception;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\Client\RequestException;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Response;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;
use Throwable;

/**
 * Class OrganizationController
 * @package App\Http\Controllers
 */
class OrganizationController extends Controller
{
    /**
     * @var OrganizationService
     */
    protected OrganizationService $organizationService;

    /**
     * @var Carbon
     */
    private Carbon $startTime;

    /**
     * OrganizationController constructor.
     * @param OrganizationService $organizationService
     */
    public function __construct(OrganizationService $organizationService)
    {
        $this->organizationService = $organizationService;
        $this->startTime = Carbon::now();
    }

    /**
     * Display a listing of the resource.
     * @param Request $request
     * @return Exception|JsonResponse|Throwable
     * @throws ValidationException|AuthorizationException
     */
    public function getList(Request $request): JsonResponse
    {
        $this->authorize('viewAny', Organization::class);

        $filter = $this->organizationService->filterValidator($request)->validate();
        try {
            $response = $this->organizationService->getAllOrganization($filter, $this->startTime);
        } catch (Throwable $e) {
            return $e;
        }
        return Response::json($response);
    }

    /**
     * Display a specified resource
     * @param int $id
     * @return Exception|JsonResponse|Throwable
     */
    public function read(int $id)
    {
        try {
            $response = $this->organizationService->getOneOrganization($id, $this->startTime);
            if (!$response) {
                abort(ResponseAlias::HTTP_NOT_FOUND);
            }
            $this->authorize('view', $response['data']);
        } catch (Throwable $e) {
            return $e;
        }
        return Response::json($response);
    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return Exception|Throwable|JsonResponse
     * @throws ValidationException|AuthorizationException
     */
    public function store(Request $request)
    {
        $organization = new Organization();

        $this->authorize('create', $organization);

        $validated = $this->organizationService->validator($request)->validate();

        DB::beginTransaction();
        try {
            $organization = $this->organizationService->store($organization, $validated);
            if ($organization) {
                $validated['organization_id'] = $organization->id;
                $createUser = $this->organizationService->createUser($validated);
                Log::info('id_user_info:' . json_encode($createUser));
                if ($createUser && $createUser['_response_status']['success']) {
                    $response = [
                        'data' => $organization ?: [],
                        '_response_status' => [
                            "success" => true,
                            "code" => ResponseAlias::HTTP_CREATED,
                            "message" => "Organization Successfully Create",
                            "query_time" => $this->startTime->diffInSeconds(\Illuminate\Support\Carbon::now()),
                        ]
                    ];
                    DB::commit();
                } else {
                    if ($createUser && $createUser['_response_status']['code'] == 400) {
                        $response = [
                            'errors' => $createUser['errors'] ?? [],
                            '_response_status' => [
                                "success" => false,
                                "code" => ResponseAlias::HTTP_BAD_REQUEST,
                                "message" => "Validation Error",
                                "query_time" => $this->startTime->diffInSeconds(Carbon::now()),
                            ]
                        ];
                    } else {
                        $response = [
                            '_response_status' => [
                                "success" => false,
                                "code" => ResponseAlias::HTTP_UNPROCESSABLE_ENTITY,
                                "message" => "Unprocessable Request,Please contact",
                                "query_time" => $this->startTime->diffInSeconds(Carbon::now()),
                            ]
                        ];
                    }

                    DB::rollBack();
                }
            }
        } catch (Throwable $e) {
            DB::rollBack();
            return $e;
        }
        return Response::json($response, ResponseAlias::HTTP_CREATED);
    }

    /**
     * @param Request $request
     * @return Exception|JsonResponse|Throwable
     * @throws CustomException
     * @throws Throwable
     * @throws ValidationException
     * @throws RequestException
     */
    public function organizationOpenRegistration(Request $request): JsonResponse
    {

        $organization = new Organization();
        $validated = $this->organizationService->registerOrganizationValidator($request)->validate();

        DB::beginTransaction();
        try {
            $organization = $this->organizationService->store($organization, $validated);

            if (!($organization && $organization->id)) {
                throw new CustomException('Organization/Industry has not been properly saved to db.');
            }

            $validated['organization_id'] = $organization->id;

            $createRegisterUser = $this->organizationService->createOpenRegisterUser($validated);

            if ($createRegisterUser && $createRegisterUser['_response_status']['success']) {
                $response = [
                    'data' => $organization,
                    '_response_status' => [
                        "success" => true,
                        "code" => ResponseAlias::HTTP_CREATED,
                        "message" => "Organization Successfully Create",
                        "query_time" => $this->startTime->diffInSeconds(\Illuminate\Support\Carbon::now()),
                    ]
                ];
                DB::commit();
            } else {
                if ($createRegisterUser && $createRegisterUser['_response_status']['code'] == ResponseAlias::HTTP_UNPROCESSABLE_ENTITY) {
                    $response = [
                        'errors' => $createRegisterUser['errors'] ?? [],
                        '_response_status' => [
                            "success" => false,
                            "code" => ResponseAlias::HTTP_BAD_REQUEST,
                            "message" => "Validation Error",
                            "query_time" => $this->startTime->diffInSeconds(Carbon::now()),
                        ]
                    ];
                } else {
                    $response = [
                        '_response_status' => [
                            "success" => false,
                            "code" => ResponseAlias::HTTP_UNPROCESSABLE_ENTITY,
                            "message" => "Unprocessable Request,Please contact",
                            "query_time" => $this->startTime->diffInSeconds(Carbon::now()),
                        ]
                    ];
                }
                DB::rollBack();
            }

            return Response::json($response, ResponseAlias::HTTP_CREATED);

        } catch (Throwable $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Update the specified resource in storage.
     * @param Request $request
     * @param int $id
     * @return Exception|JsonResponse|Throwable
     * @throws ValidationException
     * @throws AuthorizationException
     */
    public function update(Request $request, int $id): JsonResponse
    {
        $organization = Organization::findOrFail($id);

        $this->authorize('update', $organization);

        $validated = $this->organizationService->validator($request, $id)->validate();
        try {
            $data = $this->organizationService->update($organization, $validated);
            $response = [
                'data' => $data ?: null,
                '_response_status' => [
                    "success" => true,
                    "code" => ResponseAlias::HTTP_OK,
                    "message" => "Organization updated successfully.",
                    "query_time" => $this->startTime->diffInSeconds(Carbon::now())
                ]
            ];
        } catch (Throwable $e) {
            return $e;
        }
        return Response::json($response, ResponseAlias::HTTP_CREATED);
    }

    /**
     * Remove the specified resource from storage.
     * @param int $id
     * @return Exception|JsonResponse|Throwable
     * @throws AuthorizationException
     */
    public function destroy(int $id): JsonResponse
    {
        $organization = Organization::findOrFail($id);

        $this->authorize('delete', $organization);

        try {
            $this->organizationService->destroy($organization);
            $response = [
                '_response_status' => [
                    "success" => true,
                    "code" => ResponseAlias::HTTP_OK,
                    "message" => "Organization deleted successfully.",
                    "query_time" => $this->startTime->diffInSeconds(Carbon::now())
                ]
            ];
        } catch (Throwable $e) {
            return $e;
        }
        return Response::json($response, ResponseAlias::HTTP_OK);
    }

    /**
     * @param Request $request
     * @return Exception|JsonResponse|Throwable
     */
    public function getTrashedData(Request $request)
    {
        try {
            $response = $this->organizationService->getAllTrashedOrganization($request, $this->startTime);
        } catch (Throwable $e) {
            return $e;
        }
        return Response::json($response);
    }


    /**
     * @param int $id
     * @return Exception|JsonResponse|Throwable
     */
    public function restore(int $id)
    {
        $organization = Organization::onlyTrashed()->findOrFail($id);
        try {
            $this->organizationService->restore($organization);
            $response = [
                '_response_status' => [
                    "success" => true,
                    "code" => ResponseAlias::HTTP_OK,
                    "message" => "Organization restored successfully",
                    "query_time" => $this->startTime->diffInSeconds(Carbon::now())
                ]
            ];
        } catch (Throwable $e) {
            return $e;
        }
        return Response::json($response, ResponseAlias::HTTP_OK);
    }

    /**
     * @param int $id
     * @return Exception|JsonResponse|Throwable
     */
    public function forceDelete(int $id)
    {
        $organization = Organization::onlyTrashed()->findOrFail($id);
        try {
            $this->organizationService->forceDelete($organization);
            $response = [
                '_response_status' => [
                    "success" => true,
                    "code" => ResponseAlias::HTTP_OK,
                    "message" => "Organization permanently deleted successfully",
                    "query_time" => $this->startTime->diffInSeconds(Carbon::now())
                ]
            ];
        } catch (Throwable $e) {
            return $e;
        }
        return Response::json($response, ResponseAlias::HTTP_OK);
    }
}
