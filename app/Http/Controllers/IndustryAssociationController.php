<?php

namespace App\Http\Controllers;

use App\Exceptions\CustomException;
use App\Models\BaseModel;
use App\Models\IndustryAssociation;
use App\Models\Organization;
use App\Services\CommonServices\MailService;
use App\Services\CommonServices\SmsService;
use App\Services\IndustryAssociationService;
use App\Services\OrganizationService;
use Carbon\Carbon;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\Client\RequestException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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
class IndustryAssociationController extends Controller
{

    protected IndustryAssociationService $industryAssociationService;
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
     * Display a specified resource
     * @param Request $request
     * @param int $id
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function read(Request $request, int $id): JsonResponse
    {
        $industryAssociation = $this->industryAssociationService->getOneIndustryAssociation($id);

        $requestHeaders = $request->header();
        if (empty($requestHeaders[BaseModel::DEFAULT_SERVICE_TO_SERVICE_CALL_KEY][0]) ||
            $requestHeaders[BaseModel::DEFAULT_SERVICE_TO_SERVICE_CALL_KEY][0] === BaseModel::DEFAULT_SERVICE_TO_SERVICE_CALL_FLAG_FALSE) {
            $this->authorize('view', $industryAssociation);
        }
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

                $this->industryAssociationService->sendIndustryAssociationOpenRegistrationNotificationByMail($validated);

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
            if ($industryAssociation && $industryAssociation->row_status == BaseModel::ROW_STATUS_PENDING) {
                $this->industryAssociationService->industryAssociationStatusChangeAfterApproval($industryAssociation);
                $this->industryAssociationService->industryAssociationUserApproval($industryAssociation);

                /** sendSms after Industry Association Registration Approval */
                $this->industryAssociationService->sendSmsIndustryAssociationRegistrationApproval($industryAssociation);

                DB::commit();
                $response = [
                    '_response_status' => [
                        "success" => true,
                        "code" => ResponseAlias::HTTP_OK,
                        "message" => "IndustryAssociation Registration  approved successfully",
                        "query_time" => $this->startTime->diffInSeconds(Carbon::now())
                    ]
                ];
            } else {
                $response = [
                    '_response_status' => [
                        "success" => false,
                        "code" => ResponseAlias::HTTP_BAD_REQUEST,
                        "message" => "No pending status found for this IndustryAssociation",
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
            if ($industryAssociation && $industryAssociation->row_status == BaseModel::ROW_STATUS_PENDING) {

                $this->industryAssociationService->industryAssociationStatusChangeAfterRejection($industryAssociation);
                $this->industryAssociationService->industryAssociationUserRejection($industryAssociation);
                DB::commit();
                $response = [
                    '_response_status' => [
                        "success" => true,
                        "code" => ResponseAlias::HTTP_OK,
                        "message" => "IndustryAssociation Registration  rejected successfully",
                        "query_time" => $this->startTime->diffInSeconds(Carbon::now())
                    ]
                ];
            } else {
                $response = [
                    '_response_status' => [
                        "success" => false,
                        "code" => ResponseAlias::HTTP_BAD_REQUEST,
                        "message" => "No pending status found for this IndustryAssociation",
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
     * Industry registration or IndustryAssociation membership approval
     * @param Request $request
     * @param int $organizationId
     * @return JsonResponse
     * @throws ValidationException
     * @throws Throwable
     */
    public function registrationOrMembershipApproval(Request $request, int $organizationId): JsonResponse
    {
        $authUser = Auth::user();
        if ($authUser && $authUser->industry_association_id) {
            $request->offsetSet('industry_association_id', $authUser->industry_association_id);
        }

        $organization = Organization::findOrFail($organizationId);

        $validatedData = $this->industryAssociationService->registrationOrMembershipValidator($request, $organizationId)->validate();

        DB::beginTransaction();
        try {
            $approveData = $this->industryAssociationService->industryAssociationMembershipApproval($validatedData, $organization);

            if ($organization && $organization->row_status == BaseModel::ROW_STATUS_PENDING && $approveData->is_reg_approval) {
                $organization = $this->organizationService->organizationStatusChangeAfterApproval($organization);
                $this->organizationService->organizationUserApproval($organization);
                $response = [
                    '_response_status' => [
                        "success" => true,
                        "code" => ResponseAlias::HTTP_OK,
                        "message" => "organization registration approved successfully",
                        "query_time" => $this->startTime->diffInSeconds(Carbon::now())
                    ]
                ];
            } else {
                $response = [
                    '_response_status' => [
                        "success" => true,
                        "code" => ResponseAlias::HTTP_OK,
                        "message" => "IndustryAssociation membership approved successfully",
                        "query_time" => $this->startTime->diffInSeconds(Carbon::now())
                    ]
                ];
            }
            $organization = $organization->toArray();
            $organization['industry_association_id'] = $validatedData['industry_association_id'];
            $this->industryAssociationService->sendMailOrganizationUserApproval($organization);
            DB::commit();

        } catch (Throwable $e) {
            DB::rollBack();
            throw $e;
        }
        return Response::json($response, ResponseAlias::HTTP_OK);
    }

    /**
     * industry registration or industryAssociation membership rejection
     * @param Request $request
     * @param int $organizationId
     * @return JsonResponse
     * @throws RequestException
     * @throws Throwable
     * @throws ValidationException
     */
    public function registrationOrMembershipRejection(Request $request, int $organizationId): JsonResponse
    {
        $authUser = Auth::user();

        if ($authUser && $authUser->industry_association_id) {
            $request->offsetSet('industry_association_id', $authUser->industry_association_id);
        }
        $organization = Organization::findOrFail($organizationId);

        $validatedData = $this->industryAssociationService->registrationOrMembershipValidator($request, $organizationId)->validate();

        DB::beginTransaction();
        try {
            $rejectedData = $this->industryAssociationService->industryAssociationMembershipRejection($validatedData, $organization);
            if ($organization && $organization->row_status == BaseModel::ROW_STATUS_PENDING && $rejectedData->is_reg_approval) {
                $organization = $this->organizationService->organizationStatusChangeAfterRejection($organization);
                $this->organizationService->organizationUserRejection($organization);
                $response = [
                    '_response_status' => [
                        "success" => true,
                        "code" => ResponseAlias::HTTP_OK,
                        "message" => "organization registration rejected successfully",
                        "query_time" => $this->startTime->diffInSeconds(Carbon::now())
                    ]
                ];
            } else {
                $response = [
                    '_response_status' => [
                        "success" => true,
                        "code" => ResponseAlias::HTTP_OK,
                        "message" => "IndustryAssociation membership rejected successfully",
                        "query_time" => $this->startTime->diffInSeconds(Carbon::now())
                    ]
                ];
            }
            DB::commit();


        } catch (Throwable $e) {
            DB::rollBack();
            throw $e;
        }
        return Response::json($response, ResponseAlias::HTTP_OK);
    }


    public function updateIndustryAssociationAdminProfile(Request $request) :JsonResponse
    {
        //$this->authorize('updateIndustryAssociatinProfile', Organization::class);
        $authUser = Auth::user();
        $industryAssociationId = null;
        if ($authUser && $authUser->industry_association_id) {
            $industryAssociationId = $authUser->industry_association_id;
        }
        $industryAssociation = IndustryAssociation::findOrFail($industryAssociationId);

//        $this->authorize('update', $industryAssociation);

        $validated = $this->industryAssociationService->industryAssociationAdminValidator($request, $industryAssociationId)->validate();
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


    public function getIndustryAssociationAdminProfile():JsonResponse
    {
        //$this->authorize('GetIndustryAssociationAdminProfile', Organization::class);

        $authUser = Auth::user();
        $industryAssociationId = null;
        if ($authUser && $authUser->industry_association_id) {
            $industryAssociationId = $authUser->industry_association_id;
        }
        $industryAssociation = IndustryAssociation::findOrFail($industryAssociationId);
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
