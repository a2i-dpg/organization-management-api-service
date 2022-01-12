<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
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
    use SoftDeletes;

    protected $guarded = BaseModel::COMMON_GUARDED_FIELDS_SOFT_DELETE;

    public const ROW_STATUS_ACTIVE = 1;
    public const ROW_STATUS_INACTIVE = 0;
    public const ROW_STATUS_INVALID = 2;
}
