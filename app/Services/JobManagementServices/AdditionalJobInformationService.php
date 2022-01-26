<?php

namespace App\Services\JobManagementServices;

use App\Models\AdditionalJobInformation;
use App\Models\AdditionalJobInformationJobLocation;
use App\Models\LocDistrict;
use App\Models\LocDivision;
use App\Models\LocUpazila;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

/**
 *
 */
class AdditionalJobInformationService
{

    public function getAdditionalJobInformationDetails(string $jobId): AdditionalJobInformation | null
    {
        /** @var AdditionalJobInformation|Builder $additionalJobInfoBuilder */
        $additionalJobInfoBuilder = AdditionalJobInformation::select([
            'additional_job_information.id',
            'additional_job_information.job_id',
            'additional_job_information.job_responsibilities',
            'additional_job_information.job_responsibilities_en',
            'additional_job_information.job_context',
            'additional_job_information.job_context_en',
            'additional_job_information.job_place_type',
            'additional_job_information.salary_min',
            'additional_job_information.salary_max',
            'additional_job_information.is_salary_info_show',
            'additional_job_information.is_salary_compare_to_expected_salary',
            'additional_job_information.is_salary_alert_excessive_than_given_salary_range',
            'additional_job_information.salary_review',
            'additional_job_information.festival_bonus',
            'additional_job_information.additional_salary_info',
            'additional_job_information.additional_salary_info_en',
            'additional_job_information.is_other_benefits',
            'additional_job_information.lunch_facilities',
            'additional_job_information.others',
            'additional_job_information.created_at',
            'additional_job_information.updated_at',
        ]);

        $additionalJobInfoBuilder->where('additional_job_information.job_id', $jobId);

        $additionalJobInfoBuilder->with(['jobLevels', 'jobLocations', 'workPlaces', 'otherBenefits']);

        return $additionalJobInfoBuilder->first();

    }

    /**
     * @return array
     */
    public function getJobLocation(): array
    {
        return Cache::rememberForever("JOB_LOCATION_FOR_JOB_POSTING", function () {
            return $this->getLocationData();
        });

    }


    /**
     * @param array $validatedData
     * @return AdditionalJobInformation
     */
    public function store(array $validatedData): AdditionalJobInformation
    {
        return AdditionalJobInformation::updateOrCreate(
            ['job_id' => $validatedData['job_id']],
            $validatedData
        );
    }

    /**
     * @return array
     */
    private function getLocationData(): array
    {
        $jobLocation = [];
        $divisions = LocDivision::all();

        /** @var Builder $districtsBuilder */
        $districtsBuilder = LocDistrict::select([
            'loc_districts.id as loc_district_id',
            'loc_districts.title as loc_district_title',
            'loc_districts.title_en as loc_district_title_en',

            'loc_districts.is_sadar_district',

            'loc_districts.loc_division_id',
            'loc_divisions.title as loc_division_title',
            'loc_divisions.title_en as loc_division_title_en',
        ]);

        $districtsBuilder->leftJoin('loc_divisions', function ($join) {
            $join->on('loc_divisions.id', '=', 'loc_districts.loc_division_id')
                ->whereNull('loc_divisions.deleted_at');
        });
        $districts = $districtsBuilder->get();


        /** @var LocUpazila|Builder $upazilasBuilder */
        $upazilasBuilder = LocUpazila::select([
            'loc_upazilas.id as loc_area_id',
            'loc_upazilas.title as loc_area_title',
            'loc_upazilas.title_en as loc_area_title_en',

            'loc_upazilas.loc_district_id',
            'loc_districts.title as loc_district_title',
            'loc_districts.title_en as loc_district_title_en',

            'loc_upazilas.loc_division_id',
            'loc_divisions.title as loc_division_title',
            'loc_divisions.title_en as loc_division_title_en'
        ]);

        $upazilasBuilder->leftJoin('loc_divisions', function ($join) {
            $join->on('loc_divisions.id', '=', 'loc_upazilas.loc_division_id')
                ->whereNull('loc_divisions.deleted_at');
        });

        $upazilasBuilder->leftJoin('loc_districts', function ($join) {
            $join->on('loc_upazilas.loc_district_id', '=', 'loc_districts.id')
                ->whereNull('loc_districts.deleted_at');
        });

        $upazilas = $upazilasBuilder->get();

        /** LocDivision */
        foreach ($divisions as $division) {
            $key = $division->id;
            $locInfoFormat = [
                "loc_division_id" => $key,
                "loc_division_title" => $division->title,
                "loc_division_title_en" => $division->title_en
            ];
            $tempData = AdditionalJobInformationJobLocation::getJobLocationId($locInfoFormat);
            $key = $tempData['location_id'];
            $jobLocation[$key] = $tempData;
        }

        /** LocDistrict */
        foreach ($districts as $district) {
            $tempData = AdditionalJobInformationJobLocation::getJobLocationId($district->toArray());
            $key = $tempData['location_id'];
            $jobLocation[$key] = $tempData;
        }
        /** LocUpazila */
        foreach ($upazilas as $upazila) {
            $tempData = AdditionalJobInformationJobLocation::getJobLocationId($upazila->toArray());
            $key = $tempData['location_id'];
            $jobLocation[$key] = $tempData;
        }
        Log::info("===>", $jobLocation);
        return $jobLocation;
    }

    /**
     * @param AdditionalJobInformation $additionalJobInformation
     * @param array $jobLevel
     */
    public function syncWithJobLevel(AdditionalJobInformation $additionalJobInformation, array $jobLevel)
    {
        DB::table('additional_job_information_job_levels')->where('additional_job_information_id', $additionalJobInformation->id)->delete();
        foreach ($jobLevel as $item) {
            DB::table('additional_job_information_job_levels')->insert(
                [
                    'additional_job_information_id' => $additionalJobInformation->id,
                    'job_id' => $additionalJobInformation->job_id,
                    'job_level_id' => $item
                ]
            );
        }

    }

    /**
     * @param AdditionalJobInformation $additionalJobInformation
     * @param array $workPlace
     */
    public function syncWithWorkplace(AdditionalJobInformation $additionalJobInformation, array $workPlace)
    {
        DB::table('additional_job_information_work_places')->where('additional_job_information_id', $additionalJobInformation->id)->delete();
        foreach ($workPlace as $item) {
            DB::table('additional_job_information_work_places')->insert(
                [
                    'additional_job_information_id' => $additionalJobInformation->id,
                    'job_id' => $additionalJobInformation->job_id,
                    'work_place_id' => $item
                ]
            );
        }
    }

    /**
     * @param AdditionalJobInformation $additionalJobInformation
     * @param array $jobLocation
     */
    public function syncWithJobLocation(AdditionalJobInformation $additionalJobInformation, array $jobLocation)
    {
        DB::table('additional_job_information_job_locations')->where('additional_job_information_id', $additionalJobInformation->id)->delete();
        foreach ($jobLocation as $item) {
            $locIds = getLocationIdByKeyString($item);
            $jobLocationInfo = $this->getJobLocationFormat($locIds);
            $jobLocationInfo['additional_job_information_id'] = $additionalJobInformation->id;
            $jobLocationInfo['job_id'] = $additionalJobInformation->job_id;
            DB::table('additional_job_information_job_locations')->insert($jobLocationInfo);
        }
    }

    /**
     * @param AdditionalJobInformation $additionalJobInformation
     * @param array $otherBenefit
     */
    public function syncWithOtherBenefit(AdditionalJobInformation $additionalJobInformation, array $otherBenefit)
    {
        $additionalJobInformation->otherBenefits()->syncWithPivotValues($otherBenefit, ['job_id' => $additionalJobInformation->job_id]);
    }

    private function getJobLocationFormat(array $locIds): array
    {
        $locIdSize = sizeof($locIds);
        $locInfo = [];
        if ($locIdSize == 1) {
            $locInfo = [
                'loc_division_id' => $locIds[AdditionalJobInformation::DIVISION_ID_KEY]
            ];
        } elseif ($locIdSize == 2) {
            $locInfo = [
                'loc_division_id' => $locIds[AdditionalJobInformation::DIVISION_ID_KEY],
                'loc_district_id' => $locIds[AdditionalJobInformation::DISTRICT_ID_KEY]
            ];
        } elseif ($locIdSize == 3) {
            $locInfo = [
                'loc_division_id' => $locIds[AdditionalJobInformation::DIVISION_ID_KEY],
                'loc_district_id' => $locIds[AdditionalJobInformation::DISTRICT_ID_KEY]
            ];
            $this->getUpazilaOrCityCorporationId($locIds[AdditionalJobInformation::UPAZILA_OR_CITY_CORPORATION_ID_KEY], $locInfo);

        } elseif ($locIdSize == 4) {
            $locInfo = [
                'loc_division_id' => $locIds[AdditionalJobInformation::DIVISION_ID_KEY],
                'loc_district_id' => $locIds[AdditionalJobInformation::DISTRICT_ID_KEY]
            ];
            $this->getUpazilaOrCityCorporationId($locIds[AdditionalJobInformation::UPAZILA_OR_CITY_CORPORATION_ID_KEY], $locInfo);
            $this->getUnionOrCityCorporationWardId($locIds[AdditionalJobInformation::UNION_OR_CITY_CORPORATION_WARD_ID_KEY], $locInfo);
        }
        return $locInfo;
    }

    private function getUpazilaOrCityCorporationId(string $locUpazilaLevelInfoId, array &$locInfo)
    {
        $upazilaOrCityCorporationId = explode(AdditionalJobInformation::CITY_CORPORATION_IDENTITY_SYMBOL, $locUpazilaLevelInfoId);

        if (sizeof($upazilaOrCityCorporationId) == 2) {
            $locInfo['loc_city_corporation_id'] = $upazilaOrCityCorporationId[0];
        } else if (sizeof($upazilaOrCityCorporationId) == 1) {
            $locInfo['loc_upazila_id'] = $upazilaOrCityCorporationId[0];
        }
    }

    private function getUnionOrCityCorporationWardId(string $locUpazilaLevelInfoId, array &$locInfo)
    {
        $upazilaOrCityCorporationId = explode(AdditionalJobInformation::CITY_CORPORATION_IDENTITY_SYMBOL, $locUpazilaLevelInfoId);

        if (sizeof($upazilaOrCityCorporationId) == 2) {
            $locInfo['loc_city_corporation_ward_id'] = $upazilaOrCityCorporationId[0];
        } else if (sizeof($upazilaOrCityCorporationId) == 1) {
            $locInfo['loc_union_id'] = $upazilaOrCityCorporationId[0];
        }
    }

    /**
     * @param Request $request
     * @return \Illuminate\Contracts\Validation\Validator
     */
    public function validator(Request $request): \Illuminate\Contracts\Validation\Validator
    {

        $data = $request->all();
        if (!empty($data["other_benefits"])) {
            $data["other_benefits"] = is_array($data['other_benefits']) ? $data['other_benefits'] : explode(',', $data['other_benefits']);
        }
        if (!empty($data["job_levels"])) {
            $data["job_levels"] = is_array($data['job_levels']) ? $data['job_levels'] : explode(',', $data['job_levels']);
        }
        if (!empty($data["work_places"])) {
            $data["work_places"] = is_array($data['work_places']) ? $data['work_places'] : explode(',', $data['work_places']);
        }
        if (!empty($data["job_locations"])) {
            $data["job_locations"] = is_array($data['job_locations']) ? $data['job_locations'] : explode(',', $data['job_locations']);
        }

        $rules = [
            "job_id" => [
                "required",
                "exists:primary_job_information,job_id,deleted_at,NULL",
            ],
            "job_responsibilities" => [
                "required"
            ],
            "job_responsibilities_en" => [
                "string",
                "nullable"
            ],
            "job_context" => [
                "string",
                "nullable"
            ],
            "job_context_en" => [
                "string",
                "nullable"
            ],
            "job_place_type" => [
                "required",
                Rule::in(array_keys(AdditionalJobInformation::JOB_PLACE_TYPE))
            ],
            "salary_min" => [
                Rule::requiredIf(function () use ($request) {
                    return $request->offsetGet("is_salary_info_show") == 1;
                }),
                "numeric"
            ],
            "salary_max" => [
                Rule::requiredIf(function () use ($request) {
                    return $request->offsetGet("is_salary_info_show") == 1;
                }),
                "numeric"
            ],
            "is_salary_info_show" => [
                "required",
                Rule::in(array_keys(AdditionalJobInformation::IS_SALARY_SHOW))
            ],
            "is_salary_compare_to_expected_salary" => [
                "nullable",
                Rule::in(array_keys(AdditionalJobInformation::BOOLEAN_FLAG))
            ],
            "is_salary_alert_excessive_than_given_salary_range" => [
                "required",
                Rule::in(array_keys(AdditionalJobInformation::BOOLEAN_FLAG))
            ],
            "additional_salary_info" => [
                "string",
                "nullable"
            ],
            "additional_salary_info_en" => [
                "string",
                "nullable"
            ],
            "is_other_benefits" => [
                "required",
                Rule::in(array_keys(AdditionalJobInformation::BOOLEAN_FLAG))
            ],
            "other_benefits" => [
                Rule::requiredIf(function () use ($request) {
                    return $request->offsetGet("is_other_benefits") == AdditionalJobInformation::BOOLEAN_FLAG[1];
                }),
                "nullable",
                "array",
                "min:1"
            ],
            "other_benefits.*" => [
                "integer",
                "exists:other_benefits,id,deleted_at,NULL"
            ],
            "lunch_facilities" => [
                "nullable",
                Rule::in(array_keys(AdditionalJobInformation::LUNCH_FACILITIES))
            ],
            "salary_review" => [
                "nullable",
                Rule::in(array_keys(AdditionalJobInformation::SALARY_REVIEW))
            ],
            "festival_bonus" => [
                "nullable",
                Rule::in(array_keys(AdditionalJobInformation::FESTIVAL_BONUS))
            ],
            "others" => [
                "nullable"
            ],
            "job_levels" => [
                "required",
                "array"
            ],
            "job_levels.*" => [
                "required",
                Rule::in(array_keys(AdditionalJobInformation::JOB_LEVEL))
            ],
            "work_places" => [
                "required",
                "array"
            ],
            "work_places.*" => [
                "required",
                Rule::in(array_keys(AdditionalJobInformation::WORK_PLACE))
            ],
            "job_locations" => [
                "required",
                "array"
            ],
            "job_locations.*" => [
                'required',
                Rule::in(array_keys($this->getJobLocation()))
            ]
        ];
        return Validator::make($data, $rules);
    }


}
