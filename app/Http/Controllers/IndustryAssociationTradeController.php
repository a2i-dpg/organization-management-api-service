<?php

namespace App\Http\Controllers;

use App\Models\IndustryAssociation;
use App\Services\IndustryAssociationTradeService;
use Carbon\Carbon;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;

class IndustryAssociationTradeController extends Controller
{


    protected IndustryAssociationTradeService $industryAssociationTradeService;

    private Carbon $startTime;


    public function __construct(IndustryAssociationTradeService $industryAssociationTradeService)
    {
        $this->industryAssociationTradeService = $industryAssociationTradeService;
        $this->startTime = Carbon::now();
    }


    /**
     * Display a listing of the resource.
     * @param Request $request
     * @return JsonResponse
     */
    public function getList(Request $request): JsonResponse
    {
        $returnedData = $this->industryAssociationTradeService->getIndustryAssociationTradeList($this->startTime);
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
