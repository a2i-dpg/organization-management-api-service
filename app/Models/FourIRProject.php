<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class FourIRProject extends BaseModel
{
    public const COMPLETION_STEP_ONE = 1;
    public const COMPLETION_STEP_TWO = 2;
    public const COMPLETION_STEP_THREE = 3;

    public const FORM_STEP_PROJECT_INITIATION = 1;
    public const FORM_STEP_GUIDELINE = 2;
}
