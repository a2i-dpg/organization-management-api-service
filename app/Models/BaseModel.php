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

    public const ORGANIZATION_TYPE='organization';

    public const ORGANIZATION_USER_REGISTRATION_ENDPOINT_LOCAL='http://localhost:8001/api/v1/';
    public const ORGANIZATION_USER_REGISTRATION_ENDPOINT_REMOTE='http://nise3-core-api-service.default/api/v1/';

}
