<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class HrDemandInstitute extends Model
{
    use SoftDeletes;

    protected $guarded = BaseModel::COMMON_GUARDED_FIELDS_SOFT_DELETE;

    public const ROW_STATUS_ACTIVE = 1;
    public const ROW_STATUS_INACTIVE = 0;
}
