<?php

namespace App\Models;

use App\Traits\CreatedUpdatedBy;
use Illuminate\Database\Eloquent\SoftDeletes;

class FourIRFileLog extends BaseModel
{
    use SoftDeletes, CreatedUpdatedBy;

    protected $table = 'four_ir_file_logs';

    protected $guarded = BaseModel::COMMON_GUARDED_FIELDS_NON_SOFT_DELETE;
}
