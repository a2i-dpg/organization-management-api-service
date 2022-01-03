<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class CandidateRequirement extends Model
{

    use SoftDeletes;

    protected $guarded = BaseModel::COMMON_GUARDED_FIELDS_SIMPLE_SOFT_DELETE;


    public function candidateRequirementDegrees(): HasMany
    {
        return $this->hasMany(CandidateRequirementDegree::class, 'candidate_requirements_id', 'id');
    }

    public function educationalInstitutions(): BelongsToMany
    {
        return $this->belongsToMany(EducationalInstitution::class, 'candidate_requirements_preferred_educational_institution', 'candidate_requirements_id', 'id');

    }

    public function trainings(): HasMany
    {
        return $this->hasMany(CandidateRequirementTraining::class,'candidate_requirements_id','id');
    }

    public function professionalCertifications(): HasMany
    {
        return $this->hasMany(CandidateRequirementProfessionalCertification::class,'candidate_requirements_id','id');
    }


    public function areaOfExperiences(): BelongsToMany
    {
        return $this->belongsToMany(Skill::class, 'candidate_requirements_area_of_experience','candidate_requirements_id','id');
    }

    public function areaOfBusiness(): BelongsToMany
    {
        return $this->belongsToMany(AreaOfBusiness::class, 'candidate_requirements_area_of_business','candidate_requirements_id','id');
    }

    public function skills(): BelongsToMany
    {
        return $this->belongsToMany(Skill::class, 'candidate_requirements_skills','candidate_requirements_id','id');
    }

    public function genders(): HasMany
    {
        return $this->hasMany(CandidateRequirementGender::class);
    }

}
