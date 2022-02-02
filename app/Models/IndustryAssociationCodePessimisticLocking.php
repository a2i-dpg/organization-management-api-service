<?php

namespace App\Models;

/**
 * Class IndustryAssociationCodePessimisticLocking
 * @property int last_incremental_value
 */
class IndustryAssociationCodePessimisticLocking extends BaseModel
{
    public $timestamps = false;
    public $incrementing = false;
    protected $primaryKey = 'last_incremental_value';
    protected $guarded = [];
    protected $casts = [
        'last_incremental_value' => 'integer'
    ];

}
