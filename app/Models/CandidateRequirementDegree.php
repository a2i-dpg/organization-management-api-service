<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CandidateRequirementDegree extends BaseModel
{
    protected $table = 'candidate_requirements_degrees';

    public function candidateRequirement()
    {
        $this->belongsTo(CandidateRequirement::class, 'candidate_requirements_id');
    }

    public function educationLevel()
    {
        $this->hasOne(EducationLevel::class,'education_level_id');
    }

    public function eduGroup()
    {
        $this->hasOne(EduGroup::class,'edu_group_id');
    }
}
