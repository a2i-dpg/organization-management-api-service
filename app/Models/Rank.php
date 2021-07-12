<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Class Rank
 * @package App\Models
 * @property int|null organization_id
 * @property string title_en
 * @property string title_bn
 * @property int rank_type_id
 * @property-read Organization organization
 * @property-read RankType rankType
 */
class Rank extends Model
{
    protected $guarded = [];

    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }

    public function rankType(): BelongsTo
    {
        return $this->belongsTo(RankType::class);
    }
}
