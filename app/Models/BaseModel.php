<?php

namespace App\Models;


use App\Traits\Scopes\ScopeRowStatusTrait;
use Illuminate\Database\Eloquent\Model;

/**
 * Class BaseModel
 * @package App\Models
 */
abstract class BaseModel extends Model
{
    use ScopeRowStatusTrait;

    public const COMMON_GUARDED_FIELDS_SOFT_DELETE = ['id', 'created_by', 'updated_by', 'created_at', 'updated_at', 'deleted_at'];

    public const ROW_STATUS_ACTIVE = 1;
    public const ROW_STATUS_INACTIVE = 0;
    public const ROW_ORDER_ASC = 'ASC';
    public const ROW_ORDER_DESC = 'DESC';


    public const ORGANIZATION_TYPE_GOVT = 1;
    public const ORGANIZATION_TYPE_PRIVATE = 2;
    public const ORGANIZATION_TYPE_NGO = 3;
    public const ORGANIZATION_TYPE_INTERNATIONAL = 4;

    public const ORGANIZATION_TYPE = [
        self::ORGANIZATION_TYPE_GOVT => 1,
        self::ORGANIZATION_TYPE_PRIVATE => 2,
        self:: ORGANIZATION_TYPE_NGO => 3,
        self::ORGANIZATION_TYPE_INTERNATIONAL => 4,
    ];

    public const MOBILE_REGEX = 'regex: /^(01[3-9]\d{8})$/';

    public const ORGANIZATION_USER_TYPE = 2;

    /** Client Url End Point Type*/
    public const CORE_CLIENT_URL_TYPE = "CORE";

}
