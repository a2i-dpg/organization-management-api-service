<?php

namespace App\Http\Controllers;

use App\Services\StatisticsService;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Response;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;

class StatisticsController extends Controller
{
    public StatisticsService $statisticsService;
    private Carbon $startTime;

    /**
     * @param StatisticsService $statisticsService
     */
    public function __construct(StatisticsService $statisticsService)
    {
        $this->statisticsService = $statisticsService;
        $this->startTime = Carbon::now();
    }

    public function niseStatistics():JsonResponse
    {
      /**
       * Industry
       *Job provider
       *Popular job
       */
        $response['data'] = $this->statisticsService->getNiseStatistics();
        $response['_response_status'] = [
            "success" => true,
            "code" => ResponseAlias::HTTP_OK,
            "query_time" => $this->startTime->diffInSeconds(Carbon::now()),
        ];
        return Response::json($response, ResponseAlias::HTTP_OK);

    }


}
