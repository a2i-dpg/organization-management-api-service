<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RecruitmentStepCandidateScheduleInterview extends Model
{
    protected $guarded = BaseModel::COMMON_GUARDED_FIELDS_SIMPLE;

    protected $table = "recruitment_step_candidate_schedule_interviews";
}
