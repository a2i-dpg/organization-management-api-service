<?php

namespace App\Http\Controllers;

use App\Models\FourIROccupation;
use App\Models\FourIRInitiative;
use App\Services\FourIRServices\FourIROccupationService;
use Carbon\Carbon;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;
use Throwable;

class FourIROccupationController extends Controller
{
    /**
     * @var FourIROccupationService
     */
    public FourIROccupationService $fourIROccupationService;

    /**
     * @var Carbon
     */
    private Carbon $startTime;

    /**
     * FourIROccupationController constructor.
     *
     * @param FourIROccupationService $fourIROccupationService
     */
    public function __construct(FourIROccupationService $fourIROccupationService)
    {
        $this->startTime = Carbon::now();
        $this->fourIROccupationService = $fourIROccupationService;
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

        $filter = $this->fourIROccupationService->filterValidator($request)->validate();
        $response = $this->fourIROccupationService->getFourIROccupationList($filter, $this->startTime);
        return Response::json($response,ResponseAlias::HTTP_OK);
    }

    /**
     * @param int $id
     * @return JsonResponse
     */
    public function read(int $id): JsonResponse
    {
        $fourIrOccupation = $this->fourIROccupationService->getOneFourIROccupation($id);
//        $this->authorize('view', $fourIrOccupation);
        $response = [
            "data" => $fourIrOccupation,
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


        $validated = $this->fourIROccupationService->validator($request)->validate();
        $data = $this->fourIROccupationService->store($validated);

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
        $fourIrOccupation = FourIROccupation::findOrFail($id);
//        $this->authorize('update', $fourIrOccupation);

        $validated = $this->fourIROccupationService->validator($request, $id)->validate();
        $data = $this->fourIROccupationService->update($fourIrOccupation, $validated);

        $response = [
            'data' => $data,
            '_response_status' => [
                "success" => true,
                "code" => ResponseAlias::HTTP_OK,
                "message" => "Four Ir Occupation updated successfully",
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
        $fourIrOccupation = FourIROccupation::findOrFail($id);
//        $this->authorize('delete', $fourIrOccupation);
        $this->fourIROccupationService->destroy($fourIrOccupation);
        $response = [
            '_response_status' => [
                "success" => true,
                "code" => ResponseAlias::HTTP_OK,
                "message" => "Four Ir Occupation deleted successfully",
                "query_time" => $this->startTime->diffInSeconds(Carbon::now())
            ]
        ];
        return Response::json($response, ResponseAlias::HTTP_OK);
    }
}
