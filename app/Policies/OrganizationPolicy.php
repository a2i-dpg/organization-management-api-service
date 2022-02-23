<?php

namespace App\Policies;

use App\Models\Organization;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Support\Facades\Log;

class OrganizationPolicy extends BasePolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any organizations.
     *
     * @param User $authUser
     * @return bool
     */
    public function viewAny(User $authUser): bool
    {
        return $authUser->hasPermission('view_any_organization');
    }

    /**
     * Determine whether the user can view the organization.
     *
     * @param User $authUser
     * @param Organization $organization
     * @return bool
     */
    public function view(User $authUser, Organization $organization): bool
    {
        return $authUser->hasPermission('view_single_organization');
    }

    /**
     * Determine whether the user can create organizations.
     *
     * @param User $authUser
     * @return bool
     */
    public function create(User $authUser): bool
    {
        return $authUser->hasPermission('create_organization');
    }

    /**
     * Determine whether the user can update the organization.
     * @param User $authUser
     * @param Organization $organization
     * @return bool
     */
    public function update(User $authUser, Organization $organization): bool
    {
        return $authUser->hasPermission('update_organization');
    }

    /**
     * Determine whether the user can delete the organization.
     *
     * @param User $authUser
     * @param Organization $organization
     * @return bool
     */
    public function delete(User $authUser, Organization $organization): bool
    {
        return $authUser->hasPermission('delete_organization');
    }

    /**
     * @param User $authUser
     * @param Organization $organization
     * @return bool
     */
    public function viewProfile(User $authUser, Organization $organization): bool
    {
        return $authUser->hasPermission('view_organization_profile');
    }

    /**
     * @param User $authUser
     * @param Organization $organization
     * @return bool
     */
    public function updateProfile(User $authUser, Organization $organization): bool
    {
        return $authUser->hasPermission('update_organization_profile');
    }
}
