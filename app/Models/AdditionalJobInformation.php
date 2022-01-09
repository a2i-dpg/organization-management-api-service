<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Casts\AsArrayObject;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Ramsey\Collection\Collection;


/**
 * class PrimaryJobInformation
 * @property int id
 * @property string job_id
 * @property string job_responsibilities
 * @property string | null job_responsibilities_en
 * @property string job_context
 * @property string job_context_en
 * @property int job_place_type
 * @property int | null salary_min
 * @property int | null salary_max
 * @property int  is_salary_info_show
 * @property int  is_salary_compare_to_expected_salary
 * @property int  is_salary_alert_excessive_than_given_salary_range
 * @property int  salary_review
 * @property int  festival_bonus
 * @property string | null additional_salary_info
 * @property string | null additional_salary_info_en
 * @property int is_other_benefits
 * @property string | null other_benefits
 * @property int  lunch_facilities
 * @property string | null  others
 * @property Carbon |null created_at
 * @property Carbon |null updated_at
 * @property Carbon |null deleted_at
 * @property Collection jobLevels
 * @property Collection jobLocations
 * @property Collection workPlaces
 */
class AdditionalJobInformation extends BaseModel
{
    use SoftDeletes;

    protected $guarded = BaseModel::COMMON_GUARDED_FIELDS_SIMPLE_SOFT_DELETE;

    protected $casts = [
        "other_benefits" => 'array'
    ];


    public const JOB_PLACE_TYPE = [
        1 => "Inside Bangladesh",
        2 => "Outside Bangladesh"
    ];

    public const IS_SALARY_SHOW = [
        1 => "Show Salary",
        2 => "Show Nothing",
        3 => "Show Negotiable instead of given salary range"
    ];

    public const BOOLEAN_FLAG = [
        0 => "False",
        1 => "True"
    ];

    public const SALARY_REVIEW = [
        1 => "Half Yearly",
        2 => "Yearly"
    ];

    public const LUNCH_FACILITIES = [
        1 => "Partially Subsidize",
        2 => "Full Subsidize"
    ];

    public const FESTIVAL_BONUS = [
        1 => "01",
        2 => "02",
        3 => "03",
        4 => "04"
    ];

    public const JOB_LEVEL = [
        1 => "Entry",
        2 => "Mid",
        3 => "Top"
    ];

    public const WORK_PLACE = [
        1 => "Home",
        2 => "Office"
    ];

    public const DIVISION_ID_KEY = 0;
    public const DISTRICT_ID_KEY = 1;
    public const UPAZILA_OR_CITY_CORPORATION_ID_KEY = 2;
    public const UNION_OR_CITY_CORPORATION_WARD_ID_KEY = 3;

    public const CITY_CORPORATION_IDENTITY_SYMBOL = "@";
    public const CITY_CORPORATION_IDENTITY_KEY = self::CITY_CORPORATION_IDENTITY_SYMBOL . "CC";
    public const CITY_CORPORATION_WARD_IDENTITY_KEY = self::CITY_CORPORATION_IDENTITY_SYMBOL . "CCW";

    public function jobLevels(): HasMany
    {
        return $this->hasMany(AdditionalJobInformationJobLevel::class);
    }

    public function jobLocations(): HasMany
    {

        return $this->hasMany(AdditionalJobInformationJobLocation::class, 'additional_job_information_id', 'id')
            ->leftJoin('loc_divisions', "loc_divisions.id", '=', 'additional_job_information_job_locations.loc_division_id')
            ->leftJoin('loc_districts', "loc_districts.id", '=', 'additional_job_information_job_locations.loc_district_id')
            ->leftJoin('loc_upazilas', "loc_upazilas.id", "=", "additional_job_information_job_locations.loc_upazila_id")
            ->select([
                'additional_job_information_job_locations.*',
                "loc_divisions.id as loc_district_id",
                "loc_districts.id as loc_district_id",
                "loc_upazilas.id as loc_area_id",
                "loc_divisions.title as loc_division_title",
                "loc_divisions.title_en as loc_division_title_en",
                "loc_districts.title as loc_district_title",
                "loc_districts.title_en as loc_district_title_en",
                "loc_upazilas.title as loc_area_title",
                "loc_upazilas.title_en as loc_area_title_en",
            ]);

    }

    public function workPlaces(): HasMany
    {
        return $this->hasMany(AdditionalJobInformationWorkPlace::class);
    }


}
