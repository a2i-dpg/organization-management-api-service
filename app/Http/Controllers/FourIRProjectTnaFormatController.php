<?php

namespace App\Http\Controllers;

use App\Models\FourIRProjectTnaFormat;
use App\Services\FourIRServices\FourIRProjectTnaFormatService;
use Carbon\Carbon;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;
use Throwable;

class FourIRProjectTnaFormatController extends Controller
{
    public FourIRProjectTnaFormatService $FourIRProjectTnaFormatService;
    private Carbon $startTime;

    /**
     * FourIRProjectTnaFormatController constructor.
     *
     * @param FourIRProjectTnaFormatService $FourIRProjectTnaFormatService
     */
    public function __construct(FourIRProjectTnaFormatService $FourIRProjectTnaFormatService)
    {
        $this->startTime = Carbon::now();
        $this->FourIRProjectTnaFormatService = $FourIRProjectTnaFormatService;
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
//        $this->authorize('viewAny', FourIRProjectCell::class);

        $filter = $this->FourIRProjectTnaFormatService->filterValidator($request)->validate();
        $response = $this->FourIRProjectTnaFormatService->getFourIrProjectTnaFormatList($filter, $this->startTime);
        return Response::json($response,ResponseAlias::HTTP_OK);
    }

    /**
     * @param int $id
     * @return JsonResponse
     */
    public function read(int $id): JsonResponse
    {
        $fourIrProjectCell = $this->FourIRProjectTnaFormatService->getOneFourIrProjectTnaFormat($id);
//        $this->authorize('view', $fourIrProject);
        $response = [
            "data" => $fourIrProjectCell,
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
//        $this->authorize('create', FourIRProjectCell::class);

        $validated = $this->FourIRProjectTnaFormatService->validator($request)->validate();
        $data = $this->FourIRProjectTnaFormatService->store($validated);

        $response = [
            'data' => $data,
            '_response_status' => [
                "success" => true,
                "code" => ResponseAlias::HTTP_CREATED,
                "message" => "Four Ir Project Tna Formate Added added successfully",
                "query_time" => $this->startTime->diffInSeconds(Carbon::now())
            ]
        ];

        return Response::json($response, ResponseAlias::HTTP_CREATED);
    }

    /**
     * @param Request $request
     * @param int $id
     * @return JsonResponse
     * @throws ValidationException
     */

    public function update(Request $request, int $id): JsonResponse
    {

        $fourIrProjectTnaFormat = FourIRProjectTnaFormat::findOrFail($id);
        $validated = $this->FourIRProjectTnaFormatService->validator($request, $id)->validate();
        $data = $this->FourIRProjectTnaFormatService->update($fourIrProjectTnaFormat, $validated);

        $response = [
            'data' => $data,
            '_response_status' => [
                "success" => true,
                "code" => ResponseAlias::HTTP_OK,
                "message" => "Four Ir Project Tna Format updated successfully",
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
        $fourIrProjectCell = FourIRProjectTnaFormat::findOrFail($id);
//        $this->authorize('delete', $fourIrProject);
        $this->FourIRProjectTnaFormatService->destroy($fourIrProjectCell);
        $response = [
            '_response_status' => [
                "success" => true,
                "code" => ResponseAlias::HTTP_OK,
                "message" => "Four Ir Tna deleted successfully",
                "query_time" => $this->startTime->diffInSeconds(Carbon::now())
            ]
        ];
        return Response::json($response, ResponseAlias::HTTP_OK);
    }
}
