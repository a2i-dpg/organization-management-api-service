<?php

namespace App\Models;

use App\Traits\CreatedUpdatedBy;
use App\Traits\Scopes\ScopeAcl;
use App\Traits\Scopes\ScopeRowStatusTrait;
use Illuminate\Database\Eloquent\SoftDeletes;

class FourIRCblm extends BaseModel
{
    use SoftDeletes, ScopeAcl, ScopeRowStatusTrait, CreatedUpdatedBy;

    protected $table = 'four_ir_cblm';

    protected $guarded = BaseModel::COMMON_GUARDED_FIELDS_NON_SOFT_DELETE;
}
