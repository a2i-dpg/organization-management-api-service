<?php

namespace App\Models;

use App\Traits\CreatedUpdatedBy;
use App\Traits\Scopes\ScopeAcl;
use App\Traits\Scopes\ScopeRowStatusTrait;
use Illuminate\Database\Eloquent\SoftDeletes;

class FourIRShowcasing extends BaseModel
{
    use SoftDeletes, ScopeAcl, ScopeRowStatusTrait,CreatedUpdatedBy;

    protected $guarded = BaseModel::COMMON_GUARDED_FIELDS_NON_SOFT_DELETE;

    protected $table = 'four_ir_showcasings';

}
