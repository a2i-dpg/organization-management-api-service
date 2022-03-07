<?php

namespace App\Models;


class IndustryAssociationConfig extends BaseModel
{

    protected $guarded = BaseModel::COMMON_GUARDED_FIELDS_NON_SOFT_DELETE;
    protected $casts = [
        'payment_gateways' => 'array'
    ];

    public const SESSION_TYPE_JUNE_JULY = 1;
    public const SESSION_TYPE_JANUARY_DECEMBER = 2;
    public const SESSION_TYPE = [
        self::SESSION_TYPE_JUNE_JULY => 'Jun-July Session',
        self::SESSION_TYPE_JANUARY_DECEMBER => 'January-December Session',
    ];
}
