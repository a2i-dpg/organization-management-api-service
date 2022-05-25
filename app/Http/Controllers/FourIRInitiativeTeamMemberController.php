<?php

namespace App\Http\Controllers;

use App\Models\FourIRInitiative;
use App\Models\FourIRInitiativeTeamMember;
use App\Services\FourIRServices\FourIrInitiativeTeamMemberService;
use Carbon\Carbon;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Response;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;
use Throwable;

class FourIRInitiativeTeamMemberController extends Controller
{
    public FourIrInitiativeTeamMemberService $fourIrProjectTeamMemberService;
    private Carbon $startTime;

    /**
     * FourIRInitiativeTeamMemberController constructor.
     *
     * @param FourIrInitiativeTeamMemberService $fourIrProjectTeamMemberService
     */
    public function __construct(FourIrInitiativeTeamMemberService $fourIrProjectTeamMemberService)
    {
        $this->startTime = Carbon::now();
        $this->fourIrProjectTeamMemberService = $fourIrProjectTeamMemberService;
    }

    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @return JsonResponse
     * @throws ValidationException
     * @throws AuthorizationException
     */
    public function getList(Request $request): JsonResponse
    {
        $this->authorize('viewAnyInitiativeStep', FourIRInitiative::class);

        $filter = $this->fourIrProjectTeamMemberService->filterValidator($request)->validate();
        $response = $this->fourIrProjectTeamMemberService->getFourIrInitiativeTeamMemberList($filter, $this->startTime);
        return Response::json($response,ResponseAlias::HTTP_OK);
    }

    /**
     * @param int $id
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function read(int $id): JsonResponse
    {
        $fourIrProjectTeamMember = $this->fourIrProjectTeamMemberService->getOneFourIrInitiativeTeamMember($id);
        $this->authorize('viewSingleInitiativeStep', FourIRInitiative::class);
        $response = [
            "data" => $fourIrProjectTeamMember,
            "_response_status" => [
                "success" => true,
                "code" => ResponseAlias::HTTP_OK,
                "query_time" => $this->startTime->diffInSeconds(Carbon::now())
            ]
        ];
        return Response::json($response,ResponseAlias::HTTP_OK);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     * @return JsonResponse
     * @throws AuthorizationException
     * @throws ValidationException
     * @throws Throwable
     */
    function store(Request $request): JsonResponse
    {
       // $this->authorize('create', FourIRInitiativeTeamMember::class);
        $this->authorize('creatInitiativeStep', FourIRInitiative::class);
        $validated = $this->fourIrProjectTeamMemberService->validator($request)->validate();
        $data = $this->fourIrProjectTeamMemberService->store($validated);

        $response = [
            'data' => $data,
            '_response_status' => [
                "success" => true,
                "code" => ResponseAlias::HTTP_CREATED,
                "message" => "Four Ir Initiative Team Member added successfully",
                "query_time" => $this->startTime->diffInSeconds(Carbon::now())
            ]
        ];

        return Response::json($response, ResponseAlias::HTTP_CREATED);
    }

    /**
     * Update the specified resource in storage
     * @param Request $request
     * @param int $id
     * @return JsonResponse
     * @throws AuthorizationException
     * @throws ValidationException
     * @throws Throwable
     */
    public function update(Request $request, int $id): JsonResponse
    {
        $fourIrProjectTeamMember = FourIRInitiativeTeamMember::findOrFail($id);
        $this->authorize('updateInitiativeStep', FourIRInitiative::class);


        $validated = $this->fourIrProjectTeamMemberService->validator($request, $id)->validate();
        try {
            DB::beginTransaction();

            $data = $this->fourIrProjectTeamMemberService->update($fourIrProjectTeamMember, $validated);
            DB::commit();
        } catch (Throwable $e){
            DB::rollBack();
            throw $e;
        }

        $response = [
            'data' => $data,
            '_response_status' => [
                "success" => true,
                "code" => ResponseAlias::HTTP_OK,
                "message" => "Four Ir Initiative Team Member updated successfully",
                "query_time" => $this->startTime->diffInSeconds(Carbon::now())
            ]
        ];

        return Response::json($response, ResponseAlias::HTTP_CREATED);
    }

    /**
     * Remove the specified resource from storage
     *
     * @param int $id
     * @return JsonResponse
     * @throws AuthorizationException
     * @throws Throwable
     */
    public function destroy(int $id): JsonResponse
    {
        $fourIrProjectTeamMember = FourIRInitiativeTeamMember::findOrFail($id);
        $this->authorize('deleteInitiativeStep', FourIRInitiative::class);
        try {
            DB::beginTransaction();

            $this->fourIrProjectTeamMemberService->destroy($fourIrProjectTeamMember);
            DB::commit();
        } catch (Throwable $e){
            DB::rollBack();
            throw $e;
        }

        $response = [
            '_response_status' => [
                "success" => true,
                "code" => ResponseAlias::HTTP_OK,
                "message" => "Four Ir Initiative Team Member deleted successfully",
                "query_time" => $this->startTime->diffInSeconds(Carbon::now())
            ]
        ];
        return Response::json($response, ResponseAlias::HTTP_OK);
    }

    /**
     * update the specified resource from storage
     *
     * @param Request $request
     * @return JsonResponse
     * @throws ValidationException
     */
    public function setTeamLaunchingDate(Request $request): JsonResponse
    {
        $validated = $this->fourIrProjectTeamMemberService->teamLaunchingDateValidator($request)->validate();
        $data = $this->fourIrProjectTeamMemberService->addTeamLaunchingDate($validated);

        $response = [
            'data' => $data,
            '_response_status' => [
                "success" => true,
                "code" => ResponseAlias::HTTP_OK,
                "message" => "Four Ir Team launching date updated successfully",
                "query_time" => $this->startTime->diffInSeconds(Carbon::now())
            ]
        ];

        return Response::json($response, ResponseAlias::HTTP_CREATED);
    }
}
