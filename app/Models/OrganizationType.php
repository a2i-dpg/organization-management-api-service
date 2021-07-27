<?php

namespace App\Models;

use App\Traits\Scopes\ScopeRowStatusTrait;

/**
 * Class OrganizationType
 * @package App\Models
 * @property string title_en
 * @property string title_bn
 * @property bool is_government
 * @property int row_status
 */
class OrganizationType extends BaseModel
{
    use ScopeRowStatusTrait;
    /**
     * @var string[]
     */
    protected  $guarded = ['id'];
}
