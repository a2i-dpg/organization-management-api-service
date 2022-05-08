<?php

namespace App\Http\Controllers;

use App\Services\FourIRServices\FourIREnrollmentApprovalService;
use App\Services\FourIRServices\FourIRFileLogService;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;

class FourIREnrollmentApprovalController extends Controller
{
    public FourIREnrollmentApprovalService $fourIrInitiativeService;
    public FourIRFileLogService $fourIRFileLogService;

    /**
     * FourIRInitiativeController constructor.
     *
     * @param FourIREnrollmentApprovalService $fourIrInitiativeService
     * @param FourIRFileLogService $fourIRFileLogService
     */
    public function __construct(FourIREnrollmentApprovalService $fourIrInitiativeService, FourIRFileLogService $fourIRFileLogService)
    {
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
        return Response::json($response,ResponseAlias::HTTP_OK);
    }
}
