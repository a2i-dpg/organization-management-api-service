<?php

namespace App\Models;

use App\Traits\CreatedUpdatedBy;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;

/**
 * Class HrDemandInstitute
 * @package App\Models
 * @property int id
 * @property int hr_demand_id
 * @property int|null institute_id
 * @property int rejected_by_institute
 * @property int vacancy_provided_by_institute
 * @property int rejected_by_industry_association
 * @property int vacancy_approved_by_industry_association
 * @property int vacancy
 * @property int remaining_vacancy
 * @property int row_status
 * @property Carbon created_at
 * @property Carbon updated_at
 * @property Carbon deleted_at
 */
class HrDemandInstitute extends BaseModel
{
    use SoftDeletes, CreatedUpdatedBy;

    protected $guarded = BaseModel::COMMON_GUARDED_FIELDS_SOFT_DELETE;

    public const ROW_STATUS_ACTIVE = 1;
    public const ROW_STATUS_INACTIVE = 0;
    public const ROW_STATUS_INVALID = 2;

    public const REJECTED_BY_INSTITUTE_FALSE = 0;
    public const REJECTED_BY_INSTITUTE_TRUE = 1;
    public const REJECTED_BY_INSTITUTE = [
        self::REJECTED_BY_INSTITUTE_FALSE,
        self::REJECTED_BY_INSTITUTE_TRUE
    ];

    public const REJECTED_BY_INDUSTRY_ASSOCIATION_FALSE = 0;
    public const REJECTED_BY_INDUSTRY_ASSOCIATION_TRUE = 1;

    public function hrDemand(): BelongsTo
    {
        return $this->belongsTo(HrDemand::class,'hr_demand_id','id')
            ->with('hrDemandSkills');
    }
}
