<?php

namespace App\Http\Controllers;

use App\Models\FourIRInitiative;
use App\Models\FourIRResource;
use App\Models\FourIRSector;
use App\Services\FourIRServices\FourIRFileLogService;
use App\Services\FourIRServices\FourIRResourceService;
use Carbon\Carbon;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Response;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;
use Throwable;

class FourIRResourceController extends Controller
{
    public FourIRResourceService $fourIRResourceService;
    public FourIRFileLogService $fourIRFileLogService;
    private Carbon $startTime;

    /**
     * FourIRResourceController constructor.
     *
     * @param FourIRResourceService $fourIRResourceService
     * @param FourIRFileLogService $fourIRFileLogService
     */
    public function __construct(FourIRResourceService $fourIRResourceService, FourIRFileLogService $fourIRFileLogService)
    {
        $this->startTime = Carbon::now();
        $this->fourIRResourceService = $fourIRResourceService;
        $this->fourIRFileLogService = $fourIRFileLogService;
    }


    /**
     * @param Request $request
     * @return JsonResponse
     * @throws ValidationException
     */
    public function getList(Request $request): JsonResponse
    {

        $this->authorize('viewAnyInitiativeStep', FourIRInitiative::class);
        $filter = $this->fourIRResourceService->filterValidator($request)->validate();
        $response = $this->fourIRResourceService->getResourceList($filter, $this->startTime);
        return Response::json($response, ResponseAlias::HTTP_OK);
    }

    /**
     * Only one resource_management can be for an initiative. That's why only single read API is here.
     * Provide Initiative id as the path parameter of this API
     *
     * @param int $id
     * @return JsonResponse
     */
    public function read(int $id): JsonResponse
    {
        /** Here $id is the ID of FourIrInitiative */

        $this->authorize('viewAny', FourIRInitiative::class);
        Log::info("r-id" . $id);
        $fourIrResource = $this->fourIRResourceService->getOneFourIRResource($id);
        $this->authorize('viewSingleInitiativeStep', FourIRInitiative::class);
        $response = [
            "data" => $fourIrResource,
            "_response_status" => [
                "success" => true,
                "code" => ResponseAlias::HTTP_OK,
                "query_time" => $this->startTime->diffInSeconds(Carbon::now())
            ]
        ];
        return Response::json($response, ResponseAlias::HTTP_OK);
    }

    /**
     * @throws Throwable
     * @throws ValidationException
     */
    public function update(Request $request, int $id): JsonResponse
    {
        $resource = FourIRResource::findOrFail($id);

        $filePath = $resource->file_path;

        $this->authorize('updateInitiativeStep', FourIRInitiative::class);

        $validated = $this->fourIRResourceService->validator($request, $id)->validate();

        $data = $this->fourIRResourceService->update($resource, $validated);

        $this->fourIRFileLogService->updateFileLog($filePath, $validated, FourIRInitiative::FILE_LOG_PROJECT_RESOURCE_MANAGEMENT_STEP);

        $response = [
            'data' => $data,
            '_response_status' => [
                "success" => true,
                "code" => ResponseAlias::HTTP_OK,
                "message" => "FourIRSector updated successfully.",
                "query_time" => $this->startTime->diffInSeconds(Carbon::now())
            ]
        ];

        return Response::json($response, ResponseAlias::HTTP_CREATED);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     * @return JsonResponse
     * @throws AuthorizationException
     * @throws ValidationException
     * @throws Throwable
     */
    function store(Request $request): JsonResponse
    {
        $this->authorize('creatInitiativeStep', FourIRInitiative::class);

        $validated = $this->fourIRResourceService->validator($request)->validate();

        try {
            DB::beginTransaction();
            $fourIrResource = FourIRResource::where('four_ir_initiative_id', $validated['four_ir_initiative_id'])->first();

            $filePath = $fourIrResource->file_path;

            $data = $this->fourIRResourceService->store($validated, $fourIrResource);

            if(empty($fourIrResource)){
                $this->fourIRFileLogService->storeFileLog($validated, FourIRInitiative::FILE_LOG_PROJECT_RESOURCE_MANAGEMENT_STEP);
            } else {
                $this->fourIRFileLogService->updateFileLog($filePath, $validated, FourIRInitiative::FILE_LOG_PROJECT_RESOURCE_MANAGEMENT_STEP);
            }

            DB::commit();
            $response = [
                'data' => $data,
                '_response_status' => [
                    "success" => true,
                    "code" => ResponseAlias::HTTP_CREATED,
                    "message" => "Four Ir Resource added successfully",
                    "query_time" => $this->startTime->diffInSeconds(Carbon::now())
                ]
            ];
        } catch (Throwable $e) {
            DB::rollBack();
            throw $e;
        }

        return Response::json($response, ResponseAlias::HTTP_CREATED);
    }
}
