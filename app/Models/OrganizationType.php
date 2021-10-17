<?php

namespace App\Models;

use App\Traits\Scopes\ScopeRowStatusTrait;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class OrganizationType
 * @package App\Models
 * @property string title_en
 * @property string title
 * @property int is_government
 * @property int row_status
 */
class OrganizationType extends BaseModel
{
    use SoftDeletes, ScopeRowStatusTrait;

    public const ROW_STATUS_ACTIVE = 1;
    public const ROW_STATUS_INACTIVE = 0;

    /**
     * @var string[]
     */
    protected $guarded = ['id'];

    /** @return HasMany */
    public function organizations(): HasMany
    {
        return $this->hasMany(Organization::class);
    }

}
