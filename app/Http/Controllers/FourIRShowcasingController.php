<?php

namespace App\Http\Controllers;

use App\Models\FourIRShowcasing;
use App\Services\FourIRServices\FourIRShowcasingService;
use Carbon\Carbon;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;
use Throwable;

class FourIRShowcasingController extends Controller
{
    public FourIRShowcasingService $fourIRShowcasingService;
    private Carbon $startTime;

    /**
     * FourIRProjectCellController constructor.
     *
     * @param FourIRShowcasingService $fourIRShowcasingService
     */
    public function __construct(FourIRShowcasingService $fourIRShowcasingService)
    {
        $this->startTime = Carbon::now();
        $this->fourIRShowcasingService = $fourIRShowcasingService;
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
//        $this->authorize('viewAny', FourIRProjectCell::class);

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
        $fourIrProjectCell = $this->fourIRShowcasingService->getOneFourIrShowcasing($id);
//        $this->authorize('view', $fourIrProject);
        $response = [
            "data" => $fourIrProjectCell,
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
     */
    function store(Request $request): JsonResponse
    {
//        $this->authorize('create', FourIRProjectCell::class);

        $validated = $this->fourIRShowcasingService->validator($request)->validate();
        $data = $this->fourIRShowcasingService->store($validated);

        $response = [
            'data' => $data,
            '_response_status' => [
                "success" => true,
                "code" => ResponseAlias::HTTP_CREATED,
                "message" => "Four Ir Project added successfully",
                "query_time" => $this->startTime->diffInSeconds(Carbon::now())
            ]
        ];

        return Response::json($response, ResponseAlias::HTTP_CREATED);
    }

    /**
     * Update the specified resource in storage
     * @param Request $request
     * @param int $id
     * @return JsonResponse
     * @throws AuthorizationException
     * @throws ValidationException
     */
    public function update(Request $request, int $id): JsonResponse
    {
        $fourIrProjectCell = FourIRShowcasing::findOrFail($id);
//        $this->authorize('update', $fourIrProject);

        $validated = $this->fourIRShowcasingService->validator($request, $id)->validate();
        $data = $this->fourIRShowcasingService->update($fourIrProjectCell, $validated);

        $response = [
            'data' => $data,
            '_response_status' => [
                "success" => true,
                "code" => ResponseAlias::HTTP_OK,
                "message" => "Four Ir Project updated successfully",
                "query_time" => $this->startTime->diffInSeconds(Carbon::now())
            ]
        ];

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
        $fourIrProjectCell = FourIRShowcasing::findOrFail($id);
//        $this->authorize('delete', $fourIrProject);
        $this->fourIRShowcasingService->destroy($fourIrProjectCell);
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
