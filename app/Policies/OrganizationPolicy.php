<?php

namespace App\Policies;

use App\Models\Organization;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class OrganizationPolicy extends BasePolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any organizations.
     *
     * @param  User  $authUser
     * @return mixed
     */
    public function viewAny(User $authUser)
    {
        return $authUser->hasPermission('view_any_organization');
    }

    /**
     * Determine whether the user can view the organization.
     *
     * @param  User $authUser
     * @param  Organization  $organization
     * @return mixed
     */
    public function view(User $authUser, Organization $organization)
    {
        return true; //$authUser->hasPermission('view_single_organization');
    }

    /**
     * Determine whether the user can create organizations.
     *
     * @param  User $authUser
     * @return mixed
     */
    public function create(User $authUser)
    {
        return $authUser->hasPermission('create_organization');
    }

    /**
     * Determine whether the user can update the organization.
     *
     * @param  User $authUser
     * @param  Organization  $organization
     * @return mixed
     */
    public function update(User $authUser, Organization $organization)
    {
        return $authUser->hasPermission('update_organization');
    }

    /**
     * Determine whether the user can delete the organization.
     *
     * @param  User $authUser
     * @param  Organization  $organization
     * @return mixed
     */
    public function delete(User $authUser, Organization $organization)
    {
        return $authUser->hasPermission('delete_organization');
    }
}
