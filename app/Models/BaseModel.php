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

    public const ROW_STATUS_ACTIVE = 1;
    public const ROW_STATUS_INACTIVE = 0;
    public const ROW_ORDER_ASC = 'ASC';
    public const ROW_ORDER_DESC = 'DESC';

    public const MOBILE_REGEX= 'regex: /^(01[3-9]\d{8})$/';

    public const ORGANIZATION_TYPE=2;

    public const ORGANIZATION_USER_REGISTRATION_ENDPOINT_LOCAL='http://localhost:8000/api/v1/';
    public const ORGANIZATION_USER_REGISTRATION_ENDPOINT_REMOTE="https://core.local:8010/api/v1/";

}
