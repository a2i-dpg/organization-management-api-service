<?php

namespace App\Http\Controllers;

use App\Models\BaseModel;
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
    public FourIRCreateApproveCourseService $fourIrInitiativeService;
    public FourIRFileLogService $fourIRFileLogService;
    private Carbon $startTime;

    /**
     * FourIRInitiativeController constructor.
     *
     * @param FourIRCreateApproveCourseService $fourIrInitiativeService
     * @param FourIRFileLogService $fourIRFileLogService
     */
    public function __construct(FourIRCreateApproveCourseService $fourIrInitiativeService, FourIRFileLogService $fourIRFileLogService)
    {
        $this->startTime = Carbon::now();
        $this->fourIrInitiativeService = $fourIrInitiativeService;
        $this->fourIRFileLogService = $fourIRFileLogService;
    }

    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @return JsonResponse
     * @throws ValidationException
     */
    public function getList(Request $request): JsonResponse
    {
        //$this->authorize('viewAny', FourIRInitiative::class);

        $filter = $this->fourIrInitiativeService->filterValidator($request)->validate();
        $response = $this->fourIrInitiativeService->getFourIRInitiativeList($filter);
        return Response::json($response, ResponseAlias::HTTP_OK);
    }

    /**
     * @param int $id
     * @return JsonResponse
     */
    public function read(int $id): JsonResponse
    {
        /** This $id must be course_id of institute service course table */
        $response = $this->fourIrInitiativeService->getOneFourIRInitiative($id);
        //$this->authorize('view', $fourIrInitiative);
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
        // $this->authorize('create', FourIRInitiative::class);

        $validated = $this->fourIrInitiativeService->validator($request)->validate();
        $validated['row_status'] = BaseModel::ROW_STATUS_INACTIVE;
        $response = $this->fourIrInitiativeService->store($validated);

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
        $validated = $this->fourIrInitiativeService->validator($request, $id)->validate();
        $response = $this->fourIrInitiativeService->update($validated, $id);

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
        $response = $this->fourIrInitiativeService->approveFourIrCourse($id);

        return Response::json($response, ResponseAlias::HTTP_OK);
    }
}
