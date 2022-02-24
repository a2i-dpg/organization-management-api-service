<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;

class EducationalInstitution extends BaseModel
{
    use SoftDeletes;

    protected $table = 'educational_institutions';

    protected $guarded = BaseModel::COMMON_GUARDED_FIELDS_SIMPLE_SOFT_DELETE;
}
