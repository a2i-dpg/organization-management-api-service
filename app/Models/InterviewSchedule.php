<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class InterviewSchedule extends BaseModel
{
    use SoftDeletes;
    protected $guarded = BaseModel::COMMON_GUARDED_FIELDS_SOFT_DELETE;
}
