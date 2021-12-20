<?php

namespace App\Http\Controllers;

use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\Request;
use App\Models\Occupation;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Response;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;
use Throwable;
use App\Services\OccupationService;

/**
 * Class OccupationController
 * @package App\Http\Controllers
 */
class OccupationController extends Controller
{
    /**
     * @var OccupationService
     */
    public OccupationService $occupationService;

    /**
     * @var Carbon
     */
    private Carbon $startTime;

    /**
     * OccupationController constructor.
     * @param OccupationService $occupationService
     */
    public function __construct(OccupationService $occupationService)
    {
        $this->occupationService = $occupationService;
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
        $this->authorize('viewAny', Occupation::class);

        $filter = $this->occupationService->filterValidator($request)->validate();
        $response = $this->occupationService->getOccupationList($filter, $this->startTime);
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
        $occupation = $this->occupationService->getOneOccupation($id);
        $this->authorize('view', $occupation);
        $response = [
            "data" => $occupation,
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
        $this->authorize('create', Occupation::class);

        $validated = $this->occupationService->validator($request)->validate();
        $data = $this->occupationService->store($validated);
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
        $occupation = Occupation::findOrFail($id);
        $this->authorize('update', $occupation);

        $validated = $this->occupationService->validator($request, $id)->validate();
        $data = $this->occupationService->update($occupation, $validated);
        $response = [
            'data' => $data,
            '_response_status' => [
                "success" => true,
                "code" => ResponseAlias::HTTP_OK,
                "message" => "Occupation updated successfully.",
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
        $occupation = Occupation::findOrFail($id);
        $this->authorize('delete', $occupation);
        $this->occupationService->destroy($occupation);
        $response = [
            '_response_status' => [
                "success" => true,
                "code" => ResponseAlias::HTTP_OK,
                "message" => "Occupation deleted successfully.",
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
        $response = $this->occupationService->getTrashedOccupationList($request, $this->startTime);
        return Response::json($response);
    }


    /**
     * @param int $id
     * @return JsonResponse
     * @throws Throwable
     */
    public function restore(int $id): JsonResponse
    {
        $occupation = Occupation::onlyTrashed()->findOrFail($id);
        $this->occupationService->restore($occupation);
        $response = [
            '_response_status' => [
                "success" => true,
                "code" => ResponseAlias::HTTP_OK,
                "message" => "Occupation restored successfully",
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
        $occupation = Occupation::onlyTrashed()->findOrFail($id);
        $this->occupationService->forceDelete($occupation);
        $response = [
            '_response_status' => [
                "success" => true,
                "code" => ResponseAlias::HTTP_OK,
                "message" => "Occupation permanently deleted successfully",
                "query_time" => $this->startTime->diffInSeconds(Carbon::now())
            ]
        ];
        return Response::json($response, ResponseAlias::HTTP_OK);
    }
}
