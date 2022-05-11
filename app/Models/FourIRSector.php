<?php

namespace App\Models;

use App\Traits\CreatedUpdatedBy;
use App\Traits\Scopes\ScopeAcl;
use App\Traits\Scopes\ScopeRowStatusTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class FourIRSector extends Model
{
    use SoftDeletes, ScopeAcl, ScopeRowStatusTrait, CreatedUpdatedBy;

    protected $table = 'four_ir_sectors';

    protected $guarded = BaseModel::COMMON_GUARDED_FIELDS_NON_SOFT_DELETE;
}
