<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Collection;

/**
 * Class IndustrySubTrade
 * @package App\Models
 * @property int id
 * @property string title
 * @property string title_en
 * @property-read  Collection organizations
 */
class IndustrySubTrade extends BaseModel
{
    use SoftDeletes;

    protected $hidden = ['pivot'];
    protected $table = 'industry_sub_trades';

    public function organizations(): BelongsToMany
    {
        return $this->belongsToMany(IndustrySubTrade::class, 'organization_industry_sub_trade', 'organization_id', 'industry_sub_trade_id');
    }

    public function industryAssociationTrade(): BelongsTo
    {
        return $this->belongsTo(IndustryAssociationTrade::class);

    }

}
