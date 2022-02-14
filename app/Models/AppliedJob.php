<?php

namespace App\Models;

use Carbon\Carbon;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class AppliedJob
 * @package App\Models;
 * @property int id
 * @property string job_id
 * @property int youth_id
 * @property int apply_status
 * @property int current_recruitment_step_id
 * @property Carbon applied_at
 * @property Carbon profile_viewed_at
 * @property int expected_salary
 * @property Carbon hire_invited_at
 * @property Carbon hired_at
 * @property int hire_invite_type
 * @property Carbon created_at
 * @property Carbon updated_at
 * @property Carbon deleted_at
 *
 */
class AppliedJob extends Model
{
    protected $guarded = [];
    use SoftDeletes;

    public const APPLY_STATUS = [
        "Applied" => 1,
        "Rejected" => 2,
        "Shortlisted" => 3,
        "Interview_scheduled" => 4,
        "Interview_invited" => 5,
        "Interviewed" => 6,
        "Hiring_Listed" => 7,
        "Hire_invited" => 8,
        "Hired" => 9,
    ];

    public const PROFILE_VIEWED_YES = 1;
    public const PROFILE_VIEWED_NO = 0;

    public const PROFILE_VIEWED = [
        self::PROFILE_VIEWED_YES,
        self::PROFILE_VIEWED_NO
    ];

    public const QUALIFIED_YES = 1;
    public const QUALIFIED_NO = 0;


    public const QUALIFIED = [
        self::QUALIFIED_YES,
        self::QUALIFIED_NO
    ];

    public const INTERVIEW_INVITE_SOURCES = [
        "Job management" => 1,
        "CV Bank" => 2,
        "Freelance corner" => 3,
    ];

    public const INVITE_TYPES = [
        "SMS" => 1,
        "Email" => 2,
        "SMS and Email" => 3,
        "Other" => 4,
    ];

    /** recruitment step candidate filter type*/
    public const TYPE_ALL = "all";
    public const TYPE_NOT_VIEWED = 'not_viewed';
    public const TYPE_VIEWED = 'viewed';
    public const TYPE_REJECTED = 'rejected';
    public const TYPE_QUALIFIED = 'qualified';
    public const TYPE_SHORTLISTED = 'shortlisted';
    public const TYPE_SCHEDULED = 'scheduled';
    public const TYPE_INTERVIEWED = 'interviewed';
    public const TYPE_HIRE_SELECTED = 'hire_selected';
    public const TYPE_HIRE_INVITED = 'hire_invited';
    public const TYPE_HIRED = 'hired';


    public const CANDIDATE_LIST_FILTER_TYPES = [
        self:: TYPE_ALL,
        self:: TYPE_NOT_VIEWED,
        self::TYPE_VIEWED,
        self:: TYPE_REJECTED,
        self:: TYPE_QUALIFIED,
        self:: TYPE_SHORTLISTED,
        self:: TYPE_SCHEDULED,
        self:: TYPE_INTERVIEWED,
        self:: TYPE_HIRE_SELECTED,
        self:: TYPE_HIRE_INVITED,
        self:: TYPE_HIRED,

    ];

}
