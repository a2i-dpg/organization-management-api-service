<?php

namespace App\Http\Controllers;

use App\Models\HumanResource;
use App\Services\HumanResourceService;
use Carbon\Carbon;
use Exception;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;
use Throwable;

class HumanResourceController extends Controller
{

    /**
     * @var HumanResourceService
     */
    public HumanResourceService $humanResourceService;
    private Carbon $startTime;

    /**
     * HumanResourceController constructor.
     * @param HumanResourceService $humanResourceService
     */
    public function __construct(HumanResourceService $humanResourceService)
    {
        $this->humanResourceService = $humanResourceService;
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
        $this->authorize('viewAny', HumanResource::class);

        $filter = $this->humanResourceService->filterValidator($request)->validate();
        $response = $this->humanResourceService->getHumanResourceList($filter, $this->startTime);

        return Response::json($response);
    }

    /**
     * Display the specified resource.
     * @param int $id
     * @return JsonResponse
     * @throws AuthorizationException
     * @throws Throwable
     */
    public function read(int $id): JsonResponse
    {
        $response = $this->humanResourceService->getOneHumanResource($id, $this->startTime);
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
    public function store(Request $request): JsonResponse
    {
        $this->authorize('create', HumanResource::class);

        $validatedData = $this->humanResourceService->validator($request)->validate();
        $data = $this->humanResourceService->store($validatedData);
        $response = [
            'data' => $data ?: null,
            '_response_status' => [
                "success" => true,
                "code" => ResponseAlias::HTTP_CREATED,
                "message" => "Human Resource added successfully",
                "query_time" => $this->startTime->diffInSeconds(Carbon::now())
            ]
        ];

        return Response::json($response, ResponseAlias::HTTP_CREATED);
    }

    /**
     * Update the specified resource in storage.
     * @param Request $request
     * @param int $id
     * @return JsonResponse
     * @throws AuthorizationException
     * @throws Throwable
     * @throws ValidationException
     */
    public function update(Request $request, int $id): JsonResponse
    {
        $humanResource = HumanResource::findOrFail($id);
        $this->authorize('update', $humanResource);

        $validated = $this->humanResourceService->validator($request, $id)->validate();
        $data = $this->humanResourceService->update($humanResource, $validated);

        $response = [
            'data' => $data ?: null,
            '_response_status' => [
                "success" => true,
                "code" => ResponseAlias::HTTP_OK,
                "message" => "Human Resource updated successfully",
                "query_time" => $this->startTime->diffInSeconds(Carbon::now())
            ]
        ];

        return Response::json($response, ResponseAlias::HTTP_CREATED);
    }

    /**
     * Remove the specified resource from storage.
     * @param int $id
     * @return JsonResponse
     * @throws AuthorizationException
     * @throws Throwable
     */
    public function destroy(int $id): JsonResponse
    {
        $humanResource = HumanResource::findOrFail($id);
        $this->authorize('delete', $humanResource);

        try {
            $this->humanResourceService->destroy($humanResource);
            $response = [
                '_response_status' => [
                    "success" => true,
                    "code" => ResponseAlias::HTTP_OK,
                    "message" => "Human Resource deleted successfully",
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
     * @return JsonResponse
     * @throws Throwable
     */
    public function getTrashedData(Request $request): JsonResponse
    {
        $response = $this->humanResourceService->getTrashedHumanResourceList($request, $this->startTime);
        return Response::json($response);
    }


    /**
     * @param int $id
     * @return JsonResponse
     * @throws Throwable
     */
    public function restore(int $id): JsonResponse
    {
        $humanResource = HumanResource::onlyTrashed()->findOrFail($id);
        $this->humanResourceService->restore($humanResource);
        $response = [
            '_response_status' => [
                "success" => true,
                "code" => ResponseAlias::HTTP_OK,
                "message" => "HumanResource restored successfully",
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
        $humanResource = HumanResource::onlyTrashed()->findOrFail($id);
        $this->humanResourceService->forceDelete($humanResource);
        $response = [
            '_response_status' => [
                "success" => true,
                "code" => ResponseAlias::HTTP_OK,
                "message" => "HumanResource permanently deleted successfully",
                "query_time" => $this->startTime->diffInSeconds(Carbon::now())
            ]
        ];
        return Response::json($response, ResponseAlias::HTTP_OK);
    }
}
