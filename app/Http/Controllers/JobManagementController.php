<?php

namespace App\Http\Controllers;


use App\Models\AdditionalJobInformation;
use App\Models\BaseModel;
use App\Models\CandidateRequirement;
use App\Models\CompanyInfoVisibility;
use App\Models\JobContactInformation;
use App\Models\MatchingCriteria;
use App\Models\PrimaryJobInformation;
use App\Services\JobManagementServices\AdditionalJobInformationService;
use App\Services\JobManagementServices\AreaOfBusinessService;
use App\Services\JobManagementServices\CandidateRequirementsService;
use App\Services\JobManagementServices\CompanyInfoVisibilityService;
use App\Services\JobManagementServices\EducationInstitutionsService;
use App\Services\JobManagementServices\OtherBenefitService;
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

    public OtherBenefitService $otherBenefitService;


    private Carbon $startTime;


    public function __construct(AreaOfBusinessService $areaOfBusinessService, EducationInstitutionsService $educationInstitutionsService, CandidateRequirementsService $candidateRequirementsService, CompanyInfoVisibilityService $companyInfoVisibilityService, PrimaryJobInformationService $primaryJobInformationService, AdditionalJobInformationService $additionalJobInformationService, OtherBenefitService $otherBenefitService)
    {
        $this->areaOfBusinessService = $areaOfBusinessService;
        $this->educationInstitutionsService = $educationInstitutionsService;
        $this->candidateRequirementsService = $candidateRequirementsService;
        $this->companyInfoVisibilityService = $companyInfoVisibilityService;
        $this->primaryJobInformationService = $primaryJobInformationService;
        $this->additionalJobInformationService = $additionalJobInformationService;
        $this->otherBenefitService = $otherBenefitService;
        $this->startTime = Carbon::now();
    }

    /**
     * @throws ValidationException
     */
    public function getJobList(Request $request): JsonResponse
    {
        $filter = $this->primaryJobInformationService->JobListFilterValidator($request)->validate();
        $returnedData = $this->primaryJobInformationService->getJobList($filter, $this->startTime);

        $response = [
            'order' => $returnedData['order'],
            'data' => $returnedData['data'],
            '_response_status' => [
                "success" => true,
                "code" => ResponseAlias::HTTP_OK,
                'query_time' => $returnedData['query_time']
            ]
        ];
        if (isset($returnedData['total_page'])) {
            $response['total'] = $returnedData['total'];
            $response['current_page'] = $returnedData['current_page'];
            $response['total_page'] = $returnedData['total_page'];
            $response['page_size'] = $returnedData['page_size'];
        }
        return Response::json($response, ResponseAlias::HTTP_OK);
    }


    public function getPublicJobList(Request $request): JsonResponse
    {
        $filter = $this->primaryJobInformationService->JobListFilterValidator($request)->validate();
        $filter[BaseModel::IS_CLIENT_SITE_RESPONSE_KEY] = BaseModel::IS_CLIENT_SITE_RESPONSE_FLAG;
        $returnedData = $this->primaryJobInformationService->getJobList($filter, $this->startTime);

        $response = [
            'order' => $returnedData['order'],
            'data' => $returnedData['data'],
            '_response_status' => [
                "success" => true,
                "code" => ResponseAlias::HTTP_OK,
                'query_time' => $returnedData['query_time']
            ]
        ];
        if (isset($returnedData['total_page'])) {
            $response['total'] = $returnedData['total'];
            $response['current_page'] = $returnedData['current_page'];
            $response['total_page'] = $returnedData['total_page'];
            $response['page_size'] = $returnedData['page_size'];
        }
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

    /* @param Request $request
     * @return JsonResponse
     * @throws ValidationException
     */
    public function getOtherBenefits(Request $request): JsonResponse
    {
        $filter = $this->otherBenefitService->filterValidator($request)->validate();
        $response = $this->otherBenefitService->getOtherBenefitList($filter, $this->startTime);
        return Response::json($response, ResponseAlias::HTTP_OK);
    }

    public function jobPreview(string $jobId): JsonResponse
    {
        $step = JobManagementController::lastAvailableStep($jobId);
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
        if ($step >= BaseModel::FORM_STEPS['JobPreview']) {
            $data = collect([
                'primary_job_information' => $this->primaryJobInformationService->getPrimaryJobInformationDetails($jobId),
                'additional_job_information' => $this->additionalJobInformationService->getAdditionalJobInformationDetails($jobId),
                'candidate_requirements' => $this->candidateRequirementsService->getCandidateRequirements($jobId),
                'company_info_visibility' => $this->companyInfoVisibilityService->getCompanyInfoVisibility($jobId)
            ]);
            $data["latest_step"] = $step;
            $response["data"] = $data;
            $response['_response_status']["query_time"] = $this->startTime->diffInSeconds(Carbon::now());
        }
        return Response::json($response, ResponseAlias::HTTP_OK);

    }

    public static function lastAvailableStep(string $jobId): int
    {
        return PrimaryJobInformationService::lastAvailableStep($jobId);
    }


}
