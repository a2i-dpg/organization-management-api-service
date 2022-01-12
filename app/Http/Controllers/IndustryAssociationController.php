<?php

namespace App\Http\Controllers;

use App\Exceptions\CustomException;
use App\Models\BaseModel;
use App\Models\IndustryAssociation;
use App\Models\Organization;
use App\Services\IndustryAssociationService;
use App\Services\OrganizationService;
use Carbon\Carbon;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\Client\RequestException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Response;
use Illuminate\Validation\ValidationException;
use RuntimeException;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;
use Throwable;

/**
 *
 */
class IndustryAssociationController extends Controller
{

    /**
     * @var IndustryAssociationService
     */
    protected IndustryAssociationService $industryAssociationService;
    /**
     * @var OrganizationService
     */
    protected OrganizationService $organizationService;
    private Carbon $startTime;


    public function __construct(IndustryAssociationService $industryAssociationService, OrganizationService $organizationService)
    {
        $this->industryAssociationService = $industryAssociationService;
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
        $this->authorize('viewAny', IndustryAssociation::class);
        $filter = $this->industryAssociationService->filterValidator($request)->validate();
        $returnedData = $this->industryAssociationService->getIndustryAssociationList($filter, $this->startTime);

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
     * public list for industry Association members
     * @param Request $request
     * @return JsonResponse
     * @throws ValidationException
     */
    public function getPublicIndustryAssociationMemberList(Request $request): JsonResponse
    {
        $filter = $this->organizationService->IndustryAssociationMemberFilterValidator($request)->validate();
        $response = $this->organizationService->getPublicOrganizationListByIndustryAssociation($filter, $this->startTime);

        return Response::json($response, ResponseAlias::HTTP_OK);
    }

    /**
     * List for industry Association members
     * @param Request $request
     * @return JsonResponse
     * @throws ValidationException
     * @throws AuthorizationException
     */
    public function getIndustryAssociationMemberList(Request $request): JsonResponse
    {
        $this->authorize('viewAnyMember', IndustryAssociation::class);
        $filter = $this->organizationService->IndustryAssociationMemberFilterValidator($request)->validate();
        $response = $this->organizationService->getOrganizationListByIndustryAssociation($filter, $this->startTime);

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

    /**
     * Display a specified resource
     * @param Request $request
     * @param int $id
     * @return JsonResponse
     */
    public function industryAssociationDetails(Request $request, int $id): JsonResponse
    {
        $industryAssociation = $this->industryAssociationService->getOneIndustryAssociation($id);

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

    /**
     * public industry member details
     * @param int $industryId
     * @return JsonResponse
     */
    public function getPublicIndustryAssociationMemberDetails(int $industryId): JsonResponse
    {
        $industry = $this->organizationService->getOneOrganization($industryId);

        $response = [
            "data" => $industry,
            "_response_status" => [
                "success" => true,
                "code" => ResponseAlias::HTTP_OK,
                "query_time" => $this->startTime->diffInSeconds(Carbon::now())
            ]
        ];
        return Response::json($response, ResponseAlias::HTTP_OK);
    }

    /**
     *Industry member details
     * @param int $industryId
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function industryAssociationMemberDetails(int $industryId): JsonResponse
    {
        $this->authorize('viewMember', IndustryAssociation::class);
        $industry = $this->organizationService->getOneOrganization($industryId);
        $response = [
            "data" => $industry,
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
     * @throws CustomException
     * @throws RequestException
     * @throws Throwable
     * @throws ValidationException
     */
    public function store(Request $request): JsonResponse
    {
        $industryAssociation = app(IndustryAssociation::class);
        $this->authorize('create', $industryAssociation);

        $validated = $this->industryAssociationService->validator($request)->validate();

        DB::beginTransaction();
        try {
            $industryAssociation = $this->industryAssociationService->store($industryAssociation, $validated);
            if (!($industryAssociation && $industryAssociation->id)) {
                throw new CustomException('IndustryAssociation has not been properly saved to db.');
            }
            $validated['industry_association_id'] = $industryAssociation->id;
            $validated['password'] = BaseModel::ADMIN_CREATED_USER_DEFAULT_PASSWORD;
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
                //$this->industryAssociationService->sendIndustryAssociationRegistrationNotificationByMail($validated);
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
                "query_time" => $this->startTime->diffInSeconds(Carbon::now()),
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
     * Industry Open Registration.
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
            $createdRegisterUser = $this->industryAssociationService->createOpenRegisterUser($validated);

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

                $this->industryAssociationService->sendIndustryAssociationRegistrationNotificationByMail($validated);

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
                "query_time" => $this->startTime->diffInSeconds(Carbon::now()),
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
     * IndustryAssociation Open Registration Approval
     * @param int $industryAssociationId
     * @return JsonResponse
     * @throws Throwable
     */
    public function industryAssociationRegistrationApproval(int $industryAssociationId): JsonResponse
    {
        /** @var IndustryAssociation $industryAssociation */
        $industryAssociation = IndustryAssociation::findOrFail($industryAssociationId);

        DB::beginTransaction();
        try {
            $this->industryAssociationService->industryAssociationStatusChangeAfterApproval($industryAssociation);
            $this->industryAssociationService->industryAssociationUserApproval($industryAssociation);

            /** send Sms after Industry Association Registration Approval */
            //$this->industryAssociationService->sendSmsIndustryAssociationRegistrationApproval($industryAssociation);


            $mailPayload['industry_association_id'] = $industryAssociationId;
            $mailPayload['subject'] = "Industry Association Registration Approval";
            $mailPayload['contact_person_email'] = $industryAssociation->contact_person_mobile;

            /** send Email after Industry Association Registration Approval */
            //$this->industryAssociationService->sendEmailAfterIndustryAssociationRegistrationApprovalOrRejection($mailPayload);

            DB::commit();
            $response = [
                '_response_status' => [
                    "success" => true,
                    "code" => ResponseAlias::HTTP_OK,
                    "message" => "IndustryAssociation Registration  approved successfully",
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
     * IndustryAssociation Open Registration Rejection
     * @param int $industryAssociationId
     * @return JsonResponse
     * @throws Throwable
     */
    public function industryAssociationRegistrationRejection(int $industryAssociationId): JsonResponse
    {
        $industryAssociation = IndustryAssociation::findOrFail($industryAssociationId);

        DB::beginTransaction();
        try {
            $this->industryAssociationService->industryAssociationStatusChangeAfterRejection($industryAssociation);
            $this->industryAssociationService->industryAssociationUserRejection($industryAssociation);
            /** sendSms after Industry Association Registration Rejection */
            // $this->industryAssociationService->sendSmsIndustryAssociationRegistrationRejection($industryAssociation);

            $mailPayload['industry_association_id'] = $industryAssociationId;
            $mailPayload['subject'] = "Industry Association Registration Rejection";
            $mailPayload['contact_person_email'] = $industryAssociation->contact_person_mobile;

            /** send Email after Industry Association Registration Approval */
            // $this->industryAssociationService->sendEmailAfterIndustryAssociationRegistrationApprovalOrRejection($mailPayload);
            DB::commit();
            $response = [
                '_response_status' => [
                    "success" => true,
                    "code" => ResponseAlias::HTTP_OK,
                    "message" => "IndustryAssociation Registration  rejected successfully",
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

    /**
     * IndustryAssociation membership approval
     * @param Request $request
     * @param int $organizationId
     * @return JsonResponse
     * @throws ValidationException
     * @throws Throwable
     */
    public function industryAssociationMembershipApproval(Request $request, int $organizationId): JsonResponse
    {
        $organization = Organization::findOrFail($organizationId);

        $validatedData = $this->industryAssociationService->industryAssociationMembershipValidator($request, $organizationId)->validate();

        if ($organization->row_status == BaseModel::ROW_STATUS_ACTIVE) {
            $this->industryAssociationService->industryAssociationMembershipApproval($validatedData, $organization);
            $validatedData['subject'] = "Industry Association Membership Application Approval";
            $validatedData['organization_id'] = $organizationId;
            $this->industryAssociationService->sendMailToOrganizationAfterIndustryAssociationMembershipApprovalOrRejection($validatedData);

            $response = [
                '_response_status' => [
                    "success" => true,
                    "code" => ResponseAlias::HTTP_OK,
                    "message" => "IndustryAssociation membership approved successfully",
                    "query_time" => $this->startTime->diffInSeconds(Carbon::now())
                ]
            ];
        } else {
            $response = [
                '_response_status' => [
                    "success" => true,
                    "code" => ResponseAlias::HTTP_OK,
                    "message" => "organization is not active",
                    "query_time" => $this->startTime->diffInSeconds(Carbon::now())
                ]
            ];
        }
        return Response::json($response, ResponseAlias::HTTP_OK);
    }

    /**
     * industryAssociation membership rejection
     * @param Request $request
     * @param int $organizationId
     * @return JsonResponse
     * @throws Throwable
     * @throws ValidationException
     */
    public function industryAssociationMembershipRejection(Request $request, int $organizationId): JsonResponse
    {
        $organization = Organization::findOrFail($organizationId);

        $validatedData = $this->industryAssociationService->industryAssociationMembershipValidator($request, $organizationId)->validate();

        if ($organization->row_status == BaseModel::ROW_STATUS_ACTIVE) {
            $this->industryAssociationService->industryAssociationMembershipRejection($validatedData, $organization);

            $validatedData['subject'] = "Industry Association Membership Application Rejection";
            $validatedData['organization_id'] = $organizationId;
            $this->industryAssociationService->sendMailToOrganizationAfterIndustryAssociationMembershipApprovalOrRejection($validatedData);

            $response = [
                '_response_status' => [
                    "success" => true,
                    "code" => ResponseAlias::HTTP_OK,
                    "message" => "IndustryAssociation membership rejection successfully",
                    "query_time" => $this->startTime->diffInSeconds(Carbon::now())
                ]
            ];
        } else {
            $response = [
                '_response_status' => [
                    "success" => true,
                    "code" => ResponseAlias::HTTP_OK,
                    "message" => "organization is not active",
                    "query_time" => $this->startTime->diffInSeconds(Carbon::now())
                ]
            ];
        }
        return Response::json($response, ResponseAlias::HTTP_OK);
    }


    /**
     * @param Request $request
     * @return JsonResponse
     * @throws ValidationException
     */
    public function updateIndustryAssociationProfile(Request $request): JsonResponse
    {
        $industryAssociationId = $request->input('industry_association_id');

        $industryAssociation = IndustryAssociation::findOrFail($industryAssociationId);

        $validated = $this->industryAssociationService->industryAssociationAdminValidator($request)->validate();
        $data = $this->industryAssociationService->update($industryAssociation, $validated);
        $response = [
            'data' => $data,
            '_response_status' => [
                "success" => true,
                "code" => ResponseAlias::HTTP_OK,
                "message" => "IndustryAssociation admin updated successfully.",
                "query_time" => $this->startTime->diffInSeconds(Carbon::now())
            ]
        ];
        return Response::json($response, ResponseAlias::HTTP_CREATED);

    }


    /**
     * @return JsonResponse
     */
    public function getIndustryAssociationProfile(Request $request): JsonResponse
    {
        //$this->authorize('GetIndustryAssociationAdminProfile', Organization::class);

        $industryAssociationId = $request->input('industry_association_id');

        $industryAssociation = $this->industryAssociationService->getOneIndustryAssociation($industryAssociationId);

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


}
