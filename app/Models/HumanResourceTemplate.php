<?php

namespace App\Models;

use App\Traits\Scopes\ScopeFilterByOrganization;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;

/**
 * Class HumanResourceTemplate
 * @package App\Models
 * @property int id
 * @property string title_en
 * @property string title_bn
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
 */
class HumanResourceTemplate extends BaseModel
{
    use SoftDeletes, HasFactory,ScopeFilterByOrganization;

    /**
     * @var string[]
     */
    protected $guarded = ['id'];

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
    public function childTemplate(): HasMany
    {
        return $this->hasMany(self::class, 'id');
    }


}
