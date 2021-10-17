<?php

namespace App\Policies;

use App\Models\OrganizationType;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class OrganizationTypePolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any organizationTypes.
     *
     * @param User $authUser
     * @return bool
     */
    public function viewAny(User $authUser): bool
    {
        return $authUser->hasPermission('view_any_organization_type');
    }

    /**
     * Determine whether the user can view the organizationType.
     *
     * @param User $authUser
     * @param OrganizationType $organizationType
     * @return bool
     */
    public function view(User $authUser, OrganizationType $organizationType): bool
    {
        return $authUser->hasPermission('view_single_organization_type');

    }

    /**
     * Determine whether the user can create organizationTypes.
     *
     * @param User $authUser
     * @return bool
     */
    public function create(User $authUser): bool
    {
        return $authUser->hasPermission('create_organization_type');

    }

    /**
     * Determine whether the user can update the organizationType.
     *
     * @param User $authUser
     * @param OrganizationType $organizationType
     * @return bool
     */
    public function update(User $authUser, OrganizationType $organizationType): bool
    {
        return $authUser->hasPermission('update_organization_type');
    }

    /**
     * Determine whether the user can delete the organizationType.
     *
     * @param User $authUser
     * @param OrganizationType $organizationType
     * @return bool
     */
    public function delete(User $authUser, OrganizationType $organizationType): bool
    {
        return $authUser->hasPermission('delete_organization_type');
    }
}
