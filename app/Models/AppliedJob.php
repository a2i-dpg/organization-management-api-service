<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AppliedJob extends Model
{
    protected $guarded = [];
    use SoftDeletes;

    public const APPLY_STATUS = [
        "Applied" => 1,
        "Rejected" => 2,
        "Shortlisted" => 3,
        "Interview_invited" => 4,
        "Interviewed" => 5,
        "Hiring_Listed" => 6,
        "Hire_invited" => 7,
        "Hired" => 8,
    ];

    public const INTERVIEW_INVITE_SOURCES = [
        "Job management" => 1,
        "CV Bank" => 2,
        "Freelance corner" => 3,
    ];

    public const INVITE_TYPES = [
        "SMS" => 1,
        "Email" => 2,
        "SMS and email" => 3,
        "Other" => 4,
    ];
}
