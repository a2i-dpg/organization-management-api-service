<?php

namespace App\Http\Controllers;

use App\Models\FourIRInitiative;
use App\Models\FourIRInitiativeCsCurriculumCblm;
use App\Services\FourIRServices\FourIRFileLogService;
use App\Services\FourIRServices\FourIRInitiativeCsService;
use Carbon\Carbon;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Response;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;
use Throwable;

class FourIRInitiativeCsController extends Controller
{
    public FourIRInitiativeCsService $fourIrInitiativeCsCurriculumCblmService;
    public FourIRFileLogService $fourIRFileLogService;
    private Carbon $startTime;


    /**
     * FourIRInitiativeCsController constructor.
     *
     * @param FourIRInitiativeCsService $fourIrInitiativeCsCurriculumCblmService
     * @param FourIRFileLogService $fourIRFileLogService
     */
    public function __construct(FourIRInitiativeCsService $fourIrInitiativeCsCurriculumCblmService, FourIRFileLogService $fourIRFileLogService)
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
        $filter = $this->fourIrInitiativeCsCurriculumCblmService->filterValidator($request)->validate();
        $response = $this->fourIrInitiativeCsCurriculumCblmService->getFourIRInitiativeCsCurriculumCblmList($filter, $this->startTime);

        return Response::json($response, ResponseAlias::HTTP_OK);
    }

    /**
     * @param int $id
     * @return JsonResponse
     */
    public function read(int $id): JsonResponse
    {
        $fourIrInitiativeAnalysis = $this->fourIrInitiativeCsCurriculumCblmService->getOneFourIRInitiativeCsCurriculumCblm($id);
//        $this->authorize('view', $fourIrProject);
        $response = [
            "data" => $fourIrInitiativeAnalysis,
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
     *
     * @param Request $request
     * @return JsonResponse
     * @throws ValidationException
     * @throws Throwable
     */
    function store(Request $request): JsonResponse
    {
        //$this->authorize('create', FourIRInitiativeCsCurriculumCblm::class);
        $validated = $this->fourIrInitiativeCsCurriculumCblmService->validator($request)->validate();
        try {
            DB::beginTransaction();
            $data = $this->fourIrInitiativeCsCurriculumCblmService->store($validated);

            $this->fourIRFileLogService->storeFileLog($validated, FourIRInitiative::FILE_LOG_PROJECT_CS_STEP);

            DB::commit();
            $response = [
                'data' => $data,
                '_response_status' => [
                    "success" => true,
                    "code" => ResponseAlias::HTTP_CREATED,
                    "message" => "Four Ir Initiative cs added successfully",
                    "query_time" => $this->startTime->diffInSeconds(Carbon::now())
                ]
            ];
        } catch (Throwable $e) {
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
        //$this->authorize('update', $fourIrInitiativeCsCurriculumCblm);
        $validated = $this->fourIrInitiativeCsCurriculumCblmService->validator($request, $id)->validate();
        try {
            DB::beginTransaction();
            $filePath = $fourIrInitiativeCsCurriculumCblm['file_path'];
            $data = $this->fourIrInitiativeCsCurriculumCblmService->update($fourIrInitiativeCsCurriculumCblm, $validated);
            $this->fourIRFileLogService->updateFileLog($filePath, $data->toArray(), FourIRInitiative::FILE_LOG_PROJECT_CS_STEP);

            DB::commit();
            $response = [
                'data' => $data,
                '_response_status' => [
                    "success" => true,
                    "code" => ResponseAlias::HTTP_OK,
                    "message" => "Four Ir Initiative cs updated successfully",
                    "query_time" => $this->startTime->diffInSeconds(Carbon::now())
                ]
            ];
        } catch (Throwable $e) {
            DB::rollBack();
            throw $e;
        }

        return Response::json($response, ResponseAlias::HTTP_CREATED);
    }

    public function destroy(int $id): JsonResponse
    {
        $fourIrInitiativeCell = FourIRInitiativeCsCurriculumCblm::findOrFail($id);
//        $this->authorize('delete', $fourIrInitiative);
        $this->fourIrInitiativeCsCurriculumCblmService->destroy($fourIrInitiativeCell);
        $response = [
            '_response_status' => [
                "success" => true,
                "code" => ResponseAlias::HTTP_OK,
                "message" => "Four Ir Initiative Cs deleted successfully",
                "query_time" => $this->startTime->diffInSeconds(Carbon::now())
            ]
        ];
        return Response::json($response, ResponseAlias::HTTP_OK);
    }
}
