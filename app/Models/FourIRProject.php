<?php

namespace App\Models;

use App\Traits\CreatedUpdatedBy;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\SoftDeletes;
use Ramsey\Uuid\Type\Decimal;

/**
 * App\Models\FourIRProject
 *
 * @property int id
 * @property string project_name
 * @property string project_name_en
 * @property string organization_name
 * @property string organization_name_en
 * @property int occupation_id
 * @property string details
 * @property Carbon start_date
 * @property Decimal budget
 * @property string project_code
 * @property string file_path
 * @property array tasks
 * @property int completion_step
 * @property int form_step
 * @property String accessor_type
 * @property int accessor_id
 * @property int row_status
 * @property int created_by
 * @property int updated_by
 * @property Carbon created_at
 * @property Carbon updated_at
 */
class FourIRProject extends BaseModel
{
    use SoftDeletes, CreatedUpdatedBy;

    protected $table = 'four_ir_projects';

    protected $guarded = BaseModel::COMMON_GUARDED_FIELDS_NON_SOFT_DELETE;

    protected $casts = [
        'tasks' => 'array'
    ];

    public const COMPLETION_STEP_ONE = 1;
    public const COMPLETION_STEP_TWO = 2;
    public const COMPLETION_STEP_THREE = 3;

    public const FORM_STEP_PROJECT_INITIATION = 1;
    public const FORM_STEP_GUIDELINE = 2;
}
