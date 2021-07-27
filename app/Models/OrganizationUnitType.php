<?php

namespace App\Models;

use App\Traits\Scopes\ScopeRowStatusTrait;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Class OrganizationUnitType
 * @package App\Models
 * @property string title_en
 * @property string title_bn
 * @property-read int organization_id
 * @property int row_status
 * @property-read Organization $organization
 * */
class OrganizationUnitType extends BaseModel
{
    use ScopeRowStatusTrait;

    /**
     * @var string[]
     */
    protected $guarded = ['id'];

    /**
     * @return BelongsTo
     */
    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }
}
