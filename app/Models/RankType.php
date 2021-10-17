<?php

namespace App\Models;

use App\Traits\Scopes\ScopeFilterByOrganization;
use App\Traits\Scopes\ScopeRowStatusTrait;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class RankType
 * @package App\Models\
 * @property int|null organization_id
 * @property string title_en
 * @property string title
 * @property string|null description
 * @property-read Organization organization
 */
class RankType extends BaseModel
{
    use SoftDeletes, ScopeRowStatusTrait, ScopeFilterByOrganization;

    public const ROW_STATUS_ACTIVE = 1;
    public const ROW_STATUS_INACTIVE = 0;

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

    /** @return HasMany */
    public function ranks(): HasMany
    {
        return $this->hasMany(Rank::class);
    }

}
