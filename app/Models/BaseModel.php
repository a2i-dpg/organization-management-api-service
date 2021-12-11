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

    public const COMMON_GUARDED_FIELDS_ONLY_SOFT_DELETE = ['id', 'deleted_at'];
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

    public const PASSWORD_REGEX = 'regex: /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)[a-zA-Z\d]{8,}$/';
    public const PASSWORD_VALIDATION_MESSAGE = 'The password must contain at least one uppercase, lowercase letter and at least one number.[66000]';

    public const ORGANIZATION_USER_TYPE = 2;
    public const INDUSTRY_ASSOCIATION_USER_TYPE = 5;

    /** Client Url End Point Type*/
    public const CORE_CLIENT_URL_TYPE = "CORE";

    /** pagination default size */
    public const DEFAULT_PAGE_SIZE = 10;

    /** Service to service internal calling header type */
    public const DEFAULT_SERVICE_TO_SERVICE_CALL_KEY = 'service-to-service';
    public const DEFAULT_SERVICE_TO_SERVICE_CALL_FLAG_TRUE = true;
    public const DEFAULT_SERVICE_TO_SERVICE_CALL_FLAG_FALSE = false;

    public const NISE3_FROM_EMAIL = "info@nise3.com";
    public const SELF_EXCHANGE = 'institute';

    public const ADMIN_CREATED_USER_DEFAULT_PASSWORD = "ABcd1234";

    /** SAGA events Publisher & Consumer */
    public const SAGA_CORE_SERVICE = 'core_service';
    public const SAGA_INSTITUTE_SERVICE = 'institute_service';
    public const SAGA_ORGANIZATION_SERVICE = 'organization_service';
    public const SAGA_YOUTH_SERVICE = 'youth_service';
    public const SAGA_CMS_SERVICE = 'cms_service';
    public const SAGA_MAIL_SMS_SERVICE = 'mail_sms_service';
}
