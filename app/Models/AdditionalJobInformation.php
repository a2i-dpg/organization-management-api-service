<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AdditionalJobInformation extends Model
{
    use SoftDeletes;

    protected $guarded = BaseModel::COMMON_GUARDED_FIELDS_SIMPLE_SOFT_DELETE;

    protected $casts = [
        "other_benefits" => "array"
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

    public const BOOLEN_FLAG = [
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
    public const UPAZILA_ID_KEY = 2;

}
