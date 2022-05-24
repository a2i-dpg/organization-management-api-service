<?php

namespace App\Http\Controllers;

use App\Models\FourIRInitiative;
use App\Services\FourIRServices\FourIRFileLogService;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;

class FourIRFileLogController extends Controller
{
    public FourIRFileLogService $fourIRFileLogService;
    private Carbon $startTime;

    public function __construct(FourIRFileLogService $fourIRFileLogService)
    {
        $this->startTime = Carbon::now();
        $this->fourIRFileLogService = $fourIRFileLogService;
    }


    public function getList(Request $request): \Illuminate\Http\JsonResponse
    {
//        $this->authorize('viewAnyInitiativeStep', FourIRInitiative::class);
        $filter = $this->fourIRFileLogService->filterValidator($request)->validate();
        $response = $this->fourIRFileLogService->getFileLogs($filter, $this->startTime);
        return Response::json($response, ResponseAlias::HTTP_OK);
    }

    /**
     * @param int $id
     * @return JsonResponse
     */
    public function read(int $id): JsonResponse
    {
        $fileLog = $this->fourIRFileLogService->getFileLog($id);
        $this->authorize('viewSingleInitiativeStep', FourIRInitiative::class);
        $response = [
            "data" => $fileLog,
            "_response_status" => [
                "success" => true,
                "code" => ResponseAlias::HTTP_OK,
                "query_time" => $this->startTime->diffInSeconds(Carbon::now())
            ]
        ];
        return Response::json($response, ResponseAlias::HTTP_OK);
    }

    public function store(Request $request)
    {

    }

    public function update(Request $request, int $id)
    {

    }

    public function delete(int $id)
    {

    }
}
