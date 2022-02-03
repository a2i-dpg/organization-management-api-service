<?php

namespace App\Http\Controllers;


use App\Models\AppliedJob;
use App\Models\BaseModel;
use App\Models\InterviewSchedule;
use App\Models\JobManagement;
use App\Services\InterviewScheduleService;
use App\Models\PrimaryJobInformation;
use App\Models\RecruitmentStep;
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
     * @param Request $request
     * @param int $applicationId
     * @return JsonResponse
     * @throws ValidationException
     */
    public function updateInterviewedCandidate(Request $request, int $applicationId): JsonResponse
    {
        $appliedJob = AppliedJob::findOrFail($applicationId);
        $validatedData = $this->jobManagementService->interviewedCandidateUpdateValidator($request)->validate();
        $shortlistApplication = $this->jobManagementService->updateInterviewedCandidate($appliedJob, $validatedData);
        $response = [
            "data" => $shortlistApplication,
            '_response_status' => [
                "success" => true,
                "code" => ResponseAlias::HTTP_OK,
                "message" => "interviewed candidate updated successfully",
                "query_time" => $this->startTime->diffInSeconds(Carbon::now())
            ]
        ];
        return Response::json($response, ResponseAlias::HTTP_OK);

    }

    /**
     * @param int $applicationId
     * @return JsonResponse
     */
    public function removeCandidateToPreviousStep(int $applicationId): JsonResponse
    {
        $appliedJob = AppliedJob::findOrFail($applicationId);

        $shortlistApplication = $this->jobManagementService->removeCandidateToPreviousStep($appliedJob);
        $response = [
            "data" => $shortlistApplication,
            '_response_status' => [
                "success" => true,
                "code" => ResponseAlias::HTTP_OK,
                "message" => "Candidate removed successfully",
                "query_time" => $this->startTime->diffInSeconds(Carbon::now())
            ]
        ];
        return Response::json($response, ResponseAlias::HTTP_OK);


    }

    /**
     * @param int $applicationId
     * @return JsonResponse
     * @throws ValidationException
     */
    public function restoreRejectedCandidate(int $applicationId): JsonResponse
    {
        $appliedJob = AppliedJob::findOrFail($applicationId);

        $shortlistApplication = $this->jobManagementService->restoreRejectedCandidate($appliedJob);
        $response = [
            "data" => $shortlistApplication,
            '_response_status' => [
                "success" => true,
                "code" => ResponseAlias::HTTP_OK,
                "message" => "Candidate restored successfully",
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
    public function createRecruitmentStep(Request $request): JsonResponse
    {
        $validatedData = $this->jobManagementService->recruitmentStepStoreValidator($request)->validate();

        $recruitmentStep = $this->jobManagementService->storeRecruitmentStep($validatedData);
        $response = [
            "data" => $recruitmentStep,
            '_response_status' => [
                "success" => true,
                "code" => ResponseAlias::HTTP_OK,
                "message" => "Recruitment Step stored successful",
                "query_time" => $this->startTime->diffInSeconds(Carbon::now())
            ]
        ];
        return Response::json($response, ResponseAlias::HTTP_OK);

    }

    /**
     * @param int $stepId
     * @return JsonResponse
     */
    public function getRecruitmentStep(int $stepId): JsonResponse
    {
        $recruitmentStep = $this->jobManagementService->getRecruitmentStep($stepId);
        $response = [
            "data" => $recruitmentStep,
            '_response_status' => [
                "success" => true,
                "code" => ResponseAlias::HTTP_OK,
                "query_time" => $this->startTime->diffInSeconds(Carbon::now())
            ]
        ];
        return Response::json($response, ResponseAlias::HTTP_OK);

    }

    /**
     * @throws ValidationException
     */
    public function hireInviteCandidate(Request $request, int $applicationId): JsonResponse
    {
        $appliedJob = AppliedJob::findOrFail($applicationId);

        $validatedData = $this->jobManagementService->hireInviteValidator($request)->validate();
        $hireInvitedCandidate = $this->jobManagementService->hireInviteCandidate($appliedJob, $validatedData);

        $hireInviteType = $hireInvitedCandidate;
        if ($hireInviteType == AppliedJob::INVITE_TYPES['SMS']) {
            //TODO :send sms to hire invitedCandidate
        } else if ($hireInviteType == AppliedJob::INVITE_TYPES['Email']) {
            //TODO :send Email to hire invitedCandidate
        } else if ($hireInviteType == AppliedJob::INVITE_TYPES['SMS and Email']) {
            //TODO :send Email and sms to hire invitedCandidate
        } else {
            //TODO: Other system to hire Invite Candidae
        }
        $response = [
            "data" => $hireInvitedCandidate,
            '_response_status' => [
                "success" => true,
                "code" => ResponseAlias::HTTP_OK,
                "message" => "Candidate  hire  invited successfully",
                "query_time" => $this->startTime->diffInSeconds(Carbon::now())
            ]
        ];
        return Response::json($response, ResponseAlias::HTTP_OK);
    }

    /**
     * @param int $stepId
     * @return JsonResponse
     * @throws Throwable
     */
    public function destroyRecruitmentStep(int $stepId): JsonResponse
    {
        $recruitmentStep = RecruitmentStep::findOrFail($stepId);
        $isRecruitmentStepDeletable = $this->jobManagementService->isRecruitmentStepDeletable($recruitmentStep);

        throw_if(!$isRecruitmentStepDeletable, ValidationException::withMessages([
            "Recruitment Step can not be deleted"
        ]));

        $data = $this->jobManagementService->deleteRecruitmentStep($recruitmentStep);
        $response = [
            "data" => $data,
            '_response_status' => [
                "success" => true,
                "code" => ResponseAlias::HTTP_OK,
                "message" => "Recruitment Step deleted successfully.",
                "query_time" => $this->startTime->diffInSeconds(Carbon::now())
            ]
        ];
        return Response::json($response, ResponseAlias::HTTP_OK);

    }

    /**
     * @param Request $request
     * @param int $stepId
     * @return JsonResponse
     * @throws ValidationException
     */
    public function updateRecruitmentStep(Request $request, int $stepId): JsonResponse
    {
        $recruitmentStep = RecruitmentStep::findOrFail($stepId);
        $validatedData = $this->jobManagementService->recruitmentStepUpdateValidator($request)->validate();
        $recruitmentStep = $this->jobManagementService->updateRecruitmentStep($recruitmentStep, $validatedData);
        $response = [
            "data" => $recruitmentStep,
            '_response_status' => [
                "success" => true,
                "code" => ResponseAlias::HTTP_OK,
                "message" => "Recruitment Step updated successful",
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
     * @param int $id
     * @return JsonResponse
     * @throws AuthorizationException
     */
    function getOneSchedule(int $id): JsonResponse
    {
        $schedule = $this->interviewScheduleService->getOneInterviewSchedule($id);
        $this->authorize('view', $schedule);
        $response = [
            "data" => $schedule,
            "_response_status" => [
                "success" => true,
                "code" => ResponseAlias::HTTP_OK,
                "query_time" => $this->startTime->diffInSeconds(Carbon::now())
            ]
        ];
        return Response::json($response, ResponseAlias::HTTP_OK);
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

        $this->authorize('delete', $schedule);

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

    public function assignCandidates(Request $request, int $id): mixed
    {

        $validated = $this->interviewScheduleService->validatorForCandidateAssigning($request, $id)->validate();

        DB::beginTransaction();
        try {

            $this->interviewScheduleService->assignToSchedule($validated, $id);

            $response = [
//                "data" => $candidateRequirements,
                '_response_status' => [
                    "success" => true,
                    "code" => ResponseAlias::HTTP_OK,
                    "message" => "CandidateRequirements successfully submitted",
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
}
