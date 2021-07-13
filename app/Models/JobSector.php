<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


/**
 * Class JobSector
 * @package App\Models
 * @property string title_en
 * @property string title_bn
 */

class JobSector extends Model
{
    public const ROW_STATUS_ACTIVE = '1';
    public const ROW_STATUS_INACTIVE = '0';
    public const ROW_STATUS_DELETED = '99';

    protected $guarded = ['id'];
}
