<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class CandidateRequirementDegree extends BaseModel
{
    protected $table = 'candidate_requirement_degrees';

    public function candidateRequirement(): BelongsTo
    {
        return $this->belongsTo(CandidateRequirement::class, 'candidate_requirement_id');
    }

    public function educationLevel(): HasOne
    {
        return $this->hasOne(EducationLevel::class, 'id', 'education_level_id');
    }

    public function eduGroup(): HasOne
    {
        return $this->hasOne(EduGroup::class, 'id', 'edu_group_id');
    }
}
