<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrganizationUnitType extends BaseModel
{

    protected $guarded = ['id'];


    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }
}
