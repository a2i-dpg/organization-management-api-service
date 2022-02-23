<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;

class MatchingCriteria extends MatchingRelationBaseModel
{
    use SoftDeletes;

    protected $guarded = BaseModel::COMMON_GUARDED_FIELDS_SIMPLE_SOFT_DELETE;
    protected $table = "matching_criteria";
}
