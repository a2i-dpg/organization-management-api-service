<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;


/**
 * Class JobSector
 * @package App\Models
 * @property string title_en
 * @property string title_bn
 */

class JobSector extends BaseModel
{
    use SoftDeletes, HasFactory;

    protected $guarded = ['id'];
}
