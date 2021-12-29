<?php

namespace App\Http\Controllers;

use App\Services\JobManagementServices\JobContactInformationService;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;

class JobContactInformationController extends Controller
{

    public JobContactInformationService $jobContactInformationService;
    public Carbon $startTime;

    /**
     * @param JobContactInformationService $jobContactInformationService
     */
    public function __construct(JobContactInformationService $jobContactInformationService)
    {
        $this->jobContactInformationService = $jobContactInformationService;
        $this->startTime = Carbon::now();
    }


    /**
     * @param string $jobId
     * @return JsonResponse
     */
    public function getContactInformation(string $jobId): JsonResponse
    {
        $jobInformation = $this->jobContactInformationService->getContactInformation($jobId);
        $response = [
            "data" => $jobInformation,
            '_response_status' => [
                "success" => true,
                "code" => ResponseAlias::HTTP_OK,
                "query_time" => $this->startTime->diffInSeconds(Carbon::now())
            ]
        ];
        return Response::json($response, ResponseAlias::HTTP_OK);

    }


    /**
     * @param Request $request
     * @return JsonResponse
     * @throws ValidationException
     */
    public function storeContactInformation(Request $request): JsonResponse
    {
        $validatedData = $this->jobContactInformationService->validate($request)->validate();
        $jobInformation = $this->jobContactInformationService->store($validatedData);
        $response = [
            "data" => $jobInformation,
            '_response_status' => [
                "success" => true,
                "code" => ResponseAlias::HTTP_OK,
                "query_time" => $this->startTime->diffInSeconds(Carbon::now())
            ]
        ];
        return Response::json($response, ResponseAlias::HTTP_OK);
    }
}
