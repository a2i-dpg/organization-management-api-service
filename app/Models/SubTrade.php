<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Collection;

/**
 * Class SubTrade
 * @package App\Models
 * @property int id
 * @property string title
 * @property string title_en
 * @property-read  Collection organizations
 */
class SubTrade extends BaseModel
{
    use SoftDeletes;

    protected $hidden = ['pivot'];
    protected $table = 'sub_trades';

    public function organizations(): BelongsToMany
    {
        return $this->belongsToMany(SubTrade::class, 'organization_sub_trade', 'organization_id', 'sub_trade_id');
    }

    public function trade(): BelongsTo
    {
        return $this->belongsTo(Trade::class);

    }

}
