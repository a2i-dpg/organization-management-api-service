<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class CandidateRequirement extends BaseModel
{

    use SoftDeletes;

    protected $guarded = BaseModel::COMMON_GUARDED_FIELDS_SIMPLE_SOFT_DELETE;


    public function candidateRequirementDegrees(): HasMany
    {
        return $this->hasMany(CandidateRequirementDegree::class, 'candidate_requirement_id', 'id');
    }

    public function educationalInstitutions(): BelongsToMany
    {
        return $this->belongsToMany(EducationalInstitution::class, 'candidate_requirement_preferred_educational_institution', 'candidate_requirement_id', 'id');

    }

    public function trainings(): HasMany
    {
        return $this->hasMany(CandidateRequirementTraining::class,'candidate_requirement_id','id');
    }

    public function professionalCertifications(): HasMany
    {
        return $this->hasMany(CandidateRequirementProfessionalCertification::class,'candidate_requirement_id','id');
    }


    public function areaOfExperience(): BelongsToMany
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
        return $this->hasMany(CandidateRequirementGender::class, 'candidate_requirement_id','id');
    }

}
