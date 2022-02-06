<?php

namespace App\Http\Controllers;

use App\Models\HrDemandYouth;
use App\Services\HrDemandYouthService;
use Carbon\Carbon;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;

class HrDemandYouthController extends Controller
{
    public HrDemandYouthService $hrDemandYouthService;
    private Carbon $startTime;

    /**
     * HrDemandController constructor.
     *
     * @param HrDemandYouthService $hrDemandYouthService
     */
    public function __construct(HrDemandYouthService $hrDemandYouthService)
    {
        $this->hrDemandYouthService = $hrDemandYouthService;
        $this->startTime = Carbon::now();
    }

    /**
     * Display a listing of the Hr Demand Youths.
     *
     * @param Request $request
     * @param int $hr_demand_institute_id
     * @return JsonResponse
     * @throws ValidationException
     */
    public function getHrDemandYouths(Request $request, int $hr_demand_institute_id): JsonResponse
    {
//        $this->authorize('viewAny', HrDemandYouth::class);

        $filter = $this->hrDemandYouthService->filterValidator($request)->validate();
        $response = $this->hrDemandYouthService->getHrDemandYouthList($filter, $this->startTime, $hr_demand_institute_id);

        return Response::json($response, ResponseAlias::HTTP_OK);
    }
}
