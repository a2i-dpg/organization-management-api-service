<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CandidateInterview extends BaseModel
{
    protected $guarded = BaseModel::COMMON_GUARDED_FIELDS_SIMPLE;


    public const IS_CANDIDATE_PRESENT_YES = 1;
    public const IS_CANDIDATE_PRESENT_NO = 0;

    public const CANDIDATE_ATTENDANCE_STATUSES = [
        self::IS_CANDIDATE_PRESENT_YES,
        self::IS_CANDIDATE_PRESENT_NO,
    ];


    public const NOT_CONFIRMED = 1;
    public const CONFIRMED = 2;
    public const REQUEST_RESCHEDULED = 3;
    public const ABORTED = 4;

    public const CONFIRMATION_STATUS = [
        self::NOT_CONFIRMED,
        self::CONFIRMED,
        self::REQUEST_RESCHEDULED,
        self::ABORTED,
    ];

}
