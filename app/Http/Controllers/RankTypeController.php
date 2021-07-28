<?php

namespace App\Http\Controllers;

use App\Models\RankType;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Services\RankTypeService;
use Illuminate\Support\Facades\Response;
use Illuminate\Validation\ValidationException;
use Throwable;
use App\Helpers\Classes\CustomExceptionHandler;

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
     */
    public function getList(Request $request): JsonResponse
    {
        try {
            $response = $this->rankTypeService->getRankTypeList($request, $this->startTime);
        } catch (Throwable $e) {
            $handler = new CustomExceptionHandler($e);
            $response = [
                '_response_status' => array_merge([
                    "success" => false,
                    "started" => $this->startTime,
                    "finished" => Carbon::now(),
                ], $handler->convertExceptionToArray())
            ];

            return Response::json($response, $response['_response_status']['code']);
        }

        return Response::json($response);
    }

    /**
     * @param $id
     * @return JsonResponse
     */
    public function read(int $id): JsonResponse
    {
        try {
            $response = $this->rankTypeService->getOneRankType($id, $this->startTime);
        } catch (Throwable $e) {
            $handler = new CustomExceptionHandler($e);
            $response = [
                '_response_status' => array_merge([
                    "success" => false,
                    "started" => $this->startTime,
                    "finished" => Carbon::now(),
                ], $handler->convertExceptionToArray())
            ];
            return Response::json($response, $response['_response_status']['code']);
        }
        return Response::json($response);

    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return JsonResponse
     * @throws ValidationException
     */

    function store(Request $request): JsonResponse
    {
        $validated = $this->rankTypeService->validator($request)->validate();
        try {
            $data = $this->rankTypeService->store($validated);

            $response = [
                'data' => $data ? $data : null,
                '_response_status' => [
                    "success" => true,
                    "code" => JsonResponse::HTTP_CREATED,
                    "message" => "Job finished successfully.",
                    "started" => $this->startTime,
                    "finished" => Carbon::now(),
                ]
            ];
        } catch (Throwable $e) {
            $handler = new CustomExceptionHandler($e);
            $response = [
                '_response_status' => array_merge([
                    "success" => false,
                    "started" => $this->startTime,
                    "finished" => Carbon::now(),
                ], $handler->convertExceptionToArray())
            ];

            return Response::json($response, $response['_response_status']['code']);
        }

        return Response::json($response, JsonResponse::HTTP_CREATED);
    }

    /**
     * update a specified resource to storage
     * @param Request $request
     * @param $id
     * @return JsonResponse
     * @throws ValidationException
     */

    public function update(Request $request, $id): JsonResponse
    {

        $rankType = RankType::findOrFail($id);

        $validated = $this->rankTypeService->validator($request)->validate();

        try {
            $data = $this->rankTypeService->update($rankType, $validated);

            $response = [
                'data' => $data ? $data : null,
                '_response_status' => [
                    "success" => true,
                    "code" => JsonResponse::HTTP_OK,
                    "message" => "Job finished successfully.",
                    "started" => $this->startTime,
                    "finished" => Carbon::now(),
                ]
            ];

        } catch (Throwable $e) {
            $handler = new CustomExceptionHandler($e);
            $response = [
                '_response_status' => array_merge([
                    "success" => false,
                    "started" => $this->startTime,
                    "finished" => Carbon::now(),
                ], $handler->convertExceptionToArray())
            ];

            return Response::json($response, $response['_response_status']['code']);
        }

        return Response::json($response, JsonResponse::HTTP_CREATED);

    }

    /**
     * Delete the specified resource from the storage
     * @param $id
     * @return JsonResponse
     */

    public function destroy($id): JsonResponse
    {
        $rankType = RankType::findOrFail($id);

        try {
            $this->rankTypeService->destroy($rankType);
            $response = [
                '_response_status' => [
                    "success" => true,
                    "code" => JsonResponse::HTTP_OK,
                    "message" => "Job finished successfully.",
                    "started" => $this->startTime,
                    "finished" => Carbon::now(),
                ]
            ];
        } catch (Throwable $e) {
            $handler = new CustomExceptionHandler($e);
            $response = [
                '_response_status' => array_merge([
                    "success" => false,
                    "started" => $this->startTime,
                    "finished" => Carbon::now(),
                ], $handler->convertExceptionToArray())
            ];

            return Response::json($response, $response['_response_status']['code']);
        }

        return Response::json($response, JsonResponse::HTTP_OK);

    }
}
