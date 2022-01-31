<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;

/**
 * Class HrDemandYouth
 * @package App\Models
 * @property int id
 * @property int hr_demand_id
 * @property int|null hr_demand_institute_id
 * @property string|null cv_link
 * @property int|null youth_id
 * @property int approval_status
 * @property int row_status
 * @property Carbon created_at
 * @property Carbon updated_at
 * @property Carbon deleted_at
 */
class HrDemandYouth extends BaseModel
{
    use SoftDeletes;

    protected $guarded = BaseModel::COMMON_GUARDED_FIELDS_SIMPLE_SOFT_DELETE;

    public const ROW_STATUS_ACTIVE = 1;
    public const ROW_STATUS_INACTIVE = 0;
    public const ROW_STATUS_INVALID = 2;

    public const APPROVAL_STATUS_PENDING = 1;
    public const APPROVAL_STATUS_APPROVED = 2;
    public const APPROVAL_STATUS_REJECTED = 3;
    public const APPROVAL_STATUSES = [
        self::APPROVAL_STATUS_PENDING,
        self::APPROVAL_STATUS_APPROVED,
        self::APPROVAL_STATUS_REJECTED
    ];
}
