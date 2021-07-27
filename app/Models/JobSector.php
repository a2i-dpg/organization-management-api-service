<?php

namespace App\Models;

use App\Traits\Scopes\ScopeRowStatusTrait;

/**
 * Class JobSector
 * @package App\Models
 * @property string title_en
 * @property string title_bn
 * @property int row_status
 */

class JobSector extends BaseModel
{
    use ScopeRowStatusTrait;

    protected $guarded = ['id'];
}
