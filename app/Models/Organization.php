<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Class Organization
 * @package App\Models
 */
class Organization extends Model
{
    /**
     * @return BelongsTo
     */
    public function organizationType(): BelongsTo
    {
        return $this->belongsTo(OrganizationType::class);
    }

    protected  $guarded = [];
}
