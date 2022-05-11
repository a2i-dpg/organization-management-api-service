<?php

namespace App\Http\Controllers;

use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\Request;
use App\Models\FourIRSector;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Response;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;
use Throwable;
use App\Services\FourIRServices\FourIRSectorService;

/**
 * Class OccupationController
 * @package App\Http\Controllers
 */
class FourIRSectorController extends Controller
{
    /**
     * @var FourIRSectorService
     */
    public FourIRSectorService $fourIrSectorService;

    /**
     * @var Carbon
     */
    private Carbon $startTime;

    /**
     * FourIRSectorController constructor.
     * @param FourIRSectorService $fourIrSectorService
     */
    public function __construct(FourIRSectorService $fourIrSectorService)
    {
        $this->fourIrSectorService = $fourIrSectorService;
        $this->startTime = Carbon::now();
    }

    /**
     * Display a listing of the resource.
     * @param Request $request
     * @return JsonResponse
     * @throws AuthorizationException
     * @throws Throwable
     * @throws ValidationException
     */
    public function getList(Request $request): JsonResponse
    {
//        $this->authorize('viewAny', Occupation::class);

        $filter = $this->fourIrSectorService->filterValidator($request)->validate();
        $response = $this->fourIrSectorService->getSectorList($filter, $this->startTime);
        return Response::json($response,ResponseAlias::HTTP_OK);
    }


    /**
     * Display the specified resource
     * @param int $id
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function read(int $id): JsonResponse
    {
        $sector = $this->fourIrSectorService->getOneSector($id);
//        $this->authorize('view', $sector);
        $response = [
            "data" => $sector,
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
     * @param Request $request
     * @return JsonResponse
     * @throws AuthorizationException
     * @throws Throwable
     * @throws ValidationException
     */
    function store(Request $request): JsonResponse
    {
//        $this->authorize('create', Occupation::class);

        $validated = $this->fourIrSectorService->validator($request)->validate();
        $data = $this->fourIrSectorService->store($validated);
        $response = [
            'data' => $data,
            '_response_status' => [
                "success" => true,
                "code" => ResponseAlias::HTTP_CREATED,
                "message" => "Occupation added successfully.",
                "query_time" => $this->startTime->diffInSeconds(Carbon::now())
            ]
        ];
        return Response::json($response, ResponseAlias::HTTP_CREATED);
    }

    /**
     * update the specified resource in storage
     * @param Request $request
     * @param int $id
     * @return JsonResponse
     * @throws AuthorizationException
     * @throws Throwable
     * @throws ValidationException
     */
    public function update(Request $request, int $id): JsonResponse
    {
        $sector = FourIRSector::findOrFail($id);
//        $this->authorize('update', $sector);

        $validated = $this->fourIrSectorService->validator($request, $id)->validate();
        $data = $this->fourIrSectorService->update($sector, $validated);
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
     * Remove the specified resource from storage
     * @param int $id
     * @return JsonResponse
     * @throws AuthorizationException
     * @throws Throwable
     */
    public function destroy(int $id): JsonResponse
    {
        $sector = FourIRSector::findOrFail($id);
//        $this->authorize('delete', $sector);
        $this->fourIrSectorService->destroy($sector);
        $response = [
            '_response_status' => [
                "success" => true,
                "code" => ResponseAlias::HTTP_OK,
                "message" => "FourIRSector deleted successfully.",
                "query_time" => $this->startTime->diffInSeconds(Carbon::now())
            ]
        ];
        return Response::json($response, ResponseAlias::HTTP_OK);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     * @throws Throwable
     */
    public function getTrashedData(Request $request): JsonResponse
    {
        $response = $this->fourIrSectorService->getTrashedOccupationList($request, $this->startTime);
        return Response::json($response);
    }


    /**
     * @param int $id
     * @return JsonResponse
     * @throws Throwable
     */
    public function restore(int $id): JsonResponse
    {
        $sector = FourIRSector::onlyTrashed()->findOrFail($id);
        $this->fourIrSectorService->restore($sector);
        $response = [
            '_response_status' => [
                "success" => true,
                "code" => ResponseAlias::HTTP_OK,
                "message" => "FourIRSector restored successfully",
                "query_time" => $this->startTime->diffInSeconds(Carbon::now())
            ]
        ];
        return Response::json($response, ResponseAlias::HTTP_OK);
    }

    /**
     * @param int $id
     * @return JsonResponse
     * @throws Throwable
     */
    public function forceDelete(int $id): JsonResponse
    {
        $sector = FourIRSector::onlyTrashed()->findOrFail($id);
        $this->fourIrSectorService->forceDelete($sector);
        $response = [
            '_response_status' => [
                "success" => true,
                "code" => ResponseAlias::HTTP_OK,
                "message" => "FourIRSector permanently deleted successfully",
                "query_time" => $this->startTime->diffInSeconds(Carbon::now())
            ]
        ];
        return Response::json($response, ResponseAlias::HTTP_OK);
    }
}
