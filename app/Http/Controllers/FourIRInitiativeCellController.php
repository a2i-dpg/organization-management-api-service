<?php

namespace App\Http\Controllers;

use App\Models\FourIRInitiativeCell;
use App\Services\FourIRServices\FourIRInitiativeCellService;
use Carbon\Carbon;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;
use Throwable;

class FourIRInitiativeCellController extends Controller
{
    public FourIRInitiativeCellService $fourIRInitiativeCellService;
    private Carbon $startTime;

    /**
     * FourIRInitiativeCellController constructor.
     *
     * @param FourIRInitiativeCellService $fourIRInitiativeCellService
     */
    public function __construct(FourIRInitiativeCellService $fourIRInitiativeCellService)
    {
        $this->startTime = Carbon::now();
        $this->fourIRInitiativeCellService = $fourIRInitiativeCellService;
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
        //$this->authorize('viewAny', FourIRInitiativeCell::class);

        $filter = $this->fourIRInitiativeCellService->filterValidator($request)->validate();
        $response = $this->fourIRInitiativeCellService->getFourIRInitiativeCellList($filter, $this->startTime);
        return Response::json($response,ResponseAlias::HTTP_OK);
    }

    /**
     * @param int $id
     * @return JsonResponse
     */
    public function read(int $id): JsonResponse
    {
        $fourIrInitiativeCell = $this->fourIRInitiativeCellService->getOneFourIRInitiativeCell($id);
//        $this->authorize('view', $fourIrInitiative);
        $response = [
            "data" => $fourIrInitiativeCell,
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
//        $this->authorize('create', FourIRInitiativeCell::class);

        $validated = $this->fourIRInitiativeCellService->validator($request)->validate();
        $data = $this->fourIRInitiativeCellService->store($validated);

        $response = [
            'data' => $data,
            '_response_status' => [
                "success" => true,
                "code" => ResponseAlias::HTTP_CREATED,
                "message" => "Four Ir Initiative cell added successfully",
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
        $fourIrInitiativeCell = FourIRInitiativeCell::findOrFail($id);
//        $this->authorize('update', $fourIrInitiative);

        $validated = $this->fourIRInitiativeCellService->validator($request, $id)->validate();
        $data = $this->fourIRInitiativeCellService->update($fourIrInitiativeCell, $validated);

        $response = [
            'data' => $data,
            '_response_status' => [
                "success" => true,
                "code" => ResponseAlias::HTTP_OK,
                "message" => "Four Ir Initiative cell updated successfully",
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
        $fourIrInitiativeCell = FourIRInitiativeCell::findOrFail($id);
//        $this->authorize('delete', $fourIrInitiative);
        $this->fourIRInitiativeCellService->destroy($fourIrInitiativeCell);
        $response = [
            '_response_status' => [
                "success" => true,
                "code" => ResponseAlias::HTTP_OK,
                "message" => "Four Ir Initiative cell deleted successfully",
                "query_time" => $this->startTime->diffInSeconds(Carbon::now())
            ]
        ];
        return Response::json($response, ResponseAlias::HTTP_OK);
    }

    /**
     * update the specified resource from storage
     *
     * @param Request $request
     * @return JsonResponse
     * @throws ValidationException
     * @throws Throwable
     */
    public function setTeamLaunchingDate(Request $request): JsonResponse
    {
        $validated = $this->fourIRInitiativeCellService->cellLaunchingDateValidator($request)->validate();
        $data = $this->fourIRInitiativeCellService->addCellLaunchingDate($validated);

        $response = [
            'data' => $data,
            '_response_status' => [
                "success" => true,
                "code" => ResponseAlias::HTTP_OK,
                "message" => "Four Ir Team launching date updated successfully",
                "query_time" => $this->startTime->diffInSeconds(Carbon::now())
            ]
        ];

        return Response::json($response, ResponseAlias::HTTP_CREATED);
    }
}
