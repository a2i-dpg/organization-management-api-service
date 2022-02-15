<?php

namespace App\Http\Controllers;

use App\Exceptions\CustomException;
use App\Models\BaseModel;
use App\Models\IndustryAssociation;
use App\Models\Organization;
use App\Models\NascibMember;
use App\Services\CommonServices\CodeGenerateService;
use App\Services\CommonServices\MailService;
use App\Services\CommonServices\SmsService;
use App\Services\IndustryAssociationService;
use App\Services\NascibMemberService;
use App\Services\OrganizationService;
use Carbon\Carbon;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\Client\RequestException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Response;
use Illuminate\Validation\ValidationException;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
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
            $organizationMember = $this->nascibMemberService->store($organization,$organizationMember, $validated);

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
        $validated['code'] = CodeGenerateService::getIndustryAssociationCode();

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

                /** Mail send after user registration */
                $to = array($validated['contact_person_email']);
                $from = BaseModel::NISE3_FROM_EMAIL;
                $subject = "User Registration Information";
                $message = "Congratulation, You are successfully complete your registration as " . $validated['title'] . " user. Username: " . $validated['contact_person_mobile'] . " & Password: " . $validated['password'] . " You are an inactive user until approved by System Admin.";
                $messageBody = MailService::templateView($message);
                $mailService = new MailService($to, $from, $subject, $messageBody);
                $mailService->sendMail();

                /** SMS send after user registration */
                $recipient = $validated['contact_person_mobile'];
                $smsMessage = "You are successfully complete your registration as " . $validated['title'] . " user";
                $smsService = new SmsService();
                $smsService->sendSms($recipient, $smsMessage);

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
     * @param Request $request
     * @param int $industryAssociationId
     * @return JsonResponse
     * @throws RequestException
     * @throws Throwable
     */
    public function industryAssociationRegistrationApproval(Request $request, int $industryAssociationId): JsonResponse
    {

        $industryAssociation = IndustryAssociation::findOrFail($industryAssociationId);

        if ($industryAssociation->row_status == BaseModel::ROW_STATUS_PENDING) {
            throw_if(empty($request->input('permission_sub_group_id')), ValidationException::withMessages([
                "permission_sub_group_id is required.[50000]"
            ]));
        }

        DB::beginTransaction();
        try {
            $this->industryAssociationService->industryAssociationUserApproval($request, $industryAssociation);
            $this->industryAssociationService->industryAssociationStatusChangeAfterApproval($industryAssociation);

            /** send Email after Industry Association Registration Approval */

            /** Mail send */
            $to = array($industryAssociation->contact_person_email);
            $from = BaseModel::NISE3_FROM_EMAIL;
            $subject = "User Approval Information";
            $message = "Congratulation, You are  approved as a " . $industryAssociation->title . " user. You are now active user";
            $messageBody = MailService::templateView($message);
            $mailService = new MailService($to, $from, $subject, $messageBody);
            $mailService->sendMail();

            /** Sms send */
            $recipient = $industryAssociation->contact_person_mobile;
            $smsMessage = "Congratulation, You are approved as a " . $industryAssociation->title . " user";
            $smsService = new SmsService();
            $smsService->sendSms($recipient, $smsMessage);

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

            /** Mail send */
            $to = array($industryAssociation->contact_person_email);
            $from = BaseModel::NISE3_FROM_EMAIL;
            $subject = "User Rejection Information";
            $message = "You are rejected as a " . $industryAssociation->title . " user. You are not active user now";
            $messageBody = MailService::templateView($message);
            $mailService = new MailService($to, $from, $subject, $messageBody);
            $mailService->sendMail();

            /** Sms send */
            $recipient = $industryAssociation->contact_person_mobile;
            $smsMessage = "You are rejected as a " . $industryAssociation->title . " user. You are not active user now";
            $smsService = new SmsService();
            $smsService->sendSms($recipient, $smsMessage);

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
        $this->industryAssociationService->industryAssociationMembershipApproval($validatedData, $organization);
        $industryAssociation = IndustryAssociation::findOrFail($validatedData['industry_association_id']);


        /** Mail send */
        $to = array($industryAssociation->contact_person_email);
        $from = BaseModel::NISE3_FROM_EMAIL;
        $subject = "Industry Association Membership Approval";
        $message = "You are approved as a " . $industryAssociation->title . " member.";
        $messageBody = MailService::templateView($message);
        $mailService = new MailService($to, $from, $subject, $messageBody);
        $mailService->sendMail();

        /** Sms send */
        $recipient = $industryAssociation->contact_person_mobile;
        $smsMessage = "You are approved as a " . $industryAssociation->title . " member.";
        $smsService = new SmsService();
        $smsService->sendSms($recipient, $smsMessage);


        $response = [
            '_response_status' => [
                "success" => true,
                "code" => ResponseAlias::HTTP_OK,
                "message" => "IndustryAssociation membership approved successfully",
                "query_time" => $this->startTime->diffInSeconds(Carbon::now())
            ]
        ];
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
        $this->industryAssociationService->industryAssociationMembershipRejection($validatedData, $organization);
        $industryAssociation = IndustryAssociation::findOrFail($validatedData['industry_association_id']);

        /** Mail send */
        $to = array($industryAssociation->contact_person_email);
        $from = BaseModel::NISE3_FROM_EMAIL;
        $subject = "Industry Association Membership Rejection";
        $message = "You are rejected as a " . $industryAssociation->title . " member.";
        $messageBody = MailService::templateView($message);
        $mailService = new MailService($to, $from, $subject, $messageBody);
        $mailService->sendMail();

        /** Sms send */
        $recipient = $industryAssociation->contact_person_mobile;
        $smsMessage = "You are rejected as a " . $industryAssociation->title . " member.";
        $smsService = new SmsService();
        $smsService->sendSms($recipient, $smsMessage);

        $response = [
            '_response_status' => [
                "success" => true,
                "code" => ResponseAlias::HTTP_OK,
                "message" => "IndustryAssociation membership rejection successfully",
                "query_time" => $this->startTime->diffInSeconds(Carbon::now())
            ]
        ];
        return Response::json($response, ResponseAlias::HTTP_OK);
    }


    /**
     * @param Request $request
     * @return JsonResponse
     * @throws ValidationException
     * @throws AuthorizationException
     */
    public function updateIndustryAssociationProfile(Request $request): JsonResponse
    {
        $industryAssociationId = $request->input('industry_association_id');
        $industryAssociation = IndustryAssociation::findOrFail($industryAssociationId);
        $this->authorize('updateProfile', $industryAssociation);
        $validated = $this->industryAssociationService->industryAssociationProfileUpdateValidator($request)->validate();
        $data = $this->industryAssociationService->update($industryAssociation, $validated);
        $data = array_merge($data->toArray(), ["skills" => $data->skills()->get()]);
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
     * @param Request $request
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function getIndustryAssociationProfile(Request $request): JsonResponse
    {
        $industryAssociationId = $request->input('industry_association_id');
        $industryAssociation = $this->industryAssociationService->getOneIndustryAssociation($industryAssociationId);

        $this->authorize('viewProfile', $industryAssociation);


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
     * @param Request $request
     * @return JsonResponse
     */
    public function industryAssociationDashboardStatistics(Request $request): JsonResponse
    {
        $industryAssociationId = $request->input('industry_association_id');
        $industryAssociation = IndustryAssociation::findOrFail($industryAssociationId);

        $dashboardStatistics = $this->industryAssociationService->getindustryAssociationDashboardStatistics($industryAssociation);

        $response = [
            "data" => $dashboardStatistics,
            "_response_status" => [
                "success" => true,
                "code" => ResponseAlias::HTTP_OK,
                "query_time" => $this->startTime->diffInSeconds(Carbon::now())
            ]
        ];
        return Response::json($response, ResponseAlias::HTTP_OK);
    }


}
