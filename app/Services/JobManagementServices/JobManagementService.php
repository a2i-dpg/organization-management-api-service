<?php

namespace App\Services\JobManagementServices;


use App\Facade\ServiceToServiceCall;
use App\Models\AdditionalJobInformation;
use App\Models\AppliedJob;
use App\Models\BaseModel;
use App\Models\CandidateRequirement;
use App\Models\CompanyInfoVisibility;
use App\Models\JobContactInformation;
use App\Models\MatchingCriteria;
use App\Models\PrimaryJobInformation;
use App\Models\RecruitmentStep;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
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
    public function shortlistCandidate(int $applicationId): AppliedJob
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

    public function storeRecruitmentStep(string $jobId, array $data)
    {
        $recruitmentStep = app(RecruitmentStep::class);
        $recruitmentStep->fill($data);
        $recruitmentStep->save();
        return $recruitmentStep;
    }


    public function recruitmentStepValidator(Request $request): \Illuminate\Contracts\Validation\Validator
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
            'age_valid.in' => 'Age must be valid. [30000]',
            'experience_valid.in' => 'Experience must be valid. [30000]',
            'gender_valid.in' => 'Gender must be valid. [30000]',
            'location_valid.in' => 'Location must be valid. [30000]'
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
            'applied_jobs.rejected_from',
            'applied_jobs.applied_at',
            'applied_jobs.profile_viewed_at',
            'applied_jobs.rejected_at',
            'applied_jobs.shortlisted_at',
            'applied_jobs.interview_invited_at',
            'applied_jobs.interview_scheduled_at',
            'applied_jobs.interviewed_at',
            'applied_jobs.expected_salary',
            'applied_jobs.hire_invited_at',
            'applied_jobs.hired_at',
            'applied_jobs.interview_invite_source',
            'applied_jobs.interview_invite_type',
            'applied_jobs.hire_invite_type',
            'applied_jobs.interview_score',
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
        $youthProfiles = ServiceToServiceCall::getYouthProfilesByIds($youthIds);
        $indexedYouths = [];

        foreach ($youthProfiles as $item) {
            $id = $item['id'];
            $indexedYouths[$id . ""] = $item;
        }

        foreach ($resultArray["data"] as &$item) {
            $id = $item['youth_id'] . "";
            $item['youth_profile'] = $indexedYouths[$id];
        }

        $response['order'] = $order;
        $response['data'] = $resultArray['data'] ?? $resultArray;

        return $response;
    }

}
