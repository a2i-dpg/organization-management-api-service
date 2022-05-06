<?php

namespace App\Http\Controllers;

use App\Models\FourIRInitiative;
use App\Models\FourIRInitiativeTot;
use App\Services\FourIRServices\FourIRFileLogService;
use App\Services\FourIRServices\FourIRTotInitiativeService;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Response;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;
use Throwable;

class FourIRInitiativeTotController extends Controller
{
    public FourIRTotInitiativeService $fourIRTotProjectService;
    public FourIRFileLogService $fourIRFileLogService;

    private Carbon $startTime;

    /**
     * @param FourIRTotInitiativeService $fourIRTotProjectService
     * @param FourIRFileLogService $fourIRFileLogService
     */
    public function __construct(FourIRTotInitiativeService $fourIRTotProjectService, FourIRFileLogService $fourIRFileLogService)
    {
        $this->startTime = Carbon::now();
        $this->fourIRTotProjectService = $fourIRTotProjectService;
        $this->fourIRFileLogService = $fourIRFileLogService;
    }

    /**
     * @param Request $request
     * @return JsonResponse
     * @throws Throwable|ValidationException
     */
    public function getList(Request $request): JsonResponse
    {

        $filter = $this->fourIRTotProjectService->filterValidator($request)->validate();
        $response = $this->fourIRTotProjectService->getFourIrProjectTOtList($filter, $this->startTime);
        return Response::json($response, ResponseAlias::HTTP_OK);
    }

    /**
     * @param int $id
     * @return JsonResponse
     */
    public function read(int $id): JsonResponse
    {
        $fourIRTot = $this->fourIRTotProjectService->getOneFourIrProjectCs($id);
        $response = [
            "data" => $fourIRTot,
            "_response_status" => [
                "success" => true,
                "code" => ResponseAlias::HTTP_OK,
                "query_time" => $this->startTime->diffInSeconds(Carbon::now())
            ]
        ];
        return Response::json($response, ResponseAlias::HTTP_OK);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     * @return JsonResponse
     * @throws Throwable|ValidationException
     */
    function store(Request $request): JsonResponse
    {
        $validated = $this->fourIRTotProjectService->validator($request)->validate();
        try {
            DB::beginTransaction();
            $data = $this->fourIRTotProjectService->store($validated);
            $this->fourIRFileLogService->storeFileLog($data->toArray(), FourIRInitiative::FILE_LOG_TOT_STEP);

            DB::commit();
            $response = [
                'data' => $data,
                '_response_status' => [
                    "success" => true,
                    "code" => ResponseAlias::HTTP_CREATED,
                    "message" => "Four Ir Project TOT  added successfully",
                    "query_time" => $this->startTime->diffInSeconds(Carbon::now())
                ]
            ];
        } catch (Throwable $e){
            DB::rollBack();
            throw $e;
        }

        return Response::json($response, ResponseAlias::HTTP_CREATED);
    }

    /**
     * Update the specified resource in storage
     *
     * @param Request $request
     * @param int $id
     * @return JsonResponse
     * @throws ValidationException
     * @throws Throwable
     */
    public function update(Request $request, int $id): JsonResponse
    {
        $fourIrProjectTot = FourIRInitiativeTot::findOrFail($id);
        $validated = $this->fourIRTotProjectService->validator($request, $id)->validate();
        try {
            DB::beginTransaction();
            $filePath = $fourIrProjectTot['file_path'];
            $data = $this->fourIRTotProjectService->update($fourIrProjectTot, $validated);
            $this->fourIRFileLogService->updateFileLog($filePath, $data->toArray(), FourIRInitiative::FILE_LOG_TOT_STEP);

            DB::commit();
            $response = [
                'data' => $data,
                '_response_status' => [
                    "success" => true,
                    "code" => ResponseAlias::HTTP_OK,
                    "message" => "Four Ir Project TOT updated successfully",
                    "query_time" => $this->startTime->diffInSeconds(Carbon::now())
                ]
            ];
        } catch (Throwable $e){
            DB::rollBack();
            throw $e;
        }

        return Response::json($response, ResponseAlias::HTTP_CREATED);
    }

    /**
     * Remove the specified resource from storage
     * @param int $id
     * @return JsonResponse
     */
    public function destroy(int $id): JsonResponse
    {
        $fourIrProjectTot = FourIRInitiativeTot::findOrFail($id);
        $this->fourIRTotProjectService->destroy($fourIrProjectTot);
        $response = [
            '_response_status' => [
                "success" => true,
                "code" => ResponseAlias::HTTP_OK,
                "message" => "Four Ir Project TOT deleted successfully",
                "query_time" => $this->startTime->diffInSeconds(Carbon::now())
            ]
        ];
        return Response::json($response, ResponseAlias::HTTP_OK);
    }
}
