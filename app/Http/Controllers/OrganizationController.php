<?php

namespace App\Http\Controllers;

use App\Exceptions\CustomException;
use App\Models\BaseModel;
use App\Models\IndustryAssociation;
use App\Models\User;
use App\Services\CommonServices\MailService;
use App\Services\OrganizationService;
use App\Models\Organization;
use Exception;
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
     * @return JsonResponse
     * @throws AuthorizationException
     * @throws Throwable
     * @throws ValidationException
     */
    public function getList(Request $request): JsonResponse
    {
        $this->authorize('viewAny', Organization::class);
        $filter = $this->organizationService->filterValidator($request)->validate();
        if (!empty(Auth::user())) {
            $authUser = Auth::user();
            $industryAssociationId = $authUser->industry_association_id;
            $response = $this->organizationService->getOrganizationListFilterByIndustryAssociation($filter, $industryAssociationId, $this->startTime,);

        } else {
            $response = $this->organizationService->getAllOrganization($filter, $this->startTime);

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
        $organization = $this->organizationService->getOneOrganization($id);

        $requestHeaders = $request->header();
        if (empty($requestHeaders[BaseModel::DEFAULT_SERVICE_TO_SERVICE_CALL_KEY][0]) ||
            $requestHeaders[BaseModel::DEFAULT_SERVICE_TO_SERVICE_CALL_KEY][0] === BaseModel::DEFAULT_SERVICE_TO_SERVICE_CALL_FLAG_FALSE) {
            $this->authorize('view', $organization);
        }
        $response = [
            "data" => $organization ?: [],
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

        DB::beginTransaction();
        try {

            $organization = $this->organizationService->store($organization, $validated);

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
     * @throws RequestException
     * @throws Throwable
     */
    public function organizationRegistrationApproval(int $organizationId): JsonResponse
    {
        /** @var Organization $organizationId */
        $organization = Organization::findOrFail($organizationId);

        DB::beginTransaction();

        try {
            if ($organization && $organization->row_status == BaseModel::ROW_STATUS_PENDING) {
                $this->organizationService->organizationStatusChangeAfterApproval($organization);
                $this->organizationService->organizationUserApproval($organization);

                /** sendSms after Industry Association Registration Approval */
//                $this->sendSmsIndustryAssociationRegistrationApproval($industryAssociation);

                DB::commit();
                $response = [
                    '_response_status' => [
                        "success" => true,
                        "code" => ResponseAlias::HTTP_OK,
                        "message" => "Organization Registration  approved successfully",
                        "query_time" => $this->startTime->diffInSeconds(Carbon::now())
                    ]
                ];
            } else {
                $response = [
                    '_response_status' => [
                        "success" => false,
                        "code" => ResponseAlias::HTTP_BAD_REQUEST,
                        "message" => "No pending status found for this organization",
                        "query_time" => $this->startTime->diffInSeconds(Carbon::now())
                    ]
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
        /** @var Organization $organizationId */
        $organization = Organization::findOrFail($organizationId);

        DB::beginTransaction();

        try {
            if ($organization && $organization->row_status == BaseModel::ROW_STATUS_PENDING) {
                $this->organizationService->organizationStatusChangeAfterRejection($organization);
                $this->organizationService->organizationUserRejection($organization);

                /** sendSms after Industry Association Registration Approval */
//                $this->sendSmsIndustryAssociationRegistrationApproval($industryAssociation);

                DB::commit();
                $response = [
                    '_response_status' => [
                        "success" => true,
                        "code" => ResponseAlias::HTTP_OK,
                        "message" => "Organization Registration  rejected successfully",
                        "query_time" => $this->startTime->diffInSeconds(Carbon::now())
                    ]
                ];
            } else {
                $response = [
                    '_response_status' => [
                        "success" => false,
                        "code" => ResponseAlias::HTTP_BAD_REQUEST,
                        "message" => "No pending status found for this organization",
                        "query_time" => $this->startTime->diffInSeconds(Carbon::now())
                    ]
                ];
            }


        } catch (Throwable $e) {
            DB::rollBack();
            throw $e;
        }
        return Response::json($response, ResponseAlias::HTTP_OK);

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
     * @throws ValidationException
     */
    public function IndustryAssociationMembershipApplication(Request $request): JsonResponse
    {
        /** @var User $authUser */
        $authUser = Auth::user();
        if ($authUser && $authUser->organization_id) {
            $request->offsetSet('organization_id', $authUser->organization_id);
        }
        $validatedData = $this->organizationService->IndustryAssociationMembershipValidation($request)->validate();
        $this->organizationService->IndustryAssociationMembershipApplication($validatedData);
        $this->sendMailToIndustryAssociationAfterMembershipApplication($validatedData);
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

    private function sendMailToIndustryAssociationAfterMembershipApplication(array $industryAssociationInfo)
    {
        /** @var IndustryAssociation $industryAssociation */
        $industryAssociation = IndustryAssociation::findOrFail($industryAssociationInfo['industry_association_id']);

        /** @var Organization $organization */
        $organization = Organization::findOrFail($industryAssociationInfo['organization_id']);

        $mailService = new MailService();
        $mailService->setTo([
            $industryAssociation->contact_person_email
        ]);
        $from = BaseModel::NISE3_FROM_EMAIL;
        $subject = "Industry Association Registration";
        $mailService->setForm($from);
        $mailService->setSubject($subject);

        $mailService->setMessageBody([
            "organization" => $organization->toArray(),
            "industry_association_info" => $industryAssociation->toArray()
        ]);

        $instituteRegistrationTemplate = 'mail.send-mail-to-industry-association-after-member-ship-application-default-template';
        $mailService->setTemplate($instituteRegistrationTemplate);
        $mailService->sendMail();
    }
}
