<?php

namespace App\Http\Controllers;

use App\Models\FourIRGuideline;
use App\Services\FourIRServices\FourIRGuidelineService;
use Carbon\Carbon;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;
use Throwable;

class FourIRGuidelineController extends Controller
{
    public FourIRGuidelineService $fourIRGuidelineService;
    private Carbon $startTime;

    /**
     * FourIRGuidelineController constructor.
     *
     * @param FourIRGuidelineService $fourIRGuidelineService
     */
    public function __construct(FourIRGuidelineService $fourIRGuidelineService)
    {
        $this->startTime = Carbon::now();
        $this->fourIRGuidelineService = $fourIRGuidelineService;
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     * @return JsonResponse
     * @throws AuthorizationException
     * @throws ValidationException
     */
    function store(Request $request): JsonResponse
    {
        //$this->authorize('create', FourIRGuideline::class);

        $validated = $this->fourIRGuidelineService->validator($request)->validate();
        $data = $this->fourIRGuidelineService->store($validated);

        $response = [
            'data' => $data,
            '_response_status' => [
                "success" => true,
                "code" => ResponseAlias::HTTP_CREATED,
                "message" => "Four Ir Guideline added successfully",
                "query_time" => $this->startTime->diffInSeconds(Carbon::now())
            ]
        ];

        return Response::json($response, ResponseAlias::HTTP_CREATED);
    }

//    /**
//     * Update the specified resource in storage
//     * @param Request $request
//     * @param int $id
//     * @return JsonResponse
//     * @throws AuthorizationException
//     * @throws ValidationException
//     */
//    public function update(Request $request, int $id): JsonResponse
//    {
//        $guideline = FourIRGuideline::findOrFail($id);
//        //$this->authorize('update', $guideline);
//
//        $validated = $this->fourIRGuidelineService->validator($request, $id)->validate();
//        $data = $this->fourIRGuidelineService->update($guideline, $validated);
//
//        $response = [
//            'data' => $data,
//            '_response_status' => [
//                "success" => true,
//                "code" => ResponseAlias::HTTP_OK,
//                "message" => "Four Ir guideline updated successfully",
//                "query_time" => $this->startTime->diffInSeconds(Carbon::now())
//            ]
//        ];
//
//        return Response::json($response, ResponseAlias::HTTP_CREATED);
//    }
//
//    /**
//     * Remove the specified resource from storage
//     *
//     * @param int $id
//     * @return JsonResponse
//     * @throws AuthorizationException
//     * @throws Throwable
//     */
//    public function destroy(int $id): JsonResponse
//    {
//        $guideline = FourIRGuideline::findOrFail($id);
//        //$this->authorize('delete', $guideline);
//        $this->fourIRGuidelineService->destroy($guideline);
//        $response = [
//            '_response_status' => [
//                "success" => true,
//                "code" => ResponseAlias::HTTP_OK,
//                "message" => "Four Ir guideline deleted successfully",
//                "query_time" => $this->startTime->diffInSeconds(Carbon::now())
//            ]
//        ];
//        return Response::json($response, ResponseAlias::HTTP_OK);
//    }
}
