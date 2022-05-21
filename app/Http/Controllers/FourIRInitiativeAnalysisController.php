<?php

namespace App\Http\Controllers;

use App\Imports\FourIrInitiativeAnalysisTeamImport;
use App\Models\FourIRInitiative;
use App\Models\FourIRInitiativeAnalysis;
use App\Services\FourIRServices\FourIRFileLogService;
use App\Services\FourIRServices\FourIRInitiativeAnalysisService;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Response;
use Illuminate\Validation\ValidationException;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;
use Throwable;

class FourIRInitiativeAnalysisController extends Controller
{
    public FourIRInitiativeAnalysisService $fourIRInitiativeAnalysisService;
    public FourIRFileLogService $fourIRFileLogService;
    private Carbon $startTime;

    /**
     * FourIRShowcasingController constructor.
     *
     * @param FourIRInitiativeAnalysisService $fourIRInitiativeAnalysisService
     * @param FourIRFileLogService $fourIRFileLogService
     */
    public function __construct(FourIRInitiativeAnalysisService $fourIRInitiativeAnalysisService, FourIRFileLogService $fourIRFileLogService)
    {
        $this->startTime = Carbon::now();
        $this->fourIRInitiativeAnalysisService = $fourIRInitiativeAnalysisService;
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
        //$this->authorize('viewAny', FourIRShowcasing::class);

        $filter = $this->fourIRInitiativeAnalysisService->filterValidator($request)->validate();
        $response = $this->fourIRInitiativeAnalysisService->getFourIrInitiativeAnalysisList($filter, $this->startTime);
        return Response::json($response, ResponseAlias::HTTP_OK);
    }

    /**
     * @param int $id
     * @return JsonResponse
     */
    public function read(int $id): JsonResponse
    {
        $fourIrInitiativeAnalysis = $this->fourIRInitiativeAnalysisService->getOneFourIrInitiativeAnalysis($id);
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
     * @throws Throwable|ValidationException
     */
    function store(Request $request): JsonResponse
    {
        if (!empty($request->get('four_ir_initiative_analysis_id'))) {
            return $this->update($request, $request->get('four_ir_initiative_analysis_id'));
        } else {
            return $this->store($request);
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     * @return JsonResponse
     * @throws Throwable|ValidationException
     */
    function create(Request $request): JsonResponse
    {
        $validated = $this->fourIRInitiativeAnalysisService->validator($request)->validate();

        $excelRows = null;
        if (!empty($request->file('team_file'))) {
            $file = $request->file('team_file');
            $excelData = Excel::toCollection(new FourIrInitiativeAnalysisTeamImport(), $file)->toArray();

            if (!empty($excelData) && !empty($excelData[0])) {
                $excelRows = $excelData[0];
                $this->fourIRInitiativeAnalysisService->excelDataValidator($excelRows)->validate();
            }
        }

        try {
            DB::beginTransaction();
            $fourIrTot = $this->fourIRInitiativeAnalysisService->store($validated, $excelRows);
            $this->fourIRFileLogService->storeFileLog($validated, FourIRInitiative::FILE_LOG_INITIATIVE_ANALYSIS_STEP);

            DB::commit();
            $response = [
                'data' => $fourIrTot,
                '_response_status' => [
                    "success" => true,
                    "code" => ResponseAlias::HTTP_CREATED,
                    "message" => "Four Ir Initiative TOT  added successfully",
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
     * Store a newly created resource in storage.
     *
     * @param Request $request
     * @param int $id
     * @return JsonResponse
     * @throws Throwable
     * @throws ValidationException
     */
    function update(Request $request, int $id): JsonResponse
    {
        $fourIrInitiativeAnalysis = FourIRInitiativeAnalysis::findOrFail($id);

        $validated = $this->fourIRInitiativeAnalysisService->validator($request)->validate();

        $excelRows = null;
        if (!empty($request->file('team_file'))) {
            $file = $request->file('team_file');
            $excelData = Excel::toCollection(new FourIrInitiativeAnalysisTeamImport(), $file)->toArray();

            if (!empty($excelData) && !empty($excelData[0])) {
                $excelRows = $excelData[0];
                $this->fourIRInitiativeAnalysisService->excelDataValidator($excelRows)->validate();
            }
        }

        try {
            DB::beginTransaction();
            $filePath = $fourIrInitiativeAnalysis->file_path;
            $this->fourIRInitiativeAnalysisService->deletePreviousResearchTeamForUpdate($fourIrInitiativeAnalysis);
            $fourIrTot = $this->fourIRInitiativeAnalysisService->update($fourIrInitiativeAnalysis, $validated, $excelRows);
            $this->fourIRFileLogService->updateFileLog($filePath, $validated, FourIRInitiative::FILE_LOG_INITIATIVE_ANALYSIS_STEP);

            DB::commit();
            $response = [
                'data' => $fourIrTot,
                '_response_status' => [
                    "success" => true,
                    "code" => ResponseAlias::HTTP_CREATED,
                    "message" => "Four Ir Initiative Analysis  updated successfully",
                    "query_time" => $this->startTime->diffInSeconds(Carbon::now())
                ]
            ];
        } catch (Throwable $e) {
            DB::rollBack();
            throw $e;
        }

        return Response::json($response, ResponseAlias::HTTP_CREATED);
    }
}
