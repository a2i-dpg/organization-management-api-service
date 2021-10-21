<?php

namespace App\Models;

use App\Traits\Scopes\ScopeFilterByOrganization;
use App\Traits\Scopes\ScopeRowStatusTrait;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Collection;

/**
 * Class HumanResource
 * @package App\Models
 * @property int id
 * @property string title_en
 * @property string title
 * @property int display_order
 * @property int organization_id
 * @property int organization_unit_id
 * @property int parent_id
 * @property int rank_id
 * @property int is_designation
 * @property int status
 * @property int row_status
 * @property-read Organization organization
 * @property-read OrganizationUnit organizationUnit
 * @property-read  HumanResource parent
 * @property-read  Collection skills
 * @property-read  Collection children
 */
class HumanResource extends BaseModel
{
    use SoftDeletes, ScopeRowStatusTrait, ScopeFilterByOrganization;

    public const ROW_STATUS_ACTIVE = 1;
    public const ROW_STATUS_INACTIVE = 0;


    /**
     * @var string[]
     */
    protected $guarded = BaseModel::COMMON_GUARDED_FIELDS_SOFT_DELETE;

    /**
     * @var string[]
     */
    protected $hidden = ["pivot"];

    /**
     * @return BelongsTo
     */
    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }

    /**
     * @return BelongsTo
     */
    public function organizationUnit(): BelongsTo
    {
        return $this->belongsTo(OrganizationUnit::class);
    }

    /**
     * @return BelongsTo
     */
    public function parent(): BelongsTo
    {
        return $this->belongsTo(HumanResource::class);
    }

    /**
     * @return HasMany
     */
    public function children(): HasMany
    {
        return $this->hasMany(self::class, 'parent_id');
    }

    /**
     * @return BelongsTo
     */
    public function rank(): BelongsTo
    {
        return $this->belongsTo(Rank::class);
    }

    /**
     * @return BelongsToMany
     */
    public function skills(): BelongsToMany
    {
        return $this->belongsToMany(Skill::class, 'human_resource_skills');
    }
}
