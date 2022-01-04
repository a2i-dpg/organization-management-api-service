<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\HasMany;
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

    protected $guarded = BaseModel::COMMON_GUARDED_FIELDS_SOFT_DELETE;

    public const ROW_STATUS_ACTIVE = 1;
    public const ROW_STATUS_INACTIVE = 0;


    public function hrDemandInstitutes(): HasMany
    {
        return $this->hasMany(HrDemandInstitute::class,'hr_demand_id','id');
    }


}
