<?php

namespace App\Http\Controllers;

use App\Models\BaseModel;
use App\Models\MembershipType;
use App\Models\Organization;
use App\Models\SmefMember;
use App\Models\SmefCluster;
use App\Services\CommonServices\CodeGenerateService;
use App\Services\CommonServices\MailService;
use App\Services\CommonServices\SmsService;
use App\Services\SmefMemberService;
use App\Services\OrganizationService;
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

/**
 *
 */
class SmefMemberController extends Controller
{
    /**
     * @var SmefMemberService
     */
    protected SmefMemberService $smefMemberService;
    /**
     * @var OrganizationService
     */
    protected OrganizationService $organizationService;
    private Carbon $startTime;


    public function __construct(SmefMemberService $smefMemberService, OrganizationService $organizationService)
    {
        $this->smefMemberService = $smefMemberService;
        $this->organizationService = $organizationService;
        $this->startTime = Carbon::now();
    }

    public function smefMemberStaticInfo(): JsonResponse
    {
        $membershipType = MembershipType::all();
        $response = [
            'data' => [
                "form_fill_up_by" => array_map(function ($index, $value) {
                    return [
                        "id" => $index,
                        "title" => $value
                    ];
                }, array_keys(SmefMember::FORM_FILL_UP_LIST), SmefMember::FORM_FILL_UP_LIST),

                "proprietorship" => array_map(function ($index, $value) {
                    return [
                        "id" => $index,
                        "title" => $value
                    ];
                }, array_keys(SmefMember::PROPRIETORSHIP_LIST), SmefMember::PROPRIETORSHIP_LIST),
                "trade_license_authority" => array_map(function ($index, $value) {
                    return [
                        "id" => $index,
                        "title" => $value
                    ];
                }, array_keys(SmefMember::TRADE_LICENSING_AUTHORITY), SmefMember::TRADE_LICENSING_AUTHORITY),
                "sector" => array_map(function ($index, $value) {
                    return [
                        "id" => $index,
                        "title" => $value
                    ];
                }, array_keys(SmefMember::SECTOR), SmefMember::SECTOR),
                "registered_authority" => array_map(function ($index, $value) {
                    return [
                        "id" => $index,
                        "title" => $value
                    ];
                }, array_keys(SmefMember::REGISTERED_AUTHORITY), SmefMember::REGISTERED_AUTHORITY),
                "authorized_authority" => array_map(function ($index, $value) {
                    return [
                        "id" => $index,
                        "title" => $value
                    ];
                }, array_keys(SmefMember::AUTHORIZED_AUTHORITY), SmefMember::AUTHORIZED_AUTHORITY),
                "specialized_area" => array_map(function ($index, $value) {
                    return [
                        "id" => $index,
                        "title" => $value
                    ];
                }, array_keys(SmefMember::SPECIALIZED_AREA), SmefMember::SPECIALIZED_AREA),
                "import_or_export_type" => array_map(function ($index, $value) {
                    return [
                        "id" => $index,
                        "title" => $value
                    ];
                }, array_keys(SmefMember::IMPORT_EXPORT_TYPE), SmefMember::IMPORT_EXPORT_TYPE),
                "worker_type" => array_map(function ($index, $value) {
                    return [
                        "id" => $index,
                        "title" => $value
                    ];
                }, array_keys(SmefMember::WORKER_TYPE), SmefMember::WORKER_TYPE),
                "manpower_type" => array_map(function ($index, $value) {
                    return [
                        "id" => $index,
                        "title" => $value
                    ];
                }, array_keys(SmefMember::MANPOWER_TYPE), SmefMember::MANPOWER_TYPE),
                "bank_account_type" => array_map(function ($index, $value) {
                    return [
                        "id" => $index,
                        "title" => $value
                    ];
                }, array_keys(SmefMember::BANK_ACCOUNT_TYPE), SmefMember::BANK_ACCOUNT_TYPE),
                "land_type" => array_map(function ($index, $value) {
                    return [
                        "id" => $index,
                        "title" => $value
                    ];
                }, array_keys(SmefMember::LAND_TYPE), SmefMember::LAND_TYPE),
                "business_type" => array_map(function ($index, $value) {
                    return [
                        "id" => $index,
                        "title" => $value
                    ];
                }, array_keys(SmefMember::BUSINESS_TYPE), SmefMember::BUSINESS_TYPE),
                "membership_types" => $membershipType->toArray(),
                "smef_clusters" => SmefCluster::all()->toArray()
            ],
            '_response_status' => [
                "success" => true,
                "code" => ResponseAlias::HTTP_CREATED,
                "message" => "OrganizationMember has been Created Successfully",
                "query_time" => $this->startTime->diffInSeconds(\Illuminate\Support\Carbon::now()),
            ]
        ];
        return Response::json($response, ResponseAlias::HTTP_OK);
    }

    /**
     * @throws RequestException
     * @throws Throwable
     * @throws ValidationException
     */
    public function openRegistration(Request $request): JsonResponse
    {
        $organizationMember = app(SmefMember::class);
        /** @var Organization $organization */
        $organization = app(Organization::class);

        $validated = $this->smefMemberService->validator($request)->validate();

        if (!empty($validated['other_authority'])) {
            $authorizedAuthority = $validated['authorized_authority'];
            $validated['authorized_authority']=array_merge($authorizedAuthority, [$validated['other_authority']]);
            unset($validated['other_authority']);
        }



        $httpStatusCode = ResponseAlias::HTTP_CREATED;

        DB::beginTransaction();
        try {
            $validated['code']=CodeGenerateService::getIndustryCode();
            [$organization, $smefMemberData] = $this->smefMemberService->registerSmef($organization, $organizationMember, $validated);
            $validated['organization_id'] = $organization->id;
            $validated['password'] = BaseModel::ADMIN_CREATED_USER_DEFAULT_PASSWORD;

            $createdRegisterUser = $this->smefMemberService->createSmefUser($validated);

            Log::info('smef_id_user_info:' . json_encode($createdRegisterUser));

            if (!($createdRegisterUser && !empty($createdRegisterUser['_response_status']))) {
                throw new RuntimeException('Creating User during  Organization/Industry Creation has been failed!', 500);
            }

            if ($organization) {
                /** Mail send*/
                $to = array($organization->contact_person_email);
                $from = BaseModel::NISE3_FROM_EMAIL;
                $subject = "Smef Membership Registration";
                $message = "Congratulation, You are successfully complete your registration.Your Username: " . $validated['entrepreneur_mobile'] . ",Password:" . $validated['password'] . " You are approved as an active user by admin then you will be sign in.";
                $messageBody = MailService::templateView($message);
                $mailService = new MailService($to, $from, $subject, $messageBody);
                $mailService->sendMail();

                /** Sms send */
                $recipient = $organization->contact_person_mobile;
                $smsMessage = "Congratulation, You are successfully complete your registration";
                $smsService = new SmsService();
                $smsService->sendSms($recipient, $smsMessage);
            }

            $response = [
                'data' => $organization,
                '_response_status' => [
                    "success" => true,
                    "code" => $httpStatusCode,
                    "message" => "OrganizationMember has been Created Successfully",
                    "query_time" => $this->startTime->diffInSeconds(\Illuminate\Support\Carbon::now()),
                ]
            ];

            DB::commit();

        } catch (Throwable $e) {
            DB::rollBack();
            throw $e;
        }
        return Response::json($response, $httpStatusCode);
    }

}
