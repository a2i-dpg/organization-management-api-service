<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CompanyInfoVisibility extends BaseModel
{
    use SoftDeletes;

    protected $guarded = BaseModel::COMMON_GUARDED_FIELDS_SIMPLE_SOFT_DELETE;


    public const  IS_COMPANY_NAME_VISIBLE_TRUE = 1;
    public const  IS_COMPANY_NAME_VISIBLE_FALSE = 0;

    public const COMPANY_NAME_VISIBILITY = [
        self::IS_COMPANY_NAME_VISIBLE_TRUE,
        self::IS_COMPANY_NAME_VISIBLE_FALSE
    ];

    public const  IS_COMPANY_ADDRESS_VISIBLE_TRUE = 1;
    public const  IS_COMPANY_ADDRESS_VISIBLE_FALSE = 0;


    public const COMPANY_ADDRESS_VISIBILITY = [
        self::IS_COMPANY_ADDRESS_VISIBLE_TRUE,
        self::IS_COMPANY_ADDRESS_VISIBLE_FALSE
    ];

    public const  IS_COMPANY_BUSINESS_VISIBLE_TRUE = 1;
    public const  IS_COMPANY_BUSINESS_VISIBLE_FALSE = 0;


    public const COMPANY_BUSINESS_VISIBILITY = [
        self::IS_COMPANY_ADDRESS_VISIBLE_TRUE,
        self::IS_COMPANY_ADDRESS_VISIBLE_FALSE
    ];

}
