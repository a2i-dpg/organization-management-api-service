<?php


namespace App\Http\Controllers;

use App\Models\Rank;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Throwable;
use App\Helpers\Classes\CustomExceptionHandler;
use App\Services\RankService;


class RankController extends Controller
{

    public RankService $rankService;
    private Carbon $startTime;

    public function __construct(RankService $rankService)
    {
        $this->startTime = Carbon::now();
        $this->rankService = $rankService;

    }

    public function getList(Request $request): JsonResponse
    {
        try {
            $response = $this->rankService->getRankList($request);
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

    public function read(Request $request, $id): JsonResponse
    {
        try {
            $response = $this->rankService->getOneRank($id);
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

    function store(Request $request): JsonResponse
    {
        $validated = $this->rankService->validator($request)->validate();
        try {
            //TODO: Only Validated data will stored.
            $this->rankService->store($validated);

            //TODO: never response in try block if not necessary.
            $response = [
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
    public function update(Request $request,$id)
    {

        $rankType= Rank::findOrFail($id);

        $validated = $this->rankService->validator($request)->validate();

        try {
            $this->rankService->update($rankType, $validated);

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

        return Response::json($response, JsonResponse::HTTP_CREATED);

    }
    public function destroy($id)
    {
        $rankType =Rank::findOrFail($id);

        try {
            $this->rankService->destroy($rankType);
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


