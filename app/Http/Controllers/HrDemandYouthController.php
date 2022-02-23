<?php

namespace App\Http\Controllers;

use App\Models\HrDemandYouth;
use App\Services\HrDemandYouthService;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;
use Throwable;

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
        //$this->authorize('viewAny', HrDemandYouth::class);

        $filter = $this->hrDemandYouthService->filterValidator($request)->validate();
        $response = $this->hrDemandYouthService->getHrDemandYouthList($filter, $this->startTime, $hr_demand_institute_id);

        return Response::json($response, ResponseAlias::HTTP_OK);
    }

    /**
     * Display a listing of the Hr Demand Youths.
     *
     * @param int $hr_demand_youth_id
     * @return JsonResponse
     * @throws Throwable
     */
    public function rejectHrDemandYouth(int $hr_demand_youth_id): JsonResponse
    {
        //$this->authorize('delete', HrDemandYouth::class);

        $hrDemandYouth = HrDemandYouth::findOrFail($hr_demand_youth_id);

        throw_if($hrDemandYouth->row_status == HrDemandYouth::ROW_STATUS_INVALID, ValidationException::withMessages([
            "Hr Demand Youth already Invalidated by Institute User!"
        ]));

        $this->hrDemandYouthService->deleteHrDemandYouth($hrDemandYouth);

        $response = [
            '_response_status' => [
                "success" => true,
                "code" => ResponseAlias::HTTP_OK,
                "message" => "HR Demand Youth Rejected Successfully.",
                "query_time" => $this->startTime->diffInSeconds(Carbon::now()),
            ]
        ];

        return Response::json($response, ResponseAlias::HTTP_OK);
    }
}
