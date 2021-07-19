<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Class OrganizationUnitType
 * @package App\Models
 * @property string title_en
 * @property string title_bn
 * @property-read int organization_id
 * @property int row_status
 *
 * */
class OrganizationUnitType extends BaseModel
{


    protected $guarded = ['id'];


    /**
     * @return BelongsTo
     */
    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }
}
