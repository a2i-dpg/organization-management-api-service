<?php

namespace App\Models;

use App\Traits\Scopes\ScopeRowStatusTrait;

/**
 * Class JobSector
 * @package App\Models
 * @property string title_en
 * @property string title_bn
 */

class JobSector extends BaseModel
{
    use ScopeRowStatusTrait;

    protected $guarded = ['id'];
}
