<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class RecruitmentStep extends Model
{
    use SoftDeletes;

    protected $guarded = BaseModel::COMMON_GUARDED_FIELDS_SIMPLE_SOFT_DELETE;

    public const STEP_TYPE_SHORTLIST = 1;
    public const STEP_TYPE_WRITTEN = 2;
    public const STEP_TYPE_INTERVIEW = 3;
    public const STEP_TYPE_ONLINE_INTERVIEW = 4;
    public const STEP_TYPE_OTHERS = 4;

    public const STEP_TYPES = [
        self::STEP_TYPE_SHORTLIST,
        self::STEP_TYPE_WRITTEN,
        self::STEP_TYPE_INTERVIEW,
        self::STEP_TYPE_ONLINE_INTERVIEW,
        self::STEP_TYPE_OTHERS
    ];
}
