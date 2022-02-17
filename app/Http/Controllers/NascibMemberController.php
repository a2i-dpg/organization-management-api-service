<?php

namespace App\Http\Controllers;

use App\Exceptions\CustomException;
use App\Models\BaseModel;
use App\Models\IndustryAssociation;
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

    public function openRegistration(Request $request): JsonResponse
    {
        $organizationMember = app(NascibMember::class);
        $organization = app(Organization::class);
        //$this->authorize('create', $organizationMember);

        $validated = $this->nascibMemberService->validator($request)->validate();

        DB::beginTransaction();
        try {
            $organizationMember = $this->nascibMemberService->registerNascib($organization, $organizationMember, $validated);


            $validated['organization_id'] = $organizationMember->organization_id;
            $validated['password'] = BaseModel::ADMIN_CREATED_USER_DEFAULT_PASSWORD;


            $createdRegisterUser = $this->nascibMemberService->createNascibUser($validated); //TODO: IDP user is not created

            Log::info('Nascib id_user_info:' . json_encode($createdRegisterUser));

            if (!($createdRegisterUser && !empty($createdRegisterUser['_response_status']))) {
                throw new RuntimeException('Creating User during  Organization/Industry Creation has been failed!', 500);
            }

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

        } catch (Throwable $e) {
            DB::rollBack();
            throw $e;
        }
        return Response::json($response, $httpStatusCode);
    }

}
