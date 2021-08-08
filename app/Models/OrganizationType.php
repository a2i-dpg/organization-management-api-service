<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class OrganizationType
 * @package App\Models
 * @property string title_en
 * @property string title_bn
 * @property int is_government
 * @property int row_status
 */
class OrganizationType extends BaseModel
{
    use SoftDeletes;
    /**
     * @var string[]
     */
    protected  $guarded = ['id'];
}
