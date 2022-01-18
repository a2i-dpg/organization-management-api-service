<?php

namespace App\Http\Controllers;


use App\Models\AdditionalJobInformation;
use App\Models\BaseModel;
use App\Models\CandidateRequirement;
use App\Models\CompanyInfoVisibility;
use App\Models\JobContactInformation;
use App\Models\JobManagement;
use App\Models\MatchingCriteria;
use App\Models\PrimaryJobInformation;
use App\Services\JobManagementServices\AdditionalJobInformationService;
use App\Services\JobManagementServices\AreaOfBusinessService;
use App\Services\JobManagementServices\CandidateRequirementsService;
use App\Services\JobManagementServices\CompanyInfoVisibilityService;
use App\Services\JobManagementServices\EducationInstitutionsService;
use App\Services\JobManagementServices\JobContactInformationService;
use App\Services\JobManagementServices\JobManagementService;
use App\Services\JobManagementServices\MatchingCriteriaService;
use App\Services\JobManagementServices\OtherBenefitService;
use App\Services\JobManagementServices\PrimaryJobInformationService;
use Carbon\Carbon;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Response;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;


class JobManagementController extends Controller
{

    /**
     * @var AreaOfBusinessService
     */
    public AreaOfBusinessService $areaOfBusinessService;

    /**
     * @var EducationInstitutionsService
     */
    public EducationInstitutionsService $educationInstitutionsService;

    /**
     * @var CandidateRequirementsService
     */
    public CandidateRequirementsService $candidateRequirementsService;

    /**
     * @var CompanyInfoVisibilityService
     */
    public CompanyInfoVisibilityService $companyInfoVisibilityService;

    /**
     * @var PrimaryJobInformationService
     */
    public PrimaryJobInformationService $primaryJobInformationService;

    /**
     * @var AdditionalJobInformationService
     */
    public AdditionalJobInformationService $additionalJobInformationService;

    /**
     * @var OtherBenefitService
     */
    public OtherBenefitService $otherBenefitService;

    public MatchingCriteriaService $matchingCriteriaService;

    public JobContactInformationService $jobContactInformationService;

    public JobManagementService $jobManagementService;


    private Carbon $startTime;


    public function __construct(
        JobContactInformationService $jobContactInformationService,
        MatchingCriteriaService $matchingCriteriaService,
        AreaOfBusinessService $areaOfBusinessService,
        EducationInstitutionsService $educationInstitutionsService,
        CandidateRequirementsService $candidateRequirementsService,
        CompanyInfoVisibilityService $companyInfoVisibilityService,
        PrimaryJobInformationService $primaryJobInformationService,
        AdditionalJobInformationService $additionalJobInformationService,
        OtherBenefitService $otherBenefitService,
        JobManagementService $jobManagementService
    )
    {
        $this->areaOfBusinessService = $areaOfBusinessService;
        $this->educationInstitutionsService = $educationInstitutionsService;
        $this->candidateRequirementsService = $candidateRequirementsService;
        $this->companyInfoVisibilityService = $companyInfoVisibilityService;
        $this->primaryJobInformationService = $primaryJobInformationService;
        $this->additionalJobInformationService = $additionalJobInformationService;
        $this->otherBenefitService = $otherBenefitService;
        $this->matchingCriteriaService = $matchingCriteriaService;
        $this->jobContactInformationService = $jobContactInformationService;
        $this->jobManagementService = $jobManagementService;
        $this->startTime = Carbon::now();
    }

    /**
     * @throws ValidationException
     * @throws AuthorizationException
     */
    public function getJobList(Request $request): JsonResponse
    {
        $this->authorize('viewAny', JobManagement::class);
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

    /**
     * @param string $jobId
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function jobPreview(string $jobId): JsonResponse
    {
//        $primaryJobInformation = PrimaryJobInformation::where('job_id', $jobId)->firstOrFail();
//        $this->authorize('view', [JobManagement::class, $primaryJobInformation, $primaryJobInformation]);

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

    /**
     * @param string $jobId
     * @return JsonResponse
     */
    public function publicJobDetails(string $jobId): JsonResponse
    {
        $data = collect([
            'primary_job_information' => $this->primaryJobInformationService->getPrimaryJobInformationDetails($jobId),
            'additional_job_information' => $this->additionalJobInformationService->getAdditionalJobInformationDetails($jobId),
            'candidate_requirements' => $this->candidateRequirementsService->getCandidateRequirements($jobId),
            'company_info_visibility' => $this->companyInfoVisibilityService->getCompanyInfoVisibility($jobId)
        ]);
        $response["data"] = $data;
        $response['_response_status']["query_time"] = $this->startTime->diffInSeconds(Carbon::now());

        return Response::json($response, ResponseAlias::HTTP_OK);
    }


    /**
     * @param string $jobId
     * @return JsonResponse
     */
    public function getMatchingCriteria(string $jobId): JsonResponse
    {
        $data = collect([
            'matching_criteria' => $this->matchingCriteriaService->getMatchingCriteria($jobId),
        ]);
        $response["data"] = $data;
        $response['_response_status']["query_time"] = $this->startTime->diffInSeconds(Carbon::now());

        Log::info(json_encode($response));
        return Response::json($response, ResponseAlias::HTTP_OK);
    }

}
