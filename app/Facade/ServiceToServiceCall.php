<?php

namespace App\Facade;

use Illuminate\Support\Facades\Facade;

/**
 * Class AuthUser
 * @package App\Facade
 * @method static array|mixed getAuthUserWithRolePermission(string $idpUserId)
 * @method static array|mixed getInstituteTitleByIds(array $idpUserId)
 * @method static array|mixed getYouthProfilesByIds(array $youthIds)
 *
 *
 * @see \App\Helpers\Classes\ServiceToServiceCallHandler
 */
class ServiceToServiceCall extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return 'service_to_service_call';
    }
}
