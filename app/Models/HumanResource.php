<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class HumanResource
 * @package App\Models
 * @property int id
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
 * @property int row_status
 * @property array skill_ids
 * @property-read HumanResourceTemplate humanResourceTemplate
 * @property-read Organization organization
 * @property-read OrganizationUnit organizationUnit
 * @property-read  HumanResource parent
 */

class HumanResource extends BaseModel
{
    use SoftDeletes, HasFactory;

    /**
     * @var string[]
     */
    protected $guarded = ['id'];

    /**
     * @var string[]
     */
    protected $casts = [
        'skill_ids' => 'array',
    ];


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
    public function humanResourceTemplate(): BelongsTo
    {
        return $this->belongsTo(HumanResourceTemplate::class);
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
}
