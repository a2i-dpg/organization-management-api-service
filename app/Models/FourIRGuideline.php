<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;

class FourIRGuideline extends BaseModel
{
    use SoftDeletes;

    protected $guarded = ['id'];

    protected $table = ['four_ir_guidelines'];
}
