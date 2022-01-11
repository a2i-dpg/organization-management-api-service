<?php

namespace App\Http\Controllers;

use App\Services\IndustrySubTradeService;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;

class IndustrySubTradeController extends Controller
{

    public IndustrySubTradeService $industrySubTradeService;

    private Carbon $startTime;


    public function __construct(IndustrySubTradeService $industrySubTradeService)
    {
        $this->startTime = Carbon::now();
        $this->industrySubTradeService = $industrySubTradeService;
    }

    /**
     * Display a listing of the resource.
     * @param Request $request
     * @return JsonResponse
     * @throws ValidationException
     */
    public function getList(Request $request): JsonResponse
    {
        $filter = $this->industrySubTradeService->filterValidator($request)->validate();
        $returnedData = $this->industrySubTradeService->getIndustrySubTradeList($filter, $this->startTime);

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
