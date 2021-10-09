<?php

namespace App\Models;

use App\Traits\Scopes\ScopeFilterByOrganization;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\Auth;

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
    use SoftDeletes, HasFactory,ScopeFilterByOrganization;

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
