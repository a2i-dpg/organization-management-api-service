<?php

namespace App\Models;

use App\Traits\Scopes\ScopeRowStatusTrait;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class Service
 * @package App\Models
 * @property string title_en
 * @property string title
 * @property-read Organization organization
 * @method static Builder|Organization acl()
 * @method static Builder|Organization active()
 * @method static Builder|Organization newModelQuery()
 * @method static Builder|Organization newQuery()
 * @method static Builder|Organization query()
 */
class Service extends BaseModel
{
    use SoftDeletes, ScopeRowStatusTrait;

    public const ROW_STATUS_ACTIVE = 1;
    public const ROW_STATUS_INACTIVE = 0;

    protected $guarded = BaseModel::COMMON_GUARDED_FIELDS_SOFT_DELETE;

    /**
     * @return BelongsToMany
     */
    public function organizationUnits(): BelongsToMany
    {
        return $this->belongsToMany(OrganizationUnit::class, 'organization_unit_services');
    }
}
