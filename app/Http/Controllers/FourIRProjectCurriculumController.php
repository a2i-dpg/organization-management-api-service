<?php

namespace App\Http\Controllers;

use App\Models\FourIRProjectCurriculum;
use App\Services\FourIRServices\FourIRProjectCurriculumService;
use Carbon\Carbon;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;
use Throwable;

class FourIRProjectCurriculumController extends Controller
{
    public FourIRProjectCurriculumService $fourIrProjectCurriculumService;
    private Carbon $startTime;

    /**
     * FourIRProjectController constructor.
     *
     * @param FourIRProjectCurriculumService $fourIrProjectCurriculumService
     */
    public function __construct(FourIRProjectCurriculumService $fourIrProjectCurriculumService)
    {
        $this->startTime = Carbon::now();
        $this->fourIrProjectCurriculumService = $fourIrProjectCurriculumService;
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
//        $this->authorize('viewAny', FourIRProjectCurriculum::class);

        $filter = $this->fourIrProjectCurriculumService->filterValidator($request)->validate();
        $response = $this->fourIrProjectCurriculumService->getFourIRProjectCurriculumList($filter, $this->startTime);
        return Response::json($response,ResponseAlias::HTTP_OK);
    }

    /**
     * @param int $id
     * @return JsonResponse
     */
    public function read(int $id): JsonResponse
    {
        $fourIrProjectCurriculum = $this->fourIrProjectCurriculumService->getOneFourIRProjectCurriculum($id);
//        $this->authorize('view', $fourIrProjectCurriculum);
        $response = [
            "data" => $fourIrProjectCurriculum,
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
//        $this->authorize('create', FourIRProjectCurriculum::class);

        $validated = $this->fourIrProjectCurriculumService->validator($request)->validate();
        $data = $this->fourIrProjectCurriculumService->store($validated);

        $response = [
            'data' => $data,
            '_response_status' => [
                "success" => true,
                "code" => ResponseAlias::HTTP_CREATED,
                "message" => "Four Ir Project curriculum added successfully",
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
        $fourIrProjectCurriculum = FourIRProjectCurriculum::findOrFail($id);
//        $this->authorize('update', $fourIrProjectCurriculum);

        $validated = $this->fourIrProjectCurriculumService->validator($request, $id)->validate();
        $data = $this->fourIrProjectCurriculumService->update($fourIrProjectCurriculum, $validated);

        $response = [
            'data' => $data,
            '_response_status' => [
                "success" => true,
                "code" => ResponseAlias::HTTP_OK,
                "message" => "Four Ir Project curriculum updated successfully",
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
        $fourIrProjectCurriculum = FourIRProjectCurriculum::findOrFail($id);
//        $this->authorize('delete', $fourIrProjectCurriculum);
        $this->fourIrProjectCurriculumService->destroy($fourIrProjectCurriculum);
        $response = [
            '_response_status' => [
                "success" => true,
                "code" => ResponseAlias::HTTP_OK,
                "message" => "Four Ir Project curriculum deleted successfully",
                "query_time" => $this->startTime->diffInSeconds(Carbon::now())
            ]
        ];
        return Response::json($response, ResponseAlias::HTTP_OK);
    }
}
