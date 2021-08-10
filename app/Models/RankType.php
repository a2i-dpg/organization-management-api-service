<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class RankType
 * @package App\Models\
 * @property int|null organization_id
 * @property string title_en
 * @property string title_bn
 * @property string|null description
 * @property-read Organization organization
 */
class RankType extends BaseModel
{
    use SoftDeletes, HasFactory;
    /**
     * @var string[]
     */
    protected  $guarded = ['id'];

    /**
     * @return BelongsTo
     */
    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }
}
