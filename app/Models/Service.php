<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Traits\Scopes\ScopeRowStatusTrait;

/**
 * Class Service
 * @package App\Models
 * @property int organization_id
 * @property string title_en
 * @property string title_bn
 * @property-read Organization organization
 * @method static \Illuminate\Database\Eloquent\Builder|Organization acl()
 * @method static Builder|Organization active()
 * @method static Builder|Organization newModelQuery()
 * @method static Builder|Organization newQuery()
 * @method static Builder|Organization query()
 */
class Service extends BaseModel
{
    use ScopeRowStatusTrait;

    protected $guarded = ['id'];

    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }
}
