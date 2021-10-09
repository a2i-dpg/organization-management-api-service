<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Collection;

/**
 * Class Skill
 * @package App\Models
 * @property string title_en
 * @property string title
 * @property int row_status
 * @property-read  Collection humanResources
 * @property-read  Collection humanResourceTemplates
 */
class Skill extends BaseModel
{
    use SoftDeletes, HasFactory;

    /**
     * @var string[]
     */
    protected $guarded = ['id'];

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
}
