<?php

namespace App\Http\Controllers;


use App\Facade\ServiceToServiceCall;
use App\Models\AppliedJob;
use App\Models\BaseModel;
use App\Models\CandidateInterview;
use App\Models\InterviewSchedule;
use App\Models\JobManagement;
use App\Services\JobManagementServices\InterviewScheduleService;
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
        $filter = $this->jobManagementService->jobListFilterValidator($request)->validate();
        $returnedData = $this->jobManagementService->getJobList($filter, $this->startTime);

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
     * @throws ValidationException
     */
    public function getPublicJobList(Request $request): JsonResponse
    {
        $filter = $this->jobManagementService->jobListFilterValidator($request)->validate();
        $filter[BaseModel::IS_CLIENT_SITE_RESPONSE_KEY] = BaseModel::IS_CLIENT_SITE_RESPONSE_FLAG;
        $returnedData = $this->jobManagementService->getJobList($filter, $this->startTime);

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
            $primaryJobInformation = $this->primaryJobInformationService->getPrimaryJobInformationDetails($jobId);

            if ($primaryJobInformation->published_at == null) {
                $jobStatus = PrimaryJobInformation::JOB_STATUS_PENDING;
            } elseif ($primaryJobInformation->application_deadline <= Carbon::now()) {
                $jobStatus = PrimaryJobInformation::JOB_STATUS_EXPIRED;
            } else {
                $jobStatus = PrimaryJobInformation::JOB_STATUS_LIVE;

            }
            $data = collect([
                'primary_job_information' => $primaryJobInformation,
                'additional_job_information' => $this->additionalJobInformationService->getAdditionalJobInformationDetails($jobId),
                'candidate_requirements' => $this->candidateRequirementsService->getCandidateRequirements($jobId),
                'company_info_visibility' => $this->companyInfoVisibilityService->getCompanyInfoVisibility($jobId),
                'job_status' => $jobStatus
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
     * @throws ValidationException
     */
    public function publicJobDetails(Request $request, string $jobId): JsonResponse
    {
        $validatedData = $this->jobManagementService->jobDetailsFilterValidator($request)->validate();
        $data = collect([
            'candidate_information' => $this->jobManagementService->getJobCandidateAppliedDetails($validatedData, $jobId),
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
        $this->authorize('view', JobManagement::class);
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
     * @param Request $request
     * @return JsonResponse
     * @throws ValidationException|Throwable
     */
    public function respondToJob(Request $request): JsonResponse
    {
        $validatedData = $this->jobManagementService->respondJobValidator($request)->validate();
        $respondData = $this->jobManagementService->updateAppliedJobRespond($validatedData);

        $response = [
            "data" => $respondData,
            '_response_status' => [
                "success" => true,
                "code" => ResponseAlias::HTTP_OK,
                "message" => "Job apply successful",
                "query_time" => $this->startTime->diffInSeconds(Carbon::now())
            ]
        ];
        return Response::json($response, ResponseAlias::HTTP_OK);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     * @throws ValidationException|Throwable
     */
    public function youthJobs(Request $request): JsonResponse
    {
        $validatedData = $this->jobManagementService->youthJobsValidator($request)->validate();
        $validatedData = $this->jobManagementService->jobListFilterValidator($request)->validate();
        $validatedData["youth_only"] = "1";
        $youthJobs = $this->jobManagementService->getJobList($validatedData, Carbon::now());

        $response = [
            "data" => $youthJobs,
            '_response_status' => [
                "success" => true,
                "code" => ResponseAlias::HTTP_OK,
                "message" => "My jobs list get successful",
                "query_time" => $this->startTime->diffInSeconds(Carbon::now())
            ]
        ];
        return Response::json($response, ResponseAlias::HTTP_OK);
    }

    /**
     * @param int $applicationId
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function rejectCandidate(int $applicationId): JsonResponse
    {
        $this->authorize('update', JobManagement::class);
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
        $this->authorize('update', JobManagement::class);
        $shortlistApplication = $this->jobManagementService->shortlistCandidate($applicationId);
        if (empty($shortlistApplication)) {
            $success = false;
            $message = "Candidate can not be shortlisted ";
            $code = ResponseAlias::HTTP_BAD_REQUEST;
        } else {
            $success = true;
            $message = "Candidate shortlisted successfully";
            $code = ResponseAlias::HTTP_OK;
        }
        $response = [
            '_response_status' => [
                "success" => $success,
                "code" => $code,
                "message" => $message,
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
     * @throws AuthorizationException
     */
    public function updateInterviewedCandidate(Request $request, int $applicationId): JsonResponse
    {
        $this->authorize('update', JobManagement::class);
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
     * @throws AuthorizationException
     */
    public function removeCandidateToPreviousStep(int $applicationId): JsonResponse
    {
        $this->authorize('update', JobManagement::class);
        $appliedJob = AppliedJob::findOrFail($applicationId);

        $removedApplication = $this->jobManagementService->removeCandidateToPreviousStep($appliedJob);
        if($removedApplication){
            $success =true;
            $message ="Candidate removed successfully";
            $code = ResponseAlias::HTTP_OK;
        }else{
            $success =false;
            $message ="Candidate can not be removed ";
            $code = ResponseAlias::HTTP_BAD_REQUEST;
        }
        $response = [
            '_response_status' => [
                "success" => $success,
                "code" => $code,
                "message" => $message,
                "query_time" => $this->startTime->diffInSeconds(Carbon::now())
            ]
        ];
        return Response::json($response, ResponseAlias::HTTP_OK);

    }

    /**
     * @param int $applicationId
     * @return JsonResponse
     * @throws ValidationException
     * @throws AuthorizationException
     */
    public function restoreRejectedCandidate(int $applicationId): JsonResponse
    {
        $this->authorize('update', JobManagement::class);
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
     * @throws Throwable
     */
    public function getCandidateProfile(Request $request, int $applicationId): JsonResponse
    {
        $this->authorize('update', JobManagement::class);
        $appliedJob = AppliedJob::findOrFail($applicationId);

        // $requestData = ['applied_job_id' => $applicationId];
        // $validatedData = $this->jobManagementService->getCandidateProfileValidator($requestData)->validate();

        $youthId = (array)($appliedJob->youth_id);
        $youthProfiles = ServiceToServiceCall::getYouthProfilesByIds($youthId);
        $youth = $youthProfiles[0];

        $appliedJob->profile_viewed_at = Carbon::now();
        $appliedJob->save();

        $response = [
            "data" => $youth,
            '_response_status' => [
                "success" => true,
                "code" => ResponseAlias::HTTP_OK,
                "message" => "Candidate profile get successful",
                "query_time" => $this->startTime->diffInSeconds(Carbon::now())
            ]
        ];

        return Response::json($response, ResponseAlias::HTTP_OK);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     * @throws ValidationException
     * @throws AuthorizationException
     */
    public function createRecruitmentStep(Request $request): JsonResponse
    {
        $this->authorize('update', JobManagement::class);
        $jobId = $request->input('job_id');
        $isRecruitmentStepCreatable = $this->jobManagementService->isRecruitmentStepCreatable($jobId);

        if ($isRecruitmentStepCreatable) {
            $validatedData = $this->jobManagementService->recruitmentStepStoreValidator($request)->validate();
            $recruitmentStep = $this->jobManagementService->storeRecruitmentStep($validatedData);
            $code = ResponseAlias::HTTP_OK;
            $message = "Recruitment Step stored successful";
        } else {
            $code = ResponseAlias::HTTP_BAD_REQUEST;
            $message = "Recruitment Step can not be created";
        }

        $response = [
            "data" => $recruitmentStep ?? null,
            '_response_status' => [
                "success" => true,
                "code" => $code,
                "message" => $message,
                "query_time" => $this->startTime->diffInSeconds(Carbon::now())
            ]
        ];
        return Response::json($response, ResponseAlias::HTTP_OK);

    }

    /**
     * @param int $stepId
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function getRecruitmentStep(int $stepId): JsonResponse
    {
        $this->authorize('view', JobManagement::class);
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
     * @param Request $request
     * @param string $jobId
     * @return JsonResponse
     */
    public function getRecruitmentStepList(Request $request, string $jobId): JsonResponse
    {
        $this->authorize('view', JobManagement::class);
        $recruitmentStep = $this->jobManagementService->getRecruitmentStepList($jobId);
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
     * @throws Throwable
     */
    public function hireInviteCandidate(Request $request, int $applicationId): JsonResponse
    {
        $this->authorize('update', JobManagement::class);
        $appliedJob = AppliedJob::findOrFail($applicationId);

        $validatedData = $this->jobManagementService->hireInviteValidator($request)->validate();

        $hireInviteType = $validatedData['hire_invite_type'];

        $youthId = (array)($appliedJob->youth_id);
        $youthProfiles = ServiceToServiceCall::getYouthProfilesByIds($youthId);
        $youth = $youthProfiles[0];

        if ($appliedJob->hire_invited_at == null) {
            //TODO : refactor with assignCandidateToInterviewSchedule
            if ($hireInviteType == AppliedJob::INVITE_TYPES['SMS'] && !empty($youth['mobile'])) {
                $this->jobManagementService->sendCandidateHireInviteSms($appliedJob->job_id, $youth);
            } else if ($hireInviteType == AppliedJob::INVITE_TYPES['Email'] && !empty($youth['email'])) {
                $this->jobManagementService->sendCandidateHireInviteEmail($appliedJob->job_id, $youth);

            } else if ($hireInviteType == AppliedJob::INVITE_TYPES['SMS and Email']) {
                if (!empty($youth['email'])) {
                    $this->jobManagementService->sendCandidateHireInviteEmail($appliedJob->job_id, $youth);
                }
                if (!empty($youth['mobile'])) {
                    $this->jobManagementService->sendCandidateHireInviteSms($appliedJob->job_id, $youth);
                }
            }

            $hireInvitedCandidate = $this->jobManagementService->hireInviteCandidate($appliedJob, $validatedData);

            $response = [
                "data" => $hireInvitedCandidate,
                '_response_status' => [
                    "success" => true,
                    "code" => ResponseAlias::HTTP_OK,
                    "message" => "Candidate  hire  invited successfully",
                    "query_time" => $this->startTime->diffInSeconds(Carbon::now())
                ]
            ];
        } else {
            $response = [
                '_response_status' => [
                    "success" => true,
                    "code" => ResponseAlias::HTTP_OK,
                    "message" => "Candidate  already invited",
                    "query_time" => $this->startTime->diffInSeconds(Carbon::now())
                ]
            ];
        }

        return Response::json($response, ResponseAlias::HTTP_OK);
    }

    /**
     * @param int $applicationId
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function updateHiredCandidate(int $applicationId): JsonResponse
    {
        $this->authorize('update', JobManagement::class);
        $appliedJob = AppliedJob::findOrFail($applicationId);

        $hiredCandidate = $this->jobManagementService->updateHiredCandidate($appliedJob);

        $response = [
            "data" => $hiredCandidate,
            '_response_status' => [
                "success" => true,
                "code" => ResponseAlias::HTTP_OK,
                "message" => "Candidate hired successfully",
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
        $this->authorize('update', JobManagement::class);
        $recruitmentStep = RecruitmentStep::findOrFail($stepId);
        $isRecruitmentStepDeletable = $this->jobManagementService->isRecruitmentStepDeletable($recruitmentStep);

        DB::beginTransaction();
        try {
            if ($isRecruitmentStepDeletable) {
                $this->jobManagementService->deleteRecruitmentStep($recruitmentStep);
                $this->jobManagementService->deleteRecruitmentStepSchedules($recruitmentStep->id);
                $response = [
                    '_response_status' => [
                        "success" => true,
                        "code" => ResponseAlias::HTTP_OK,
                        "message" => "Recruitment Step deleted successfully.",
                        "query_time" => $this->startTime->diffInSeconds(Carbon::now())
                    ]
                ];
            } else {
                $response = [
                    '_response_status' => [
                        "success" => false,
                        "code" => ResponseAlias::HTTP_BAD_REQUEST,
                        "message" => "Recruitment Step can not be deleted.",
                        "query_time" => $this->startTime->diffInSeconds(Carbon::now())
                    ]
                ];
            }
            DB::commit();
        } catch (Throwable $e) {
            DB::rollBack();
            throw $e;
        }


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
        $this->authorize('update', JobManagement::class);
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
        $this->authorize('view', JobManagement::class);
        $response = $this->jobManagementService->getCandidateList($request, $jobId, $status);

        $response['_response_status'] = [
            "success" => true,
            "code" => ResponseAlias::HTTP_OK,
            "query_time" => $this->startTime->diffInSeconds(Carbon::now()),
        ];

        return Response::json($response, ResponseAlias::HTTP_OK);
    }

    /**
     * @param Request $request
     * @param string $jobId
     * @return JsonResponse
     * @throws ValidationException
     * @throws AuthorizationException
     */
    public function recruitmentStepCandidateList(Request $request, string $jobId): JsonResponse
    {
        $this->authorize('view', JobManagement::class);

        $filter = $this->jobManagementService->recruitmentStepCandidateListFilterValidator($request)->validate();

        $response = $this->jobManagementService->getRecruitmentStepCandidateList($filter, $jobId);

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
        $this->authorize('view', JobManagement::class);
        $schedule = $this->interviewScheduleService->getOneInterviewSchedule($id);
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
     * @throws AuthorizationException
     */
    function stepSchedules(int $id): JsonResponse
    {
        $this->authorize('view', JobManagement::class);
        $schedule = $this->interviewScheduleService->getSchedulesByStepId($id);
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
     * @throws ValidationException
     * @throws AuthorizationException
     */

    function createSchedule(Request $request): JsonResponse
    {
        $this->authorize('update', JobManagement::class);
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
     * @throws ValidationException
     * @throws AuthorizationException
     */
    public function updateSchedule(Request $request, int $id): JsonResponse
    {
        $this->authorize('update', JobManagement::class);
        $schedule = InterviewSchedule::findOrFail($id);

        $isScheduleUpdatable = $this->interviewScheduleService->isScheduleUpdatable($schedule->id);

        if ($isScheduleUpdatable) {
            $validated = $this->interviewScheduleService->validator($request, $id)->validate();
            $this->interviewScheduleService->update($schedule, $validated);

            $success = false;
            $message = "schedule updated successfully.";
            $code = ResponseAlias::HTTP_BAD_REQUEST;
        } else {
            $success = true;
            $message = "schedule can not updated.";
            $code = ResponseAlias::HTTP_OK;
        }

        $response = [
            '_response_status' => [
                "success" => $success,
                "code" => $code,
                "message" => $message,
                "query_time" => $this->startTime->diffInSeconds(Carbon::now())
            ]
        ];
        return Response::json($response, ResponseAlias::HTTP_CREATED);
    }


    /**
     * @param int $id
     * @return JsonResponse
     * @throws Throwable
     */
    public function destroySchedule(int $id): JsonResponse
    {
        $this->authorize('update', JobManagement::class);
        $schedule = InterviewSchedule::findOrFail($id);


        $deleteStatus = $this->interviewScheduleService->destroy($schedule);

        if (empty($deleteStatus)) {
            $success = false;
            $code = ResponseAlias::HTTP_BAD_REQUEST;
            $message = "schedule can not be deleted.";
        } else {
            $success = true;
            $code = ResponseAlias::HTTP_OK;
            $message = "schedule deleted successfully.";
        }
        $response = [
            '_response_status' => [
                "success" => $success,
                "code" => $code,
                "message" => $message,
                "query_time" => $this->startTime->diffInSeconds(Carbon::now())
            ]
        ];
        return Response::json($response, ResponseAlias::HTTP_OK);
    }

    /**
     * Assign candidate to schedule
     * @param Request $request
     * @param int $scheduleId
     * @return JsonResponse
     * @throws ValidationException|Throwable
     */
    public function assignCandidateToInterviewSchedule(Request $request, int $scheduleId): JsonResponse
    {
        $this->authorize('update', JobManagement::class);
        $schedule = InterviewSchedule::findOrFail($scheduleId);

        $job = PrimaryJobInformation::where('job_id', $schedule->job_id)->firstOrFail();

        $validatedData = $this->interviewScheduleService->CandidateAssigningToScheduleValidator($request, $schedule)->validate();

        $this->interviewScheduleService->assignCandidateToSchedule($scheduleId, $schedule->recruitment_step_id, $validatedData);

        $applicationIds = $validatedData['applied_job_ids'];
        $appliedJob = AppliedJob::whereIn('id', $applicationIds)->get();
        $interviewInviteType = $validatedData['interview_invite_type'];

        $youthIds = $appliedJob->pluck('youth_id')->toArray();
        $youthProfiles = ServiceToServiceCall::getYouthProfilesByIds($youthIds);
        foreach ($youthProfiles as $youth) {
            if ($validatedData['notify'] == CandidateInterview::NOTIFY_NOW) {
                //TODO : refactor with hireInviteCandidate
                if ($interviewInviteType == AppliedJob::INVITE_TYPES['SMS'] && !empty($youth['mobile'])) {
                    $this->jobManagementService->sendCandidateInterviewInviteSms($job->job_id, $youth);
                } else if ($interviewInviteType == AppliedJob::INVITE_TYPES['Email'] && !empty($youth['email'])) {
                    $this->jobManagementService->sendCandidateInterviewInviteEmail($job->job_id, $youth);

                } else if ($interviewInviteType == AppliedJob::INVITE_TYPES['SMS and Email']) {
                    if (!empty($youth['email'])) {
                        $this->jobManagementService->sendCandidateInterviewInviteEmail($job->job_id, $youth);
                    }
                    if (!empty($youth['mobile'])) {
                        $this->jobManagementService->sendCandidateInterviewInviteSms($job->job_id, $youth);
                    }
                }

                /** set youth calendar event for interview using ServiceToServiceCall */
                $scheduleData = [
                    'job_title' => $job->job_title,
                    'job_title_en' => $job->job_title_en,
                    'youth_id' => $youth['id'],
                    'start_date' => Carbon::parse($schedule->interview_scheduled_at)->format('Y-m-d'),
                    'end_date' => Carbon::parse($schedule->interview_scheduled_at)->format('Y-m-d'),
                    'start_time' => Carbon::parse($schedule->interview_scheduled_at)->format('H:i'),
                    'end_time' => Carbon::parse($schedule->interview_scheduled_at)->format('H:i'),
                ];
                ServiceToServiceCall::createEventAfterInterviewScheduleAssign($scheduleData);
            }
        }
        $response = [
            '_response_status' => [
                "success" => true,
                "code" => ResponseAlias::HTTP_OK,
                "message" => "Candidate assigned to schedule  successfully",
                "query_time" => $this->startTime->diffInSeconds(Carbon::now())
            ]
        ];


        return Response::json($response, ResponseAlias::HTTP_OK);

    }

    /**
     * Remove Candidate From Schedule
     * @param Request $request
     * @param int $scheduleId
     * @return JsonResponse
     * @throws ValidationException
     * @throws AuthorizationException
     */
    public function removeCandidateFromInterviewSchedule(Request $request, int $scheduleId): JsonResponse
    {
        $this->authorize('update', JobManagement::class);

        $schedule = InterviewSchedule::findOrFail($scheduleId);

        $validatedData = $this->interviewScheduleService->CandidateRemoveFromScheduleValidator($request, $schedule)->validate();

        $this->interviewScheduleService->removeCandidateFromSchedule($scheduleId, $validatedData);
        $response = [
            '_response_status' => [
                "success" => true,
                "code" => ResponseAlias::HTTP_OK,
                "message" => "Candidate removed from schedule  successfully",
                "query_time" => $this->startTime->diffInSeconds(Carbon::now())
            ]
        ];


        return Response::json($response, ResponseAlias::HTTP_OK);

    }


    /**
     * @param Request $request
     * @param int $youthId
     * @return array
     */
    public function youthFeedStatistics(Request $request, int $youthId): array
    {
        $requestData = $request->all();
        if (!empty($requestData["skill_ids"])) {
            $requestData["skill_ids"] = is_array($requestData['skill_ids']) ? $requestData['skill_ids'] : explode(',', $requestData['skill_ids']);
        }
        $totalJobCount = $this->jobManagementService->getJobCount();
        $youthAppliedJobCount = $this->jobManagementService->getAppliedJobCount($youthId);
        $skillMatchingJobCount = 0;
        if (!empty($requestData["skill_ids"]) && is_array($requestData["skill_ids"]) && count($requestData["skill_ids"]) > 0) {
            $skillMatchingJobCount = $this->jobManagementService->getSkillMatchingJobCount($requestData["skill_ids"]);
        }

        return [
            'applied_jobs' => $youthAppliedJobCount,
            'total_jobs' => $totalJobCount,
            'skill_matching_jobs' => $skillMatchingJobCount
        ];
    }
}
