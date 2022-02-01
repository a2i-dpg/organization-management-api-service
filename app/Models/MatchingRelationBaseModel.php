<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use phpDocumentor\Reflection\Types\This;
use Ramsey\Uuid\Uuid;

class MatchingRelationBaseModel extends BaseModel
{
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

    public function areaOfExperiences(): HasMany
    {
        return $this->hasMany(CandidateRequirementAreaOfExperience::class,'job_id','job_id')
            ->leftJoin('area_of_experiences', "area_of_experiences.id", '=', 'candidate_requirement_area_of_experience.area_of_experience_id')
            ->select([
                "candidate_requirement_area_of_experience.*",
                "area_of_experiences.title as title",
                "area_of_experiences.title_en as title_en",
            ]);
    }

    public function areaOfBusiness(): HasMany
    {
        return $this->hasMany(CandidateRequirementAreaOfBusiness::class,'job_id','job_id')
            ->leftJoin('area_of_business', "area_of_business.id", '=', 'candidate_requirement_area_of_business.area_of_business_id')
            ->select([
                "candidate_requirement_area_of_business.*",
                "area_of_business.title as title",
                "area_of_business.title_en as title_en",
            ]);
    }

    public function skills(): HasMany
    {
        return $this->hasMany(CandidateRequirementSkill::class,'job_id','job_id')
            ->leftJoin('skills', "skills.id", '=', 'candidate_requirement_skill.skill_id')
            ->select([
                "candidate_requirement_skill.*",
                "skills.title as title",
                "skills.title_en as title_en",
            ]);
    }

    public function genders(): HasMany
    {
        return $this->hasMany(CandidateRequirementGender::class, 'job_id','job_id');
    }
}
