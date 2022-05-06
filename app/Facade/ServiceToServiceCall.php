<?php

namespace App\Facade;

use Illuminate\Support\Facades\Facade;

/**
 * Class AuthUser
 * @package App\Facade
 * @method static array|mixed getAuthUserWithRolePermission(string $idpUserId)
 * @method static array|mixed getInstituteTitleByIds(array $idpUserId)
 * @method static array|mixed getYouthProfilesByIds(array $youthIds)
 * @method static array|mixed createEventAfterInterviewScheduleAssign(array $data)
 * @method static array|mixed getPermissionSubGroupsByTitle(string $permissionSubGroupTitle)
 * @method static array|mixed getCoreUserByUsername(string $username)
 * @method static array|mixed getYouthUserByUsername(string $username)
 * @method static array|mixed getDomain(string $username)
 * @method static array|mixed createFourIrUser(array $payload)
 * @method static array|mixed getFourIrCourseList(array $payload)
 * @method static array|mixed getFourIrCourseByCourseId(int $courseId)
 * @method static array|mixed createFourIrCourse(array $payload)
 * @method static array|mixed updateFourIrCourse(array $payload, int $courseId)
 * @method static array|mixed approveFourIrCourse(int $courseId)
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
