<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Collection;

/**
 * Class Skill
 * @package App\Models
 * @property string title_en
 * @property string title
 * @property-read  Collection humanResources
 * @property-read  Collection humanResourceTemplates
 */
class Skill extends BaseModel
{
    use SoftDeletes;

    /**
     * @var string[]
     */
    protected $guarded = BaseModel::COMMON_GUARDED_FIELDS_SIMPLE_SOFT_DELETE;

    protected $hidden = ["pivot"];

    /**
     * @return BelongsToMany
     */
    public function humanResources(): BelongsToMany
    {
        return $this->belongsToMany(HumanResource::class, 'human_resource_skills');
    }

    /**
     * @return BelongsToMany
     */
    public function humanResourceTemplates(): BelongsToMany
    {
        return $this->belongsToMany(HumanResourceTemplate::class, 'human_resource_template_skills');
    }

    public function candidateRequirements(): BelongsToMany
    {
        return $this->belongsToMany(CandidateRequirement::class, 'candidate_requirement_skill', 'skill_id', 'candidate_requirement_id');
    }
}
