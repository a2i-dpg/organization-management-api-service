<?php

namespace App\Http\Controllers;

use App\Exceptions\CustomException;
use App\Models\BaseModel;
use App\Models\IndustryAssociation;
use App\Models\MembershipType;
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

    public function nascibMemberStaticInfo(): JsonResponse
    {
        $membershipType = MembershipType::all();
        $response = [
            'data' => [
                "form_fill_up_by" => NascibMember::FORM_FILL_UP_LIST,
                "proprietorship" => NascibMember::PROPRIETORSHIP_LIST,
                "trade_license_authority" => NascibMember::TRADE_LICENSING_AUTHORITY,
                "sector" => NascibMember::SECTOR,
                "registered_authority" => NascibMember::REGISTERED_AUTHORITY,
                "authorized_authority" => NascibMember::AUTHORIZED_AUTHORITY,
                "specialized_area" => NascibMember::SPECIALIZED_AREA,
                "import_or_export_type" => NascibMember::IMPORT_EXPORT_TYPE,
                "worker_type" => NascibMember::WORKER_TYPE,
                "manpower_type" => NascibMember::MANPOWER_TYPE,
                "bank_account_type" => NascibMember::BANK_ACCOUNT_TYPE,
                "land_type" => NascibMember::LAND_TYPE,
                "business_type" => NascibMember::BUSINESS_TYPE,
                "membership_types" => $membershipType->toArray()
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

    public function openRegistration(Request $request): JsonResponse
    {
        $organizationMember = app(NascibMember::class);
        /** @var Organization $organization */
        $organization = app(Organization::class);

        $validated = $this->nascibMemberService->validator($request)->validate();
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

            $response = [
                'data' => $organization,
                '_response_status' => [
                    "success" => true,
                    "code" => ResponseAlias::HTTP_CREATED,
                    "message" => "OrganizationMember has been Created Successfully",
                    "query_time" => $this->startTime->diffInSeconds(\Illuminate\Support\Carbon::now()),
                ]
            ];

            DB::commit();
            $httpStatusCode = ResponseAlias::HTTP_BAD_REQUEST;

        } catch (Throwable $e) {
            DB::rollBack();
            throw $e;
        }
        return Response::json($response, $httpStatusCode);
    }

}
