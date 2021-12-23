<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;

/**
 *
 */
class ContactInfo extends BaseModel
{
    use SoftDeletes;

    protected $guarded = BaseModel::COMMON_GUARDED_FIELDS_SOFT_DELETE;
}
