<?php

namespace App\Models;

use App\Traits\CreatedUpdatedBy;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;

/**
 * Class HrDemand
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
 * @property int all_institutes
 * @property int row_status
 * @property int created_by
 * @property int updated_by
 * @property Carbon created_at
 * @property Carbon updated_at
 * @property Carbon deleted_at
 */
class HrDemand extends BaseModel
{
    use SoftDeletes, CreatedUpdatedBy;

    protected $guarded = BaseModel::COMMON_GUARDED_FIELDS_SOFT_DELETE;

    public const ROW_STATUS_INACTIVE = 0;
    public const ROW_STATUS_ACTIVE = 1;
    public const ROW_STATUS_INVALID = 2;

    public const ALL_INSTITUTES_FALSE = 0;
    public const ALL_INSTITUTES_TRUE = 1;

    public const BOOLEAN_FLAG = [
        0 => "False",
        1 => "True"
    ];

    public const SHOW_ONLY_HR_DEMAND_INSTITUTES_APPROVED_BY_TSP_KEY = 'approved_by_institutes';

    public function hrDemandInstitutes(): HasMany
    {
        return $this->hasMany(HrDemandInstitute::class,'hr_demand_id','id')
            ->whereNotNull('institute_id');
    }


}
