<?php

namespace App\Services\JobManagementServices;


use App\Facade\ServiceToServiceCall;
use App\Models\AdditionalJobInformation;
use App\Models\AppliedJob;
use App\Models\BaseModel;
use App\Models\CandidateInterview;
use App\Models\CandidateRequirement;
use App\Models\CompanyInfoVisibility;
use App\Models\JobContactInformation;
use App\Models\MatchingCriteria;
use App\Models\PrimaryJobInformation;
use App\Models\RecruitmentStep;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

class JobManagementService
{
    public MatchingCriteriaService $matchingCriteriaService;

    /**
     * @param MatchingCriteriaService $matchingCriteriaService
     */
    public function __construct(MatchingCriteriaService $matchingCriteriaService)
    {
        $this->matchingCriteriaService = $matchingCriteriaService;
    }

    /**
     * @param string $jobId
     * @return int
     */

    public function lastAvailableStep(string $jobId): int
    {
        $step = 1;
        $isPrimaryJobInformationComplete = (bool)PrimaryJobInformation::where('job_id', $jobId)->count('id');
        $step = $isPrimaryJobInformationComplete ? 2 : $step;
        $isAdditionalJobInformationComplete = (bool)AdditionalJobInformation::where('job_id', $jobId)->count('id');
        $step = $isPrimaryJobInformationComplete && $isAdditionalJobInformationComplete ? 3 : $step;
        $isCandidateRequirementComplete = (bool)CandidateRequirement::where('job_id', $jobId)->count('id');
        $step = $isPrimaryJobInformationComplete && $isAdditionalJobInformationComplete && $isCandidateRequirementComplete ? 4 : $step;
        $isCompanyInfoVisibilityComplete = (bool)CompanyInfoVisibility::where('job_id', $jobId)->count('id');
        $step = $isPrimaryJobInformationComplete && $isAdditionalJobInformationComplete && $isCandidateRequirementComplete && $isCompanyInfoVisibilityComplete ? 5 : $step;
        $isMatchingCriteriaComplete = (bool)MatchingCriteria::where('job_id', $jobId)->count('id');
        $step = $isPrimaryJobInformationComplete && $isAdditionalJobInformationComplete && $isCandidateRequirementComplete && $isCompanyInfoVisibilityComplete && $isMatchingCriteriaComplete ? 6 : $step;
        $isJobContactInformationComplete = (bool)JobContactInformation::where('job_id', $jobId)->count('id');
        $step = $isPrimaryJobInformationComplete && $isAdditionalJobInformationComplete && $isCandidateRequirementComplete && $isCompanyInfoVisibilityComplete && $isMatchingCriteriaComplete && $isJobContactInformationComplete ? 7 : $step;

        return $step;
    }

    /**
     * @param array $data
     * @return array
     */
    public function storeAppliedJob(array $data): array
    {
        $jobId = $data['job_id'];
        $expectedSalary = $data['expected_salary'];
        $youthId = intval($data['youth_id']);
        return AppliedJob::updateOrCreate(
            [
                'job_id' => $jobId,
                'youth_id' => $youthId,
            ],
            [
                'job_id' => $jobId,
                'youth_id' => $youthId,
                'apply_status' => AppliedJob::APPLY_STATUS["Applied"],
                'expected_salary' => $expectedSalary,
                'applied_at' => Carbon::now()
            ]
        )->toArray();
    }

    /**
     * Reject a candidate from a certain interview step
     * @param int $applicationId
     * @return AppliedJob
     */
    public function rejectCandidate(int $applicationId): AppliedJob
    {
        $appliedJob = AppliedJob::findOrFail($applicationId);

        $appliedJob->apply_status = AppliedJob::APPLY_STATUS["Rejected"];
        $appliedJob->current_recruitment_step_id = null;
        $appliedJob->save();

        return $appliedJob;
    }

    /**
     * @param AppliedJob $appliedJob
     * @return mixed
     */
    public function findFirstRecruitmentStep(AppliedJob $appliedJob): mixed
    {
        return RecruitmentStep::where('job_id', $appliedJob->job_id)->first();
    }

    /**
     * Shortlist a candidate for next interview step
     * @param int $applicationId
     * @return AppliedJob
     * @throws Throwable
     */
    public function shortlistCandidate(int $applicationId): AppliedJob
    {
        $appliedJob = AppliedJob::findOrFail($applicationId);
        $firstRecruitmentStep = $this->findFirstRecruitmentStep($appliedJob);
        if (!empty($appliedJob->current_recruitment_step_id)) {
            $currentRecruitmentStepId = $appliedJob->current_recruitment_step_id;
            $recruitmentStep = RecruitmentStep::findOrFail($currentRecruitmentStepId);
            $lastRecruitmentStepId = $this->findLastRecruitmentStep($recruitmentStep);
            $nextRecruitmentStepId = $this->findNextRecruitmentStep($recruitmentStep);
        }

        if ($appliedJob->apply_status == AppliedJob::APPLY_STATUS["Applied"] && $firstRecruitmentStep) {
            $appliedJob->apply_status = AppliedJob::APPLY_STATUS["Shortlisted"];
            $appliedJob->current_recruitment_step_id = $firstRecruitmentStep->id;
            $appliedJob->save();

        } else if ($appliedJob->apply_status = AppliedJob::APPLY_STATUS["Shortlisted"] && !empty($nextRecruitmentStepId) && !empty($lastRecruitmentStepId) && !empty($currentRecruitmentStepId) && $lastRecruitmentStepId > $currentRecruitmentStepId) {
            $appliedJob->current_recruitment_step_id = $nextRecruitmentStepId;
            $appliedJob->apply_status = AppliedJob::APPLY_STATUS["Shortlisted"];
            $appliedJob->save();

        } else if (!$firstRecruitmentStep || (!empty($lastRecruitmentStepId) && !empty($currentRecruitmentStepId) && $lastRecruitmentStepId == $currentRecruitmentStepId)) {
            $appliedJob->apply_status = AppliedJob::APPLY_STATUS["Hiring_Listed"];
            $appliedJob->current_recruitment_step_id = null;
            $appliedJob->save();

        } else {
            throw ValidationException::withMessages(['candidate can not be selected for  next step']);
        }

        return $appliedJob;
    }


    /**
     * @param RecruitmentStep $recruitmentStep
     * @return mixed
     */
    public function findNextRecruitmentStep(RecruitmentStep $recruitmentStep): mixed
    {
        $nextStep = RecruitmentStep::where('job_id', $recruitmentStep->job_id)
            ->where('id', '>', $recruitmentStep->id)
            ->first();

        return $nextStep->id ?? null;
    }

    /**
     * @param AppliedJob $appliedJob
     * @param array $data
     * @return CandidateInterview
     */
    public function updateInterviewedCandidate(AppliedJob $appliedJob, array $data): CandidateInterview
    {
        $candidateInterview = CandidateInterview::where('job_id', $appliedJob->job_id)
            ->where('applied_job_id', $appliedJob->id)
            ->where('recruitment_step_id', $appliedJob->current_recruitment_step_id)
            ->first();

        if (!$candidateInterview) {
            $candidateInterview = app(CandidateInterview::class);
        }

        $appliedJob->apply_status = AppliedJob::APPLY_STATUS["Interviewed"];
        $appliedJob->save();

        $candidateInterview->job_id = $appliedJob->job_id;
        $candidateInterview->applied_job_id = $appliedJob->id;
        $candidateInterview->is_candidate_present = $data['is_candidate_present'];
        $candidateInterview->interview_score = $data['interview_score'];

        $candidateInterview->save();

        return $candidateInterview;
    }

    public function findPreviousRecruitmentStep(AppliedJob $appliedJob)
    {
        $currentRecruitmentStep = $appliedJob->current_recruitment_step_id;
        return RecruitmentStep::where('id', '<', $currentRecruitmentStep)
            ->orderBy('id', 'desc')
            ->first();
    }

    /**
     * @param AppliedJob $appliedJob
     * @return AppliedJob
     */
    public function removeCandidateToPreviousStep(AppliedJob $appliedJob): AppliedJob
    {
        $currentRecruitmentStepId = $appliedJob->current_recruitment_step_id;
        $firstRecruitmentStep = $this->findFirstRecruitmentStep($appliedJob);


        if (!empty($firstRecruitmentStep) && $firstRecruitmentStep->id == $currentRecruitmentStepId) {
            $appliedJob->apply_status = AppliedJob::APPLY_STATUS["Applied"];
            $appliedJob->current_recruitment_step_id = null;
            $appliedJob->save();
        } else {
            $previousRecruitmentStep = $this->findPreviousRecruitmentStep($appliedJob);
            if (!empty($previousRecruitmentStep)) {
                $appliedJob->apply_status = AppliedJob::APPLY_STATUS["Shortlisted"];
                $appliedJob->current_recruitment_step_id = $previousRecruitmentStep->id;
                $appliedJob->save();
            }
        }

        return $appliedJob;
    }

    /**
     * @param Request $request
     * @return \Illuminate\Contracts\Validation\Validator
     */
    public function interviewedCandidateUpdateValidator(Request $request): \Illuminate\Contracts\Validation\Validator
    {
        $rules = [
            'is_candidate_present' => [
                'required',
                'integer',
                Rule::in(CandidateInterview::CANDIDATE_ATTENDANCE_STATUSES),
            ],
            'interview_score' => [
                Rule::requiredIf(function () use ($request) {
                    return $request->input('is_candidate_present') == CandidateInterview::IS_CANDIDATE_PRESENT_YES;
                }),
                'nullable',
                'numeric',
            ]
        ];

        return Validator::make($request->all(), $rules);

    }

    public function storeRecruitmentStep(array $data)
    {
        $recruitmentStep = app(RecruitmentStep::class);
        $recruitmentStep->fill($data);
        $recruitmentStep->save();
        return $recruitmentStep;
    }

    public function updateRecruitmentStep(RecruitmentStep $recruitmentStep, array $data): RecruitmentStep
    {
        $recruitmentStep->fill($data);
        $recruitmentStep->save();
        return $recruitmentStep;
    }

    /**
     * @param int $stepId
     * @return Model|Builder
     */
    public function getRecruitmentStep(int $stepId): Model|Builder
    {
        /** @var Builder|RecruitmentStep $recruitmentStepBuilder */
        $recruitmentStepBuilder = RecruitmentStep::select([
            'recruitment_steps . id',
            'recruitment_steps . title',
            'recruitment_steps . title_en',
            'recruitment_steps . step_type',
            'recruitment_steps . is_interview_reschedule_allowed',
            'recruitment_steps . interview_contact',
            'recruitment_steps . created_at',
            'recruitment_steps . updated_at',
        ]);

        $recruitmentStepBuilder->where('recruitment_steps . id', ' = ', $stepId);

        return $recruitmentStepBuilder->firstOrFail();
    }

    /**
     * @param RecruitmentStep $recruitmentStep
     * @return bool
     */
    public function deleteRecruitmentStep(RecruitmentStep $recruitmentStep): bool
    {
        return $recruitmentStep->delete();

    }

    /**
     * @param RecruitmentStep $recruitmentStep
     * @return bool
     */
    public function isRecruitmentStepDeletable(RecruitmentStep $recruitmentStep): bool
    {
        $maxStep = $this->findLastRecruitmentStep($recruitmentStep);
        $currentStepCandidates = $this->countCurrentRecruitmentStepCandidate($recruitmentStep);

        return $maxStep == $recruitmentStep->id && $currentStepCandidates == 0;
    }

    /**
     * @param RecruitmentStep $recruitmentStep
     * @return mixed
     */
    public function countCurrentRecruitmentStepCandidate(RecruitmentStep $recruitmentStep): mixed
    {
        return AppliedJob::where('job_id', $recruitmentStep->job_id)
            ->where('current_recruitment_step_id', $recruitmentStep->id)
            ->count('id');
    }

    /**
     * @param RecruitmentStep $recruitmentStep
     * @return mixed
     */
    public function findLastRecruitmentStep(RecruitmentStep $recruitmentStep): mixed
    {
        return RecruitmentStep::where('job_id', $recruitmentStep->job_id)
            ->max('id');
    }


    public function recruitmentStepStoreValidator(Request $request): \Illuminate\Contracts\Validation\Validator
    {
        $rules = [
            'job_id' => [
                'required',
                'string',
                'exists:primary_job_information,id,deleted_at,NULL'
            ],
            'title' => [
                'string',
                'required',
                'max:300'
            ],
            'title_en' => [
                'string',
                'nullable',
                'max:150'
            ],
            'step_type' => [
                'required',
                'integer',
                Rule::in(RecruitmentStep::STEP_TYPES)
            ],
            'is_interview_reschedule_allowed' => [
                'required',
                'integer'
            ],
            'interview_contact' => [
                'string'
            ]
        ];

        return validator::make($request->all(), $rules);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Contracts\Validation\Validator
     */
    public function recruitmentStepUpdateValidator(Request $request): \Illuminate\Contracts\Validation\Validator
    {
        $rules = [
            'title' => [
                'string',
                'required',
                'max:300'
            ],
            'title_en' => [
                'string',
                'nullable',
                'max:150'
            ]
        ];

        return validator::make($request->all(), $rules);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Contracts\Validation\Validator
     */
    public function applyJobValidator(Request $request): \Illuminate\Contracts\Validation\Validator
    {
        $requestData = $request->all();
        $jobId = $requestData['job_id'];
        $matchingCriteria = $this->matchingCriteriaService->getMatchingCriteria($jobId)->toArray();
        $youthData = $requestData["youth_data"];

        $requestData["youth_id"] = $youthData["id"];
        $requestData["age_valid"] = 1;
        $requestData["experience_valid"] = 1;
        $requestData["gender_valid"] = 1;
        $requestData["location_valid"] = 1;

        if ($matchingCriteria["is_age_enabled"] == 1 && $matchingCriteria["is_age_mandatory"] == 1) {
            $ageMin = intval($matchingCriteria["candidate_requirement"]["age_minimum"]);
            $ageMax = intval($matchingCriteria["candidate_requirement"]["age_maximum"]);
            $dbDate = Carbon::parse($youthData["date_of_birth"]);
            $age = Carbon::now()->diffInYears($dbDate);
            $requestData["age_valid"] = $age >= $ageMin && $age <= $ageMax ? 1 : 0;
        }

        if ($matchingCriteria["is_total_year_of_experience_enabled"] == 1 && $matchingCriteria["is_total_year_of_experience_mandatory"] == 1) {
            $expMin = intval($matchingCriteria["candidate_requirement"]["minimum_year_of_experience"]);
            $expMax = intval($matchingCriteria["candidate_requirement"]["maximum_year_of_experience"]);
            $exp = intval($youthData["total_job_experience"]["year"]);
            $requestData["experience_valid"] = $exp >= $expMin && $exp <= $expMax ? 1 : 0;
        }

        if ($matchingCriteria["is_gender_enabled"] == 1 && $matchingCriteria["is_gender_mandatory"] == 1) {
            $gender = intval($youthData["gender"]);
            $genderMatch = false;
            foreach ($matchingCriteria["genders"] as $genderItem) {
                $genderMatch = ($genderMatch || (intval($genderItem['gender_id']) == $gender));
            }
            $requestData["gender_valid"] = $genderMatch ? 1 : 0;
        }

        if ($matchingCriteria["is_job_location_enabled"] == 1 && $matchingCriteria["is_job_location_mandatory"] == 1) {
            $location = [
                "division" => $youthData["loc_division_id"],
                "district" => $youthData["loc_district_id"],
                "upazila" => $youthData["loc_upazila_id"],
            ];
            $locationMatch = false;
            foreach ($matchingCriteria["job_locations"] as $jobLoc) {
                $division = $jobLoc["loc_division_id"];
                $district = $jobLoc["loc_district_id"];
                $upazila = $jobLoc["loc_upazila_id"];
                // TODO: match these with youth data when available
                // $union = $jobLoc["loc_union_id"];
                // $cityCorporation = $jobLoc["loc_city_corporation_id"];
                // $cityCorporationWard = $jobLoc["loc_city_corporation_ward_id"];
                // $area = $jobLoc["loc_area_id"];
                $locationMatch = $locationMatch || (
                        $division == $location["division"] ||
                        $district == $location["district"] ||
                        $upazila == $location["upazila"]
                    );
            }
            $requestData["location_valid"] = $locationMatch ? 1 : 0;
        }

        $rules = [
            "job_id" => [
                "required",
                "string",
                "exists:primary_job_information,job_id,deleted_at,NULL",
            ],
            "youth_id" => [
                "required",
                "integer"
            ],
            "age_valid" => [
                'required',
                'integer',
                Rule::in([1])
            ],
            "experience_valid" => [
                'required',
                'integer',
                Rule::in([1])
            ],
            "gender_valid" => [
                'required',
                'integer',
                Rule::in([1])
            ],
            "location_valid" => [
                'required',
                'integer',
                Rule::in([1])
            ],
            "expected_salary" => [
                'nullable',
                'integer',
            ],
        ];

        $customMessage = [
            'age_valid . in' => 'Age must be valid . [30000]',
            'experience_valid . in' => 'Experience must be valid . [30000]',
            'gender_valid . in' => 'Gender must be valid . [30000]',
            'location_valid . in' => 'Location must be valid . [30000]'
        ];

        return Validator::make($requestData, $rules, $customMessage);
    }

    public function getCandidateList(Request $request, string $jobId, int $status = 0): array|null
    {
        $limit = $request->query('limit', 10);
        $paginate = $request->query('page');
        $order = $request->filled('order') ? $request->query('order') : 'ASC';
        $response = [];

        /** @var AppliedJob|Builder $appliedJobBuilder */
        $appliedJobBuilder = AppliedJob::select([
            'applied_jobs . id',
            'applied_jobs . job_id',
            'applied_jobs . youth_id',
            'applied_jobs . apply_status',
            'applied_jobs . rejected_from',
            'applied_jobs . applied_at',
            'applied_jobs . profile_viewed_at',
            'applied_jobs . rejected_at',
            'applied_jobs . shortlisted_at',
            'applied_jobs . interview_invited_at',
            'applied_jobs . interview_scheduled_at',
            'applied_jobs . interviewed_at',
            'applied_jobs . expected_salary',
            'applied_jobs . hire_invited_at',
            'applied_jobs . hired_at',
            'applied_jobs . interview_invite_source',
            'applied_jobs . interview_invite_type',
            'applied_jobs . hire_invite_type',
            'applied_jobs . interview_score',
            'applied_jobs . created_at',
            'applied_jobs . updated_at',
        ]);

        $appliedJobBuilder->where('applied_jobs . job_id', $jobId);
        if ($status > 0) $appliedJobBuilder->where('applied_jobs . apply_status', $status);

        /** @var Collection $candidates */
        if (is_numeric($paginate) || is_numeric($limit)) {
            $limit = $limit ?: BaseModel::DEFAULT_PAGE_SIZE;
            $candidates = $appliedJobBuilder->paginate($limit);
            $paginateData = (object)$candidates->toArray();
            $response['current_page'] = $paginateData->current_page;
            $response['total_page'] = $paginateData->last_page;
            $response['page_size'] = $paginateData->per_page;
            $response['total'] = $paginateData->total;
        } else {
            $candidates = $appliedJobBuilder->get();
        }

        $resultArray = $candidates->toArray();
        $youthIds = $candidates->pluck('youth_id')->toArray();
        $youthProfiles = ServiceToServiceCall::getYouthProfilesByIds($youthIds);
        $indexedYouths = [];

        foreach ($youthProfiles as $item) {
            $id = $item['id'];
            $indexedYouths[$id] = $item;
        }

        $matchingCriteria = $this->matchingCriteriaService->getMatchingCriteria($jobId)->toArray();

        foreach ($resultArray["data"] as &$item) {
            $id = $item['youth_id'];
            $youthData = $indexedYouths[$id];
            $matchRate = $this->getMatchPercent($item, $youthData, $matchingCriteria);
            $item['match_rate'] = $matchRate;
            $item['youth_profile'] = $youthData;
        }

        $resultData = $resultArray['data'] ?? $resultArray;

        $response['order'] = $order;
        $response['data'] = $resultData;

        return $response;
    }

    public function getMatchPercent($requestData, $youthData, $matchingCriteria): float|int
    {
        $shouldMatchTotal = 0;
        $matchTotal = 0;

        if ($matchingCriteria["is_age_enabled"]) {
            $ageMin = intval($matchingCriteria["candidate_requirement"]["age_minimum"]);
            $ageMax = intval($matchingCriteria["candidate_requirement"]["age_maximum"]);
            $dbDate = Carbon::parse($youthData["date_of_birth"]);
            $age = Carbon::now()->diffInYears($dbDate);
            $requestData["age_valid"] = $age >= $ageMin && $age <= $ageMax ? 1 : 0;
            $shouldMatchTotal += 1;
            $matchTotal += $requestData["age_valid"];
        }

        if ($matchingCriteria["is_total_year_of_experience_enabled"]) {
            $expMin = intval($matchingCriteria["candidate_requirement"]["minimum_year_of_experience"]);
            $expMax = intval($matchingCriteria["candidate_requirement"]["maximum_year_of_experience"]);
            $exp = intval($youthData["total_job_experience"]["year"]);
            $requestData["experience_valid"] = $exp >= $expMin && $exp <= $expMax ? 1 : 0;
            $shouldMatchTotal += 1;
            $matchTotal += $requestData["experience_valid"];
        }

        if ($matchingCriteria["is_gender_enabled"]) {
            $gender = intval($youthData["gender"]);
            $genderMatch = false;
            foreach ($matchingCriteria["genders"] as $genderItem) {
                $genderMatch = ($genderMatch || (intval($genderItem['gender_id']) == $gender));
            }
            $requestData["gender_valid"] = $genderMatch ? 1 : 0;
            $shouldMatchTotal += 1;
            $matchTotal += $requestData["gender_valid"];
        }

        if ($matchingCriteria["is_job_location_enabled"]) {
            $location = [
                "division" => $youthData["loc_division_id"],
                "district" => $youthData["loc_district_id"],
                "upazila" => $youthData["loc_upazila_id"],
            ];
            $locationMatch = false;
            foreach ($matchingCriteria["job_locations"] as $jobLoc) {
                $division = $jobLoc["loc_division_id"];
                $district = $jobLoc["loc_district_id"];
                $upazila = $jobLoc["loc_upazila_id"];
                // TODO: match these with youth data when available
                // $union = $jobLoc["loc_union_id"];
                // $cityCorporation = $jobLoc["loc_city_corporation_id"];
                // $cityCorporationWard = $jobLoc["loc_city_corporation_ward_id"];
                // $area = $jobLoc["loc_area_id"];
                $locationMatch = $locationMatch || (
                        $division == $location["division"] ||
                        $district == $location["district"] ||
                        $upazila == $location["upazila"]
                    );
            }
            $requestData["location_valid"] = $locationMatch ? 1 : 0;
            $shouldMatchTotal += 1;
            $matchTotal += $requestData["location_valid"];
        }

        $youthSkillsIds = array_map(function ($v) {
            return $v["id"];
        }, $youthData["skills"]);

        if ($matchingCriteria["is_area_of_experience_enabled"]) {
            $aoeMatch = false;
            foreach ($matchingCriteria["area_of_experiences"] as $aoe) {
                $aoeMatch = $aoeMatch || in_array($aoe["id"], $youthSkillsIds);
            }
            $requestData["area_of_experience_valid"] = $aoeMatch ? 1 : 0;
            $shouldMatchTotal += 1;
            $matchTotal += $requestData["area_of_experience_valid"];
        }

        if ($matchingCriteria["is_skills_enabled"]) {
            $skillsMatch = false;
            foreach ($matchingCriteria["area_of_experiences"] as $skill) {
                $skillsMatch = $skillsMatch || in_array($skill["id"], $youthSkillsIds);
            }
            $requestData["area_of_experience_valid"] = $skillsMatch ? 1 : 0;
            $shouldMatchTotal += 1;
            $matchTotal += $requestData["area_of_experience_valid"];
        }

        if ($matchingCriteria["is_salary_enabled"]) {
            $salaryMin = intval($matchingCriteria["additional_job_information"]["salary_min"]);
            $salaryMax = intval($matchingCriteria["additional_job_information"]["salary_max"]);
            $expectedSalary = $requestData["expected_salary"] ?? 0;
            $requestData["expected_salary_valid"] = $expectedSalary >= $salaryMin && $expectedSalary <= $salaryMax ? 1 : 0;
            $shouldMatchTotal += 1;
            $matchTotal += $requestData["expected_salary_valid"];

        }

        // if ($matchingCriteria["is_area_of_business_enabled"]) {}

        // if ($matchingCriteria["is_job_level_enabled"]) {}

        return $matchTotal / $shouldMatchTotal;
    }

}
