<?php

namespace App\Policies;

use App\Models\OrganizationUnit;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class OrganizationUnitPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any organizationUnits.
     *
     * @param User $authUser
     * @return bool
     */
    public function viewAny(User $authUser): bool
    {
        return $authUser->hasPermission('view_any_organization_unit');
    }

    /**
     * Determine whether the user can view the organizationUnit.
     *
     * @param User $authUser
     * @param OrganizationUnit $organizationUnit
     * @return bool
     */
    public function view(User $authUser, OrganizationUnit $organizationUnit): bool
    {
        return $authUser->hasPermission('view_single_organization_unit');
    }

    /**
     * Determine whether the user can create organizationUnits.
     *
     * @param User $authUser
     * @return bool
     */
    public function create(User $authUser): bool
    {
        return $authUser->hasPermission('create_organization_unit');
    }

    /**
     * Determine whether the user can update the organizationUnit.
     *
     * @param User $authUser
     * @param OrganizationUnit $organizationUnit
     * @return bool
     */
    public function update(User $authUser, OrganizationUnit $organizationUnit): bool
    {
        return $authUser->hasPermission('update_organization_unit');
    }

    /**
     * Determine whether the user can delete the organizationUnit.
     *
     * @param User $authUser
     * @param OrganizationUnit $organizationUnit
     * @return bool
     */
    public function delete(User $authUser, OrganizationUnit $organizationUnit): bool
    {
        return $authUser->hasPermission('delete_organization_unit');
    }
}
