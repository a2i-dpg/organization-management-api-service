<?php

namespace App\Http\Controllers;


use App\Models\CompanyInfoVisibility;
use App\Models\AdditionalJobInformation;
use App\Models\PrimaryJobInformation;
use App\Services\JobManagementServices\AdditionalJobInformationService;
use App\Services\JobManagementServices\CompanyInfoVisibilityService;
use App\Services\JobManagementServices\PrimaryJobInformationService;
use App\Services\JobManagementServices\CandidateRequirementsService;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Response;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;
use Throwable;
use function Symfony\Component\Translation\t;


class JobManagementController extends Controller
{
    public PrimaryJobInformationService $primaryJobInformationService;
    public AdditionalJobInformationService $additionalJobInformationService;
    public CandidateRequirementsService $candidateRequirementsService;
    public CompanyInfoVisibilityService $companyInfoVisibilityService;
    public Carbon $startTime;

    /**
     * @param PrimaryJobInformationService $primaryJobInformationService
     * @param AdditionalJobInformationService $additionalJobInformationService
     * @param CompanyInfoVisibilityService $companyInfoVisibilityService
     */
    public function __construct(PrimaryJobInformationService $primaryJobInformationService, AdditionalJobInformationService $additionalJobInformationService,CompanyInfoVisibilityService $companyInfoVisibilityService)
    {
        $this->primaryJobInformationService = $primaryJobInformationService;
        $this->additionalJobInformationService = $additionalJobInformationService;
        $this->companyInfoVisibilityService = $companyInfoVisibilityService;
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


    /**
     * @param Request $request
     * @return JsonResponse
     * @throws ValidationException|Throwable
     */
    public function storeCandidateRequirements(Request $request): JsonResponse
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


    /**
     * @param Request $request
     * @return JsonResponse
     * @throws ValidationException
     */
    public function storeCompanyInfoVisibility(Request $request): JsonResponse
    {
        $validatedData = $this->companyInfoVisibilityService->companyInfoVisibilityValidator($request)->validate();
        $companyInfoVisibility = $this->companyInfoVisibilityService->storeOrUpdate($validatedData);
        $response = [
            "data" => $companyInfoVisibility,
            '_response_status' => [
                "success" => true,
                "code" => ResponseAlias::HTTP_OK,
                "message" => "Company Info Visibility successfully submitted",
                "query_time" => $this->startTime->diffInSeconds(Carbon::now())
            ]
        ];
        return Response::json($response, ResponseAlias::HTTP_OK);

    }

    /**
     * @param string $jobId
     * @return JsonResponse
     */
    public function getCompanyInfoVisibility(string $jobId): JsonResponse
    {
        $companyInfoVisibility = $this->companyInfoVisibilityService->getCompanyInfoVisibility($jobId);
        $response = [
            "data" => $companyInfoVisibility,
            '_response_status' => [
                "success" => true,
                "code" => ResponseAlias::HTTP_OK,
                "query_time" => $this->startTime->diffInSeconds(Carbon::now())
            ]
        ];
        return Response::json($response, ResponseAlias::HTTP_OK);

    }

    /**
     * @param string $jobId
     * @return JsonResponse
     */
    public function getPrimaryJobInformation(string $jobId): JsonResponse
    {
        $primaryJobInformation = $this->primaryJobInformationService->getPrimaryJobInformationDetails($jobId);
        $response = [
            "data" => $primaryJobInformation,
            '_response_status' => [
                "success" => true,
                "code" => ResponseAlias::HTTP_OK,
                "query_time" => $this->startTime->diffInSeconds(Carbon::now())
            ]
        ];
        return Response::json($response, ResponseAlias::HTTP_OK);

    }

    /**
     * @param string $jobId
     * @return JsonResponse
     */
    public function getAdditionalJobInformation(string $jobId): JsonResponse
    {
        $additionalJobInformation = $this->additionalJobInformationService->getAdditionalJobInformationDetails($jobId);
        $response = [
            "data" => $additionalJobInformation,
            '_response_status' => [
                "success" => true,
                "code" => ResponseAlias::HTTP_OK,
                "query_time" => $this->startTime->diffInSeconds(Carbon::now())
            ]
        ];
        return Response::json($response, ResponseAlias::HTTP_OK);

    }

}
