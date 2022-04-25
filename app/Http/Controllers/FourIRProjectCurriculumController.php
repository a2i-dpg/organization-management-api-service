<?php

namespace App\Http\Controllers;

use App\Models\FourIRProject;
use App\Models\FourIRProjectCurriculum;
use App\Services\FourIRServices\FourIRFileLogService;
use App\Services\FourIRServices\FourIRProjectCurriculumService;
use Carbon\Carbon;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Response;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;
use Throwable;

class FourIRProjectCurriculumController extends Controller
{
    public FourIRProjectCurriculumService $fourIrProjectCurriculumService;
    public FourIRFileLogService $fourIRFileLogService;
    private Carbon $startTime;

    /**
     * FourIRProjectCurriculumController constructor.
     *
     * @param FourIRProjectCurriculumService $fourIrProjectCurriculumService
     * @param FourIRFileLogService $fourIRFileLogService
     */
    public function __construct(FourIRProjectCurriculumService $fourIrProjectCurriculumService, FourIRFileLogService $fourIRFileLogService)
    {
        $this->startTime = Carbon::now();
        $this->fourIrProjectCurriculumService = $fourIrProjectCurriculumService;
        $this->fourIRFileLogService = $fourIRFileLogService;
    }

    /**
     * @param int $id
     * @return JsonResponse
     */
    public function read(int $id): JsonResponse
    {
        $fourIrProjectCurriculum = $this->fourIrProjectCurriculumService->getOneFourIRProjectCurriculum($id);
//        $this->authorize('view', $fourIrProjectCurriculum);
        $response = [
            "data" => $fourIrProjectCurriculum,
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
        //$this->authorize('create', FourIRProjectCurriculum::class);
        $validated = $this->fourIrProjectCurriculumService->validator($request)->validate();
        try {
            DB::beginTransaction();
            $data = $this->fourIrProjectCurriculumService->store($validated);
            $this->fourIRFileLogService->storeFileLog($data->toArray(), FourIRProject::FILE_LOG_PROJECT_CURRICULUM_STEP);

            DB::commit();
            $response = [
                'data' => $data,
                '_response_status' => [
                    "success" => true,
                    "code" => ResponseAlias::HTTP_CREATED,
                    "message" => "Four Ir Project curriculum added successfully",
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
