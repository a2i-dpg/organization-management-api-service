<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Class BaseModel
 * @package App\Models
 */
abstract class BaseModel extends Model
{
    use HasFactory;

    public const COMMON_GUARDED_FIELDS_SIMPLE = ['id', 'created_at', 'updated_at'];
    public const COMMON_GUARDED_FIELDS_SIMPLE_SOFT_DELETE = ['id', 'created_at', 'updated_at', 'deleted_at'];
    public const COMMON_GUARDED_FIELDS_SOFT_DELETE = ['id', 'created_by', 'updated_by', 'created_at', 'updated_at', 'deleted_at'];
    public const COMMON_GUARDED_FIELDS_NON_SOFT_DELETE = ['id', 'created_by', 'updated_by', 'created_at', 'updated_at'];

    public const ROW_ORDER_ASC = 'ASC';
    public const ROW_ORDER_DESC = 'DESC';

    public const ROW_STATUS_ACTIVE = 1;
    public const ROW_STATUS_INACTIVE = 0;

    public const ROW_STATUS_PENDING = 2;
    public const ROW_STATUS_REJECTED = 3;

    public const PASSWORD_MIN_LENGTH = 8;
    public const PASSWORD_MAX_LENGTH = 50;

    public const MOBILE_REGEX = 'regex: /^(01[3-9]\d{8})$/';

    public const ORGANIZATION_USER_TYPE = 2;

    /** Client Url End Point Type*/
    public const CORE_CLIENT_URL_TYPE = "CORE";

    /** pagination default size */
    public const DEFAULT_PAGE_SIZE = 10;

}
