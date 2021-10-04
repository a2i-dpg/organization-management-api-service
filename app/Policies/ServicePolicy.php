<?php

namespace App\Policies;

use App\Models\Service;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class ServicePolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any services.
     *
     * @param User $authUser
     * @return bool
     */
    public function viewAny(User $authUser): bool
    {
        return $authUser->hasPermission('view_any_service');
    }

    /**
     * Determine whether the user can view the service.
     *
     * @param User $authUser
     * @param Service $service
     * @return bool
     */
    public function view(User $authUser, Service $service): bool
    {
        return $authUser->hasPermission('view_single_service');
    }

    /**
     * Determine whether the user can create services.
     *
     * @param User $authUser
     * @return bool
     */
    public function create(User $authUser): bool
    {
        return $authUser->hasPermission('create_service');
    }

    /**
     * Determine whether the user can update the service.
     *
     * @param User $authUser
     * @param Service $service
     * @return bool
     */
    public function update(User $authUser, Service $service): bool
    {
        return $authUser->hasPermission('update_service');
    }

    /**
     * Determine whether the user can delete the service.
     *
     * @param User $authUser
     * @param Service $service
     * @return bool
     */
    public function delete(User $authUser, Service $service): bool
    {
        return $authUser->hasPermission('delete_service');
    }
}
