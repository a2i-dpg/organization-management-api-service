<?php

namespace App\Http\Controllers;

use App\Models\FourIrProject;
use App\Services\FourIrProjectService;
use Carbon\Carbon;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;
use Throwable;

class FourIRProjectController extends Controller
{
    public FourIrProjectService $fourIrProjectService;
    private Carbon $startTime;

    /**
     * FourIrProjectController constructor.
     *
     * @param FourIrProjectService $fourIrProjectService
     */
    public function __construct(FourIrProjectService $fourIrProjectService)
    {
        $this->startTime = Carbon::now();
        $this->fourIrProjectService = $fourIrProjectService;
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
//        $this->authorize('viewAny', FourIrProject::class);

        $filter = $this->fourIrProjectService->filterValidator($request)->validate();
        $response = $this->fourIrProjectService->getFourIrProjectList($filter, $this->startTime);
        return Response::json($response,ResponseAlias::HTTP_OK);
    }

    /**
     * @param int $id
     * @return JsonResponse
     */
    public function read(int $id): JsonResponse
    {
        $fourIrProject = $this->fourIrProjectService->getOneFourIrProject($id);
//        $this->authorize('view', $fourIrProject);
        $response = [
            "data" => $fourIrProject,
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
//        $this->authorize('create', FourIrProject::class);

        $validated = $this->fourIrProjectService->validator($request)->validate();
        $data = $this->fourIrProjectService->store($validated);

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
        $fourIrProject = FourIrProject::findOrFail($id);
//        $this->authorize('update', $fourIrProject);

        $validated = $this->fourIrProjectService->validator($request, $id)->validate();
        $data = $this->fourIrProjectService->update($fourIrProject, $validated);

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
        $fourIrProject = FourIrProject::findOrFail($id);
//        $this->authorize('delete', $fourIrProject);
        $this->fourIrProjectService->destroy($fourIrProject);
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
