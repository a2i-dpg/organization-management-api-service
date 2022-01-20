<?php

namespace App\Services\JobManagementServices;


use App\Facade\ServiceToServiceCall;
use App\Models\AdditionalJobInformation;
use App\Models\AppliedJob;
use App\Models\CandidateRequirement;
use App\Models\CompanyInfoVisibility;
use App\Models\JobContactInformation;
use App\Models\MatchingCriteria;
use App\Models\PrimaryJobInformation;
use Carbon\Carbon;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;

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
        $isMatchingCriteriaComplete = (bool)MatchingCriteria::where('job_id', $jobId)->count('id');
        $step = $isPrimaryJobInformationComplete && $isAdditionalJobInformationComplete && $isCandidateRequirementComplete && $isMatchingCriteriaComplete ? 5 : $step;
        $isCompanyInfoVisibilityComplete = (bool)CompanyInfoVisibility::where('job_id', $jobId)->count('id');
        $step = $isPrimaryJobInformationComplete && $isAdditionalJobInformationComplete && $isCandidateRequirementComplete && $isMatchingCriteriaComplete && $isCompanyInfoVisibilityComplete ? 6 : $step;
        $isJobContactInformationComplete = (bool)JobContactInformation::where('job_id', $jobId)->count('id');
        $step = $isPrimaryJobInformationComplete && $isAdditionalJobInformationComplete && $isCandidateRequirementComplete && $isMatchingCriteriaComplete && $isCompanyInfoVisibilityComplete && $isJobContactInformationComplete ? 7 : $step;

        return $step;
    }

    /**
     * @param array $data
     * @return AppliedJob
     */
    public function storeAppliedJob(array $data): Array
    {
        $jobId = $data['job_id'];
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
            ]
        )->toArray();
    }

    /**
     * @param Request $request
     * @return Validator
     */
    public function applyJobValidator(Request $request): Validator
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
        ];

        $customMessage = [
            'age_valid.in' => 'Age must be valid. [30000]',
            'experience_valid.in' => 'Experience must be valid. [30000]',
            'gender_valid.in' => 'Gender must be valid. [30000]',
            'location_valid.in' => 'Location must be valid. [30000]'
        ];

        return \Illuminate\Support\Facades\Validator::make($requestData, $rules, $customMessage);
    }

}
