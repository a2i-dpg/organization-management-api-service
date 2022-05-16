<?php

namespace App\Http\Controllers;

use App\Models\FourIRScaleUp;
use App\Services\FourIRServices\FourIRScaleUpService;
use Carbon\Carbon;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;
use Throwable;
//TODO: FourIR Employment need to check
class FourIRScaleUpController extends Controller
{
    public FourIRScaleUpService $fourIRScaleUpService;
    private Carbon $startTime;

    /**
     * FourIRShowcasingController constructor.
     *
     * @param FourIRScaleUpService $fourIRScaleUpService
     */
    public function __construct(FourIRScaleUpService $fourIRScaleUpService)
    {
        $this->startTime = Carbon::now();
        $this->fourIRScaleUpService = $fourIRScaleUpService;
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
//        $this->authorize('viewAny', FourIRScaleUp::class);

        $filter = $this->fourIRScaleUpService->filterValidator($request)->validate();
        $response = $this->fourIRScaleUpService->getFourShowcasingList($filter, $this->startTime);
        return Response::json($response,ResponseAlias::HTTP_OK);
    }

    /**
     * @param int $id
     * @return JsonResponse
     */
    public function read(int $id): JsonResponse
    {
        $fourIrScaleUp = $this->fourIRScaleUpService->getOneFourIrShowcasing($id);
//        $this->authorize('view', $fourIrProject);
        $response = [
            "data" => $fourIrScaleUp,
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
//        $this->authorize('create', FourIRScaleUp::class);

        $validated = $this->fourIRScaleUpService->validator($request)->validate();
        $data = $this->fourIRScaleUpService->store($validated);

        $response = [
            'data' => $data,
            '_response_status' => [
                "success" => true,
                "code" => ResponseAlias::HTTP_CREATED,
                "message" => "Four Ir ScaleUp added successfully",
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
     * @throws ValidationException|Throwable
     */
    public function update(Request $request, int $id): JsonResponse
    {
        $fourIrScaleUp = FourIRScaleUp::findOrFail($id);
//        $this->authorize('update', $fourIrProject);

        $validated = $this->fourIRScaleUpService->validator($request, $id)->validate();
        $data = $this->fourIRScaleUpService->update($fourIrScaleUp, $validated);

        $response = [
            'data' => $data,
            '_response_status' => [
                "success" => true,
                "code" => ResponseAlias::HTTP_OK,
                "message" => "Four Ir ScaleUp updated successfully",
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
        $fourIrScaleUp = FourIRScaleUp::findOrFail($id);
//        $this->authorize('delete', $fourIrProject);
        $this->fourIRScaleUpService->destroy($fourIrScaleUp);
        $response = [
            '_response_status' => [
                "success" => true,
                "code" => ResponseAlias::HTTP_OK,
                "message" => "Four Ir ScaleUp deleted successfully",
                "query_time" => $this->startTime->diffInSeconds(Carbon::now())
            ]
        ];
        return Response::json($response, ResponseAlias::HTTP_OK);
    }
}
