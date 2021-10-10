<?php

namespace App\Http\Controllers;

use App\Models\Rank;
use Carbon\Carbon;
use Exception;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;
use Throwable;
use App\Services\RankService;


/**
 * Class RankController
 * @package App\Http\Controllers
 */
class RankController extends Controller
{
    public RankService $rankService;
    private Carbon $startTime;

    /**
     * RankController constructor.
     * @param RankService $rankService
     */
    public function __construct(RankService $rankService)
    {
        $this->startTime = Carbon::now();
        $this->rankService = $rankService;
    }

    /**
     * Display a listing of the resource.
     * @param Request $request
     * @return Exception|JsonResponse|Throwable
     * @throws ValidationException
     * @throws AuthorizationException
     */
    public function getList(Request $request)
    {
        $this->authorize('viewAny', Rank::class);

        $filter = $this->rankService->filterValidator($request)->validate();
        try {
            $response = $this->rankService->getRankList($filter, $this->startTime);
        } catch (Throwable $e) {
            throw $e;
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
            $response = $this->rankService->getOneRank($id, $this->startTime);
            if (!$response) {
                abort(ResponseAlias::HTTP_NOT_FOUND);
            }
            $this->authorize('view', $response['data']);
        } catch (Throwable $e) {
            throw $e;
        }
        return Response::json($response);
    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return Exception|JsonResponse|Throwable
     * @throws ValidationException
     * @throws AuthorizationException
     */
    function store(Request $request): JsonResponse
    {
        $this->authorize('create', Rank::class);

        $validated = $this->rankService->validator($request)->validate();
        try {
            $data = $this->rankService->store($validated);

            $response = [
                'data' => $data ? $data : null,
                '_response_status' => [
                    "success" => true,
                    "code" => ResponseAlias::HTTP_CREATED,
                    "message" => "Rank added successfully",
                    "query_time" => $this->startTime->diffInSeconds(Carbon::now())
                ]
            ];
        } catch (Throwable $e) {
            throw $e;
        }

        return Response::json($response, ResponseAlias::HTTP_CREATED);
    }

    /**
     * Update the specified resource in storage
     * @param Request $request
     * @param int $id
     * @return Exception|JsonResponse|Throwable
     * @throws ValidationException
     * @throws AuthorizationException
     */
    public function update(Request $request, int $id): JsonResponse
    {
        $rank = Rank::findOrFail($id);
        $this->authorize('update', $rank);

        $validated = $this->rankService->validator($request, $id)->validate();
        try {
            $data = $this->rankService->update($rank, $validated);

            $response = [
                'data' => $data ?: null,
                '_response_status' => [
                    "success" => true,
                    "code" => ResponseAlias::HTTP_OK,
                    "message" => "Rank updated successfully",
                    "query_time" => $this->startTime->diffInSeconds(Carbon::now())
                ]
            ];

        } catch (Throwable $e) {
            throw $e;
        }

        return Response::json($response, ResponseAlias::HTTP_CREATED);

    }

    /**
     * Remove the specified resource from storage
     * @param int $id
     * @return Exception|JsonResponse|Throwable
     * @throws AuthorizationException
     */
    public function destroy(int $id): JsonResponse
    {
        $rank = Rank::findOrFail($id);
        $this->authorize('delete', $rank);
        try {
            $this->rankService->destroy($rank);
            $response = [
                '_response_status' => [
                    "success" => true,
                    "code" => ResponseAlias::HTTP_OK,
                    "message" => "Rank deleted successfully",
                    "query_time" => $this->startTime->diffInSeconds(Carbon::now())
                ]
            ];
        } catch (Throwable $e) {
            throw $e;
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
            $response = $this->rankService->getTrashedRankList($request, $this->startTime);
        } catch (Throwable $e) {
            throw $e;
        }
        return Response::json($response);
    }


    /**
     * @param int $id
     * @return Exception|JsonResponse|Throwable
     */
    public function restore(int $id)
    {
        $rank = Rank::onlyTrashed()->findOrFail($id);
        try {
            $this->rankService->restore($rank);
            $response = [
                '_response_status' => [
                    "success" => true,
                    "code" => ResponseAlias::HTTP_OK,
                    "message" => "Rank restored successfully",
                    "query_time" => $this->startTime->diffInSeconds(Carbon::now())
                ]
            ];
        } catch (Throwable $e) {
            throw $e;
        }
        return Response::json($response, ResponseAlias::HTTP_OK);
    }

    /**
     * @param int $id
     * @return Exception|JsonResponse|Throwable
     */
    public function forceDelete(int $id)
    {
        $rank = Rank::onlyTrashed()->findOrFail($id);
        try {
            $this->rankService->forceDelete($rank);
            $response = [
                '_response_status' => [
                    "success" => true,
                    "code" => ResponseAlias::HTTP_OK,
                    "message" => "Rank permanently deleted successfully",
                    "query_time" => $this->startTime->diffInSeconds(Carbon::now())
                ]
            ];
        } catch (Throwable $e) {
            throw $e;
        }
        return Response::json($response, ResponseAlias::HTTP_OK);
    }
}


