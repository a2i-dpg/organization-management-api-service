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
        return $this->hasOne(AdditionalJobInformation::class,'job_id','job_id');
    }

    public function candidateRequirement(): HasOne
    {
        return $this->hasOne(CandidateRequirement::class,'job_id','job_id');
    }

    public function jobLevels(): HasMany
    {
        return $this->hasMany(AdditionalJobInformationJobLevel::class,'job_id','job_id');
    }

    public function jobLocations(): HasMany
    {
        return $this->hasMany(AdditionalJobInformationJobLocation::class,'job_id','job_id')
            ->leftJoin('loc_divisions', "loc_divisions.id", '=', 'additional_job_information_job_locations.loc_division_id')
            ->leftJoin('loc_districts', "loc_districts.id", '=', 'additional_job_information_job_locations.loc_district_id')
            ->leftJoin('loc_upazilas', "loc_upazilas.id", "=", "additional_job_information_job_locations.loc_upazila_id")
            ->select([
                'additional_job_information_job_locations.*',
                "loc_divisions.id as loc_district_id",
                "loc_districts.id as loc_district_id",
                "loc_upazilas.id as loc_area_id",
                "loc_divisions.title as loc_division_title",
                "loc_divisions.title_en as loc_division_title_en",
                "loc_districts.title as loc_district_title",
                "loc_districts.title_en as loc_district_title_en",
                "loc_upazilas.title as loc_area_title",
                "loc_upazilas.title_en as loc_area_title_en",
            ]);
    }

    public function areaOfExperiences(): BelongsToMany
    {
        return $this->belongsToMany(AreaOfExperience::class, 'candidate_requirement_area_of_experience','candidate_requirement_id','area_of_experience_id');
    }

    public function areaOfBusiness(): BelongsToMany
    {
        return $this->belongsToMany(AreaOfBusiness::class, 'candidate_requirement_area_of_business','candidate_requirement_id','area_of_business_id');
    }

    public function skills(): BelongsToMany
    {
        return $this->belongsToMany(Skill::class, 'candidate_requirement_skill','candidate_requirement_id','skill_id');
    }

    public function genders(): HasMany
    {
        return $this->hasMany(CandidateRequirementGender::class, 'job_id','job_id');
    }
}
