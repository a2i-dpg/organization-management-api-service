<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CandidateInterview extends Model
{
    protected $guarded = BaseModel::COMMON_GUARDED_FIELDS_SIMPLE_SOFT_DELETE;

    public const IS_CANDIDATE_PRESENT_YES = 1;
    public const IS_CANDIDATE_PRESENT_NO = 0;

    public const CANDIDATE_ATTENDANCE_STATUSES = [
        self::IS_CANDIDATE_PRESENT_YES,
        self::IS_CANDIDATE_PRESENT_NO,
    ];
}
