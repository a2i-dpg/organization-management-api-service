<?php

namespace App\Models;

use App\Traits\Scopes\ScopeFilterByOrganization;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;

/**
 * Class Rank
 * @package App\Models
 * @property int organization_id
 * @property string title_en
 * @property string title_bn
 * @property string|null grade
 * @property int|null display_order
 * @property int rank_type_id
 * @property int row_status
 * @property-read Organization organization
 * @property-read RankType rankType
 */
class Rank extends BaseModel
{
    use softDeletes, HasFactory,ScopeFilterByOrganization;

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
    public function rankType(): BelongsTo
    {
        return $this->belongsTo(RankType::class);
    }

}
