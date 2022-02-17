<?php

namespace App\Services\JobManagementServices;


use App\Facade\ServiceToServiceCall;
use App\Models\AdditionalJobInformation;
use App\Models\AppliedJob;
use App\Models\BaseModel;
use App\Models\CandidateInterview;
use App\Models\CandidateRequirement;
use App\Models\CompanyInfoVisibility;
use App\Models\InterviewSchedule;
use App\Models\JobContactInformation;
use App\Models\MatchingCriteria;
use App\Models\PrimaryJobInformation;
use App\Models\RecruitmentStep;
use App\Services\CommonServices\MailService;
use App\Services\CommonServices\SmsService;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use phpDocumentor\Reflection\DocBlock\Description;
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
     * @param array $request
     * @param string $jobId
     * @return array
     */
    public function getJobCandidateAppliedDetails(array $request, string $jobId): array
    {
        $youthId = $request['youth_id'] ?? "";
        $isRequestFromClientSide = !empty($request[BaseModel::IS_CLIENT_SITE_RESPONSE_KEY]);

        $jobInformationBuilder = PrimaryJobInformation::select([
            'primary_job_information.job_id',
        ]);

        $jobInformationBuilder->leftJoin('applied_jobs', function ($join) {
            $join->on('primary_job_information.job_id', '=', 'applied_jobs.job_id')
                ->whereNull('applied_jobs.deleted_at');
        });
        $jobInformationBuilder->groupBy('primary_job_information.job_id');
        $jobInformationBuilder->selectRaw("SUM(CASE WHEN apply_status>=0 THEN 1 ELSE 0 END ) as applications");
        $jobInformationBuilder->selectRaw("SUM(CASE WHEN apply_status = ? THEN 1 ELSE 0 END) as shortlisted", [AppliedJob::APPLY_STATUS["Shortlisted"]]);
        $jobInformationBuilder->selectRaw("SUM(CASE WHEN apply_status = ? THEN 1 ELSE 0 END) as interviewed", [AppliedJob::APPLY_STATUS["Interviewed"]]);

        $jobInformationBuilder->selectRaw("SUM(CASE WHEN youth_id = ? THEN 1 ELSE 0 END) as has_applied", [$youthId]);
        $jobInformationBuilder->where("primary_job_information.job_id", $jobId);

        return $jobInformationBuilder->first()->toArray();
    }

    /**
     * @param array $request
     * @param Carbon $startTime
     * @return array
     */
    public function getJobList(array $request, Carbon $startTime): array
    {
        $searchText = $request['search_text'] ?? "";
        $paginate = $request['page'] ?? "";
        $pageSize = $request['page_size'] ?? "";
        $skillIds = $request['skill_ids'] ?? [];
        $jobSectorIds = $request['job_sector_ids'] ?? [];
        $occupationIds = $request['occupation_ids'] ?? [];
        $locDistrictIds = $request['loc_district_ids'] ?? [];
        $industryAssociationId = $request['industry_association_id'] ?? "";
        $instituteId = $request['institute_id'] ?? "";
        $organizationId = $request['organization_id'] ?? "";
        $youthOnly = $request['youth_only'] ?? "";
        $youthId = $request['youth_id'] ?? "";
        $jobLevel = $request['job_level'] ?? "";
        $rowStatus = $request['row_status'] ?? "";
        $order = $request['order'] ?? "ASC";
        $type = $request['type'] ?? "";
        $isRequestFromClientSide = !empty($request[BaseModel::IS_CLIENT_SITE_RESPONSE_KEY]);

        /** @var Builder $jobInformationBuilder */
        $jobInformationBuilder = PrimaryJobInformation::select([
            'primary_job_information.id',
            'primary_job_information.job_id',
            'primary_job_information.service_type',
            'primary_job_information.job_title',
            'primary_job_information.job_title_en',
            'primary_job_information.no_of_vacancies',
            'primary_job_information.occupation_id',
            'primary_job_information.job_sector_id',
            'primary_job_information.industry_association_id',
            'primary_job_information.organization_id',
            'primary_job_information.institute_id',
            'primary_job_information.application_deadline',
            'primary_job_information.published_at',
            'primary_job_information.archived_at',
            'primary_job_information.is_apply_online',

            'candidate_requirements.minimum_year_of_experience',
            'candidate_requirements.maximum_year_of_experience',

            'industry_associations.title as industry_association_title',
            'industry_associations.title_en as industry_association_title_en',
            'industry_associations.logo as industry_association_logo',
            'industry_associations.address as industry_association_address',
            'industry_associations.address_en as industry_association_address_en',

            'organizations.title as organization_title',
            'organizations.title_en as organization_title_en',
            'organizations.logo as organization_logo',
            'organizations.address as organization_address',
            'organizations.address_en as organization_address_en',
            'primary_job_information.row_status'
        ]);

        if (!$isRequestFromClientSide) {
            $jobInformationBuilder->acl();
        }

        if ($type != PrimaryJobInformation::JOB_FILTER_TYPE_POPULAR) {
            $jobInformationBuilder->orderBy('primary_job_information.id', $order);
        }

        if (is_numeric($industryAssociationId)) {
            $jobInformationBuilder->where('primary_job_information.industry_association_id', $industryAssociationId);
        }

        if (is_numeric($instituteId)) {
            $jobInformationBuilder->where('primary_job_information.institute_id', $instituteId);
        }

        if (is_numeric($organizationId)) {
            $jobInformationBuilder->where('primary_job_information.organization_id', $organizationId);
        }

        if (is_numeric($rowStatus)) {
            $jobInformationBuilder->where('primary_job_information.row_status', $rowStatus);
        }

        if (!empty($searchText)) {
            $jobInformationBuilder->where(function ($builder) use ($searchText) {
                $builder->orWhere('primary_job_information.job_title', 'like', '%' . $searchText . '%');
                $builder->orWhere('primary_job_information.job_title_en', 'like', '%' . $searchText . '%');
            });

        }

        $jobInformationBuilder->leftJoin('industry_associations', function ($join) {
            $join->on('primary_job_information.industry_association_id', '=', 'industry_associations.id')
                ->whereNull('industry_associations.deleted_at')
                ->whereNotNull('primary_job_information.industry_association_id');
        });

        $jobInformationBuilder->leftJoin('candidate_requirements', function ($join) {
            $join->on('primary_job_information.job_id', '=', 'candidate_requirements.job_id')
                ->whereNull('candidate_requirements.deleted_at');
        });

        $jobInformationBuilder->leftJoin('organizations', function ($join) {
            $join->on('primary_job_information.organization_id', '=', 'organizations.id')
                ->whereNull('organizations.deleted_at')
                ->whereNotNull('primary_job_information.organization_id');
        });

        $jobInformationBuilder->leftJoin('applied_jobs', function ($join) {
            $join->on('primary_job_information.job_id', '=', 'applied_jobs.job_id')
                ->whereNull('applied_jobs.deleted_at');
        });
        $jobInformationBuilder->groupBy('primary_job_information.job_id');
        $jobInformationBuilder->selectRaw("SUM(CASE WHEN apply_status>=0 THEN 1 ELSE 0 END ) as applications");
        $jobInformationBuilder->selectRaw("SUM(CASE WHEN apply_status = ? THEN 1 ELSE 0 END) as shortlisted", [AppliedJob::APPLY_STATUS["Shortlisted"]]);
        $jobInformationBuilder->selectRaw("SUM(CASE WHEN apply_status = ? THEN 1 ELSE 0 END) as interviewed", [AppliedJob::APPLY_STATUS["Interviewed"]]);

        $jobInformationBuilder->selectRaw("SUM(CASE WHEN youth_id = ? THEN 1 ELSE 0 END) as has_applied", [$youthId]);


        if (!empty($type) && $type == PrimaryJobInformation::JOB_FILTER_TYPE_RECENT) {
            $jobInformationBuilder->whereDate('primary_job_information.published_at', '>', $startTime->subDays(7)->endOfDay());
            $jobInformationBuilder->whereDate('primary_job_information.application_deadline', '>', $startTime);
            $jobInformationBuilder->active();
        }

        if (!empty($type) && $type == PrimaryJobInformation::JOB_FILTER_TYPE_POPULAR) {
            $jobInformationBuilder->whereDate('primary_job_information.published_at', '<=', $startTime);
            $jobInformationBuilder->whereDate('primary_job_information.application_deadline', '>', $startTime);
            $jobInformationBuilder->orderBy(DB::raw('count(applied_jobs.id)'), 'DESC');
            $jobInformationBuilder->active();
        }

        if (is_array($skillIds) && count($skillIds) > 0) {
            $skillMatchingJobIds = DB::table('candidate_requirement_skill')->whereIn('skill_id', $skillIds)->pluck('job_id');
            $jobInformationBuilder->whereIn('primary_job_information.job_id', $skillMatchingJobIds);
        }

        if (is_array($jobSectorIds) && count($jobSectorIds) > 0) {
            $jobInformationBuilder->whereIn('job_sector_id', $jobSectorIds);
        }

        if (is_array($occupationIds) && count($occupationIds) > 0) {
            $jobInformationBuilder->whereIn('occupation_id', $occupationIds);
        }

        /** If request from client side */
        if ($isRequestFromClientSide) {
            $jobInformationBuilder->whereDate('primary_job_information.published_at', '<=', $startTime);
            $jobInformationBuilder->whereDate('primary_job_information.application_deadline', '>', $startTime);
            $jobInformationBuilder->active();
        }

        $jobInformationBuilder->with('additionalJobInformation');
        $jobInformationBuilder->with('additionalJobInformation.jobLocations');
        $jobInformationBuilder->with('additionalJobInformation.jobLevels');


        if (is_array($locDistrictIds) && count($locDistrictIds) > 0) {
            $jobInformationBuilder->whereHas('additionalJobInformation.jobLocations', function ($query) use ($locDistrictIds) {
                $query->whereIn('additional_job_information_job_locations.loc_district_id', $locDistrictIds);
            });
        }

        if (is_numeric($jobLevel)) {
            $jobInformationBuilder->whereHas('additionalJobInformation.jobLevels', function ($query) use ($jobLevel) {
                $query->where('additional_job_information_job_levels.job_level_id', $jobLevel);
            });
        } else {

            $jobInformationBuilder->with('additionalJobInformation.jobLevels');

        }

        $jobInformationBuilder->with('candidateRequirement');
        $jobInformationBuilder->with('candidateRequirement.skills');

        if (!empty($youthOnly) && !empty($youthId)) {
            $jobInformationBuilder->where("applied_jobs.youth_id", $youthId);
        }

        if (is_numeric($paginate) || is_numeric($pageSize)) {
            $pageSize = $pageSize ?: BaseModel::DEFAULT_PAGE_SIZE;
            $jobInformation = $jobInformationBuilder->paginate($pageSize);
            $paginateData = (object)$jobInformation->toArray();
            $response['current_page'] = $paginateData->current_page;
            $response['total_page'] = $paginateData->last_page;
            $response['page_size'] = $paginateData->per_page;
            $response['total'] = $paginateData->total;
        } else {
            $jobInformation = $jobInformationBuilder->get();
        }

        $response['order'] = $order;
        $response['data'] = $jobInformation->toArray()['data'] ?? $jobInformation->toArray();
        $response['query_time'] = $startTime->diffInSeconds(Carbon::now());
        return $response;
    }

    /**
     * @param Request $request
     * @return \Illuminate\Contracts\Validation\Validator
     */
    public function jobDetailsFilterValidator(Request $request): \Illuminate\Contracts\Validation\Validator
    {
        $requestData = $request->all();

        $rules = [
            'youth_id' => 'nullable|integer'
        ];

        return Validator::make($requestData, $rules);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Contracts\Validation\Validator
     */
    public function jobListFilterValidator(Request $request): \Illuminate\Contracts\Validation\Validator
    {
        $customMessage = [
            'order.in' => 'Order must be within ASC or DESC.[30000]',
            'row_status.in' => 'Row status must be within 1 or 0. [30000]'
        ];

        if ($request->filled('order')) {
            $request->offsetSet('order', strtoupper($request->get('order')));
        }

        $requestData = $request->all();

        if (!empty($requestData['skill_ids'])) {
            $requestData['skill_ids'] = is_array($requestData['skill_ids']) ? $requestData['skill_ids'] : explode(',', $requestData['skill_ids']);
        }
        if (!empty($requestData['loc_district_ids'])) {
            $requestData['loc_district_ids'] = is_array($requestData['loc_district_ids']) ? $requestData['loc_district_ids'] : explode(',', $requestData['loc_district_ids']);
        }

        if (!empty($requestData['job_sector_ids'])) {
            $requestData['job_sector_ids'] = is_array($requestData['job_sector_ids']) ? $requestData['job_sector_ids'] : explode(',', $requestData['job_sector_ids']);
        }

        if (!empty($requestData['occupation_ids'])) {
            $requestData['occupation_ids'] = is_array($requestData['occupation_ids']) ? $requestData['occupation_ids'] : explode(',', $requestData['occupation_ids']);
        }


        $rules = [
            'search_text' => 'nullable',
            'page' => 'nullable|integer|gt:0',
            'page_size' => 'nullable|integer|gt:0',

            'industry_association_id' => 'nullable|integer',
            'organization_id' => 'nullable|integer',
            'institute_id' => 'nullable|integer',
            'youth_id' => 'nullable|integer',
            'job_level' => [
                'nullable',
                'integer',
                Rule::in(array_keys(AdditionalJobInformation::JOB_LEVEL))
            ],
            'loc_district_ids' => [
                'nullable',
                'array',
                'min:1'
            ],
            'loc_district_ids.*' => [
                'nullable',
                'integer',
                'distinct',
            ],
            'order' => [
                'nullable',
                'string',
                Rule::in([BaseModel::ROW_ORDER_ASC, BaseModel::ROW_ORDER_DESC])
            ],
            'row_status' => [
                'nullable',
                "integer",
                Rule::in([BaseModel::ROW_STATUS_ACTIVE, BaseModel::ROW_STATUS_INACTIVE]),
            ],
            'type' => [
                'nullable',
                'string',
                Rule::in(PrimaryJobInformation::JOB_FILTER_TYPES)
            ],
            'skill_ids' => [
                'nullable',
                'array',
                'min:1'
            ],
            'skill_ids.*' => [
                'nullable',
                'integer',
                'distinct',
            ],
            'job_sector_ids' => [
                'nullable',
                'array',
                'min:1'
            ],
            'job_sector_ids.*' => [
                'nullable',
                'integer',
                'distinct',
            ],
            'occupation_ids' => [
                'nullable',
                'array',
                'min:1'
            ],
            'occupation_ids.*' => [
                'nullable',
                'integer',
                'distinct',
            ],
        ];


        return Validator::make($requestData, $rules, $customMessage);

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
        $appliedJob->save();

        return $appliedJob;
    }

    /**
     * Shortlist a candidate for next interview step
     * @param int $applicationId
     * @return AppliedJob
     * @throws Throwable
     */
    public function shortlistCandidate(int $applicationId): mixed
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

        } else if (!empty($lastRecruitmentStepId) && !empty($currentRecruitmentStepId) && $lastRecruitmentStepId == $currentRecruitmentStepId) {
            $appliedJob->apply_status = AppliedJob::APPLY_STATUS["Hiring_Listed"];
            $appliedJob->current_recruitment_step_id = null;
            $appliedJob->save();

        } else {
            return false;
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
     * @throws ValidationException
     */
    public function inviteCandidateForInterview(int $applicationId)
    {
        $appliedJob = AppliedJob::findOrFail($applicationId);

        if ($appliedJob->apply_status = AppliedJob::APPLY_STATUS["Shortlisted"]) {
            $appliedJob->apply_status = AppliedJob::APPLY_STATUS["Interview_invited"];
            $appliedJob->shortlisted_at = Carbon::now();
        } else {
            throw ValidationException::withMessages(['candidate can not be selected for  next step']);
        }
        $appliedJob->save();

        return $appliedJob;

    }

    /**
     * @param array $data
     * @return RecruitmentStep
     */
    public function storeRecruitmentStep(array $data): RecruitmentStep
    {
        $recruitmentStep = app(RecruitmentStep::class);
        $recruitmentStep->fill($data);
        $recruitmentStep->save();
        return $recruitmentStep;
    }

    /**
     * @param string $jobId
     * @return bool
     */
    public function isRecruitmentStepCreatable(string $jobId): bool
    {
        return $this->countTotalFinalHiringListCandidate($jobId) == 0;
    }

    /**
     * @param RecruitmentStep $recruitmentStep
     * @param array $data
     * @return RecruitmentStep
     */
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
            'recruitment_steps.id',
            'recruitment_steps.title',
            'recruitment_steps.title_en',
            'recruitment_steps.step_type',
            'recruitment_steps.is_interview_reschedule_allowed',
            'recruitment_steps.interview_contact',
            'recruitment_steps.created_at',
            'recruitment_steps.updated_at',
        ]);

        $recruitmentStepBuilder->where('recruitment_steps.id', '=', $stepId);

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

    public function deleteRecruitmentStepSchedules(int $recruitmentStepId)
    {
        return InterviewSchedule::where('recruitment_step_id', $recruitmentStepId)->delete();

    }

    public function isLastRecruitmentStep(RecruitmentStep $recruitmentStep): bool
    {
        $maxStep = RecruitmentStep::where('job_id', $recruitmentStep->job_id)
            ->max('id');

        $currentStepCandidates = AppliedJob::where('job_id', $recruitmentStep->job_id)
            ->where('current_recruitment_step_id', $recruitmentStep->job_id)
            ->count('id');

        return $maxStep == $recruitmentStep->id && $currentStepCandidates == 0;
    }


    public function recruitmentStepStoreValidator(Request $request): \Illuminate\Contracts\Validation\Validator
    {
        $rules = [
            'job_id' => [
                'required',
                'string',
                'exists:primary_job_information,job_id,deleted_at,NULL'
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
                'nullable',
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
    public function youthJobsValidator(Request $request): \Illuminate\Contracts\Validation\Validator
    {
        $requestData = $request->all();

        $rules = [
            "youth_id" => [
                "required",
                "integer"
            ],
        ];

        return Validator::make($requestData, $rules);
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
            'age_valid.in' => 'Age must be valid.[30000]',
            'experience_valid.in' => 'Experience must be valid.[30000]',
            'gender_valid.in' => 'Gender must be valid.[30000]',
            'location_valid.in' => 'Location must be vali  [30000]'
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
            'applied_jobs.id',
            'applied_jobs.job_id',
            'applied_jobs.youth_id',
            'applied_jobs.apply_status',
            'applied_jobs.current_recruitment_step_id',
            'applied_jobs.applied_at',
            'applied_jobs.profile_viewed_at',
            'applied_jobs.expected_salary',
            'applied_jobs.hire_invited_at',
            'applied_jobs.hired_at',
            // 'applied_jobs.interview_invite_source',
            'applied_jobs.hire_invite_type',
            'applied_jobs.created_at',
            'applied_jobs.updated_at',
        ]);

        $appliedJobBuilder->where('applied_jobs.job_id', $jobId);
        if ($status > 0) $appliedJobBuilder->where('applied_jobs.apply_status', $status);

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
        $youthProfiles = !empty($youthIds) ? ServiceToServiceCall::getYouthProfilesByIds($youthIds) : [];
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

    /**
     * @param array $request
     * @param string $jobId
     * @return array
     */
    public function getRecruitmentStepCandidateList(array $request, string $jobId): array
    {
        $type = $request['type'] ?? "";
        $stepId = $request['step_id'] ?? "";
        $paginate = $request['page'] ?? "";
        $pageSize = $request['page_size'] ?? BaseModel::DEFAULT_PAGE_SIZE;
        $order = $request['order'] ?? "ASC";

        /** @var AppliedJob|Builder $appliedJobBuilder */
        $appliedJobBuilder = AppliedJob::select([
            'applied_jobs.id',
            'applied_jobs.job_id',
            'applied_jobs.youth_id',
            'applied_jobs.apply_status',
            'applied_jobs.current_recruitment_step_id',
            'recruitment_steps.title as current_recruitment_step_title',
            'recruitment_steps.title_en as current_recruitment_step_title_en',
            'applied_jobs.applied_at',
            'applied_jobs.profile_viewed_at',
            'applied_jobs.expected_salary',
            'applied_jobs.hire_invited_at',
            'applied_jobs.hired_at',
            'applied_jobs.hire_invite_type',
            'applied_jobs.created_at',
            'applied_jobs.updated_at',
        ]);
        $appliedJobBuilder->where('applied_jobs.job_id', $jobId);

        $appliedJobBuilder->leftJoin('recruitment_steps', function ($join) {
            $join->on('applied_jobs.current_recruitment_step_id', '=', 'recruitment_steps.id')
                ->whereNull('recruitment_steps.deleted_at');
        });

        if ($type != AppliedJob::TYPE_QUALIFIED && is_numeric($stepId)) {
            $appliedJobBuilder->where('applied_jobs.current_recruitment_step_id', $stepId);
        }

        if ($type == AppliedJob::TYPE_ALL) {
            $appliedJobBuilder->where(function ($query) {
                $query->where('applied_jobs.apply_status', '!=', AppliedJob::APPLY_STATUS['Rejected'])
                    ->whereNull('applied_jobs.current_recruitment_Step_id');

            });
            $appliedJobBuilder->orwhereNotNull('applied_jobs.current_recruitment_step_id');

        } elseif ($type == AppliedJob::TYPE_VIEWED) {
            $appliedJobBuilder->whereNotNull('applied_jobs.profile_viewed_at');
            $appliedJobBuilder->where(function ($query) {
                $query->where('applied_jobs.apply_status', '!=', AppliedJob::APPLY_STATUS['Rejected'])
                    ->whereNull('applied_jobs.current_recruitment_step_id');

            });
            $appliedJobBuilder->orwhereNotNull('applied_jobs.current_recruitment_step_id');

        } elseif ($type == AppliedJob::TYPE_NOT_VIEWED) {
            $appliedJobBuilder->whereNull('applied_jobs.profile_viewed_at');
            $appliedJobBuilder->where(function ($query) {
                $query->where('applied_jobs.apply_status', '!=', AppliedJob::APPLY_STATUS['Rejected'])
                    ->whereNull('applied_jobs.current_recruitment_step_id');

            });
            $appliedJobBuilder->orwhereNotNull('applied_jobs.current_recruitment_step_id');

        } elseif ($type == AppliedJob::TYPE_REJECTED) {
            $appliedJobBuilder->where('applied_jobs.apply_status', AppliedJob::APPLY_STATUS['Rejected']);
            if (empty($stepId)) {
                $appliedJobBuilder->whereNull('applied_jobs.current_recruitment_step_id');
            }

        } elseif ($type == AppliedJob::TYPE_QUALIFIED) {
            if (empty($stepId)) {
                $appliedJobBuilder->where('applied_jobs.current_recruitment_step_id', '>', 0);
            } else {
                $appliedJobBuilder->where('applied_jobs.current_recruitment_step_id', '>', $stepId);
            }
        } else if ($type == AppliedJob::TYPE_SHORTLISTED) {
            $appliedJobBuilder->where('applied_jobs.apply_status', AppliedJob::APPLY_STATUS['Shortlisted']);

        } else if ($type == AppliedJob::TYPE_SCHEDULED) {
            $appliedJobBuilder->where('applied_jobs.apply_status', AppliedJob::APPLY_STATUS['Interview_scheduled']);

        } else if ($type == AppliedJob::TYPE_INTERVIEWED) {
            $appliedJobBuilder->where('applied_jobs.apply_status', AppliedJob::APPLY_STATUS['Interviewed']);

        } else if ($type == AppliedJob::TYPE_HIRE_SELECTED) {
            $appliedJobBuilder->where('applied_jobs.apply_status', AppliedJob::APPLY_STATUS['Hiring_Listed']);

        } else if ($type == AppliedJob::TYPE_HIRE_INVITED) {
            $appliedJobBuilder->where('applied_jobs.apply_status', AppliedJob::APPLY_STATUS['Hire_invited']);

        } else if ($type == AppliedJob::TYPE_HIRED) {
            $appliedJobBuilder->where('applied_jobs.apply_status', AppliedJob::APPLY_STATUS['Hired']);
        }

        /** @var Collection $candidates */
        if (is_numeric($paginate) || is_numeric($pageSize)) {
            $pageSize = $pageSize ?: BaseModel::DEFAULT_PAGE_SIZE;
            $candidates = $appliedJobBuilder->paginate($pageSize);
            $paginateData = (object)$candidates->toArray();
            $response['current_page'] = $paginateData->current_page;
            $response['total_page'] = $paginateData->last_page;
            $response['page_size'] = $paginateData->per_page;
            $response['total'] = $paginateData->total;
        } else {
            $candidates = $appliedJobBuilder->get();
        }


        /** TODO: fix reason why duplicates are coming (unique used below) */
        $resultArray = $candidates->unique('youth_id')->toArray();
        $youthIds = $candidates->unique('youth_id')->pluck('youth_id')->toArray();
        $youthProfiles = !empty($youthIds) ? ServiceToServiceCall::getYouthProfilesByIds($youthIds) : [];
        $indexedYouths = [];

        foreach ($youthProfiles as $item) {
            $id = $item['id'];
            $indexedYouths[$id] = $item;
        }

        $matchingCriteria = $this->matchingCriteriaService->getMatchingCriteria($jobId)->toArray();

        $resultData = $resultArray['data'] ?? $resultArray;
        foreach ($resultData as &$item) {
            $id = $item['youth_id'];
            $youthData = $indexedYouths[$id];
            $matchRate = $this->getMatchPercent($item, $youthData, $matchingCriteria);
            $item['match_rate'] = $matchRate;
            $item['apply_count'] = $this->youthApplyCountToSpecificOrganization($item['youth_id'], $item['job_id']);
            $item['youth_profile'] = $youthData;
        }

        $response['order'] = $order;
        $response['data'] = $resultData;

        return $response;
    }

    /**
     * @param int $youthId
     * @param string $jobId
     * @return int
     */
    public function youthApplyCountToSpecificOrganization(int $youthId, string $jobId): int
    {


        $youthAppliedJobs = AppliedJob::where('youth_id', $youthId)->pluck('job_id');
        $youthAppliedJobIds = $youthAppliedJobs->toArray();

        $job = PrimaryJobInformation::where('job_id', $jobId)->first();

        if ($job->industry_association_id) {
            return PrimaryJobInformation::where('industry_association_id', $job->industry_association_id)
                ->whereIn('job_id', $youthAppliedJobIds)
                ->count('id');
        } else if ($job->organization_id) {
            return PrimaryJobInformation::where('organization_id', $job->organization_id)
                ->whereIn('job_id', $youthAppliedJobIds)
                ->count('id');
        } else if ($job->institute_id) {
            return PrimaryJobInformation::where('institute_id', $job->institute_id)
                ->whereIn('job_id', $youthAppliedJobIds)
                ->count('id');
        } else {
            return 0;
        }


    }

    /**
     * @param string $jobId
     * @return array
     */
    public function getRecruitmentStepList(string $jobId): array
    {

        /** @var Builder $recruitmentStepBuilder */
        $recruitmentStepBuilder = RecruitmentStep::select([
            'recruitment_steps.id',
            'recruitment_steps.job_id',
            'recruitment_steps.title',
            'recruitment_steps.title_en',
            'recruitment_steps.step_type',
            'recruitment_steps.is_interview_reschedule_allowed',
            'recruitment_steps.interview_contact',
            'recruitment_steps.created_at',
            'recruitment_steps.updated_at',
        ]);

        $recruitmentStepBuilder->where('recruitment_steps.job_id', $jobId);


        /** @var Collection $recruitmentSteps */
        $recruitmentSteps = $recruitmentStepBuilder->get();


        foreach ($recruitmentSteps as &$recruitmentStep) {

            $recruitmentStep['total_candidate'] = $this->countStepCandidate($jobId, $recruitmentStep->id);
            $recruitmentStep['shortlisted'] = $this->countStepShortlistedCandidate($jobId, $recruitmentStep->id);
            $recruitmentStep['qualified'] = $this->countStepQualifiedCandidate($jobId, $recruitmentStep->id);

            if ($recruitmentStep->step_type != RecruitmentStep::STEP_TYPE_SHORTLIST) {
                $recruitmentStep['interview_scheduled'] = $this->countStepInterviewScheduledCandidate($jobId, $recruitmentStep->id);
                $recruitmentStep['rejected'] = $this->countStepRejectedCandidate($jobId, $recruitmentStep->id);
            }
        }

        $response['all_applications'] = [
            'total_candidate' => $this->countAllAppliedCandidate($jobId),
            'all' => $this->countAllApplicationAcceptedCandidate($jobId),
            'viewed' => $this->countProfileViewedCandidate($jobId),
            'not_viewed' => $this->countProfileNotViewedCandidate($jobId),
            'qualified' => $this->countStepQualifiedCandidate($jobId),
            'rejected' => $this->countStepRejectedCandidate($jobId)
        ];

        $response['final_hiring_list'] = [
            'total_candidate' => $this->countTotalFinalHiringListCandidate($jobId),
            'hire_selected' => $this->countHireSelectedCandidate($jobId),
            'hire_invited' => $this->countHireInvitedCandidate($jobId),
            'hired' => $this->countHiredCandidate($jobId),
        ];

        $response['steps'] = $recruitmentSteps->toArray() ?? [];


        return $response;

    }

    /**
     * @param string $jobId
     * @return mixed
     */
    public function countHireSelectedCandidate(string $jobId): mixed
    {
        return AppliedJob::where('apply_status', AppliedJob::APPLY_STATUS['Hiring_Listed'])
            ->whereNull('current_recruitment_step_id')
            ->where('job_id', $jobId)
            ->count('id');
    }

    /**
     * @param string $jobId
     * @return mixed
     */
    public function countHireInvitedCandidate(string $jobId): mixed
    {
        return AppliedJob::where('apply_status', AppliedJob::APPLY_STATUS['Hire_invited'])
            ->whereNull('current_recruitment_step_id')
            ->whereNotNull('hire_invited_at')
            ->where('job_id', $jobId)
            ->count('id');
    }

    /**
     * @param string $jobId
     * @return mixed
     */
    public function countHiredCandidate(string $jobId): mixed
    {
        return AppliedJob::where('apply_status', AppliedJob::APPLY_STATUS['Hired'])
            ->whereNull('current_recruitment_step_id')
            ->whereNotNull('hired_at')
            ->where('job_id', $jobId)
            ->count('id');
    }

    public function countAllAppliedCandidate(string $jobId)
    {
        return AppliedJob::where('job_id', $jobId)
            ->count('id');
    }


    public function countProfileViewedCandidate(string $jobId, int $stepId = null)
    {
        return AppliedJob::whereNotNull('profile_viewed_at')
            ->where(function ($query) {
                $query->where('applied_jobs.apply_status', '!=', AppliedJob::APPLY_STATUS['Rejected'])
                    ->whereNull('applied_jobs.current_recruitment_Step_id');
                $query->orwhereNotNull('applied_jobs.current_recruitment_step_id');

            })
            ->where('job_id', $jobId)
            ->count('id');
    }

    public function countProfileNotViewedCandidate(string $jobId, int $stepId = null)
    {
        return AppliedJob::whereNull('profile_viewed_at')
            ->where(function ($query) {
                $query->where('applied_jobs.apply_status', '!=', AppliedJob::APPLY_STATUS['Rejected'])
                    ->whereNull('applied_jobs.current_recruitment_Step_id');
                $query->orwhereNotNull('applied_jobs.current_recruitment_step_id');

            })
            ->where('job_id', $jobId)
            ->count('id');

    }

    public function countStepCandidate(string $jobId, int $stepId = null)
    {
        return AppliedJob::where('current_recruitment_step_id', $stepId)
            ->where('job_id', $jobId)
            ->count('id');
    }

    /**
     * @param string $jobId
     * @return mixed
     */
    public function countTotalFinalHiringListCandidate(string $jobId): mixed
    {
        return AppliedJob::whereIn('apply_status', [AppliedJob::APPLY_STATUS['Hiring_Listed'], [AppliedJob::APPLY_STATUS['Hire_invited']], [AppliedJob::APPLY_STATUS['Hired']]])
            ->whereNull('current_recruitment_step_id')
            ->where('job_id', $jobId)
            ->count('id');
    }

    public function countAllApplicationAcceptedCandidate(string $jobId)
    {
        /*return AppliedJob::where(function ($query) {
            $query->where('applied_jobs.apply_status', '!=', AppliedJob::APPLY_STATUS['Rejected'])
                ->whereNull('applied_jobs.current_recruitment_step_id');
        })
            ->orwhereNotNull('applied_jobs.current_recruitment_step_id')
            ->where('job_id', $jobId)
            ->count('id');*/
        return AppliedJob::where(function ($query) {
            $query->where('applied_jobs.apply_status', '!=', AppliedJob::APPLY_STATUS['Rejected']);
        })
            ->where('job_id', $jobId)
            ->count('id');
    }

    public function countStepShortlistedCandidate(string $jobId, int $stepId = null)
    {
        return AppliedJob::where('apply_status', AppliedJob::APPLY_STATUS['Shortlisted'])
            ->where('current_recruitment_step_id', $stepId)
            ->where('job_id', $jobId)
            ->count('id');
    }

    public function countStepInterviewScheduledCandidate(string $jobId, int $stepId)
    {
        return AppliedJob::where('apply_status', AppliedJob::APPLY_STATUS['Interview_scheduled'])
            ->where('current_recruitment_step_id', $jobId)
            ->where('job_id', $stepId)
            ->count('id');
    }

    public function countStepRejectedCandidate(string $jobId, int $stepId = null)
    {
        return AppliedJob::where('apply_status', AppliedJob::APPLY_STATUS['Rejected'])
            ->where('current_recruitment_step_id', $stepId)
            ->where('job_id', $jobId)
            ->count('id');
    }

    public function countStepQualifiedCandidate(string $jobId, int $stepId = 0)
    {
        return AppliedJob::where('current_recruitment_step_id', '>', $stepId)
            ->where('job_id', $jobId)
            ->count('id');
    }

    /**
     * @param Request $request
     * @return \Illuminate\Contracts\Validation\Validator
     */

    public function recruitmentStepCandidateListFilterValidator(Request $request): \Illuminate\Contracts\Validation\Validator
    {
        $customMessage = [
            'order.in' => 'Order must be within ASC or DESC.[30000]',
        ];

        if ($request->filled('order')) {
            $request->offsetSet('order', strtoupper($request->get('order')));
        }

        return Validator::make($request->all(), [

            'step_id' => [
                'nullable',
                'integer',
                'exists:recruitment_steps,id,deleted_at,NULL'
            ],
            'type' => [
                'nullable',
                'string',
                Rule::in(AppliedJob::CANDIDATE_LIST_FILTER_TYPES)
            ],
            'page' => 'integer|gt:0',
            'page_size' => 'integer|gt:0',
            'organization_type_id' => 'nullable|integer|gt:0',
            'order' => [
                'string',
                Rule::in([BaseModel::ROW_ORDER_ASC, BaseModel::ROW_ORDER_DESC])
            ],

        ], $customMessage);
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

        $youthSkillsIds = array_map(function ($skill) {
            return $skill["id"];
        }, $youthData["skills"]);

        if ($matchingCriteria["is_area_of_experience_enabled"]) {
            $areaOfExperienceMatch = false;
            foreach ($matchingCriteria["area_of_experiences"] as $areaOfExperience) {
                $areaOfExperienceMatch = $areaOfExperienceMatch || in_array($areaOfExperience["id"], $youthSkillsIds);
            }
            $requestData["area_of_experience_valid"] = $areaOfExperienceMatch ? 1 : 0;
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

        return $shouldMatchTotal == 0 ? 0 : $matchTotal / $shouldMatchTotal;
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


    /**
     * @param AppliedJob $appliedJob
     * @param array $data
     * @return CandidateInterview
     */
    public function updateInterviewedCandidate(AppliedJob $appliedJob, array $data): CandidateInterview
    {
        $appliedJob->apply_status = AppliedJob::APPLY_STATUS["Interviewed"];
        $appliedJob->save();


        $candidateInterview = CandidateInterview::where('applied_job_id', $appliedJob->id)
            ->where('recruitment_step_id', $appliedJob->current_recruitment_step_id)
            ->first();

        $candidateInterview->job_id = $appliedJob->job_id;
        $candidateInterview->is_candidate_present = $data['is_candidate_present'];
        $candidateInterview->interview_score = $data['interview_score'];

        $candidateInterview->save();

        return $candidateInterview;

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
     * @param AppliedJob $appliedJob
     * @return mixed
     */
    public function findFirstRecruitmentStep(AppliedJob $appliedJob): mixed
    {
        return RecruitmentStep::where('job_id', $appliedJob->job_id)->first();
    }

    /**
     * @param AppliedJob $appliedJob
     * @return mixed
     */
    public function findPreviousRecruitmentStep(AppliedJob $appliedJob): mixed
    {
        $currentRecruitmentStep = $appliedJob->current_recruitment_step_id;
        return RecruitmentStep::where('id', '<', $currentRecruitmentStep)
            ->orderBy('id', 'desc')
            ->first();
    }

    /**
     * @param AppliedJob $appliedJob
     * @return AppliedJob
     * @throws ValidationException
     */
    public function restoreRejectedCandidate(AppliedJob $appliedJob): AppliedJob
    {

        if ($appliedJob->apply_status == AppliedJob::APPLY_STATUS["Rejected"]) {
            if (!empty($appliedJob->current_recruitment_step_id)) {
                $appliedJob->apply_status = AppliedJob::APPLY_STATUS["Shortlisted"];
            } else {
                $appliedJob->apply_status = AppliedJob::APPLY_STATUS["Applied"];
            }
            $appliedJob->save();
        } else {
            throw ValidationException::withMessages(["Candidate apply_status must be rejected to restore"]);
        }

        return $appliedJob;

    }

    /**
     * @return int
     */
    public function getJobCount(): int
    {
        return PrimaryJobInformation::count('id');


    }

    /**
     * @param int $youthId
     * @return int
     */
    public function getAppliedJobCount(int $youthId): int
    {
        return AppliedJob::where('youth_id', $youthId)->count('id');
    }


    public function getSkillMatchingJobCount(array $skillIds)
    {

        return CandidateRequirement::whereHas('skills', function ($query) use ($skillIds) {
            $query->whereIn('skill_id', $skillIds);
        })->count('id');
    }

    /**
     * @param Request $request
     * @return \Illuminate\Contracts\Validation\Validator
     */
    public function hireInviteValidator(Request $request): \Illuminate\Contracts\Validation\Validator
    {
        $rules = [
            'hire_invite_type' => [
                'integer',
                'required',
                Rule::in(AppliedJob::INVITE_TYPES)
            ]
        ];

        return Validator::make($request->all(), $rules);

    }


    /**
     * @param AppliedJob $appliedJob
     * @param array $data
     * @return AppliedJob
     */
    public function hireInviteCandidate(AppliedJob $appliedJob, array $data): AppliedJob
    {
        $data['hire_invited_at'] = Carbon::now();
        $appliedJob->fill($data);
        $appliedJob->save();
        return $appliedJob;
    }

    /***
     * @param AppliedJob $appliedJob
     * @return AppliedJob
     */
    public function updateHiredCandidate(AppliedJob $appliedJob): AppliedJob
    {
        $appliedJob->hired_at = Carbon::now();
        $appliedJob->apply_status = AppliedJob::APPLY_STATUS["Hired"];
        $appliedJob->current_recruitment_step_id = null;
        $appliedJob->save();
        return $appliedJob;
    }


    /**
     * @param RecruitmentStep $recruitmentStep
     * @return bool
     */
    public function isRecruitmentStepDeletable(RecruitmentStep $recruitmentStep): bool
    {
        $maxStep = $this->findLastRecruitmentStep($recruitmentStep);
        $currentStepCandidates = $this->countCurrentRecruitmentStepCandidate($recruitmentStep);
        $finalHiringListCandidates = $this->countTotalFinalHiringListCandidate($recruitmentStep->job_id);

        return $maxStep == $recruitmentStep->id && $currentStepCandidates == 0 && $finalHiringListCandidates == 0;
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
     * Send candidate invite through sms
     * @param array $youth
     * @param string $smsMessage
     */
    public function sendCandidateInviteSms(array $youth, string $smsMessage)
    {
        $recipient = $youth['mobile'];
        $smsService = new SmsService();
        $smsService->sendSms($recipient, $smsMessage);
    }

    /**
     * @param array $youth
     * @param string $subject
     * @param string $message
     * @throws Throwable
     */
    public function sendCandidateInviteEmail(array $youth, string $subject, string $message)
    {
        /** Mail send */
        $to = array($youth['email']);
        $from = BaseModel::NISE3_FROM_EMAIL;
        $messageBody = MailService::templateView($message);
        $mailService = new MailService($to, $from, $subject, $messageBody);
        $mailService->sendMail();
    }

    /**
     * Send hiring listed candidate invite through sms
     * @param AppliedJob $appliedJob
     * @param array $youth
     */
    public function sendCandidateInterviewInviteSms(AppliedJob $appliedJob, array $youth)
    {
        $job = PrimaryJobInformation::where('job_id', $appliedJob->job_id)->first();
        $youthName = $youth['first_name'] . " " . $youth['last_name'];
        $smsMessage = "Hello, " . $youthName . " You have been selected for an interview for the " . $job->job_title . " role. You have been scheduled for the interview on " . ". We look forward to see your talents.";
        $this->sendCandidateInviteSms($youth, $smsMessage);
    }

    /**
     * @param AppliedJob $appliedJob
     * @param array $youth
     * @throws Throwable
     */
    public function sendCandidateInterviewInviteEmail(AppliedJob $appliedJob, array $youth)
    {
        $job = PrimaryJobInformation::where('job_id', $appliedJob->job_id)->first();
        /** Mail send */
        $youthName = $youth['first_name'] . " " . $youth['last_name'];
        $subject = "Job Offer letter";
        $message = "Hello, " . $youthName . " You have been selected for an interview for the " . $job->job_title . " role. You have been scheduled for the interview on " . ". We look forward to see your talents.";
        $this->sendCandidateInviteEmail($youth, $subject, $message);
    }


    /**
     * Send hiring listed candidate invite through sms
     * @param AppliedJob $appliedJob
     * @param array $youth
     */
    public function sendCandidateHireInviteSms(AppliedJob $appliedJob, array $youth)
    {
        $job = PrimaryJobInformation::where('job_id', $appliedJob->job_id)->first();
        $youthName = $youth['first_name'] . " " . $youth['last_name'];
        $smsMessage = "Congratulation, " . $youthName . " You have been admitted for the " . $job->job_title . " role.We are eager to have you as part of our team.We look forward to hearing your decision on our offer";
        $this->sendCandidateInviteSms($youth, $smsMessage);
    }

    /**
     * @param AppliedJob $appliedJob
     * @param array $youth
     * @throws Throwable
     */
    public function sendCandidateHireInviteEmail(AppliedJob $appliedJob, array $youth)
    {
        $job = PrimaryJobInformation::where('job_id', $appliedJob->job_id)->first();
        /** Mail send */
        $youthName = $youth['first_name'] . " " . $youth['last_name'];
        $subject = "Job Offer letter";
        $message = "Congratulation, " . $youthName . " You have been admitted for the " . $job->job_title . " role.We are eager to have you as part of our team.We look forward to hearing your decision on our offer";
        $this->sendCandidateInviteEmail($youth, $subject, $message);
    }


}
