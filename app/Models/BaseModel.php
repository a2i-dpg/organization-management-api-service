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

    public const MOBILE_REGEX= 'regex: /^(01[3-9]\d{8})$/';

    public const ORGANIZATION_TYPE=2;

    /**User Type*/
    public const SYSTEM_USER = 1;
    public const ORGANIZATION_USER = 2;
    public const INSTITUTE_USER = 3;

    /**User Types*/
    public const USER_TYPES = [self::SYSTEM_USER, self::ORGANIZATION_USER, self::INSTITUTE_USER];

    /** System Admin Role Key */
    public const SYSTEM_USER_ROLE_KEY = 'system_user';

    public const USER_TYPE = [
        self::SYSTEM_USER => 'system',
        self::ORGANIZATION_USER => 'organization',
        self::INSTITUTE_USER => 'institute',
    ];


    public const ORGANIZATION_USER_REGISTRATION_ENDPOINT_LOCAL='http://localhost:8001/api/v1/';
    public const ORGANIZATION_USER_REGISTRATION_ENDPOINT_REMOTE='http://nise3-core-api-service.default/api/v1/';

}
