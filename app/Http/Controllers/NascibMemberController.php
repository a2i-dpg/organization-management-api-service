<?php

namespace App\Http\Controllers;

use App\Exceptions\CustomException;
use App\Models\BaseModel;
use App\Models\IndustryAssociation;
use App\Models\MembershipType;
use App\Models\Organization;
use App\Models\NascibMember;
use App\Models\SmefCluster;
use App\Services\CommonServices\MailService;
use App\Services\CommonServices\SmsService;
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

    public function nascibMemberStaticInfo(): JsonResponse
    {
        $membershipType = MembershipType::all();
        $response = [
            'data' => [
                "form_fill_up_by" => array_map(function ($index, $value) {
                    return [
                        "id" => $index,
                        "title" => $value
                    ];
                }, array_keys(NascibMember::FORM_FILL_UP_LIST), NascibMember::FORM_FILL_UP_LIST),

                "proprietorship" => array_map(function ($index, $value) {
                    return [
                        "id" => $index,
                        "title" => $value
                    ];
                }, array_keys(NascibMember::PROPRIETORSHIP_LIST), NascibMember::PROPRIETORSHIP_LIST),
                "trade_license_authority" => array_map(function ($index, $value) {
                    return [
                        "id" => $index,
                        "title" => $value
                    ];
                }, array_keys(NascibMember::TRADE_LICENSING_AUTHORITY), NascibMember::TRADE_LICENSING_AUTHORITY),
                "sector" => array_map(function ($index, $value) {
                    return [
                        "id" => $index,
                        "title" => $value
                    ];
                }, array_keys(NascibMember::SECTOR), NascibMember::SECTOR),
                "registered_authority" => array_map(function ($index, $value) {
                    return [
                        "id" => $index,
                        "title" => $value
                    ];
                }, array_keys(NascibMember::REGISTERED_AUTHORITY), NascibMember::REGISTERED_AUTHORITY),
                "authorized_authority" => array_map(function ($index, $value) {
                    return [
                        "id" => $index,
                        "title" => $value
                    ];
                }, array_keys(NascibMember::AUTHORIZED_AUTHORITY), NascibMember::AUTHORIZED_AUTHORITY),
                "specialized_area" => array_map(function ($index, $value) {
                    return [
                        "id" => $index,
                        "title" => $value
                    ];
                }, array_keys(NascibMember::SPECIALIZED_AREA), NascibMember::SPECIALIZED_AREA),
                "import_or_export_type" => array_map(function ($index, $value) {
                    return [
                        "id" => $index,
                        "title" => $value
                    ];
                }, array_keys(NascibMember::IMPORT_EXPORT_TYPE), NascibMember::IMPORT_EXPORT_TYPE),
                "worker_type" => array_map(function ($index, $value) {
                    return [
                        "id" => $index,
                        "title" => $value
                    ];
                }, array_keys(NascibMember::WORKER_TYPE), NascibMember::WORKER_TYPE),
                "manpower_type" => array_map(function ($index, $value) {
                    return [
                        "id" => $index,
                        "title" => $value
                    ];
                }, array_keys(NascibMember::MANPOWER_TYPE), NascibMember::MANPOWER_TYPE),
                "bank_account_type" => array_map(function ($index, $value) {
                    return [
                        "id" => $index,
                        "title" => $value
                    ];
                }, array_keys(NascibMember::BANK_ACCOUNT_TYPE), NascibMember::BANK_ACCOUNT_TYPE),
                "land_type" => array_map(function ($index, $value) {
                    return [
                        "id" => $index,
                        "title" => $value
                    ];
                }, array_keys(NascibMember::LAND_TYPE), NascibMember::LAND_TYPE),
                "business_type" => array_map(function ($index, $value) {
                    return [
                        "id" => $index,
                        "title" => $value
                    ];
                }, array_keys(NascibMember::BUSINESS_TYPE), NascibMember::BUSINESS_TYPE),
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

        $organizationMember = app(NascibMember::class);
        /** @var Organization $organization */
        $organization = app(Organization::class);

        $validated = $this->nascibMemberService->validator($request)->validate();

        $httpStatusCode = ResponseAlias::HTTP_CREATED;

        DB::beginTransaction();
        try {
            [$organization, $nascibMemberData] = $this->nascibMemberService->registerNascib($organization, $organizationMember, $validated);
            $validated['organization_id'] = $organization->id;
            $validated['password'] = BaseModel::ADMIN_CREATED_USER_DEFAULT_PASSWORD;

            $createdRegisterUser = $this->nascibMemberService->createNascibUser($validated);

            Log::info('nascib_id_user_info:' . json_encode($createdRegisterUser));

            if (!($createdRegisterUser && !empty($createdRegisterUser['_response_status']))) {
                throw new RuntimeException('Creating User during  Organization/Industry Creation has been failed!', 500);
            }

            if ($organization) {
                /** Mail send*/
                $to = array($organization->contact_person_email);
                $from = BaseModel::NISE3_FROM_EMAIL;
                $subject = "Nascib Membership Registration";
                $message = "Congratulation, You are successfully complete your registration.<br> Your Username: " . $validated['entrepreneur_mobile'] . ",<br> Password:" . $validated['password'] . "<br> You are approved as an active user by admin then you will be sign in.";
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
