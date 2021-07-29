<?php

namespace App\Http\Controllers;

use App\Helpers\Classes\CustomExceptionHandler;
use App\Models\HumanResourceTemplate;
use App\Services\HumanResourceTemplateService;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Throwable;


/**
 * Class HumanResourceTemplateController
 * @package App\Http\Controllers
 */
class HumanResourceTemplateController extends Controller
{
    /**
     * @var HumanResourceTemplateService
     */
    public HumanResourceTemplateService $humanResourceTemplateService;
    /**
     * @var Carbon
     */
    private Carbon $startTime;

    /**
     * HumanResourceTemplateController constructor.
     * @param HumanResourceTemplateService $humanResourceTemplateService
     */
    public function __construct(HumanResourceTemplateService $humanResourceTemplateService)
    {
        $this->humanResourceTemplateService = $humanResourceTemplateService;
        $this->startTime = Carbon::now();
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function getList(Request $request): JsonResponse
    {
        try {
            $response = $this->humanResourceTemplateService->getHumanResourceTemplateList($request, $this->startTime);
        } catch (Throwable $e) {
            $handler = new CustomExceptionHandler($e);
            $response = [
                '_response_status' => array_merge([
                    "success" => false,
                    "started" => $this->startTime->format('H i s'),
                    "finished" => Carbon::now()->format('H i s'),
                ], $handler->convertExceptionToArray())
            ];
            return Response::json($response, $response['_response_status']['code']);
        }

        return Response::json($response);
    }

    /**
     * @param Request $request
     * @param $id
     * @return JsonResponse
     */
    public function read(Request $request, $id): JsonResponse
    {
        try {
            $response = $this->humanResourceTemplateService->getOneHumanResourceTemplate($id,$this->startTime);
        } catch (Throwable $e) {
            $handler = new CustomExceptionHandler($e);
            $response = [
                '_response_status' => array_merge([
                    "success" => false,
                    "started" => $this->startTime->format('H i s'),
                    "finished" => Carbon::now()->format('H i s'),
                ], $handler->convertExceptionToArray())
            ];
            return Response::json($response, $response['_response_status']['code']);
        }
        return Response::json($response);

    }


    function store(Request $request): JsonResponse
    {
        $validatedData = $this->humanResourceTemplateService->validator($request)->validate();
        try {
            $data = $this->humanResourceTemplateService->store($validatedData);

            $response = [
                'data' => $data ?: null,
                '_response_status' => [
                    "success" => true,
                    "code" => JsonResponse::HTTP_CREATED,
                    "message" => "Human Resource Template added successfully",
                    "started" => $this->startTime->format('H i s'),
                    "finished" => Carbon::now()->format('H i s'),
                ]
            ];
        } catch (Throwable $e) {
            $handler = new CustomExceptionHandler($e);
            $response = [
                '_response_status' => array_merge([
                    "success" => false,
                    "started" => $this->startTime->format('H i s'),
                    "finished" => Carbon::now()->format('H i s'),
                ], $handler->convertExceptionToArray())
            ];

            return Response::json($response, $response['_response_status']['code']);
        }

        return Response::json($response, JsonResponse::HTTP_CREATED);
    }

    /**
     * @param Request $request
     * @param int $id
     * @return JsonResponse
     * @throws \Illuminate\Validation\ValidationException
     */
    public function update(Request $request, int $id): JsonResponse
    {

        $humanResourceTemplate = HumanResourceTemplate::findOrFail($id);

        $validated = $this->humanResourceTemplateService->validator($request)->validate();

        try {
            $data = $this->humanResourceTemplateService->update($humanResourceTemplate, $validated);

            $response = [
                'data' => $data ?: null,
                '_response_status' => [
                    "success" => true,
                    "code" => JsonResponse::HTTP_OK,
                    "message" => "Human Resource Template updated successfully.",
                    "started" => $this->startTime->format('H i s'),
                    "finished" => Carbon::now()->format('H i s'),
                ]
            ];

        } catch (Throwable $e) {
            $handler = new CustomExceptionHandler($e);
            $response = [
                '_response_status' => array_merge([
                    "success" => false,
                    "started" => $this->startTime->format('H i s'),
                    "finished" => Carbon::now()->format('H i s'),
                ], $handler->convertExceptionToArray())
            ];

            return Response::json($response, $response['_response_status']['code']);
        }

        return Response::json($response, JsonResponse::HTTP_CREATED);

    }

    /**
     *  remove the specified resource from storage

     * @param $id
     * @return JsonResponse
     */
    public function destroy($id): JsonResponse
    {
        $humanResourceTemplate = HumanResourceTemplate::findOrFail($id);

        try {
            $this->humanResourceTemplateService->destroy($humanResourceTemplate);
            $response = [
                '_response_status' => [
                    "success" => true,
                    "code" => JsonResponse::HTTP_OK,
                    "message" => "HumanResource Template delete successfully.",
                    "started" => $this->startTime->format('H i s'),
                    "finished" => Carbon::now()->format('H i s'),
                ]
            ];
        } catch (Throwable $e) {
            $handler = new CustomExceptionHandler($e);
            $response = [
                '_response_status' => array_merge([
                    "success" => false,
                    "started" => $this->startTime->format('H i s'),
                    "finished" => Carbon::now()->format('H i s'),
                ], $handler->convertExceptionToArray())
            ];

            return Response::json($response, $response['_response_status']['code']);
        }

        return Response::json($response, JsonResponse::HTTP_OK);

    }

}
