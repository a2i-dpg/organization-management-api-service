<?php

namespace App\Http\Controllers;

use App\Models\FourIRInitiative;
use App\Models\FourIRInitiativeCsCurriculumCblm;
use App\Services\FourIRServices\FourIRFileLogService;
use App\Services\FourIRServices\FourIRInitiativeCsCurriculumCblmService;
use Carbon\Carbon;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Response;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;
use Throwable;

class FourIRInitiativeCsCurriculumCblmController extends Controller
{
    public FourIRInitiativeCsCurriculumCblmService $fourIrInitiativeCsCurriculumCblmService;
    public FourIRFileLogService $fourIRFileLogService;
    private Carbon $startTime;

    /**
     * FourIRInitiativeCsCurriculumCblmController constructor.
     *
     * @param FourIRInitiativeCsCurriculumCblmService $fourIrInitiativeCsCurriculumCblmService
     * @param FourIRFileLogService $fourIRFileLogService
     */
    public function __construct(FourIRInitiativeCsCurriculumCblmService $fourIrInitiativeCsCurriculumCblmService, FourIRFileLogService $fourIRFileLogService)
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
        $fourIrInitiativeCsCurriculumCblm = $this->fourIrInitiativeCsCurriculumCblmService->getOneFourIRProjectCs($filter);
//        $this->authorize('view', $fourIrInitiativeCsCurriculumCblm);
        $response = [
            "data" => $fourIrInitiativeCsCurriculumCblm,
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
        //$this->authorize('create', FourIRInitiativeCsCurriculumCblm::class);
        $validated = $this->fourIrInitiativeCsCurriculumCblmService->validator($request)->validate();
        try {
            DB::beginTransaction();
            $data = $this->fourIrInitiativeCsCurriculumCblmService->store($validated);

            if($validated['type'] == FourIRInitiativeCsCurriculumCblm::TYPE_CS){
                $this->fourIRFileLogService->storeFileLog($validated, FourIRInitiative::FILE_LOG_PROJECT_CS_STEP);
            } else if($validated['type'] == FourIRInitiativeCsCurriculumCblm::TYPE_CURRICULUM){
                $this->fourIRFileLogService->storeFileLog($validated, FourIRInitiative::FILE_LOG_PROJECT_CURRICULUM_STEP);
            } else {
                $this->fourIRFileLogService->storeFileLog($validated, FourIRInitiative::FILE_LOG_CBLM_STEP);
            }

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
        } catch (Throwable $e){
            DB::rollBack();
            throw $e;
        }

        return Response::json($response, ResponseAlias::HTTP_CREATED);
    }
}
