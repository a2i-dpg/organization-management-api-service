<?php

namespace App\Http\Controllers;

use App\Models\FourIRInitiative;
use App\Services\FourIRServices\FourIRFileLogService;
use App\Services\FourIRServices\FourIrInitiativeService;
use Carbon\Carbon;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Response;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;
use Throwable;

class FourIRInitiativeController extends Controller
{
    public FourIrInitiativeService $fourIrProjectService;
    public FourIRFileLogService $fourIRFileLogService;
    private Carbon $startTime;

    /**
     * FourIRInitiativeController constructor.
     *
     * @param FourIrInitiativeService $fourIrProjectService
     * @param FourIRFileLogService $fourIRFileLogService
     */
    public function __construct(FourIrInitiativeService $fourIrProjectService, FourIRFileLogService $fourIRFileLogService)
    {
        $this->startTime = Carbon::now();
        $this->fourIrProjectService = $fourIrProjectService;
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

        $filter = $this->fourIrProjectService->filterValidator($request)->validate();
        $response = $this->fourIrProjectService->getFourIRProjectList($filter, $this->startTime);
        return Response::json($response,ResponseAlias::HTTP_OK);
    }

    /**
     * @param int $id
     * @return JsonResponse
     */
    public function read(int $id): JsonResponse
    {
        $fourIrProject = $this->fourIrProjectService->getOneFourIRProject($id);
//        $this->authorize('view', $fourIrProject);
        $response = [
            "data" => $fourIrProject,
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
        // $this->authorize('create', FourIRInitiative::class);
        $validated = $this->fourIrProjectService->validator($request)->validate();
        try {
            DB::beginTransaction();
            $data = $this->fourIrProjectService->store($validated);
            $this->fourIRFileLogService->storeFileLog($data->toArray(), FourIRInitiative::FILE_LOG_INITIATIVE_STEP);

            DB::commit();
            $response = [
                'data' => $data,
                '_response_status' => [
                    "success" => true,
                    "code" => ResponseAlias::HTTP_CREATED,
                    "message" => "Four Ir Project added successfully",
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
     * @throws AuthorizationException
     * @throws ValidationException
     * @throws Throwable
     */
    public function update(Request $request, int $id): JsonResponse
    {
        $fourIrProject = FourIRInitiative::findOrFail($id);
        // $this->authorize('update', $fourIrProject);
        $validated = $this->fourIrProjectService->validator($request, $id)->validate();
        try {
            DB::beginTransaction();
            $filePath = $fourIrProject['file_path'];
            $data = $this->fourIrProjectService->update($fourIrProject, $validated);
            $this->fourIRFileLogService->updateFileLog($filePath, $data->toArray(), FourIRInitiative::FILE_LOG_INITIATIVE_STEP);

            DB::commit();
            $response = [
                'data' => $data,
                '_response_status' => [
                    "success" => true,
                    "code" => ResponseAlias::HTTP_OK,
                    "message" => "Four Ir Project updated successfully",
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
        $fourIrProject = FourIRInitiative::findOrFail($id);
//        $this->authorize('delete', $fourIrProject);
        $this->fourIrProjectService->destroy($fourIrProject);
        $response = [
            '_response_status' => [
                "success" => true,
                "code" => ResponseAlias::HTTP_OK,
                "message" => "Four Ir Project deleted successfully",
                "query_time" => $this->startTime->diffInSeconds(Carbon::now())
            ]
        ];
        return Response::json($response, ResponseAlias::HTTP_OK);
    }
}
