<?php

namespace App\Services\JobManagementServices;

use App\Models\AdditionalJobInformation;
use App\Models\AdditionalJobInformationJobLevel;
use App\Models\AdditionalJobInformationJobLocation;
use App\Models\AdditionalJobInformationWorkPlace;
use App\Models\LocCityCorporation;
use App\Models\LocCityCorporationWard;
use App\Models\LocDistrict;
use App\Models\LocDivision;
use App\Models\LocUnion;
use App\Models\LocUpazila;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

/**
 *
 */
class AdditionalJobInformationService
{

    public function getAdditionalJobInformationDetails(string $jobId): Model|Builder
    {
        /** @var Builder $additionalJobInfoBuilder */
        $additionalJobInfoBuilder = AdditionalJobInformation::select([
            'additional_job_information.id',
            'additional_job_information.job_id',
            'additional_job_information.job_responsibilities',
            'additional_job_information.job_content',
            'additional_job_information.job_place_type',
            'additional_job_information.salary_min',
            'additional_job_information.salary_max',
            'additional_job_information.is_salary_info_show',
            'additional_job_information.is_salary_compare_to_expected_salary',
            'additional_job_information.is_salary_alert_excessive_than_given_salary_range',
            'additional_job_information.salary_review',
            'additional_job_information.festival_bonus',
            'additional_job_information.additional_salary_info',
            'additional_job_information.is_other_benefits',
            'additional_job_information.other_benefits',
            'additional_job_information.lunch_facilities',
            'additional_job_information.others',
            'additional_job_information.created_at',
            'additional_job_information.updated_at',
        ]);

        $additionalJobInfoBuilder->where('additional_job_information.job_id', $jobId);


        $additionalJobInfo = $additionalJobInfoBuilder->firstOrFail();

        $additionalJobInfo['job_location'] = $this->getJobLocation();

        return $additionalJobInfo;

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

        /** @var LocCityCorporation|Builder $cityCorporationBuilder */
        $cityCorporationBuilder = LocCityCorporation::select([
            'loc_city_corporations.id',
            'loc_city_corporations.title',
            'loc_city_corporations.title_en',

            'loc_city_corporations.loc_district_id',
            'loc_districts.title as district_title',
            'loc_districts.title_en as district_title_en',

            'loc_city_corporations.loc_division_id',
            'loc_divisions.title as division_title',
            'loc_divisions.title_en as division_title_en'
        ]);

        $cityCorporationBuilder->leftJoin('loc_divisions', function ($join) {
            $join->on('loc_divisions.id', '=', 'loc_city_corporations.loc_division_id')
                ->whereNull('loc_divisions.deleted_at');
        });

        $cityCorporationBuilder->leftJoin('loc_districts', function ($join) {
            $join->on('loc_city_corporations.loc_district_id', '=', 'loc_districts.id')
                ->whereNull('loc_districts.deleted_at');
        });

        $cityCorporations = $cityCorporationBuilder->get();

        /** @var LocUnion|Builder $unionBuilder */
        $unionBuilder = LocUnion::select([
            'loc_unions.id',
            'loc_unions.title',
            'loc_unions.title_en',

            'loc_unions.loc_division_id',
            'loc_divisions.title as division_title',
            'loc_divisions.title_en as division_title_en',

            'loc_unions.loc_district_id',
            'loc_districts.title as district_title',
            'loc_districts.title_en as district_title_en',

            'loc_unions.loc_upazila_id',
            'loc_upazilas.title as upazila_title',
            'loc_upazilas.title_en as upazila_title_en',
        ]);

        $unionBuilder->leftJoin('loc_divisions', function ($join) {
            $join->on('loc_divisions.id', '=', 'loc_unions.loc_division_id')
                ->whereNull('loc_divisions.deleted_at');
        });

        $unionBuilder->leftJoin('loc_districts', function ($join) {
            $join->on('loc_unions.loc_district_id', '=', 'loc_districts.id')
                ->whereNull('loc_districts.deleted_at');
        });

        $unionBuilder->leftJoin('loc_upazilas', function ($join) {
            $join->on('loc_unions.loc_upazila_id', '=', 'loc_upazilas.id')
                ->whereNull('loc_upazilas.deleted_at');
        });


        $unions = $unionBuilder->get();


        /** @var LocCityCorporationWard|Builder $cityCorporationWardBuilder */
        $cityCorporationWardBuilder = LocCityCorporationWard::select([
            'loc_city_corporation_wards.id',
            'loc_city_corporation_wards.title',
            'loc_city_corporation_wards.title_en',

            'loc_city_corporation_wards.loc_division_id',
            'loc_divisions.title as division_title',
            'loc_divisions.title_en as division_title_en',

            'loc_city_corporation_wards.loc_district_id',
            'loc_districts.title as district_title',
            'loc_districts.title_en as district_title_en',

            'loc_city_corporation_wards.loc_city_corporation_id',
            'loc_city_corporations.title as city_corporation_title',
            'loc_city_corporations.title_en as city_corporation_title_en',
        ]);

        $cityCorporationWardBuilder->leftJoin('loc_divisions', function ($join) {
            $join->on('loc_divisions.id', '=', 'loc_city_corporation_wards.loc_division_id')
                ->whereNull('loc_divisions.deleted_at');
        });

        $cityCorporationWardBuilder->leftJoin('loc_districts', function ($join) {
            $join->on('loc_city_corporation_wards.loc_district_id', '=', 'loc_districts.id')
                ->whereNull('loc_districts.deleted_at');
        });

        $cityCorporationWardBuilder->leftJoin('loc_city_corporations', function ($join) {
            $join->on('loc_city_corporation_wards.loc_city_corporation_id', '=', 'loc_city_corporations.id')
                ->whereNull('loc_city_corporations.deleted_at');
        });

        $cityCorporationWards = $cityCorporationWardBuilder->get();

        /** LocDivision */
        foreach ($divisions as $division) {
            $key = $division->id;
            $jobLocation[] = [
                "id" => $key,
                "title" => $division->title,
                "title_en" => $division->title_en
            ];
        }

        /** LocDistrict */
        foreach ($districts as $district) {
            $key = $district->loc_division_id . "_" . $district->id;
            $titleEn = $district->division_title_en . " => " . $district->title_en;
            $titleBn = $district->division_title . " => " . $district->title;
            if ($district->is_sadar_district) {
                $titleEn = $district->division_title_en . " => " . $district->title_en . "(Zilla Sadar)";
                $titleBn = $district->division_title . " => " . $district->title . "(জেলা সদর)";
            }
            $jobLocation[] = [
                "id" => $key,
                "title" => $titleBn,
                "title_en" => $titleEn
            ];

        }
        /** LocUpazila */
        foreach ($upazilas as $upazila) {
            $key = $upazila->loc_division_id . "_" . $upazila->loc_district_id . "_" . $upazila->id;
            $titleEn = $upazila->division_title_en . " => " . $upazila->district_title_en . " => " . $upazila->title_en;
            $titleBn = $upazila->division_title . " => " . $upazila->district_title . " => " . $upazila->title;
            $jobLocation[] = [
                "id" => $key,
                "title" => $titleBn,
                "title_en" => $titleEn
            ];
        }

        /** City Corporations */
        foreach ($cityCorporations as $cityCorporation) {
            $key = $cityCorporation->loc_division_id . "_" . $cityCorporation->loc_district_id . "_" . $cityCorporation->id . AdditionalJobInformation::CITY_CORPORATION_IDENTITY_KEY;
            $titleEn = $cityCorporation->division_title_en . " => " . $cityCorporation->district_title_en . " => " . $cityCorporation->title_en;
            $titleBn = $cityCorporation->division_title . " => " . $cityCorporation->district_title . " => " . $cityCorporation->title;
            $jobLocation[] = [
                "id" => $key,
                "title" => $titleBn,
                "title_en" => $titleEn
            ];
        }

        /** LocUnion */
        foreach ($unions as $union) {
            $key = $union->loc_division_id . "_" . $union->loc_district_id . "_" . $union->loc_upazila_id . "_" . $union->id;
            $titleEn = $union->division_title_en . " => " . $union->district_title_en . " => " . $union->upazila_title_en . " => " . $union->title_en;
            $titleBn = $union->division_title . " => " . $union->district_title . " => " . $union->upazila_title . " => " . $union->title;
            $jobLocation[] = [
                "id" => $key,
                "title" => $titleBn,
                "title_en" => $titleEn
            ];
        }

        /** CityCorporation wards */
        foreach ($cityCorporationWards as $cityCorporationWard) {
            $key = $cityCorporationWard->loc_division_id . "_" . $cityCorporationWard->loc_district_id . "_" . $cityCorporationWard->loc_city_corporation_id . AdditionalJobInformation::CITY_CORPORATION_IDENTITY_KEY . "_" . $cityCorporationWard->id . AdditionalJobInformation::CITY_CORPORATION_WARD_IDENTITY_KEY;
            $titleEn = $cityCorporationWard->division_title_en . " => " . $cityCorporationWard->district_title_en . " => " . $cityCorporationWard->city_corporation_title_en . " => " . $cityCorporationWard->title_en;
            $titleBn = $cityCorporationWard->division_title . " => " . $cityCorporationWard->district_title . " => " . $cityCorporationWard->city_corporation_title . " => " . $cityCorporationWard->title;

            $jobLocation[] = [
                "id" => $key,
                "title" => $titleBn,
                "title_en" => $titleEn
            ];
        }

        return array_values($jobLocation);
    }

    /**
     * @param AdditionalJobInformation $additionalJobInformation
     * @param array $jobLevel
     */
    public function syncWithJobLevel(AdditionalJobInformation $additionalJobInformation, array $jobLevel)
    {
        foreach ($jobLevel as $item) {
            AdditionalJobInformationJobLevel::updateOrCreate(
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
            AdditionalJobInformationWorkPlace::updateOrCreate(
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
            $locIds = getLocationIdByKeyString($item);
            $jobLocationInfo = $this->getJobLocationFormat($locIds);
            $jobLocationInfo['additional_job_information_id'] = $additionalJobInformation->id;
            AdditionalJobInformationJobLocation::updateOrCreate($jobLocationInfo, $jobLocationInfo);
        }
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
                Rule::in(array_keys(AdditionalJobInformation::BOOLEAN_FLAG))
            ],
            "is_salary_alert_excessive_than_given_salary_range" => [
                "required",
                Rule::in(array_keys(AdditionalJobInformation::BOOLEAN_FLAG))
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
                Rule::in(array_keys(AdditionalJobInformation::BOOLEAN_FLAG))
            ],
            "other_benefits" => [
                Rule::requiredIf(function () use ($request) {
                    return $request->is_other_benefits == AdditionalJobInformation::BOOLEAN_FLAG[1];
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
