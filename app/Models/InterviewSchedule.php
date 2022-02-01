<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class InterviewSchedule extends BaseModel
{
    use SoftDeletes;
    protected $guarded = BaseModel::COMMON_GUARDED_FIELDS_SOFT_DELETE;


    public const NOT_CONFIRMED = 0;
    public const CONFIRMED = 1;

    public const REQUEST_RESCHEDULED = 2;
    public const ABORTED = 3;

    public const CONFIRMATION_STATUS = [
        self::ROW_STATUS_ACTIVE,
        self::ROW_STATUS_INACTIVE,
        self::ROW_STATUS_PENDING,
        self::ROW_STATUS_REJECTED,
    ];
}
