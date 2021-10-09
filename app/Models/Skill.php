<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class Skill
 * @package App\Models
 * @property string title_en
 * @property string title
 * @property int row_status
 * @property-read Organization organization
 */
class Skill extends BaseModel
{
    use SoftDeletes, HasFactory;

    /**
     * @var string[]
     */
    protected $guarded = ['id'];

    protected $hidden = ["pivot"];

    public function humanResources(): BelongsToMany
    {
        return $this->belongsToMany(HumanResource::class, 'human_resource_skills');
    }
}
