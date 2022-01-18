<?php

namespace App\Http\Controllers;

use App\Models\BaseModel;
use App\Models\JobManagement;
use App\Services\JobManagementServices\AdditionalJobInformationService;
use App\Services\JobManagementServices\JobManagementService;
use Carbon\Carbon;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Response;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;
use Throwable;

class AdditionalJobInfoController extends Controller
{
    public AdditionalJobInformationService $additionalJobInformationService;
    public JobManagementService $jobManagementService;
    public Carbon $startTime;

    /**
     * @param AdditionalJobInformationService $additionalJobInformationService
     * @param JobManagementService $jobManagementService
     */
    public function __construct(AdditionalJobInformationService $additionalJobInformationService, JobManagementService $jobManagementService)
    {
        $this->additionalJobInformationService = $additionalJobInformationService;
        $this->jobManagementService = $jobManagementService;
        $this->startTime = Carbon::now();

    }

    /**
     * @param Request $request
     * @return JsonResponse
     * @throws ValidationException|Throwable
     */
    public function storeAdditionalJobInformation(Request $request): JsonResponse
    {
        $this->authorize('create', JobManagement::class);
        $validatedData = $this->additionalJobInformationService->validator($request)->validate();

        $jobLevel = $validatedData['job_level'];
        $workPlace = $validatedData['work_place'];
        $jobLocation = $validatedData['job_location'];
        $otherBenefit = $validatedData['other_benefits'];

        DB::beginTransaction();
        try {
            $additionalJobInformation = $this->additionalJobInformationService->store($validatedData);
            $this->additionalJobInformationService->syncWithJobLevel($additionalJobInformation, $jobLevel);
            $this->additionalJobInformationService->syncWithWorkplace($additionalJobInformation, $workPlace);
            $this->additionalJobInformationService->syncWithJobLocation($additionalJobInformation, $jobLocation);
            $this->additionalJobInformationService->syncWithOtherBenefit($additionalJobInformation, $otherBenefit);

            $response = [
                "data" => $additionalJobInformation,
                '_response_status' => [
                    "success" => true,
                    "code" => ResponseAlias::HTTP_OK,
                    "message" => "AdditionalJobInformation successfully submitted",
                    "query_time" => $this->startTime->diffInSeconds(Carbon::now())
                ]
            ];
            DB::commit();
        } catch (Throwable $exception) {
            DB::rollBack();
            throw $exception;
        }
        return Response::json($response, ResponseAlias::HTTP_OK);
    }

    public function jobLocation(): JsonResponse
    {
        $jobLocations = array_values($this->additionalJobInformationService->getJobLocation());
        $response = [
            "data" => $jobLocations,
            '_response_status' => [
                "success" => true,
                "code" => ResponseAlias::HTTP_OK,
                "message" => "Job Location list",
                "query_time" => $this->startTime->diffInSeconds(Carbon::now())
            ]
        ];
        return Response::json($response, ResponseAlias::HTTP_OK);
    }

    /**
     * @param string $jobId
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function getAdditionalJobInformation(string $jobId): JsonResponse
    {

        $this->authorize('view', JobManagement::class);

        $step = $this->jobManagementService->lastAvailableStep($jobId);
        $response = [
            "data" => [
                "latest_step" => $step
            ],
            '_response_status' => [
                "success" => true,
                "code" => ResponseAlias::HTTP_OK,
                "query_time" => $this->startTime->diffInSeconds(Carbon::now())
            ]
        ];
        if ($step >= BaseModel::FORM_STEPS['AdditionalJobInformation']) {
            $additionalJobInformation = $this->additionalJobInformationService->getAdditionalJobInformationDetails($jobId);
            $additionalJobInformation["latest_step"] = $step;
            $response["data"] = $additionalJobInformation;
            $response['_response_status']["query_time"] = $this->startTime->diffInSeconds(Carbon::now());
        }
        return Response::json($response, ResponseAlias::HTTP_OK);

    }
}
