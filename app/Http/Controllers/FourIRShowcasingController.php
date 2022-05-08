<?php

namespace App\Http\Controllers;

use App\Models\FourIRInitiative;
use App\Models\FourIRShowcasing;
use App\Services\FourIRServices\FourIRFileLogService;
use App\Services\FourIRServices\FourIRShowcasingService;
use Carbon\Carbon;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Response;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;
use Throwable;

class FourIRShowcasingController extends Controller
{
    public FourIRShowcasingService $fourIRShowcasingService;
    public FourIRFileLogService $fourIRFileLogService;
    private Carbon $startTime;

    /**
     * FourIRShowcasingController constructor.
     *
     * @param FourIRShowcasingService $fourIRShowcasingService
     * @param FourIRFileLogService $fourIRFileLogService
     */
    public function __construct(FourIRShowcasingService $fourIRShowcasingService, FourIRFileLogService $fourIRFileLogService)
    {
        $this->startTime = Carbon::now();
        $this->fourIRShowcasingService = $fourIRShowcasingService;
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
//        $this->authorize('viewAny', FourIRShowcasing::class);

        $filter = $this->fourIRShowcasingService->filterValidator($request)->validate();
        $response = $this->fourIRShowcasingService->getFourShowcasingList($filter, $this->startTime);
        return Response::json($response,ResponseAlias::HTTP_OK);
    }

    /**
     * @param int $id
     * @return JsonResponse
     */
    public function read(int $id): JsonResponse
    {
        $fourIrShowcasing = $this->fourIRShowcasingService->getOneFourIrShowcasing($id);
//        $this->authorize('view', $fourIrProject);
        $response = [
            "data" => $fourIrShowcasing,
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
     * @throws ValidationException|Throwable
     */
    function store(Request $request): JsonResponse
    {
        //$this->authorize('create', FourIRShowcasing::class);

        $validated = $this->fourIRShowcasingService->validator($request)->validate();
        try {
            DB::beginTransaction();
            $data = $this->fourIRShowcasingService->store($validated);
            $this->fourIRFileLogService->storeFileLog($validated, FourIRInitiative::FILE_LOG_SHOWCASING_STEP);

            DB::commit();
            $response = [
                'data' => $data,
                '_response_status' => [
                    "success" => true,
                    "code" => ResponseAlias::HTTP_CREATED,
                    "message" => "Four Ir Showcasing added successfully",
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
     * @throws ValidationException|Throwable
     */
    public function update(Request $request, int $id): JsonResponse
    {
        $fourIrShowcasing = FourIRShowcasing::findOrFail($id);
        //$this->authorize('update', $fourIrProject);

        $validated = $this->fourIRShowcasingService->validator($request, $id)->validate();
        try {
            DB::beginTransaction();
            $filePath = $fourIrShowcasing['file_path'];

            $data = $this->fourIRShowcasingService->update($fourIrShowcasing, $validated);
            $this->fourIRFileLogService->updateFileLog($filePath, $validated, FourIRInitiative::FILE_LOG_SHOWCASING_STEP);

            DB::commit();
            $response = [
                'data' => $data,
                '_response_status' => [
                    "success" => true,
                    "code" => ResponseAlias::HTTP_OK,
                    "message" => "Four Ir Showcasing updated successfully",
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
        $fourIrShowcasing = FourIRShowcasing::findOrFail($id);
        //$this->authorize('delete', $fourIrProject);

        $this->fourIRShowcasingService->destroy($fourIrShowcasing);
        $response = [
            '_response_status' => [
                "success" => true,
                "code" => ResponseAlias::HTTP_OK,
                "message" => "Four Ir Showcasing deleted successfully",
                "query_time" => $this->startTime->diffInSeconds(Carbon::now())
            ]
        ];
        return Response::json($response, ResponseAlias::HTTP_OK);
    }
}
