<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CandidateRequirement extends Model
{

    use SoftDeletes;

    protected $guarded = BaseModel::COMMON_GUARDED_FIELDS_SIMPLE_SOFT_DELETE;

    public function candidateRequirementDegrees()
    {
        $this->hasMany(CandidateRequirementDegree::class);
    }

    public function educationalInstitutions()
    {
        $this->belongsToMany(EducationalInstitution::class, 'candidate_requirements_preferred_educational_institution');
    }

    public function trainings()
    {
        $this->hasMany(CandidateRequirementTraining::class);
    }

    public function professionalCertifications()
    {
        $this->hasMany(CandidateRequirementProfessionalCertification::class);
    }


    public function areaOfExperiences()
    {
        $this->belongsToMany(Skill::class, 'candidate_requirements_area_of_experience');
    }

    public function areaOfBusiness()
    {
        $this->belongsToMany(AreaOfBusiness::class, 'candidate_requirements_area_of_business');
    }

    public function skills()
    {
        $this->belongsToMany(Skill::class, 'candidate_requirements_skills');
    }

    public function genders()
    {
        $this->hasMany(CandidateRequirementGender::class);
    }


}
