<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AreaOfExperience extends BaseModel
{
    protected $hidden = ['pivot'];

    use SoftDeletes;
}
