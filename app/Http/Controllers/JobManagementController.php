<?php

namespace App\Http\Controllers;


use App\Models\AppliedJob;
use App\Models\BaseModel;
use App\Models\InterviewSchedule;
use App\Models\JobManagement;
use App\Services\InterviewScheduleService;
use App\Services\JobManagementServices\AdditionalJobInformationService;
use App\Services\JobManagementServices\AreaOfBusinessService;
use App\Services\JobManagementServices\AreaOfExperienceService;
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
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Response;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;
use Throwable;


class JobManagementController extends Controller
{

    /**
     * @var AreaOfBusinessService
     */
    public AreaOfBusinessService $areaOfBusinessService;

    /**
     * @var AreaOfExperienceService
     */
    public AreaOfExperienceService $areaOfExperienceService;

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
    private InterviewScheduleService $interviewScheduleService;


    public function __construct(
        JobContactInformationService    $jobContactInformationService,
        MatchingCriteriaService         $matchingCriteriaService,
        AreaOfBusinessService           $areaOfBusinessService,
        EducationInstitutionsService    $educationInstitutionsService,
        CandidateRequirementsService    $candidateRequirementsService,
        CompanyInfoVisibilityService    $companyInfoVisibilityService,
        PrimaryJobInformationService    $primaryJobInformationService,
        AdditionalJobInformationService $additionalJobInformationService,
        OtherBenefitService             $otherBenefitService,
        JobManagementService            $jobManagementService,
        AreaOfExperienceService         $areaOfExperienceService,
        InterviewScheduleService        $interviewScheduleService
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
        $this->areaOfExperienceService = $areaOfExperienceService;
        $this->interviewScheduleService = $interviewScheduleService;
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
    public function getAreaOfExperience(Request $request): JsonResponse
    {
        $filter = $this->areaOfExperienceService->filterAreaOfExperienceValidator($request)->validate();
        $response = $this->areaOfExperienceService->getAreaOfExperienceList($filter, $this->startTime);
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

    /**
     * @param Request $request
     * @return JsonResponse
     * @throws ValidationException|Throwable
     */
    public function applyToJob(Request $request): JsonResponse
    {
        $validatedData = $this->jobManagementService->applyJobValidator($request)->validate();
        DB::beginTransaction();
        try {
            $appliedJobData = $this->jobManagementService->storeAppliedJob($validatedData);
            $response = [
                "data" => $appliedJobData,
                '_response_status' => [
                    "success" => true,
                    "code" => ResponseAlias::HTTP_OK,
                    "message" => "Job apply successful",
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
     * @param int $applicationId
     * @return JsonResponse
     */
    public function rejectCandidate(int $applicationId): JsonResponse
    {
        $rejectApplication = $this->jobManagementService->rejectCandidate($applicationId);
        $response = [
            "data" => $rejectApplication,
            '_response_status' => [
                "success" => true,
                "code" => ResponseAlias::HTTP_OK,
                "message" => "Candidate rejected successfully",
                "query_time" => $this->startTime->diffInSeconds(Carbon::now())
            ]
        ];
        return Response::json($response, ResponseAlias::HTTP_OK);

    }

    /**
     * @param int $applicationId
     * @return JsonResponse
     * @throws Throwable
     */
    public function shortlistCandidate(int $applicationId): JsonResponse
    {
        $shortlistApplication = $this->jobManagementService->shortlistCandidate($applicationId);
        $response = [
            "data" => $shortlistApplication,
            '_response_status' => [
                "success" => true,
                "code" => ResponseAlias::HTTP_OK,
                "message" => "Candidate shortlisted successfully",
                "query_time" => $this->startTime->diffInSeconds(Carbon::now())
            ]
        ];
        return Response::json($response, ResponseAlias::HTTP_OK);

    }
    /**
     * @param int $applicationId
     * @return JsonResponse
     * @throws Throwable
     */
    public function inviteCandidateForInterview(int $applicationId): JsonResponse
    {
        $shortlistApplication = $this->jobManagementService->inviteCandidateForInterview($applicationId);
        $response = [
            "data" => $shortlistApplication,
            '_response_status' => [
                "success" => true,
                "code" => ResponseAlias::HTTP_OK,
                "message" => "Job apply successful",
                "query_time" => $this->startTime->diffInSeconds(Carbon::now())
            ]
        ];
        return Response::json($response, ResponseAlias::HTTP_OK);

    }

    public function getCandidateList(Request $request, string $jobId, int $status = 0): JsonResponse
    {
        $response = $this->jobManagementService->getCandidateList($request, $jobId, $status);

        $response['_response_status'] = [
            "success" => true,
            "code" => ResponseAlias::HTTP_OK,
            "query_time" => $this->startTime->diffInSeconds(Carbon::now()),
        ];

        return Response::json($response, ResponseAlias::HTTP_OK);
    }

    public function getAllCandidateList(Request $request, string $jobId): JsonResponse
    {
        return $this->getCandidateList($request, $jobId);
    }

    public function getAppliedCandidateList(Request $request, string $jobId): JsonResponse
    {
        return $this->getCandidateList($request, $jobId, AppliedJob::APPLY_STATUS["Applied"]);
    }

    public function getRejectedCandidateList(Request $request, string $jobId): JsonResponse
    {
        return $this->getCandidateList($request, $jobId, AppliedJob::APPLY_STATUS["Rejected"]);
    }

    public function getShortlistedCandidateList(Request $request, string $jobId): JsonResponse
    {
        return $this->getCandidateList($request, $jobId, AppliedJob::APPLY_STATUS["Shortlisted"]);
    }

    public function getInterviewInvitedCandidateList(Request $request, string $jobId): JsonResponse
    {
        return $this->getCandidateList($request, $jobId, AppliedJob::APPLY_STATUS["Interview_invited"]);
    }

    public function getInterviewedCandidateList(Request $request, string $jobId): JsonResponse
    {
        return $this->getCandidateList($request, $jobId, AppliedJob::APPLY_STATUS["Interviewed"]);
    }

    public function getHireInvitedCandidateList(Request $request, string $jobId): JsonResponse
    {
        return $this->getCandidateList($request, $jobId, AppliedJob::APPLY_STATUS["Hire_invited"]);
    }

    public function getHiredCandidateList(Request $request, string $jobId): JsonResponse
    {
        return $this->getCandidateList($request, $jobId, AppliedJob::APPLY_STATUS["Hired"]);
    }



    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return JsonResponse
     * @throws AuthorizationException
     * @throws ValidationException
     */

    function createSchedule(Request $request): JsonResponse
    {
        $this->authorize('create', InterviewSchedule::class);

        $validated = $this->interviewScheduleService->validator($request)->validate();
        $data = $this->interviewScheduleService->store($validated);
        $response = [
            'data' => $data,
            '_response_status' => [
                "success" => true,
                "code" => ResponseAlias::HTTP_CREATED,
                "message" => "interview schedule created successfully.",
                "query_time" => $this->startTime->diffInSeconds(Carbon::now())
            ]
        ];
        return Response::json($response, ResponseAlias::HTTP_CREATED);
    }

    /**
     * @param Request $request
     * @param int $id
     * @return JsonResponse
     * @throws AuthorizationException
     * @throws ValidationException
     */
    public function updateSchedule(Request $request, int $id): JsonResponse
    {
        $schedule = InterviewSchedule::findOrFail($id);

        $this->authorize('update', $schedule);

        $validated = $this->interviewScheduleService->validator($request, $id)->validate();

        $data = $this->interviewScheduleService->update($schedule, $validated);

        $response = [
            'data' => $data,
            '_response_status' => [
                "success" => true,
                "code" => ResponseAlias::HTTP_OK,
                "message" => "schedule updated successfully.",
                "query_time" => $this->startTime->diffInSeconds(Carbon::now())
            ]
        ];
        return Response::json($response, ResponseAlias::HTTP_CREATED);
    }


    /**
     * @param int $id
     * @return JsonResponse
     * @throws AuthorizationException
     * @throws Throwable
     */
    public function destroySchedule(int $id): JsonResponse
    {
        $schedule = InterviewSchedule::findOrFail($id);

//        $this->authorize('delete', $schedule);

        DB::beginTransaction();
        try {
            $this->interviewScheduleService->destroy($schedule);

            DB::commit();
            $response = [
                '_response_status' => [
                    "success" => true,
                    "code" => ResponseAlias::HTTP_OK,
                    "message" => "schedule deleted successfully.",
                    "query_time" => $this->startTime->diffInSeconds(Carbon::now())
                ]
            ];
        } catch (Throwable $e) {
            DB::rollBack();
            throw $e;
        }

        return Response::json($response, ResponseAlias::HTTP_OK);
    }
}
