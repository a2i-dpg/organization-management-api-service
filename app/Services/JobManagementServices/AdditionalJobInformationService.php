<?php

namespace App\Services\JobManagementServices;

use App\Models\AdditionalJobInformation;
use App\Models\LocDistrict;
use App\Models\LocDivision;
use App\Models\LocUpazila;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

/**
 *
 */
class AdditionalJobInformationService
{

    public function getAdditionalJobInformationDetails(string $jobId){

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
        $additionalJobInformation = new AdditionalJobInformation();
        $additionalJobInformation->fill($validatedData);
        $additionalJobInformation->save();
        return $additionalJobInformation;
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
            'loc_districts.id',
            'loc_districts.loc_division_id',
            'loc_districts.title',
            'loc_districts.title_en',
            'loc_districts.is_sadar_district',
            'loc_divisions.title as division_title',
            'loc_divisions.title_en as division_title_en',
        ]);

        $districtsBuilder->leftJoin('loc_divisions', function ($join) {
            $join->on('loc_divisions.id', '=', 'loc_districts.loc_division_id')
                ->whereNull('loc_divisions.deleted_at');
        });
        $districts = $districtsBuilder->get();


        /** @var LocUpazila|Builder $upazilasBuilder */
        $upazilasBuilder = LocUpazila::select([
            'loc_upazilas.id',
            'loc_upazilas.title',
            'loc_upazilas.title_en',
            'loc_upazilas.loc_district_id',
            'loc_districts.title as district_title',
            'loc_districts.title_en as district_title_en',
            'loc_upazilas.loc_division_id',
            'loc_divisions.title as division_title',
            'loc_divisions.title_en as division_title_en'
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

        foreach ($divisions as $division) {
            $key = $division->id;
            $jobLocation[$key] = strtoupper($division->title_en . "(" . $division->title . ")");
        }

        foreach ($districts as $district) {
            $key = $district->loc_division_id . "_" . $district->id;
            $titleEn = $district->division_title_en . " => " . $district->title_en;
            $titleBn = " (" . $district->division_title . " => " . $district->title . ")";
            if ($district->is_sadar_district) {
                $titleEn = $district->division_title_en . " => " . $district->title_en . "(Zilla Sadar)";
                $titleBn = " (" . $district->division_title . " => " . $district->title . "(জেলা সদর))";
            }
            $jobLocation[$key] = strtoupper($titleEn . $titleBn);
        }

        foreach ($upazilas as $upazila) {
            $key = $upazila->loc_division_id . "_" . $upazila->loc_district_id . "_" . $upazila->id;
            $titleEn = $upazila->division_title_en . " => " . $upazila->district_title_en . " => " . $upazila->title_en;
            $titleBn = " (" . $upazila->division_title . " => " . $upazila->district_title . " => " . $upazila->title . ")";
            $jobLocation[$key] = strtoupper($titleEn . $titleBn);
        }

        return $jobLocation;
    }

    /**
     * @param AdditionalJobInformation $additionalJobInformation
     * @param array $jobLevel
     */
    public function syncWithJobLevel(AdditionalJobInformation $additionalJobInformation, array $jobLevel)
    {
        foreach ($jobLevel as $item) {
            DB::table('additional_job_information_job_level')->updateOrInsert(
                [
                    'additional_job_information_id' => $additionalJobInformation->id,
                    'job_level_id' => $item
                ],
                [
                    'additional_job_information_id' => $additionalJobInformation->id,
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
        foreach ($workPlace as $item) {
            DB::table('additional_job_information_work_place')->updateOrInsert(
                [
                    'additional_job_information_id' => $additionalJobInformation->id,
                    'work_place_id' => $item
                ],
                [
                    'additional_job_information_id' => $additionalJobInformation->id,
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
        foreach ($jobLocation as $item) {
            $locIds = explode('_',$item);
            $locDivisionId = $locIds[0];
            $locDistrictId = $locIds[1];
            $locUpazilaId = $locIds[2];
            DB::table('additional_job_information_job_location')->updateOrInsert(
                [
                    'additional_job_information_id' => $additionalJobInformation->id,
                    'loc_division_id' => $locDivisionId,
                    'loc_district_id' => $locDistrictId,
                    'loc_upazila_id' => $locUpazilaId
                ],
                [
                    'additional_job_information_id' => $additionalJobInformation->id,
                    'loc_division_id' => $locDivisionId,
                    'loc_district_id' => $locDistrictId,
                    'loc_upazila_id' => $locUpazilaId

                ]
            );
        }
    }


    /**
     * @param Request $request
     * @return \Illuminate\Contracts\Validation\Validator
     */
    public function validator(Request $request): \Illuminate\Contracts\Validation\Validator
    {
        $data = $request->all();

        $data["other_benefits"] = is_array($data['other_benefits']) ? $data['other_benefits'] : explode(',', $data['other_benefits']);
        $data["job_level"] = is_array($data['job_level']) ? $data['job_level'] : explode(',', $data['job_level']);
        $data["work_place"] = is_array($data['work_place']) ? $data['work_place'] : explode(',', $data['work_place']);
        $data["job_location"] = is_array($data['job_location']) ? $data['job_location'] : explode(',', $data['job_location']);

        $rules = [
            "job_id" => [
                "required",
                "exists:primary_job_information,job_id,deleted_at,NULL",
            ],
            "job_responsibilities" => [
                "nullable"
            ],
            "job_content" => [
                "required"
            ],
            "job_place_type" => [
                "required",
                Rule::in(array_keys(AdditionalJobInformation::JOB_PLACE_TYPE))
            ],
            "salary_min" => [
                "nullable",
                "numeric"
            ],
            "salary_max" => [
                "nullable",
                "numeric"
            ],
            "is_salary_info_show" => [
                "required",
                Rule::in(array_keys(AdditionalJobInformation::IS_SALARY_SHOW))
            ],
            "is_salary_compare_to_expected_salary" => [
                "nullable",
                Rule::in(array_keys(AdditionalJobInformation::BOOLEN_FLAG))
            ],
            "is_salary_alert_excessive_than_given_salary_range" => [
                "required",
                Rule::in(array_keys(AdditionalJobInformation::BOOLEN_FLAG))
            ],
            "salary_review" => [
                "required",
                Rule::in(array_keys(AdditionalJobInformation::SALARY_REVIEW))
            ],
            "festival_bonus" => [
                "required",
                Rule::in(array_keys(AdditionalJobInformation::FESTIVAL_BONUS))
            ],
            "additional_salary_info" => [
                "nullable"
            ],
            "is_other_benefits" => [
                "required",
                Rule::in(array_keys(AdditionalJobInformation::BOOLEN_FLAG))
            ],
            "other_benefits" => [
                Rule::requiredIf(function () use ($request) {
                    return $request->is_other_benefits == AdditionalJobInformation::BOOLEN_FLAG[1];
                }),
                "nullable",
                "array"
            ],
            "lunch_facilities" => [
                "required",
                Rule::in(array_keys(AdditionalJobInformation::LUNCH_FACILITIES))
            ],
            "others" => [
                "nullable"
            ],
            "job_level" => [
                "required",
                "array"
            ],
            "job_level.*" => [
                "required",
                Rule::in(array_keys(AdditionalJobInformation::JOB_LEVEL))
            ],
            "work_place" => [
                "required",
                "array"
            ],
            "work_place.*" => [
                "required",
                Rule::in(array_keys(AdditionalJobInformation::WORK_PLACE))
            ],
            "job_location" => [
                "required",
                "array"
            ]

        ];
        return Validator::make($data, $rules);
    }

}
