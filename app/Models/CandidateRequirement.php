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

    public function educationalInstitutes()
    {
        $this->belongsToMany(CandidateRequirement::class, 'candidate_requirements_preferred_educational_institution');
    }


}
