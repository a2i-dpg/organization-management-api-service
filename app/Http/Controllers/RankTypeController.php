<?php

namespace App\Http\Controllers;

use App\Models\RankType;
use Carbon\Carbon;
use Exception;
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
     * @return Exception|JsonResponse|Throwable
     * @throws ValidationException
     */
    public function getList(Request $request): JsonResponse
    {
        $filter = $this->rankTypeService->filterValidator($request)->validate();
        try {
            $response = $this->rankTypeService->getRankTypeList($filter ,$this->startTime);
        } catch (Throwable $e) {
            return $e;
        }
        return Response::json($response);
    }

    /**
     * @param int $id
     * @return Exception|JsonResponse|Throwable
     */
    public function read(int $id): JsonResponse
    {
        try {
            $response = $this->rankTypeService->getOneRankType($id, $this->startTime);
        } catch (Throwable $e) {
            return $e;
        }
        return Response::json($response);
    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return Exception|JsonResponse|Throwable
     * @throws ValidationException
     */
    function store(Request $request): JsonResponse
    {
        $validated = $this->rankTypeService->validator($request)->validate();
        try {
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
        } catch (Throwable $e) {
            return $e;
        }
        return Response::json($response, ResponseAlias::HTTP_CREATED);
    }

    /**
     * Update a specified resource to storage
     * @param Request $request
     * @param int $id
     * @return Exception|JsonResponse|Throwable
     * @throws ValidationException
     */
    public function update(Request $request, int $id): JsonResponse
    {
        $rankType = RankType::findOrFail($id);
        $validated = $this->rankTypeService->validator($request, $id)->validate();

        try {
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

        } catch (Throwable $e) {
            return $e;
        }
        return Response::json($response, ResponseAlias::HTTP_CREATED);
    }

    /**
     * Delete the specified resource from the storage
     * @param int $id
     * @return Exception|JsonResponse|Throwable
     */
    public function destroy(int $id): JsonResponse
    {
        $rankType = RankType::findOrFail($id);
        try {
            $this->rankTypeService->destroy($rankType);
            $response = [
                '_response_status' => [
                    "success" => true,
                    "code" => ResponseAlias::HTTP_OK,
                    "message" => "Rank Type deleted successfully",
                    "query_time" => $this->startTime->diffInSeconds(Carbon::now())
                ]
            ];
        } catch (Throwable $e) {
            return $e;
        }
        return Response::json($response, ResponseAlias::HTTP_OK);
    }

    /**
     * @param Request $request
     * @return Exception|JsonResponse|Throwable
     */
    public function getTrashedData(Request $request)
    {
        try {
            $response = $this->rankTypeService->getTrashedRankTypeList($request, $this->startTime);
        } catch (Throwable $e) {
            return $e;
        }
        return Response::json($response);
    }


    /**
     * @param int $id
     * @return Exception|JsonResponse|Throwable
     */
    public function restore(int $id)
    {
        $rankType = RankType::onlyTrashed()->findOrFail($id);
        try {
            $this->rankTypeService->restore($rankType);
            $response = [
                '_response_status' => [
                    "success" => true,
                    "code" => ResponseAlias::HTTP_OK,
                    "message" => "Rank Type restored successfully",
                    "query_time" => $this->startTime->diffInSeconds(Carbon::now())
                ]
            ];
        } catch (Throwable $e) {
            return $e;
        }
        return Response::json($response, ResponseAlias::HTTP_OK);
    }

    /**
     * @param int $id
     * @return Exception|JsonResponse|Throwable
     */
    public function forceDelete(int $id)
    {
        $rankType = RankType::onlyTrashed()->findOrFail($id);
        try {
            $this->rankTypeService->forceDelete($rankType);
            $response = [
                '_response_status' => [
                    "success" => true,
                    "code" => ResponseAlias::HTTP_OK,
                    "message" => "Rank Type permanently deleted successfully",
                    "query_time" => $this->startTime->diffInSeconds(Carbon::now())
                ]
            ];
        } catch (Throwable $e) {
            return $e;
        }
        return Response::json($response, ResponseAlias::HTTP_OK);
    }
}
