<?php

namespace App\Models;

use App\Traits\Scopes\ScopeRowStatusTrait;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Collection;


/**
 * Class JobSector
 * @package App\Models
 * @property string title_en
 * @property string title
 * @property-read  Collection children
 */
class JobSector extends BaseModel
{
    use SoftDeletes, ScopeRowStatusTrait;

    public const ROW_STATUS_ACTIVE = 1;
    public const ROW_STATUS_INACTIVE = 0;

    protected $guarded = BaseModel::COMMON_GUARDED_FIELDS_SOFT_DELETE;

    public function occupations(): HasMany
    {
        return $this->hasMany(Occupation::class);
    }
}
