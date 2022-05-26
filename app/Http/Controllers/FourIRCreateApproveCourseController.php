<?php

namespace App\Http\Controllers;

use App\Models\BaseModel;
use App\Models\FourIRCreateAndApprove;
use App\Models\FourIRInitiative;
use App\Services\FourIRServices\FourIRFileLogService;
use App\Services\FourIRServices\FourIRCreateApproveCourseService;
use Carbon\Carbon;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;
use Throwable;

class FourIRCreateApproveCourseController extends Controller
{
    public FourIRCreateApproveCourseService $fourIrCreateAndApproveService;
    public FourIRFileLogService $fourIRFileLogService;
    private Carbon $startTime;

    /**
     * FourIRInitiativeController constructor.
     *
     * @param FourIRCreateApproveCourseService $fourIrCreateAndApproveService
     * @param FourIRFileLogService $fourIRFileLogService
     */
    public function __construct(FourIRCreateApproveCourseService $fourIrCreateAndApproveService, FourIRFileLogService $fourIRFileLogService)
    {
        $this->startTime = Carbon::now();
        $this->fourIrCreateAndApproveService = $fourIrCreateAndApproveService;
        $this->fourIRFileLogService = $fourIRFileLogService;
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

        $filter = $this->fourIrCreateAndApproveService->filterValidator($request)->validate();
        $response = $this->fourIrCreateAndApproveService->getFourIRCourseList($filter);
        return Response::json($response, ResponseAlias::HTTP_OK);
    }

    /**
     * @param int $id
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function read(int $id): JsonResponse
    {
        /** This $id must be course_id of institute service course table */
        $response = $this->fourIrCreateAndApproveService->getOneFourIRCourse($id);
        $this->authorize('viewSingleInitiativeStep',FourIRInitiative::class);

        return Response::json($response, ResponseAlias::HTTP_OK);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     * @return JsonResponse
     * @throws ValidationException
     * @throws Throwable
     */
    function store(Request $request): JsonResponse
    {
        $this->authorize('creatInitiativeStep', FourIRInitiative::class);
        $validated = $this->fourIrCreateAndApproveService->validator($request)->validate();
        $validated['row_status'] = BaseModel::ROW_STATUS_INACTIVE;
        $response = $this->fourIrCreateAndApproveService->store($validated);

        return Response::json($response, ResponseAlias::HTTP_CREATED);
    }

    /**
     * Update the specified resource in storage
     *
     * @param Request $request
     * @param int $id
     * @return JsonResponse
     * @throws AuthorizationException
     * @throws ValidationException
     * @throws Throwable
     */
    public function update(Request $request, int $id): JsonResponse
    {
        $validated = $this->fourIrCreateAndApproveService->validator($request, $id)->validate();
        $response = $this->fourIrCreateAndApproveService->update($validated, $id);

        return Response::json($response, ResponseAlias::HTTP_OK);
    }

    /**
     * Update the specified resource in storage
     *
     * @param int $id
     * @return JsonResponse
     */
    public function approveFourIrCourse(int $id): JsonResponse
    {
        $this->authorize('approveCourseInitiativeStep', FourIRInitiative::class);
        $response = $this->fourIrCreateAndApproveService->approveFourIrCourse($id);

        return Response::json($response, ResponseAlias::HTTP_OK);
    }
}
