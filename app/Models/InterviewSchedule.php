<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
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
        self::NOT_CONFIRMED,
        self::CONFIRMED,
        self::REQUEST_RESCHEDULED,
        self::ABORTED,
    ];


    public function jobApplicants(): hasMany
    {
        return $this->hasMany(AppliedJob::class, 'assign_candidates_to_schedules', 'applied_job_id', 'id');
    }
}
