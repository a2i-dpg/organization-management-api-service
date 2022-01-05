<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use phpDocumentor\Reflection\Types\This;
use Ramsey\Uuid\Uuid;

class MatchingCriteria extends BaseModel
{
    use SoftDeletes;

    protected $guarded = BaseModel::COMMON_GUARDED_FIELDS_SIMPLE_SOFT_DELETE;
    protected $table = "matching_criteria";

    public function additionalJobInformation(): HasOne
    {
        return $this->hasOne('additional_job_information','job_id','job_id');
    }

    public function candidateRequirement(): HasOne
    {
        return $this->hasOne('candidate_requirements','job_id','job_id');
    }

    public function jobLevels(): BelongsToMany
    {
        return $this->belongsToMany(AdditionalJobInformationJobLevel::class, 'additional_job_information_job_levels','job_id','job_id');
    }

    public function jobLocations(): BelongsToMany
    {
        return $this->belongsToMany(AdditionalJobInformationJobLocation::class, 'additional_job_information_job_locations','job_id','job_id');
    }

    public function areaOfExperiences(): BelongsToMany
    {
        return $this->belongsToMany(AreaOfExperience::class, 'candidate_requirement_area_of_experience','job_id','job_id');
    }

    public function areaOfBusiness(): BelongsToMany
    {
        return $this->belongsToMany(AreaOfBusiness::class, 'candidate_requirement_area_of_business','job_id','job_id');
    }

    public function skills(): BelongsToMany
    {
        return $this->belongsToMany(Skill::class, 'candidate_requirement_skill','job_id','job_id');
    }

    public function genders(): HasMany
    {
        return $this->hasMany(CandidateRequirementGender::class, 'job_id','job_id');
    }
}
