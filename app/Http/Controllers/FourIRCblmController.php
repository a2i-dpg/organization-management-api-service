<?php

namespace App\Http\Controllers;

use App\Models\FourIRProject;
use App\Models\FourIRCblm;
use App\Services\FourIRServices\FourIRFileLogService;
use App\Services\FourIRServices\FourIRCblmService;
use Carbon\Carbon;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Response;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;
use Throwable;

class FourIRCblmController extends Controller
{
    public FourIRCblmService $fourIrCblmService;
    public FourIRFileLogService $fourIRFileLogService;
    private Carbon $startTime;

    /**
     * FourIRCblmController constructor.
     *
     * @param FourIRCblmService $fourIrCblmService
     * @param FourIRFileLogService $fourIRFileLogService
     */
    public function __construct(FourIRCblmService $fourIrCblmService, FourIRFileLogService $fourIRFileLogService)
    {
        $this->startTime = Carbon::now();
        $this->fourIrCblmService = $fourIrCblmService;
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
//        $this->authorize('viewAny', FourIRCblm::class);

        $filter = $this->fourIrCblmService->filterValidator($request)->validate();
        $response = $this->fourIrCblmService->getFourIRCblmList($filter, $this->startTime);
        return Response::json($response,ResponseAlias::HTTP_OK);
    }

    /**
     * @param int $id
     * @return JsonResponse
     */
    public function read(int $id): JsonResponse
    {
        $fourIrCblm = $this->fourIrCblmService->getOneFourIRCblm($id);
//        $this->authorize('view', $fourIrCblm);
        $response = [
            "data" => $fourIrCblm,
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
     * @throws ValidationException
     * @throws Throwable
     */
    function store(Request $request): JsonResponse
    {
        //$this->authorize('create', FourIRCblm::class);
        $validated = $this->fourIrCblmService->validator($request)->validate();
        try {
            DB::beginTransaction();
            $data = $this->fourIrCblmService->store($validated);
            $this->fourIRFileLogService->storeFileLog($data->toArray(), FourIRProject::FILE_LOG_CBLM_STEP);

            DB::commit();
            $response = [
                'data' => $data,
                '_response_status' => [
                    "success" => true,
                    "code" => ResponseAlias::HTTP_CREATED,
                    "message" => "Four Ir Cblm added successfully",
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
     * @param Request $request
     * @param int $id
     * @return JsonResponse
     * @throws AuthorizationException
     * @throws ValidationException
     * @throws Throwable
     */
    public function update(Request $request, int $id): JsonResponse
    {
        $fourIrCblm = FourIRCblm::findOrFail($id);
        //$this->authorize('update', $fourIrCblm);
        $validated = $this->fourIrCblmService->validator($request, $id)->validate();
        try {
            DB::beginTransaction();
            $filePath = $fourIrCblm['file_path'];
            $data = $this->fourIrCblmService->update($fourIrCblm, $validated);
            $this->fourIRFileLogService->updateFileLog($filePath, $data->toArray(), FourIRProject::FILE_LOG_CBLM_STEP);

            DB::commit();
            $response = [
                'data' => $data,
                '_response_status' => [
                    "success" => true,
                    "code" => ResponseAlias::HTTP_OK,
                    "message" => "Four Ir Cblm updated successfully",
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
     *
     * @param int $id
     * @return JsonResponse
     * @throws AuthorizationException
     * @throws Throwable
     */
    public function destroy(int $id): JsonResponse
    {
        $fourIrCblm = FourIRCblm::findOrFail($id);
//        $this->authorize('delete', $fourIrCblm);
        $this->fourIrCblmService->destroy($fourIrCblm);
        $response = [
            '_response_status' => [
                "success" => true,
                "code" => ResponseAlias::HTTP_OK,
                "message" => "Four Ir Cblm deleted successfully",
                "query_time" => $this->startTime->diffInSeconds(Carbon::now())
            ]
        ];
        return Response::json($response, ResponseAlias::HTTP_OK);
    }
}
