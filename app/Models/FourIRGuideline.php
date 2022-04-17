<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;

class FourIRGuideline extends BaseModel
{
    protected $guarded = ['id'];

    use SoftDeletes;
}
