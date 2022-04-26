<?php

namespace App\Models;

use App\Traits\CreatedUpdatedBy;
use App\Traits\Scopes\ScopeAcl;
use Illuminate\Database\Eloquent\SoftDeletes;

class FourIRResource extends BaseModel
{
    use SoftDeletes,ScopeAcl, CreatedUpdatedBy;

    protected $guarded = BaseModel::COMMON_GUARDED_FIELDS_NON_SOFT_DELETE;

    protected $table = 'four_ir_resources';

}
