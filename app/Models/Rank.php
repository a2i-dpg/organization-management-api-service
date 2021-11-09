<?php

namespace App\Models;

use App\Traits\Scopes\ScopeFilterByOrganization;
use App\Traits\Scopes\ScopeRowStatusTrait;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class Rank
 * @package App\Models
 * @property int organization_id
 * @property int id
 * @property string title_en
 * @property string title
 * @property string|null grade
 * @property int|null display_order
 * @property int rank_type_id
 * @property int row_status
 * @property-read Organization organization
 * @property-read RankType rankType
 */
class Rank extends BaseModel
{
    use softDeletes, ScopeRowStatusTrait, ScopeFilterByOrganization;

    public const ROW_STATUS_ACTIVE = 1;
    public const ROW_STATUS_INACTIVE = 0;

    /**
     * @var string[]
     */
    protected $guarded = BaseModel::COMMON_GUARDED_FIELDS_SOFT_DELETE;

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
    public function rankType(): BelongsTo
    {
        return $this->belongsTo(RankType::class);
    }

}
