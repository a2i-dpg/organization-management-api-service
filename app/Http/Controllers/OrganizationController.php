<?php

namespace App\Http\Controllers;

use App\Exceptions\CustomException;
use App\Models\BaseModel;
use App\Models\User;
use App\Services\OrganizationService;
use App\Models\Organization;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\Client\RequestException;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Response;
use Illuminate\Validation\ValidationException;
use RuntimeException;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;
use Throwable;

/**
 * Class OrganizationController
 * @package App\Http\Controllers
 */
class OrganizationController extends Controller
{

    protected OrganizationService $organizationService;
    private Carbon $startTime;


    public function __construct(OrganizationService $organizationService)
    {
        $this->organizationService = $organizationService;
        $this->startTime = Carbon::now();
    }

    /**
     * Display a listing of the resource.
     * @param Request $request
     * @return JsonResponse
     * @throws AuthorizationException
     * @throws Throwable
     * @throws ValidationException
     */
    public function getList(Request $request): JsonResponse
    {
        $this->authorize('viewAny', Organization::class);
        $filter = $this->organizationService->filterValidator($request)->validate();
        $response = $this->organizationService->getAllOrganization($filter, $this->startTime);
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
        $organization = $this->organizationService->getOneOrganization($id);
        $this->authorize('view', $organization);
        $response = [
            "data" => $organization,
            "_response_status" => [
                "success" => true,
                "code" => ResponseAlias::HTTP_OK,
                "query_time" => $this->startTime->diffInSeconds(Carbon::now())
            ]
        ];
        return Response::json($response, ResponseAlias::HTTP_OK);
    }

    /**
     * Display a specified resource for public
     * @param Request $request
     * @param int $id
     * @return JsonResponse
     */
    public function organizationDetails(Request $request, int $id): JsonResponse
    {
        $organization = $this->organizationService->getOneOrganization($id);

        $response = [
            "data" => $organization,
            "_response_status" => [
                "success" => true,
                "code" => ResponseAlias::HTTP_OK,
                "query_time" => $this->startTime->diffInSeconds(Carbon::now())
            ]
        ];
        return Response::json($response, ResponseAlias::HTTP_OK);
    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return JsonResponse
     * @throws AuthorizationException
     * @throws RequestException
     * @throws Throwable
     * @throws ValidationException
     */
    public function store(Request $request): JsonResponse
    {
        /** @var Organization $organization */
        $organization = app(Organization::class);

        $this->authorize('create', $organization);

        $validated = $this->organizationService->validator($request)->validate();
        $subTrades = $validated['sub_trades'];
        DB::beginTransaction();
        try {

            $organization = $this->organizationService->store($organization, $validated);

            $this->organizationService->syncWithSubTrades($organization, $subTrades);

            if (!($organization && $organization->id)) {
                throw new RuntimeException('Saving Organization/Industry to DB failed!', 500);
            }

            $validated['organization_id'] = $organization->id;
            $validated['password'] = BaseModel::ADMIN_CREATED_USER_DEFAULT_PASSWORD;

            $createdRegisterUser = $this->organizationService->createUser($validated);

            Log::info('id_user_info:' . json_encode($createdRegisterUser));

            if (!($createdRegisterUser && !empty($createdRegisterUser['_response_status']))) {
                throw new RuntimeException('Creating User during  Organization/Industry Creation has been failed!', 500);
            }

            $response = [
                '_response_status' => [
                    "success" => true,
                    "code" => ResponseAlias::HTTP_CREATED,
                    "message" => "Organization has been Created Successfully",
                    "query_time" => $this->startTime->diffInSeconds(\Illuminate\Support\Carbon::now()),
                ]
            ];

            if (isset($createdRegisterUser['_response_status']['success']) && $createdRegisterUser['_response_status']['success']) {

                /** Send User Information After Completing Organization Registration */
                $this->organizationService->userInfoSendByMail($validated);
                $recipient = $validated['contact_person_mobile'];
                $message = "Dear, " . $validated['contact_person_name'] . " your username: " . $validated['contact_person_mobile'] . " & password: " . $validated['password'];
                $this->organizationService->userInfoSendBySMS($recipient, $message);

                $response['data'] = $organization;
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
     * Update the specified resource in storage.
     * @param Request $request
     * @param int $id
     * @return JsonResponse
     * @throws AuthorizationException
     * @throws Throwable
     * @throws ValidationException
     */
    public function update(Request $request, int $id): JsonResponse
    {
        $organization = Organization::findOrFail($id);

        $this->authorize('update', $organization);

        $validated = $this->organizationService->validator($request, $id)->validate();
        $industrySubTrades = $validated['industry_sub_trades'];
        $data = $this->organizationService->update($organization, $validated);
        $this->organizationService->syncWithSubTrades($organization, $industrySubTrades);
        $response = [
            'data' => $data,
            '_response_status' => [
                "success" => true,
                "code" => ResponseAlias::HTTP_OK,
                "message" => "Organization updated successfully.",
                "query_time" => $this->startTime->diffInSeconds(Carbon::now())
            ]
        ];
        return Response::json($response, ResponseAlias::HTTP_CREATED);
    }

    /**
     * Remove the specified resource from storage.
     * @param int $id
     * @return JsonResponse
     * @throws AuthorizationException
     * @throws Throwable
     */
    public function destroy(int $id): JsonResponse
    {
        $organization = Organization::findOrFail($id);

        $this->authorize('delete', $organization);

        DB::beginTransaction();
        try {
            $this->organizationService->destroy($organization);
            $this->organizationService->userDestroy($organization);
            DB::commit();
            $response = [
                '_response_status' => [
                    "success" => true,
                    "code" => ResponseAlias::HTTP_OK,
                    "message" => "Organization deleted successfully.",
                    "query_time" => $this->startTime->diffInSeconds(Carbon::now())
                ]
            ];
        } catch (Throwable $e) {
            DB::rollBack();
            throw $e;
        }

        return Response::json($response, ResponseAlias::HTTP_OK);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     * @throws CustomException
     * @throws RequestException
     * @throws Throwable
     * @throws ValidationException
     */
    public function organizationOpenRegistration(Request $request): JsonResponse
    {
        /** @var Organization $organization */
        $organization = app(Organization::class);
        $validated = $this->organizationService->registerOrganizationValidator($request)->validate();

        Log::channel('org_reg')->info('organization_registration_validated_data', $validated);

        DB::beginTransaction();
        try {
            $organization = $this->organizationService->store($organization, $validated);

            if (!($organization && $organization->id)) {
                throw new CustomException('Organization/Industry has not been properly saved to db.');
            }

            Log::channel('org_reg')->info('organization_stored_data', $organization->toArray());

            $validated['organization_id'] = $organization->id;
            $validated['password'] = BaseModel::ADMIN_CREATED_USER_DEFAULT_PASSWORD;

            $createdRegisterUser = $this->organizationService->createOpenRegisterUser($validated);

            Log::info("userCreateInfo" . json_encode($createdRegisterUser));

            if (!($createdRegisterUser && !empty($createdRegisterUser['_response_status']))) {
                throw new RuntimeException('Creating User during  Organization/Industry Registration has been failed!', 500);
            }

            $response = [
                '_response_status' => [
                    "success" => true,
                    "code" => ResponseAlias::HTTP_CREATED,
                    "message" => "Organization has been Created Successfully",
                    "query_time" => $this->startTime->diffInSeconds(\Illuminate\Support\Carbon::now()),
                ]
            ];

            if (isset($createdRegisterUser['_response_status']['success']) && $createdRegisterUser['_response_status']['success']) {

                /** Send User Information After Completing Organization Registration */
                $this->organizationService->userInfoSendByMail($validated);

                $response['data'] = $organization;
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
     * @param int $organizationId
     * @return JsonResponse
     * @throws RequestException
     * @throws Throwable
     */
    public function organizationRegistrationApproval(int $organizationId): JsonResponse
    {
        $organization = Organization::findOrFail($organizationId);
        DB::beginTransaction();
        try {
            if ($organization->row_status == BaseModel::ROW_STATUS_PENDING) {
                $this->organizationService->organizationStatusChangeAfterApproval($organization);
                $userApproval = $this->organizationService->organizationUserApproval($organization);
                if (isset($userApproval['_response_status']['success']) && $userApproval['_response_status']['success']) {

                    $mailPayload['organization_id'] = $organizationId;
                    $mailPayload['subject'] = "Organization Registration Approval";
                    $this->organizationService->sendMailToOrganizationAfterRegistrationApprovalOrRejection($mailPayload);
                }
                $response['_response_status'] = [
                    "success" => false,
                    "code" => ResponseAlias::HTTP_OK,
                    "message" => "organization approved successfully",
                    "query_time" => $this->startTime->diffInSeconds(\Carbon\Carbon::now()),
                ];
                DB::commit();
            } else {
                $response['_response_status'] = [
                    "success" => false,
                    "code" => ResponseAlias::HTTP_OK,
                    "message" => "organization can not be approved",
                    "query_time" => $this->startTime->diffInSeconds(\Carbon\Carbon::now()),
                ];
            }
        } catch (Throwable $e) {
            DB::rollBack();
            throw $e;
        }
        return Response::json($response, ResponseAlias::HTTP_OK);
    }

    /**
     * @param int $organizationId
     * @return JsonResponse
     * @throws RequestException
     * @throws Throwable
     */
    public function organizationRegistrationRejection(int $organizationId): JsonResponse
    {
        $organization = Organization::findOrFail($organizationId);
        DB::beginTransaction();
        try {
            if ($organization->row_status == BaseModel::ROW_STATUS_PENDING) {
                $this->organizationService->organizationStatusChangeAfterRejection($organization);
                $userRejection = $this->organizationService->organizationUserRejection($organization);

                if (isset($userRejection['_response_status']['success']) && $userRejection['_response_status']['success']) {
                    $mailPayload['organization_id'] = $organizationId;
                    $mailPayload['subject'] = "Organization Registration Rejection";

                    $this->organizationService->sendMailToOrganizationAfterRegistrationApprovalOrRejection($mailPayload);
                }
                $response['_response_status'] = [
                    "success" => false,
                    "code" => ResponseAlias::HTTP_OK,
                    "message" => "organization rejected successfully",
                    "query_time" => $this->startTime->diffInSeconds(\Carbon\Carbon::now()),
                ];
                DB::commit();
            } else {
                $response['_response_status'] = [
                    "success" => false,
                    "code" => ResponseAlias::HTTP_OK,
                    "message" => "organization can not be rejected",
                    "query_time" => $this->startTime->diffInSeconds(\Carbon\Carbon::now()),
                ];
            }
        } catch (Throwable $e) {
            DB::rollBack();
            throw $e;
        }
        return Response::json($response, ResponseAlias::HTTP_OK);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     * @throws Throwable
     */
    public function getOrganizationTitleByIds(Request $request): JsonResponse
    {
        throw_if(!is_array($request->get('organization_ids')), ValidationException::withMessages([
            "The Organization ids must be array.[8000]"
        ]));

        $organizationTitle = $this->organizationService->getOrganizationTitle($request);
        $response = [
            "data" => $organizationTitle,
            '_response_status' => [
                "success" => true,
                "code" => ResponseAlias::HTTP_OK,
                "message" => "Organization Title List.",
                "query_time" => $this->startTime->diffInSeconds(Carbon::now())
            ]
        ];
        return Response::json($response, ResponseAlias::HTTP_OK);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     * @throws Throwable
     */
    public function getIndustryAssociationTitleByIds(Request $request): JsonResponse
    {
        throw_if(!is_array($request->get('industry_association_ids')), ValidationException::withMessages([
            "The Industry Association ids must be array.[8000]"
        ]));

        $industryAssociationTitle = $this->organizationService->getIndustryAssociationTitle($request);
        $response = [
            "data" => $industryAssociationTitle,
            '_response_status' => [
                "success" => true,
                "code" => ResponseAlias::HTTP_OK,
                "message" => "Industry Association Title List.",
                "query_time" => $this->startTime->diffInSeconds(Carbon::now())
            ]
        ];
        return Response::json($response, ResponseAlias::HTTP_OK);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     * @throws Throwable
     */
    public function getTrashedData(Request $request): JsonResponse
    {
        $response = $this->organizationService->getAllTrashedOrganization($request, $this->startTime);
        return Response::json($response);
    }


    /**
     * @param int $id
     * @return JsonResponse
     */
    public function restore(int $id): JsonResponse
    {
        $organization = Organization::onlyTrashed()->findOrFail($id);
        $this->organizationService->restore($organization);
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


    /**
     * Industry association membership request from industry
     * @param Request $request
     * @return JsonResponse
     * @throws Throwable
     * @throws ValidationException
     */
    public function IndustryAssociationMembershipApplication(Request $request): JsonResponse
    {
        $validatedData = $this->organizationService->IndustryAssociationMembershipValidation($request)->validate();
        $this->organizationService->IndustryAssociationMembershipApplication($validatedData);
        $this->organizationService->sendMailToIndustryAssociationAfterMembershipApplication($validatedData);
        $response = [
            '_response_status' => [
                "success" => true,
                "code" => ResponseAlias::HTTP_OK,
                "message" => "industryAssociation membership application successfully submitted",
                "query_time" => $this->startTime->diffInSeconds(Carbon::now())
            ]
        ];
        return Response::json($response, ResponseAlias::HTTP_CREATED);
    }


    /**
     * @param Request $request
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function getOrganizationProfile(Request $request): JsonResponse
    {
        $organizationId = $request->input('organization_id');
        $organization = $this->organizationService->getOneOrganization($organizationId);

        $this->authorize('viewProfile', $organization);

        $response = [
            "data" => $organization,
            "_response_status" => [
                "success" => true,
                "code" => ResponseAlias::HTTP_OK,
                "query_time" => $this->startTime->diffInSeconds(Carbon::now()),
            ]
        ];
        return Response::json($response, ResponseAlias::HTTP_OK);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     * @throws ValidationException|AuthorizationException
     */
    public function updateOrganizationProfile(Request $request): JsonResponse
    {
        $organizationId = $request->input('organization_id');

        $organization = Organization::findOrFail($organizationId);

        $this->authorize('updateProfile', $organization);


        $validated = $this->organizationService->organizationAdminProfileValidator($request, $organizationId)->validate();
        $data = $this->organizationService->update($organization, $validated);
        $response = [
            'data' => $data ?: [],
            '_response_status' => [
                "success" => true,
                "code" => ResponseAlias::HTTP_OK,
                "message" => "Organization admin profile updated successfully.",
                "query_time" => $this->startTime->diffInSeconds(Carbon::now()),
            ]
        ];
        return Response::json($response, ResponseAlias::HTTP_CREATED);
    }


}
