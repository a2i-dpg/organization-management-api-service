<?php

namespace App\Http\Controllers;

use App\Models\FourIRCourseDevelopment;
use App\Services\FourIRServices\FourIRSkillDevelopmentService;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;
use Throwable;

class FourIRSkillDevelopmentController extends Controller
{
    public FourIRSkillDevelopmentService $fourIRCourseDevelopmentService;
    private Carbon $startTime;


    public function __construct(FourIRSkillDevelopmentService $fourIRCourseDevelopmentService)
    {
        $this->startTime = Carbon::now();
        $this->fourIRCourseDevelopmentService = $fourIRCourseDevelopmentService;
    }


    /**
     * @param Request $request
     * @return JsonResponse
     * @throws Throwable|ValidationException
     */
    public function getList(Request $request): JsonResponse
    {

        $filter = $this->fourIRCourseDevelopmentService->filterValidator($request)->validate();
        $response = $this->fourIRCourseDevelopmentService->getFourIRCourseDevelopmentList($filter, $this->startTime);
        return Response::json($response, ResponseAlias::HTTP_OK);
    }

    /**
     * @param int $id
     * @return JsonResponse
     */
    public function read(int $id): JsonResponse
    {
        $fourIRTot = $this->fourIRCourseDevelopmentService->getOneFourIRCourseDevelopment($id);
        $response = [
            "data" => $fourIRTot,
            "_response_status" => [
                "success" => true,
                "code" => ResponseAlias::HTTP_OK,
                "query_time" => $this->startTime->diffInSeconds(Carbon::now())
            ]
        ];
        return Response::json($response, ResponseAlias::HTTP_OK);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     * @return JsonResponse
     * @throws Throwable
     */
    function store(Request $request): JsonResponse
    {
        $validated = $this->fourIRCourseDevelopmentService->validator($request)->validate();
        $data = $this->fourIRCourseDevelopmentService->store($validated);

        $response = [
            'data' => $data,
            '_response_status' => [
                "success" => true,
                "code" => ResponseAlias::HTTP_CREATED,
                "message" => "Four Ir Course Development  added successfully",
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
     * @throws Throwable
     */
    public function update(Request $request, int $id): JsonResponse
    {
        $fourIrCourseDevelopment = FourIRCourseDevelopment::findOrFail($id);
        $validated = $this->fourIRCourseDevelopmentService->validator($request, $id)->validate();

        $data = $this->fourIRCourseDevelopmentService->update($fourIrCourseDevelopment, $validated);

        $response = [
            'data' => $data,
            '_response_status' => [
                "success" => true,
                "code" => ResponseAlias::HTTP_OK,
                "message" => "Four Ir Course Development updated successfully",
                "query_time" => $this->startTime->diffInSeconds(Carbon::now())
            ]
        ];

        return Response::json($response, ResponseAlias::HTTP_OK);
    }

    /**
     * Remove the specified resource from storage
     * @param int $id
     * @return JsonResponse
     */
    public function destroy(int $id): JsonResponse
    {
        $fourIrCourseDevelopment = FourIRCourseDevelopment::findOrFail($id);
        $this->fourIRCourseDevelopmentService->destroy($fourIrCourseDevelopment);
        $response = [
            '_response_status' => [
                "success" => true,
                "code" => ResponseAlias::HTTP_OK,
                "message" => "Four Ir Course Development deleted successfully",
                "query_time" => $this->startTime->diffInSeconds(Carbon::now())
            ]
        ];
        return Response::json($response, ResponseAlias::HTTP_OK);
    }
}
