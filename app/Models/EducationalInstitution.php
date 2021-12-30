<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EducationalInstitution extends Model
{
    use SoftDeletes;
    protected $table = 'educational_institutions';
}
