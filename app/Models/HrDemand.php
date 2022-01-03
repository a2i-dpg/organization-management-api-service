<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class ContactInfo
 * @package App\Models
 * @property int id
 * @property int industry_association_id
 * @property int industry_id
 * @property string end_date
 * @property int skill_id
 * @property string requirement
 * @property string requirement_en
 * @property int vacancy
 * @property int remaining_vacancy
 * @property int row_status
 * @property int created_by
 * @property int updated_by
 */
class HrDemand extends BaseModel
{
    use SoftDeletes;

    protected $guarded = BaseModel::COMMON_GUARDED_FIELDS_SIMPLE_SOFT_DELETE;
}
