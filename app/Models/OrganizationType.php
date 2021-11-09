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

    public const ORGANIZATION_TYPE_IS_GOVERNMENT_TRUE = 1;
    public const ORGANIZATION_TYPE_IS_GOVERNMENT_FALSE = 0;

    /**
     * @var string[]
     */
    protected $guarded = BaseModel::COMMON_GUARDED_FIELDS_SOFT_DELETE;

    /** @return HasMany */
    public function organizations(): HasMany
    {
        return $this->hasMany(Organization::class);
    }

}
