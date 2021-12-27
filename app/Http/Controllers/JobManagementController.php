<?php

namespace App\Http\Controllers;


use App\Models\PrimaryJobInformation;
use App\Services\JobManagementServices\AdditionalJobInformationService;
use App\Services\JobManagementServices\PrimaryJobInformationService;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Response;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;
use Throwable;


class JobManagementController extends Controller
{
    public PrimaryJobInformationService $primaryJobInformationService;
    public AdditionalJobInformationService $additionalJobInformationService;
    public Carbon $startTime;

    /**
     * @param PrimaryJobInformationService $primaryJobInformationService
     * @param AdditionalJobInformationService $additionalJobInformationService
     */
    public function __construct(PrimaryJobInformationService $primaryJobInformationService, AdditionalJobInformationService $additionalJobInformationService)
    {
        $this->primaryJobInformationService = $primaryJobInformationService;
        $this->additionalJobInformationService = $additionalJobInformationService;
        $this->startTime = Carbon::now();

    }

    public function getJobId(): string
    {
        return PrimaryJobInformation::jobId();
    }


    /**
     * @param Request $request
     * @return JsonResponse
     * @throws ValidationException|Throwable
     */
    public function storePrimaryJobInformation(Request $request): JsonResponse
    {

        $validatedData = $this->primaryJobInformationService->validator($request)->validate();
        $employmentTypes = $validatedData['employment_type'];
        DB::beginTransaction();
        try {
            $primaryJobInformation = $this->primaryJobInformationService->store($validatedData);
            $this->primaryJobInformationService->syncWithEmploymentStatus($primaryJobInformation, $employmentTypes);
            $response = [
                "data" => $primaryJobInformation,
                '_response_status' => [
                    "success" => true,
                    "code" => ResponseAlias::HTTP_OK,
                    "message" => "PrimaryJobInformation successfully submitted",
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


    /**
     * @param Request $request
     * @return JsonResponse
     * @throws ValidationException|Throwable
     */
    public function storeAdditionalJobInformation(Request $request): JsonResponse
    {
        $validatedData = $this->additionalJobInformationService->validator($request)->validate();

        $jobLevel = $validatedData['job_level'];
        $workPlace = $validatedData['work_place'];
        $jobLocation = $validatedData['job_location'];

        DB::beginTransaction();
        try {
            $additionalJobInformation = $this->additionalJobInformationService->store($validatedData);
            $this->additionalJobInformationService->syncWithJobLevel($additionalJobInformation, $jobLevel);
            $this->additionalJobInformationService->syncWithWorkplace($additionalJobInformation, $workPlace);
            $this->additionalJobInformationService->syncWithJobLocation($additionalJobInformation, $jobLocation);

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
        $response = [
            "data" => $this->additionalJobInformationService->getJobLocation(),
            '_response_status' => [
                "success" => true,
                "code" => ResponseAlias::HTTP_OK,
                "message" => "Job Location list",
                "query_time" => $this->startTime->diffInSeconds(Carbon::now())
            ]
        ];
        return Response::json($response, ResponseAlias::HTTP_OK);
    }

}
