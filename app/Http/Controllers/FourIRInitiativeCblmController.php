<?php

namespace App\Http\Controllers;

use App\Models\FourIRInitiative;
use App\Models\FourIRInitiativeCsCurriculumCblm;
use App\Services\FourIRServices\FourIRFileLogService;
use App\Services\FourIRServices\FourIRInitiativeCblmService;
use Carbon\Carbon;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Response;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;
use Throwable;

class FourIRInitiativeCblmController extends Controller
{
    public FourIRInitiativeCblmService $fourIrInitiativeCsCurriculumCblmService;
    public FourIRFileLogService $fourIRFileLogService;
    private Carbon $startTime;

    /**
     * FourIRInitiativeCsController constructor.
     *
     * @param FourIRInitiativeCblmService $fourIrInitiativeCsCurriculumCblmService
     * @param FourIRFileLogService $fourIRFileLogService
     */
    public function __construct(FourIRInitiativeCblmService $fourIrInitiativeCsCurriculumCblmService, FourIRFileLogService $fourIRFileLogService)
    {
        $this->startTime = Carbon::now();
        $this->fourIrInitiativeCsCurriculumCblmService = $fourIrInitiativeCsCurriculumCblmService;
        $this->fourIRFileLogService = $fourIRFileLogService;
    }

    /**
     * @param Request $request
     * @return JsonResponse
     * @throws ValidationException
     */
    public function getList(Request $request): JsonResponse
    {
        $this->authorize('viewAnyInitiativeStep', FourIRInitiative::class);
        $filter = $this->fourIrInitiativeCsCurriculumCblmService->filterValidator($request)->validate();
        $response = $this->fourIrInitiativeCsCurriculumCblmService->getFourIRInitiativeCsCurriculumCblmList($filter, $this->startTime);

        return Response::json($response,ResponseAlias::HTTP_OK);
    }

    /**
     * @param int $id
     * @return JsonResponse
     */
    public function read(int $id): JsonResponse
    {
        $fourIrInitiativeAnalysis = $this->fourIrInitiativeCsCurriculumCblmService->getOneFourIRInitiativeCsCurriculumCblm($id);
        $this->authorize('viewSingleInitiativeStep', FourIRInitiative::class);
        $response = [
            "data" => $fourIrInitiativeAnalysis,
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
     * @throws ValidationException
     * @throws Throwable
     */
    function store(Request $request): JsonResponse
    {
        $this->authorize('creatInitiativeStep', FourIRInitiative::class);
        $validated = $this->fourIrInitiativeCsCurriculumCblmService->validator($request)->validate();
        try {
            DB::beginTransaction();
            $data = $this->fourIrInitiativeCsCurriculumCblmService->store($validated);

            $this->fourIRFileLogService->storeFileLog($validated, FourIRInitiative::FILE_LOG_CBLM_STEP);

            DB::commit();
            $response = [
                'data' => $data,
                '_response_status' => [
                    "success" => true,
                    "code" => ResponseAlias::HTTP_CREATED,
                    "message" => "Four Ir Initiative cblm added successfully",
                    "query_time" => $this->startTime->diffInSeconds(Carbon::now())
                ]
            ];
        } catch (Throwable $e){
            DB::rollBack();
            throw $e;
        }

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
        $fourIrInitiativeCsCurriculumCblm = FourIRInitiativeCsCurriculumCblm::findOrFail($id);
        $this->authorize('updateInitiativeStep', FourIRInitiative::class);
        $validated = $this->fourIrInitiativeCsCurriculumCblmService->validator($request, $id)->validate();
        try {
            DB::beginTransaction();
            $filePath = $fourIrInitiativeCsCurriculumCblm['file_path'];
            $data = $this->fourIrInitiativeCsCurriculumCblmService->update($fourIrInitiativeCsCurriculumCblm, $validated);
            $this->fourIRFileLogService->updateFileLog($filePath, $data->toArray(), FourIRInitiative::FILE_LOG_CBLM_STEP);

            DB::commit();
            $response = [
                'data' => $data,
                '_response_status' => [
                    "success" => true,
                    "code" => ResponseAlias::HTTP_OK,
                    "message" => "Four Ir Initiative cblm updated successfully",
                    "query_time" => $this->startTime->diffInSeconds(Carbon::now())
                ]
            ];
        } catch (Throwable $e){
            DB::rollBack();
            throw $e;
        }

        return Response::json($response, ResponseAlias::HTTP_CREATED);
    }
}
