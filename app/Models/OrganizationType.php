<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class OrganizationType
 * @package App\Models
 * @property string title_en
 * @property string title_bn
 * @property bool is_government
 */
class OrganizationType extends Model
{
    public const ROW_STATUS_ACTIVE = '1';
    public const ROW_STATUS_INACTIVE = '0';
    public const ROW_STATUS_DELETED = '99';
    /**
     * @var string[]
     */
    protected  $guarded = ['id'];
}
