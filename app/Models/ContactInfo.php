<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Collection;

/**
 * Class ContactInfo
 * @package App\Models
 * @property int id
 * @property string title_en
 * @property string title
 * @property string country
 * @property string phone_code
 * @property string phone
 * @property string mobile
 * @property string email
 * @property int display_order
 * @property int industry_association_id
 * @property int row_status
 */
class ContactInfo extends BaseModel
{
    use SoftDeletes;

    protected $guarded = BaseModel::COMMON_GUARDED_FIELDS_SOFT_DELETE;
}
