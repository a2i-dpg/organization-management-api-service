<?php

namespace App\Http\Controllers;

use App\Services\TradeService;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;

class TradeController extends Controller
{

    protected TradeService $tradeService;

    private Carbon $startTime;


    public function __construct(TradeService $tradeService)
    {
        $this->tradeService = $tradeService;
        $this->startTime = Carbon::now();
    }


    /**
     * Display a listing of the resource.
     * @param Request $request
     * @return JsonResponse
     */
    public function getList(Request $request): JsonResponse
    {
        $returnedData = $this->tradeService->getTradeList($this->startTime);
        $response = [
            'data' => $returnedData['data'],
            '_response_status' => [
                "success" => true,
                "code" => ResponseAlias::HTTP_OK,
                'query_time' => $returnedData['query_time']
            ]
        ];
        return Response::json($response, ResponseAlias::HTTP_OK);
    }
}
