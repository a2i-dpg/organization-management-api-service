<?php

namespace App\Http\Controllers;

use App\Models\FourIRResource;
use App\Services\FourIRServices\FourIRFileLogService;
use App\Services\FourIRServices\FourIRResourceService;
use Carbon\Carbon;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Response;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;
use Throwable;

class FourIRResourceController extends Controller
{
    public FourIRResourceService $fourIRGuidelineService;
    public FourIRFileLogService $fourIRFileLogService;
    private Carbon $startTime;

    /**
     * FourIRResourceController constructor.
     *
     * @param FourIRResourceService $fourIRGuidelineService
     * @param FourIRFileLogService $fourIRFileLogService
     */
    public function __construct(FourIRResourceService $fourIRGuidelineService, FourIRFileLogService $fourIRFileLogService)
    {
        $this->startTime = Carbon::now();
        $this->fourIRGuidelineService = $fourIRGuidelineService;
        $this->fourIRFileLogService = $fourIRFileLogService;
    }

    /**
     * @param int $id
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function read(int $id): JsonResponse
    {
        $guideline = $this->fourIRGuidelineService->getOneGuideline($id);
        // $this->authorize('view', $rank);
        $response = [
            "data" => $guideline,
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
     * @throws AuthorizationException
     * @throws ValidationException
     * @throws Throwable
     */
    function store(Request $request): JsonResponse
    {
        //$this->authorize('create', FourIRGuideline::class);
        $validated = $this->fourIRGuidelineService->validator($request)->validate();
        try {
            DB::beginTransaction();
            $data = $this->fourIRGuidelineService->store($validated);
            $this->fourIRFileLogService->storeFileLog($data->toArray(), FourIRResource::FILE_LOG_PROJECT_GUIDELINE_STEP);

            DB::commit();
            $response = [
                'data' => $data,
                '_response_status' => [
                    "success" => true,
                    "code" => ResponseAlias::HTTP_CREATED,
                    "message" => "Four Ir Guideline added successfully",
                    "query_time" => $this->startTime->diffInSeconds(Carbon::now())
                ]
            ];
        } catch (Throwable $e){
            DB::rollBack();
            throw $e;
        }

        return Response::json($response, ResponseAlias::HTTP_CREATED);
    }
}
