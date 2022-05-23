<?php

namespace App\Http\Controllers;

use App\Models\FourIRInitiative;
use App\Models\FourIRAssessment;
use App\Services\FourIRServices\FourIRFileLogService;
use App\Services\FourIRServices\FourIRAssessmentService;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Response;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;
use Throwable;

class FourIRAssessmentController extends Controller
{
    public FourIRAssessmentService $fourIRAssessmentService;
    public FourIRFileLogService $fourIRFileLogService;

    private Carbon $startTime;

    /**
     * @param FourIRAssessmentService $fourIRAssessmentService
     * @param FourIRFileLogService $fourIRFileLogService
     */
    public function __construct(FourIRAssessmentService $fourIRAssessmentService, FourIRFileLogService $fourIRFileLogService)
    {
        $this->startTime = Carbon::now();
        $this->fourIRAssessmentService = $fourIRAssessmentService;
        $this->fourIRFileLogService = $fourIRFileLogService;
    }

    /**
     * @param Request $request
     * @param int $fourIrInitiativeId
     * @return JsonResponse
     * @throws ValidationException
     */
    public function getList(Request $request, int $fourIrInitiativeId): JsonResponse
    {
        $this->authorize('viewAnyInitiativeStep', FourIRAssessment::class);
        $filter = $this->fourIRAssessmentService->filterValidator($request)->validate();
        $responseData=$this->fourIRAssessmentService->getFourIrAssessmentList($filter, $fourIrInitiativeId);
        $response = [
            "data" => $responseData,
            "_response_status" => [
                "success" => true,
                "code" => ResponseAlias::HTTP_OK,
                "query_time" => $this->startTime->diffInSeconds(Carbon::now())
            ]
        ];
        return Response::json($response, $response['_response_status']['code']);
    }

    /**
     * @param int $id
     * @return JsonResponse
     */
    public function read(int $id): JsonResponse
    {
        $fourIRAssessment = $this->fourIRAssessmentService->getOneFourIrAssessment($id);
        $this->authorize('viewSingleInitiativeStep', $fourIRAssessment);
        $response = [
            "data" => $fourIRAssessment,
            "_response_status" => [
                "success" => true,
                "code" => ResponseAlias::HTTP_OK,
                "query_time" => $this->startTime->diffInSeconds(Carbon::now())
            ]
        ];
        return Response::json($response, ResponseAlias::HTTP_OK);
    }

}
