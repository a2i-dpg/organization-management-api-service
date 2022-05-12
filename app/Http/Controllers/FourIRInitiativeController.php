<?php

namespace App\Http\Controllers;

use App\Imports\FourIrInitiativesImport;
use App\Models\FourIRInitiative;
use App\Models\FourIROccupation;
use App\Services\FourIRServices\FourIRFileLogService;
use App\Services\FourIRServices\FourIrInitiativeService;
use Carbon\Carbon;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Response;
use Illuminate\Validation\ValidationException;
use Maatwebsite\Excel\Facades\Excel;
use PhpOffice\PhpSpreadsheet\Exception;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;
use Throwable;

class FourIRInitiativeController extends Controller
{
    public FourIrInitiativeService $fourIrInitiativeService;
    public FourIRFileLogService $fourIRFileLogService;
    private Carbon $startTime;

    /**
     * FourIRInitiativeController constructor.
     *
     * @param FourIrInitiativeService $fourIrInitiativeService
     * @param FourIRFileLogService $fourIRFileLogService
     */
    public function __construct(FourIrInitiativeService $fourIrInitiativeService, FourIRFileLogService $fourIRFileLogService)
    {
        $this->startTime = Carbon::now();
        $this->fourIrInitiativeService = $fourIrInitiativeService;
        $this->fourIRFileLogService = $fourIRFileLogService;
    }

    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @return JsonResponse
     * @throws ValidationException
     */
    public function getList(Request $request): JsonResponse
    {
//        $this->authorize('viewAny', FourIRInitiative::class);

        $filter = $this->fourIrInitiativeService->filterValidator($request)->validate();
        $response = $this->fourIrInitiativeService->getFourIRInitiativeList($filter, $this->startTime);
        return Response::json($response, ResponseAlias::HTTP_OK);
    }

    /**
     * @param int $id
     * @return JsonResponse
     */
    public function read(int $id): JsonResponse
    {
        $fourIrInitiative = $this->fourIrInitiativeService->getOneFourIRInitiative($id);
//        $this->authorize('view', $fourIrInitiative);
        $response = [
            "data" => $fourIrInitiative,
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
     * @throws ValidationException
     * @throws Throwable
     */
    function store(Request $request): JsonResponse
    {
        // $this->authorize('create', FourIRInitiative::class);
        $validated = $this->fourIrInitiativeService->validator($request)->validate();
        try {
            DB::beginTransaction();
            $data = $this->fourIrInitiativeService->store($validated);

            $validated['four_ir_initiative_id'] = $data->id;
            $this->fourIRFileLogService->storeFileLog($validated, FourIRInitiative::FILE_LOG_INITIATIVE_STEP);

            DB::commit();
            $response = [
                'data' => $data,
                '_response_status' => [
                    "success" => true,
                    "code" => ResponseAlias::HTTP_CREATED,
                    "message" => "Four Ir Initiative added successfully",
                    "query_time" => $this->startTime->diffInSeconds(Carbon::now())
                ]
            ];
        } catch (Throwable $e) {
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
     * @throws AuthorizationException
     * @throws ValidationException
     * @throws Throwable
     */
    public function update(Request $request, int $id): JsonResponse
    {
        $fourIrInitiative = FourIRInitiative::findOrFail($id);
        // $this->authorize('update', $fourIrInitiative);
        $validated = $this->fourIrInitiativeService->validator($request, $id)->validate();
        try {
            DB::beginTransaction();
            $filePath = $fourIrInitiative['file_path'];
            $data = $this->fourIrInitiativeService->update($fourIrInitiative, $validated);

            $validated['four_ir_initiative_id'] = $data->id;
            $this->fourIRFileLogService->updateFileLog($filePath, $validated, FourIRInitiative::FILE_LOG_INITIATIVE_STEP);

            DB::commit();
            $response = [
                'data' => $data,
                '_response_status' => [
                    "success" => true,
                    "code" => ResponseAlias::HTTP_OK,
                    "message" => "Four Ir Initiative updated successfully",
                    "query_time" => $this->startTime->diffInSeconds(Carbon::now())
                ]
            ];
        } catch (Throwable $e) {
            DB::rollBack();
            throw $e;
        }

        return Response::json($response, ResponseAlias::HTTP_CREATED);
    }

    /**
     * Remove the specified resource from storage
     *
     * @param int $id
     * @return JsonResponse
     * @throws AuthorizationException
     * @throws Throwable
     */
    public function destroy(int $id): JsonResponse
    {
        $fourIrInitiative = FourIRInitiative::findOrFail($id);
//        $this->authorize('delete', $fourIrInitiative);
        $this->fourIrInitiativeService->destroy($fourIrInitiative);
        $response = [
            '_response_status' => [
                "success" => true,
                "code" => ResponseAlias::HTTP_OK,
                "message" => "Four Ir Initiative deleted successfully",
                "query_time" => $this->startTime->diffInSeconds(Carbon::now())
            ]
        ];
        return Response::json($response, ResponseAlias::HTTP_OK);
    }

    /**
     * Store organizations as bulk.
     *
     * @param Request $request
     * @return JsonResponse
     * @throws Throwable
     */
    public function bulkStoreByExcel(Request $request): JsonResponse
    {
        $this->fourIrInitiativeService->excelImportValidator($request)->validate();
        $file = $request->file('file');
        $excelData = Excel::toCollection(new FourIrInitiativesImport(), $file)->toArray()[0];
        $this->fourIrInitiativeService->explodeData($excelData);

        if (!empty($excelData) && !empty($excelData)) {
            $rows = $excelData;
            $this->fourIrInitiativeService->excelDataValidator($request, $rows)->validate();
            $errorOccurOccupations = [];

            foreach ($rows as $rowData) {
                DB::beginTransaction();
                try {
                    $rowData['accessor_type'] = $request->input('accessor_type');
                    $rowData['accessor_id'] = $request->input('accessor_id');
                    $rowData['four_ir_tagline_id'] = $request->input('four_ir_tagline_id');

                    $data = $this->fourIrInitiativeService->store($rowData);

                    /** Store file path for versioning */
                    $initiativeData = $data->toArray();
                    $initiativeData['four_ir_initiative_id'] = $initiativeData['id'];
                    $this->fourIRFileLogService->storeFileLog($initiativeData, FourIRInitiative::FILE_LOG_INITIATIVE_STEP);

                    DB::commit();
                } catch (Throwable $e) {
                    Log::info("Error occurred. Inside catch block. Error is: " . json_encode($e->getMessage()));
                    DB::rollBack();
                    $fourIrOccupation = FourIROccupation::find($rowData['four_ir_occupation_id']);
                    $errorOccurOccupations[] = $fourIrOccupation['title'];
                }
            }
        }

        $response = [
            '_response_status' => [
                "success" => true,
                "code" => ResponseAlias::HTTP_CREATED,
                "message" => "Four IR initiatives Created Successfully"
            ]
        ];

        if (!empty($errorOccurOccupations)) {
            $response['_response_status']['error_occur_occupations'] = $errorOccurOccupations;
        }

        return Response::json($response, ResponseAlias::HTTP_CREATED);
    }

    /**
     * @throws Exception
     */
    public function bulkImporterExcelFormat(): JsonResponse
    {
        $excelFile = $this->fourIrInitiativeService->getBulkImporterExcelFormat();
        $response = [
            "data" => $excelFile,
            '_response_status' => [
                "success" => true,
                "code" => ResponseAlias::HTTP_CREATED,
                "message" => "Four IR initiatives Created Successfully"
            ]
        ];
        return Response::json($response, $response['_response_status']['code']);
    }
}
