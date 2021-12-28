<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use phpDocumentor\Reflection\Types\This;
use Ramsey\Uuid\Uuid;

class PrimaryJobInformation extends BaseModel
{
    use SoftDeletes;

    protected $guarded = BaseModel::COMMON_GUARDED_FIELDS_SIMPLE_SOFT_DELETE;

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
        1 => "Apply Online",
        2 => "Email",
        3 => "Hard Copy",
        4 => "Walk in interview"
    ];

    public const BOOLEAN_FLAG_TRUE = 1;
    public const BOOLEAN_FLAG_FALSE = 0;

    public const BOOLEAN_FLAG = [
        self::BOOLEAN_FLAG_TRUE,
        self::BOOLEAN_FLAG_FALSE
    ];


    public static function jobCategoryId(int $job_category_type): array
    {
        $categoryId = [];
        if ($job_category_type == self::JOB_CATEGORY_TYPE[1]) {
            $categoryId = JobSector::all('id')->toArray();
        } elseif ($job_category_type == self::JOB_CATEGORY_TYPE[2]) {
            $categoryId = Occupation::all('id')->toArray();
        }
        return $categoryId;
    }

    public static function jobId(): string
    {
        $id = self::JOB_ID_PREFIX . Uuid::uuid4();
        $isUnique = !(bool)PrimaryJobInformation::where('job_id', $id)->count('id');
        if ($isUnique) {
            return $id;
        }
        return self::jobId();
    }

    public function employmentTypes():BelongsToMany
    {
        return $this->belongsToMany(EmploymentType::class,"primary_job_information_employment_status");
    }

}
