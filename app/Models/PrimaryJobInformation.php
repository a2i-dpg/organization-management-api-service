<?php

namespace App\Models;


use App\Traits\Scopes\ScopeAcl;
use App\Traits\Scopes\ScopeRowStatusTrait;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Ramsey\Uuid\Uuid;

/**
 * class PrimaryJobInformation
 * @property int id
 * @property string job_id
 * @property int service_type
 * @property string job_title
 * @property string | null job_title_en
 * @property int | null no_of_vacancies
 * @property int job_sector_id
 * @property int occupation_id
 * @property int industry_association_id
 * @property int organization_id
 * @property int institute_id
 * @property Carbon application_deadline
 * @property int is_apply_online
 * @property int resume_receiving_option
 * @property string | null email
 * @property int is_use_nise3_mail_system
 * @property string | null special_instruction_for_job_seekers
 * @property string | null special_instruction_for_job_seekers_en
 * @property string | null instruction_for_hard_copy
 * @property string | null instruction_for_hard_copy_en
 * @property string | null instruction_for_walk_in_interview
 * @property string | null instruction_for_walk_in_interview_en
 * @property int is_photograph_enclose_with_resume
 * @property int is_prefer_video_resume
 * @property Carbon |null published_at
 * @property Carbon |null archived_at
 * @property Carbon |null created_at
 * @property Carbon |null updated_at
 * @property Carbon |null deleted_at
 */
class PrimaryJobInformation extends BaseModel
{
    use SoftDeletes, ScopeAcl, ScopeRowStatusTrait;

    protected $guarded = BaseModel::COMMON_GUARDED_FIELDS_SIMPLE_SOFT_DELETE;

    public const JOB_FILTER_TYPE_POPULAR = 'popular';
    public const JOB_FILTER_TYPE_RECENT = 'recent';


    public const JOB_FILTER_TYPES = [
        self::JOB_FILTER_TYPE_POPULAR,
        self::JOB_FILTER_TYPE_RECENT,
    ];

    public const JOB_ID_PREFIX = "IDSA-";
    public const JOB_SERVICE_TYPE = [
        1 => "Basic Listing",
        2 => "Stand-out-listing",
        3 => "Stand Out Premium"
    ];
    public const VACANCY_NOT_NEEDED = 1;
    public const JOB_CATEGORY_TYPE = [
        1 => "Functional",
        2 => "Special Skilled"
    ];

    public const RESUME_RECEIVING_OPTION = [
        1 => "Email",
        2 => "Hard Copy",
        3 => "Walk in interview"
    ];

    public const BOOLEAN_FLAG_TRUE = 1;
    public const BOOLEAN_FLAG_FALSE = 0;

    public const BOOLEAN_FLAG = [
        self::BOOLEAN_FLAG_TRUE,
        self::BOOLEAN_FLAG_FALSE
    ];


    public static function jobId(): string
    {
        $id = self::JOB_ID_PREFIX . Uuid::uuid4();
        $isUnique = !(bool)PrimaryJobInformation::where('job_id', $id)->count('id');
        if ($isUnique) {
            return $id;
        }
        return self::jobId();
    }

    public function employmentTypes(): BelongsToMany
    {
        return $this->belongsToMany(EmploymentType::class, "primary_job_information_employment_status");
    }

    public function additionalJobInformation(): HasOne
    {
        return $this->hasOne(AdditionalJobInformation::class, 'job_id', "job_id");
    }

    public function candidateRequirement(): HasOne
    {
        return $this->hasOne(CandidateRequirement::class, 'job_id', "job_id");
    }

    /**publish or archive status */

    public const STATUS_PUBLISH = 1;
    public const STATUS_ARCHIVE = 2;
    public const STATUS_DRAFT = 3;

    public const PUBLISH_OR_ARCHIVE_STATUSES = [
        self::STATUS_PUBLISH,
        self::STATUS_ARCHIVE,
        self::STATUS_DRAFT
    ];


    public const JOB_STATUS_LIVE = "live";
    public const JOB_STATUS_PENDING = "pending";
    public const JOB_STATUS_EXPIRED = "expired";

}
