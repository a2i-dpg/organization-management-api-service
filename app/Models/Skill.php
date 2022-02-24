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

    public $timestamps = false;

    /**
     * @var string[]
     */
    protected $guarded = BaseModel::COMMON_GUARDED_FIELDS_ONLY_SOFT_DELETE;

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

    /**
     * @return BelongsToMany
     */
    public function candidateRequirements(): BelongsToMany
    {
        return $this->belongsToMany(CandidateRequirement::class, 'candidate_requirement_skill', 'skill_id', 'candidate_requirement_id');
    }

    /**
     * @return BelongsToMany
     */
    public function hrDemands(): BelongsToMany
    {
        return $this->belongsToMany(HrDemandSkill::class, 'hr_demand_skills', 'skill_id', 'hr_demand_id');
    }
}
