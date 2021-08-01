<?php

namespace App\Http\Controllers;

use App\Helpers\Classes\CustomExceptionHandler;
use App\Models\OrganizationUnitService;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Response;
use Illuminate\Validation\ValidationException;
use Throwable;
use App\Services\OrganizationUnitServiceService;
use Illuminate\Http\Request;

/**
 * Class OrganizationUnitServiceController
 * @package App\Http\Controllers
 */
class OrganizationUnitServiceController extends Controller
{
    /**
     * @var OrganizationUnitServiceService
     */
    public OrganizationUnitServiceService $organizationUnitServiceService;

    /**
     * @var Carbon
     */
    private Carbon $startTime;

    /**
     * OrganizationUnitServiceController constructor.
     * @param OrganizationUnitServiceService $organizationUnitServiceService
     */
    public function __construct(OrganizationUnitServiceService $organizationUnitServiceService)
    {
        $this->organizationUnitServiceService = $organizationUnitServiceService;
        $this->startTime = Carbon::now();
    }

    /**
     * Display a listing of the resource.
     * @param Request $request
     * @return JsonResponse
     */
    public function getList(Request $request): JsonResponse
    {
        try {
            $response = $this->organizationUnitServiceService->getOrganizationUnitServiceList($request, $this->startTime);
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
     * Display the specified resource
     * @param Request $request
     * @param $id
     * @return JsonResponse
     */
    public function read(Request $request, int $id): JsonResponse
    {
        try {
            $response = $this->organizationUnitServiceService->getOneOrganizationUnitService($id, $this->startTime);
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
     * Store a newly created resource in storage.
     * @param Request $request
     * @return JsonResponse
     * @throws ValidationException
     */
    function store(Request $request): JsonResponse
    {
        $validated = $this->organizationUnitServiceService->validator($request)->validate();
        try {
            $data = $this->organizationUnitServiceService->store($validated);
            $response = [
                'data' => $data ? : null,
                '_response_status' => [
                    "success" => true,
                    "code" => JsonResponse::HTTP_CREATED,
                    "message" => "Organization Unit Service added successfully",
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
     * update the specified resource in storage
     * @param int $id
     * @return JsonResponse
     */
    public function update(int $id): JsonResponse
    {
        $organizationUnitService = OrganizationUnitService::findOrFail($id);
        try {
            $data = $this->organizationUnitServiceService->update($organizationUnitService);
            $response = [
                'data' => $data ? $data : null,
                '_response_status' => [
                    "success" => true,
                    "code" => JsonResponse::HTTP_OK,
                    "message" => "Organization Unit Service updated successfully.",
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
    public function destroy(int $id): JsonResponse
    {
        $organizationUnitService = OrganizationUnitService::findOrFail($id);
        try {
            $this->organizationUnitServiceService->destroy($organizationUnitService);
            $response = [
                '_response_status' => [
                    "success" => true,
                    "code" => JsonResponse::HTTP_OK,
                    "message" => "Organization Unit Service deleted successfully.",
                    "started" => $this->startTime->format('H i s'),
                    "finished" => Carbon::now()->format('H i s'),
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
