<?php

namespace App\Policies;

use App\Models\OrganizationUnitType;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class OrganizationUnitTypePolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any organizationUnitTypes.
     *
     * @param User $authUser
     * @return bool
     */
    public function viewAny(User $authUser): bool
    {
        return $authUser->hasPermission('view_any_organization_unit_type');
    }

    /**
     * Determine whether the user can view the organizationUnitType.
     *
     * @param User $authUser
     * @param OrganizationUnitType $organizationUnitType
     * @return bool
     */
    public function view(User $authUser, OrganizationUnitType $organizationUnitType): bool
    {
        return $authUser->hasPermission('view_single_organization_unit_type');
    }

    /**
     * Determine whether the user can create organizationUnitTypes.
     *
     * @param User $authUser
     * @return bool
     */
    public function create(User $authUser): bool
    {
        return $authUser->hasPermission('create_organization_unit_type');
    }

    /**
     * Determine whether the user can update the organizationUnitType.
     *
     * @param User $authUser
     * @param OrganizationUnitType $organizationUnitType
     * @return bool
     */
    public function update(User $authUser, OrganizationUnitType $organizationUnitType): bool
    {
        return $authUser->hasPermission('update_organization_unit_type');
    }

    /**
     * Determine whether the user can delete the organizationUnitType.
     *
     * @param User $authUser
     * @param OrganizationUnitType $organizationUnitType
     * @return bool
     */
    public function delete(User $authUser, OrganizationUnitType $organizationUnitType): bool
    {
        return $authUser->hasPermission('delete_organization_unit_type');
    }
}
