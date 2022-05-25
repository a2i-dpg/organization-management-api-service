<?php

namespace App\Models;

use App\Traits\CreatedUpdatedBy;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\SoftDeletes;
use Ramsey\Uuid\Type\Decimal;

/**
 * App\Models\FourIRInitiative
 *
 * @property int id
 * @property string name
 * @property string name_en
 * @property string organization_name
 * @property string organization_name_en
 * @property int four_ir_occupation_id
 * @property string details
 * @property Carbon start_date
 * @property Decimal budget
 * @property string initiative_code
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
class FourIRInitiative extends BaseModel
{
    use SoftDeletes, CreatedUpdatedBy;

    protected $table = 'four_ir_initiatives';

    protected $guarded = BaseModel::COMMON_GUARDED_FIELDS_NON_SOFT_DELETE;

    protected $casts = [
        'tasks' => 'array'
    ];

    public const COMPLETION_STEP_ONE = 1;
    public const COMPLETION_STEP_TWO = 2;
    public const COMPLETION_STEP_THREE = 3;
    public const COMPLETION_STEP_FOUR = 4;
    public const COMPLETION_STEP_FIVE = 5;
    public const COMPLETION_STEP_SIX = 6;
    public const COMPLETION_STEP_SEVEN = 7;
    public const COMPLETION_STEP_EIGHT = 8;
    public const COMPLETION_STEP_NINE = 9;
    public const COMPLETION_STEP_THIRTEEN = 13;
    public const COMPLETION_STEP_FOURTEEN = 14;
    public const COMPLETION_STEP_FIFTEEN = 15;
    public const COMPLETION_STEP_SIXTEEN = 16;
    public const COMPLETION_STEP_SEVENTEEN = 17;

    public const FORM_STEP_PROJECT_INITIATION = 1;
    public const FORM_STEP_IMPLEMENTING_TEAM = 2;
    public const FORM_STEP_EXPERT_TEAM = 3;
    public const FORM_STEP_CELL = 4;
    public const FORM_STEP_TNA = 5;
    public const FORM_STEP_CS = 6;
    public const FORM_STEP_CURRICULUM = 7;
    public const FORM_STEP_CBLM = 8;
    public const FORM_STEP_RESOURCE_MANAGEMENT = 9;
    public const FORM_STEP_TOT = 10;
    public const FORM_STEP_CREATE_APPROVE_COURSE = 11;
    public const FORM_STEP_EMPLOYMENT = 12;
    public const FORM_STEP_SHOWCASING = 13;
    public const FORM_STEP_PROJECT_ANALYSIS = 14;
    public const FORM_STEP_SCALE_UP = 15;

    public const FILE_LOG_INITIATIVE_STEP = 1;
    public const FILE_LOG_TNA_STEP = 2;
    public const FILE_LOG_PROJECT_CS_STEP = 3;
    public const FILE_LOG_PROJECT_CURRICULUM_STEP = 4;
    public const FILE_LOG_CBLM_STEP = 5;
    public const FILE_LOG_PROJECT_RESOURCE_MANAGEMENT_STEP = 6;
    public const FILE_LOG_SHOWCASING_STEP = 7;
    public const FILE_LOG_INITIATIVE_ANALYSIS_STEP = 8;
    public const FILE_LOG_INITIATIVE_TOT_STEP = 9;
    public const FILE_LOG_INITIATIVE_SCALE_UP_STEP = 10;

    public const TASK_ROADMAP_FINALIZED = 1;
    public const TASK_PROJECT_REVIEWED_BY_SECRETARY_OF_RELEVANT_MINISTRIES = 2;
    public const TASK_PROJECT_APPROVED = 3;
    public const TASK_ROADMAP_FINALIZED_LABEL = "TASK_ROADMAP_FINALIZED";
    public const TASK_PROJECT_REVIEWED_BY_SECRETARY_OF_RELEVANT_MINISTRIES_LABEL = "TASK_PROJECT_REVIEWED_BY_SECRETARY_OF_RELEVANT_MINISTRIES";
    public const TASK_PROJECT_APPROVED_LABEL = "TASK_PROJECT_APPROVED";

    public const TASKS = [
        self::TASK_ROADMAP_FINALIZED => self::TASK_ROADMAP_FINALIZED_LABEL,
        self::TASK_PROJECT_REVIEWED_BY_SECRETARY_OF_RELEVANT_MINISTRIES => self::TASK_PROJECT_REVIEWED_BY_SECRETARY_OF_RELEVANT_MINISTRIES_LABEL,
        self::TASK_PROJECT_APPROVED => self::TASK_PROJECT_APPROVED_LABEL,
    ];

    public const SKILL_PROVIDE_FALSE = 0;
    public const SKILL_PROVIDE_TRUE = 1;
}
