<?php

namespace App\Http\Controllers;


use App\Services\JobManagementServices\AdditionalJobInformationService;
use App\Services\JobManagementServices\AreaOfBusinessService;
use App\Services\JobManagementServices\CandidateRequirementsService;
use App\Services\JobManagementServices\CompanyInfoVisibilityService;
use App\Services\JobManagementServices\EducationInstitutionsService;
use App\Services\JobManagementServices\PrimaryJobInformationService;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;


class JobManagementController extends Controller
{

    public AreaOfBusinessService $areaOfBusinessService;

    public EducationInstitutionsService $educationInstitutionsService;

    public CandidateRequirementsService $candidateRequirementsService;

    public CompanyInfoVisibilityService $companyInfoVisibilityService;

    public PrimaryJobInformationService $primaryJobInformationService;

    public AdditionalJobInformationService $additionalJobInformationService;

    private Carbon $startTime;


    public function __construct(AreaOfBusinessService $areaOfBusinessService, EducationInstitutionsService $educationInstitutionsService, CandidateRequirementsService $candidateRequirementsService, CompanyInfoVisibilityService $companyInfoVisibilityService, PrimaryJobInformationService $primaryJobInformationService, AdditionalJobInformationService $additionalJobInformationService)
    {
        $this->areaOfBusinessService = $areaOfBusinessService;
        $this->educationInstitutionsService = $educationInstitutionsService;
        $this->candidateRequirementsService = $candidateRequirementsService;
        $this->companyInfoVisibilityService = $companyInfoVisibilityService;
        $this->primaryJobInformationService = $primaryJobInformationService;
        $this->additionalJobInformationService = $additionalJobInformationService;
        $this->startTime = Carbon::now();
    }

    public function getJobList(Request $request): JsonResponse
    {
        $filter = $this->primaryJobInformationService->filterValidatJobList($request)->validate();
        $response = $this->primaryJobInformationService->getJobList($filter, $this->startTime);
        return Response::json($response, ResponseAlias::HTTP_OK);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     * @throws ValidationException
     */
    public function getAreaOfBusiness(Request $request): JsonResponse
    {
        $filter = $this->areaOfBusinessService->filterAreaOfBusinessValidator($request)->validate();
        $response = $this->areaOfBusinessService->getAreaOfBusinessList($filter, $this->startTime);
        return Response::json($response, ResponseAlias::HTTP_OK);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     * @throws ValidationException
     */
    public function getEducationalInstitutions(Request $request): JsonResponse
    {
        $filter = $this->educationInstitutionsService->filterEducationInstitutionValidator($request)->validate();
        $response = $this->educationInstitutionsService->getEducationalInstitutionList($filter, $this->startTime);

        return Response::json($response, ResponseAlias::HTTP_OK);

    }

    public function jobPreview(string $jobId): JsonResponse
    {
        $data = collect([
            'primary_job_information' => $this->primaryJobInformationService->getPrimaryJobInformationDetails($jobId),
            'additional_job_information' => $this->additionalJobInformationService->getAdditionalJobInformationDetails($jobId),
            'candidate_requirement' => $this->candidateRequirementsService->getCandidateRequirements($jobId),
            'company_info_visibility' => $this->companyInfoVisibilityService->getCompanyInfoVisibility($jobId)
        ]);

        $response = [
            "data" => $data,
            '_response_status' => [
                "success" => true,
                "code" => ResponseAlias::HTTP_OK,
                "query_time" => $this->startTime->diffInSeconds(Carbon::now())
            ]
        ];
        return Response::json($response, ResponseAlias::HTTP_OK);

    }


}
