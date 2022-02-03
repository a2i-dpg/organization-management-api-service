<?php

namespace App\Services\JobManagementServices;


use App\Facade\ServiceToServiceCall;
use App\Jobs\Job;
use App\Models\AdditionalJobInformation;
use App\Models\AppliedJob;
use App\Models\BaseModel;
use App\Models\CandidateRequirement;
use App\Models\CompanyInfoVisibility;
use App\Models\JobContactInformation;
use App\Models\MatchingCriteria;
use App\Models\PrimaryJobInformation;
use App\Models\RecruitmentStep;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
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
     * @param array $request
     * @param Carbon $startTime
     * @return array
     */
    public function getJobList(array $request, Carbon $startTime): array
    {
        $titleEn = $request['title_en'] ?? "";
        $title = $request['title'] ?? "";
        $paginate = $request['page'] ?? "";
        $pageSize = $request['page_size'] ?? "";
        $skillIds = $request['skill_ids'] ?? [];
        $jobSectorIds = $request['job_sector_ids'] ?? [];
        $occupationIds = $request['occupation_ids'] ?? [];
        $locDistrictIds = $request['loc_district_ids'] ?? [];
        $industryAssociationId = $request['industry_association_id'] ?? "";
        $instituteId = $request['institute_id'] ?? "";
        $organizationId = $request['organization_id'] ?? "";
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
            'primary_job_information.occupation_id',
            'primary_job_information.job_sector_id',
            'primary_job_information.industry_association_id',
            'primary_job_information.organization_id',
            'primary_job_information.institute_id',
            'primary_job_information.application_deadline',
            'primary_job_information.published_at',
            'primary_job_information.archived_at',
            'primary_job_information.application_deadline',
            'primary_job_information.id',

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

        ])->acl();

        if (!$type == PrimaryJobInformation::JOB_FILTER_TYPE_POPULAR) {
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

        if (!empty($titleEn)) {
            $jobInformationBuilder->where('primary_job_information.title_en', 'like', '%' . $titleEn . '%');
        }
        if (!empty($title)) {
            $jobInformationBuilder->where('primary_job_information.title', 'like', '%' . $title . '%');
        }


        $jobInformationBuilder->leftJoin('applied_jobs', function ($join) {
            $join->on('primary_job_information.job_id', '=', 'applied_jobs.job_id')
                ->whereNull('applied_jobs.deleted_at');
        });

        $jobInformationBuilder->leftJoin('industry_associations', function ($join) {
            $join->on('primary_job_information.industry_association_id', '=', 'industry_associations.id')
                ->whereNull('industry_associations.deleted_at')
                ->whereNotNull('primary_job_information.industry_association_id');
        });

        $jobInformationBuilder->leftJoin('organizations', function ($join) {
            $join->on('primary_job_information.organization_id', '=', 'organizations.id')
                ->whereNull('organizations.deleted_at')
                ->whereNotNull('primary_job_information.organization_id');
        });

        if (!empty($type) && $type == PrimaryJobInformation::JOB_FILTER_TYPE_RECENT) {

            $jobInformationBuilder->whereDate('primary_job_information.published_at', '>', $startTime->subDays(7)->endOfDay());
            $jobInformationBuilder->whereDate('primary_job_information.application_deadline', '>', $startTime);
            $jobInformationBuilder->active();
        }

        if (!empty($type) && $type == PrimaryJobInformation::JOB_FILTER_TYPE_POPULAR) {
            $jobInformationBuilder->whereDate('primary_job_information.published_at', '<=', $startTime);
            $jobInformationBuilder->whereDate('primary_job_information.application_deadline', '>', $startTime);
            $jobInformationBuilder->orderBy(DB::raw('count(applied_jobs.id)'), 'DESC');
            $jobInformationBuilder->groupBy('applied_jobs.job_id');
            $jobInformationBuilder->active();
        }


        if (is_array($skillIds) && count($skillIds) > 0) {
            $skillMatchingJobIds = DB::table('candidate_requirement_skill')->whereIn('skill_id', $skillIds)->pluck('job_id');
            $jobInformationBuilder->whereIn('job_id', $skillMatchingJobIds);
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

        $jobInformationBuilder->with('candidateRequirement.degrees:candidate_requirement_id,education_level_id,exam_degree_id,major_subject');


        if (is_array($locDistrictIds) && count($locDistrictIds) > 0) {
            $jobInformationBuilder->with(['additionalJobInformation.jobLocations' => function ($query) use ($locDistrictIds) {
                $query->whereIn('additional_job_information_job_locations.loc_district_id', $locDistrictIds);
            }]);
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
    public
    function jobListFilterValidator(Request $request): \Illuminate\Contracts\Validation\Validator
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
            'job_title_en' => 'nullable|max:300|min:2',
            'job_title' => 'nullable|max:500|min:2',
            'page' => 'nullable|integer|gt:0',
            'page_size' => 'nullable|integer|gt:0',

            'industry_association_id' => 'nullable|integer',
            'organization_id' => 'nullable|integer',
            'institute_id' => 'nullable|integer',

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

    public
    function lastAvailableStep(string $jobId): int
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
    public
    function storeAppliedJob(array $data): array
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
    public
    function rejectCandidate(int $applicationId): AppliedJob
    {
        $appliedJob = AppliedJob::findOrFail($applicationId);

        $appliedJob->apply_status = AppliedJob::APPLY_STATUS["Rejected"];
        $appliedJob->rejected_from = $appliedJob->apply_status;
        $appliedJob->rejected_at = Carbon::now();
        $appliedJob->save();

        return $appliedJob;
    }

    /**
     * Shortlist a candidate for next interview step
     * @param int $applicationId
     * @return AppliedJob
     * @throws Throwable
     */
    public
    function shortlistCandidate(int $applicationId): AppliedJob
    {
        $appliedJob = AppliedJob::findOrFail($applicationId);

        if ($appliedJob->apply_status == AppliedJob::APPLY_STATUS["Applied"]) {
            $appliedJob->apply_status = AppliedJob::APPLY_STATUS["Shortlisted"];
            $appliedJob->shortlisted_at = Carbon::now();
        } else {
            throw ValidationException::withMessages(['candidate can not be selected for  next step']);
        }
        $appliedJob->save();

        return $appliedJob;
    }

    /**
     * @throws ValidationException
     */
    public
    function inviteCandidateForInterview(int $applicationId)
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

    public
    function storeRecruitmentStep(string $jobId, array $data)
    {
        $recruitmentStep = app(RecruitmentStep::class);
        $data['job_id'] = $jobId;
        $recruitmentStep->fill($data);
        $recruitmentStep->save();
        return $recruitmentStep;
    }

    public
    function updateRecruitmentStep(RecruitmentStep $recruitmentStep, array $data): RecruitmentStep
    {
        $recruitmentStep->fill($data);
        $recruitmentStep->save();
        return $recruitmentStep;
    }

    /**
     * @param int $stepId
     * @return Model|Builder
     */
    public
    function getRecruitmentStep(int $stepId): Model|Builder
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
    public
    function deleteRecruitmentStep(RecruitmentStep $recruitmentStep): bool
    {
        return $recruitmentStep->delete();

    }

    public
    function isLastRecruitmentStep(RecruitmentStep $recruitmentStep): bool
    {
        $maxStep = RecruitmentStep::where('job_id', $recruitmentStep->job_id)
            ->max('id');

        $currentStepCandidates = AppliedJob::where('job_id', $recruitmentStep->job_id)
            ->where('current_recruitment_step_id', $recruitmentStep->job_id)
            ->count('id');

        return $maxStep == $recruitmentStep->id && $currentStepCandidates == 0;
    }


    public
    function recruitmentStepStoreValidator(Request $request): \Illuminate\Contracts\Validation\Validator
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
    public
    function recruitmentStepUpdateValidator(Request $request): \Illuminate\Contracts\Validation\Validator
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
    public
    function applyJobValidator(Request $request): \Illuminate\Contracts\Validation\Validator
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

    public
    function getCandidateList(Request $request, string $jobId, int $status = 0): array|null
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

    public
    function getMatchPercent($requestData, $youthData, $matchingCriteria): float|int
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

        return $matchTotal / $shouldMatchTotal;
    }

}
