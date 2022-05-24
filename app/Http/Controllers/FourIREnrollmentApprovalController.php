<?php

namespace App\Http\Controllers;

use App\Models\FourIREmployment;
use App\Models\FourIRInitiative;
use App\Services\FourIRServices\FourIREnrollmentApprovalService;
use App\Services\FourIRServices\FourIRFileLogService;
use Carbon\Carbon;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;

class FourIREnrollmentApprovalController extends Controller
{
    public FourIREnrollmentApprovalService $fourIREnrollmentApprovalService;
    public FourIRFileLogService $fourIRFileLogService;

    /**
     * FourIRInitiativeController constructor.
     *
     * @param FourIREnrollmentApprovalService $fourIREnrollmentApprovalService
     * @param FourIRFileLogService $fourIRFileLogService
     */
    public function __construct(FourIREnrollmentApprovalService $fourIREnrollmentApprovalService, FourIRFileLogService $fourIRFileLogService)
    {
        $this->fourIREnrollmentApprovalService = $fourIREnrollmentApprovalService;
        $this->fourIRFileLogService = $fourIRFileLogService;
    }

    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @return JsonResponse
     * @throws ValidationException|AuthorizationException
     */
    public function getList(Request $request): JsonResponse
    {
        $this->authorize('viewAnyInitiativeStep', FourIRInitiative::class);

        $filter = $this->fourIREnrollmentApprovalService->filterValidator($request)->validate();
        $response = $this->fourIREnrollmentApprovalService->getFourIrEnrollmentList($filter);
        return Response::json($response,ResponseAlias::HTTP_OK);
    }
}
