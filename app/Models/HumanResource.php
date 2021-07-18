<?php

namespace App\Models;

use App\Traits\Scopes\ScopeRowStatusTrait;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Class HumanResource
 * @package App\Models
 * @property string title_en
 * @property string title_bn
 * @property int display_order
 * @property int human_resource_template_id
 * @property int organization_id
 * @property int organization_unit_id
 * @property int parent_id
 * @property int rank_id
 * @property int is_designation
 * @property int status
 * @property array skill_ids
 * @property-read HumanResourceTemplate humanResourceTemplate
 * @property-read Organization organization
 * @property-read OrganizationUnit organizationUnit
 * @property-read  HumanResource parent
 */

class HumanResource extends BaseModel
{
    use ScopeRowStatusTrait;

    protected $guarded = ['id'];

    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }

    public function organizationUnit(): BelongsTo
    {
        return $this->belongsTo(OrganizationUnit::class);
    }

    public function humanResourceTemplate(): BelongsTo
    {
        return $this->belongsTo(HumanResourceTemplate::class);
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(HumanResource::class);
    }

    public function children(): HasMany
    {
        return $this->hasMany(self::class, 'parent_id');
    }

    public function rank(): BelongsTo
    {
        return $this->belongsTo(Rank::class);
    }
}
