<?php

namespace App\Http\Controllers;

use App\Models\RankType;
use Carbon\Carbon;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Services\RankTypeService;
use Illuminate\Support\Facades\Response;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;
use Throwable;

/**
 * Class RankTypeController
 * @package App\Http\Controllers
 */
class RankTypeController extends Controller
{
    /**
     * @var RankTypeService
     */
    public RankTypeService $rankTypeService;

    /**
     * @var Carbon
     */
    private Carbon $startTime;

    /**
     * RankTypeController constructor.
     * @param RankTypeService $rankTypeService
     */
    public function __Construct(RankTypeService $rankTypeService)
    {
        $this->startTime = Carbon::now();
        $this->rankTypeService = $rankTypeService;
    }

    /**
     * Display a listing  of  the resources
     * @param Request $request
     * @return JsonResponse
     * @throws AuthorizationException
     * @throws Throwable
     * @throws ValidationException
     */
    public function getList(Request $request): JsonResponse
    {
        $this->authorize('viewAny', RankType::class);

        $filter = $this->rankTypeService->filterValidator($request)->validate();
        $response = $this->rankTypeService->getRankTypeList($filter, $this->startTime);
        return Response::json($response);
    }

    /**
     * @param int $id
     * @return JsonResponse
     * @throws AuthorizationException
     * @throws Throwable
     */
    public function read(int $id): JsonResponse
    {
        $response = $this->rankTypeService->getOneRankType($id, $this->startTime);
        if (!$response) {
            abort(ResponseAlias::HTTP_NOT_FOUND);
        }
        $this->authorize('view', $response['data']);
        return Response::json($response);
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
        $this->authorize('create', RankType::class);

        $validated = $this->rankTypeService->validator($request)->validate();
        $data = $this->rankTypeService->store($validated);
        $response = [
            'data' => $data ?: null,
            '_response_status' => [
                "success" => true,
                "code" => ResponseAlias::HTTP_CREATED,
                "message" => "Rank Type added successfully",
                "query_time" => $this->startTime->diffInSeconds(Carbon::now())
            ]
        ];
        return Response::json($response, ResponseAlias::HTTP_CREATED);
    }

    /**
     * Update a specified resource to storage
     * @param Request $request
     * @param int $id
     * @return JsonResponse
     * @throws AuthorizationException
     * @throws Throwable
     * @throws ValidationException
     */
    public function update(Request $request, int $id): JsonResponse
    {
        $rankType = RankType::findOrFail($id);
        $this->authorize('update', $rankType);

        $validated = $this->rankTypeService->validator($request, $id)->validate();

        $data = $this->rankTypeService->update($rankType, $validated);

        $response = [
            'data' => $data ? $data : null,
            '_response_status' => [
                "success" => true,
                "code" => ResponseAlias::HTTP_OK,
                "message" => "Rank Type updated successfully",
                "query_time" => $this->startTime->diffInSeconds(Carbon::now())
            ]
        ];
        return Response::json($response, ResponseAlias::HTTP_CREATED);
    }

    /**
     * Delete the specified resource from the storage
     * @param int $id
     * @return JsonResponse
     * @throws AuthorizationException
     * @throws Throwable
     */
    public function destroy(int $id): JsonResponse
    {
        $rankType = RankType::findOrFail($id);
        $this->authorize('delete', $rankType);

        $this->rankTypeService->destroy($rankType);
        $response = [
            '_response_status' => [
                "success" => true,
                "code" => ResponseAlias::HTTP_OK,
                "message" => "Rank Type deleted successfully",
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
        $response = $this->rankTypeService->getTrashedRankTypeList($request, $this->startTime);
        return Response::json($response);
    }


    /**
     * @param int $id
     * @return JsonResponse
     * @throws Throwable
     */
    public function restore(int $id): JsonResponse
    {
        $rankType = RankType::onlyTrashed()->findOrFail($id);
        $this->rankTypeService->restore($rankType);
        $response = [
            '_response_status' => [
                "success" => true,
                "code" => ResponseAlias::HTTP_OK,
                "message" => "Rank Type restored successfully",
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
        $rankType = RankType::onlyTrashed()->findOrFail($id);
        $this->rankTypeService->forceDelete($rankType);
        $response = [
            '_response_status' => [
                "success" => true,
                "code" => ResponseAlias::HTTP_OK,
                "message" => "Rank Type permanently deleted successfully",
                "query_time" => $this->startTime->diffInSeconds(Carbon::now())
            ]
        ];
        return Response::json($response, ResponseAlias::HTTP_OK);
    }
}
