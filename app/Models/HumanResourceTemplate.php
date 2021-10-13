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
 * Class HumanResourceTemplate
 * @package App\Models
 * @property int id
 * @property string title_en
 * @property string title
 * @property int display_order
 * @property int is_designation
 * @property int parent_id
 * @property int organization_id
 * @property int organization_unit_type_id
 * @property int rank_id
 * @property int status
 * @property int row_status
 * @property-read  Organization organization
 * @property-read  OrganizationUnitType organizationUnitType
 * @property-read  HumanResourceTemplate parent
 * @property-read  Rank rank
 * @property-read  Collection skills
 * @property-read  Collection childTemplates
 * @property-read  Collection children
 */
class HumanResourceTemplate extends BaseModel
{
    use SoftDeletes, ScopeRowStatusTrait, ScopeFilterByOrganization;

    public const ROW_STATUS_ACTIVE = 1;
    public const ROW_STATUS_INACTIVE = 0;


    /**
     * @var string[]
     */
    protected $guarded = ['id'];

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
    public function organizationUnitType(): BelongsTo
    {
        return $this->belongsTo(OrganizationUnitType::class);
    }

    /**
     * @return BelongsTo
     */
    public function parent(): BelongsTo
    {
        return $this->belongsTo(HumanResourceTemplate::class);
    }

    /**
     * @return BelongsTo
     */
    public function rank(): BelongsTo
    {
        return $this->belongsTo(Rank::class);
    }

    /**
     * @return HasMany
     */
    public function children(): HasMany
    {
        return $this->hasMany(self::class, 'parent_id');
    }

    /**
     * @return HasMany
     */
    public function childTemplates(): HasMany
    {
        return $this->hasMany(self::class, 'id');
    }

    /**
     * @return BelongsToMany
     */
    public function skills(): BelongsToMany
    {
        return $this->belongsToMany(Skill::class, 'human_resource_template_skills');
    }

}
