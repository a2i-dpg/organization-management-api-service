<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;

class FourIRGuideline extends BaseModel
{
    use SoftDeletes;

    protected $guarded = BaseModel::COMMON_GUARDED_FIELDS_NON_SOFT_DELETE;

    protected $table = 'four_ir_guidelines';
}
