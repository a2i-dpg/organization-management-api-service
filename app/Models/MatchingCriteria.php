<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use phpDocumentor\Reflection\Types\This;
use Ramsey\Uuid\Uuid;

class MatchingCriteria extends BaseModel
{
    use SoftDeletes;

    protected $guarded = BaseModel::COMMON_GUARDED_FIELDS_SIMPLE_SOFT_DELETE;
    protected $table = "matching_criteria";
}
