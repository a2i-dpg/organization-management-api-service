<?php

namespace App\Http\Controllers;

use App\Imports\FourIrTotParticipantsImport;
use App\Models\FourIRInitiativeTot;
use App\Services\FourIRServices\FourIRTotInitiativeService;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Response;
use Illuminate\Validation\ValidationException;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;
use Throwable;

class FourIRInitiativeTotController extends Controller
{
    public FourIRTotInitiativeService $fourIRTotInitiativeService;

    private Carbon $startTime;

    /**
     * @param FourIRTotInitiativeService $fourIRTotInitiativeService
     */
    public function __construct(FourIRTotInitiativeService $fourIRTotInitiativeService)
    {
        $this->startTime = Carbon::now();
        $this->fourIRTotInitiativeService = $fourIRTotInitiativeService;
    }

    /**
     * @param Request $request
     * @return JsonResponse
     * @throws Throwable|ValidationException
     */
    public function getList(Request $request): JsonResponse
    {
        $filter = $this->fourIRTotInitiativeService->filterValidator($request)->validate();
        $response = $this->fourIRTotInitiativeService->getFourIrProjectTOtList($filter, $this->startTime);
        return Response::json($response,ResponseAlias::HTTP_OK);
    }

    /**
     * @param int $id
     * @return JsonResponse
     */
    public function read(int $id): JsonResponse
    {
        $fourIrInitiativeAnalysis = $this->fourIRTotInitiativeService->getOneFourIrInitiativeAnalysis($id);
//        $this->authorize('view', $fourIrProject);
        $response = [
            "data" => $fourIrInitiativeAnalysis,
            "_response_status" => [
                "success" => true,
                "code" => ResponseAlias::HTTP_OK,
                "query_time" => $this->startTime->diffInSeconds(Carbon::now())
            ]
        ];
        return Response::json($response,ResponseAlias::HTTP_OK);
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
        $validated = $this->fourIRTotInitiativeService->validator($request)->validate();
        $excelRows = null;
        if(!empty($request->file('participants_file'))){
            $file = $request->file('participants_file');
            $excelData = Excel::toCollection(new FourIrTotParticipantsImport(), $file)->toArray();

            if (!empty($excelData) && !empty($excelData[0])) {
                $excelRows = $excelData[0];
                $this->fourIRTotInitiativeService->excelDataValidator($excelRows)->validate();
            }
        }

        try {
            DB::beginTransaction();
            $fourIrTot = $this->fourIRTotInitiativeService->store($validated, $excelRows);

            DB::commit();
            $response = [
                'data' => $fourIrTot,
                '_response_status' => [
                    "success" => true,
                    "code" => ResponseAlias::HTTP_CREATED,
                    "message" => "Four Ir Initiative TOT  added successfully",
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
        Log::info(FourIRInitiativeTot::class.json_encode(["id"=>$id,"request:"=>$request->all()],JSON_PRETTY_PRINT));

        $fourIrInitiativeTot = FourIRInitiativeTot::findOrFail($id);

        $validated = $this->fourIRTotInitiativeService->validator($request,$id)->validate();

        $excelRows = null;
        if(!empty($request->file('participants_file'))){
            $file = $request->file('participants_file');
            $excelData = Excel::toCollection(new FourIrTotParticipantsImport(), $file)->toArray();

            if (!empty($excelData) && !empty($excelData[0])) {
                $excelRows = $excelData[0];
                $this->fourIRTotInitiativeService->excelDataValidator($excelRows)->validate();
            }
        }

        try {
            DB::beginTransaction();
            $this->fourIRTotInitiativeService->deletePreviousMasterTrainersForUpdate($fourIrInitiativeTot);
            $fourIrTot = $this->fourIRTotInitiativeService->update($fourIrInitiativeTot, $validated, $excelRows);

            DB::commit();
            $response = [
                'data' => $fourIrTot,
                '_response_status' => [
                    "success" => true,
                    "code" => ResponseAlias::HTTP_CREATED,
                    "message" => "Four Ir Initiative TOT update successfully",
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
     * @param Request $request
     * @param int $id
     * @return JsonResponse
     * @throws Throwable
     * @throws ValidationException
     */
    public function fourIrTotupdate(Request $request, int $id): JsonResponse
    {

        $fourIrInitiativeTot = FourIRInitiativeTot::findOrFail($id);
        $validated = $this->fourIRTotInitiativeService->validator($request,$id)->validate();

        $excelRows = null;
        if(!empty($request->file('participants_file'))){
            $file = $request->file('participants_file');
            $excelData = Excel::toCollection(new FourIrTotParticipantsImport(), $file)->toArray();

            if (!empty($excelData) && !empty($excelData[0])) {
                $excelRows = $excelData[0];
                $this->fourIRTotInitiativeService->excelDataValidator($excelRows)->validate();
            }
        }

        try {
            DB::beginTransaction();
            $this->fourIRTotInitiativeService->deletePreviousMasterTrainersForUpdate($fourIrInitiativeTot);
            $fourIrTot = $this->fourIRTotInitiativeService->update($fourIrInitiativeTot, $validated, $excelRows);

            DB::commit();
            $response = [
                'data' => $fourIrTot,
                '_response_status' => [
                    "success" => true,
                    "code" => ResponseAlias::HTTP_CREATED,
                    "message" => "Four Ir Initiative TOT update successfully",
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
     * @param int $id
     * @return JsonResponse
     */
    public function destroy(int $id): JsonResponse
    {
        Log::info("FourIRInitiativeTot ".json_encode($request->all()));
        $fourIrInitiativeTot = FourIRInitiativeTot::findOrFail($id);
        try {
            DB::beginTransaction();
            $this->fourIRTotInitiativeService->deletePreviousMasterTrainersForUpdate($fourIrInitiativeTot);
            $fourIrTot = $this->fourIRTotInitiativeService->destroy($fourIrInitiativeTot);

            DB::commit();
            $response = [
                'data' => $fourIrTot,
                '_response_status' => [
                    "success" => true,
                    "code" => ResponseAlias::HTTP_CREATED,
                    "message" => "Four Ir Initiative TOT Deleted successfully",
                    "query_time" => $this->startTime->diffInSeconds(Carbon::now())
                ]
            ];
        } catch (Throwable $e){
            DB::rollBack();
            throw $e;
        }
        return Response::json($response, ResponseAlias::HTTP_OK);
    }
}
