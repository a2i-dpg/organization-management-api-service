<?php

namespace App\Policies;

use App\OrganizationUnitType;
use App\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class OrganizationUnitTypePolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any organizationUnitTypes.
     *
     * @param  App\User  $user
     * @return mixed
     */
    public function viewAny(User $user)
    {
        //
    }

    /**
     * Determine whether the user can view the organizationUnitType.
     *
     * @param  App\User  $user
     * @param  App\OrganizationUnitType  $organizationUnitType
     * @return mixed
     */
    public function view(User $user, OrganizationUnitType $organizationUnitType)
    {
        //
    }

    /**
     * Determine whether the user can create organizationUnitTypes.
     *
     * @param  App\User  $user
     * @return mixed
     */
    public function create(User $user)
    {
        //
    }

    /**
     * Determine whether the user can update the organizationUnitType.
     *
     * @param  App\User  $user
     * @param  App\OrganizationUnitType  $organizationUnitType
     * @return mixed
     */
    public function update(User $user, OrganizationUnitType $organizationUnitType)
    {
        //
    }

    /**
     * Determine whether the user can delete the organizationUnitType.
     *
     * @param  App\User  $user
     * @param  App\OrganizationUnitType  $organizationUnitType
     * @return mixed
     */
    public function delete(User $user, OrganizationUnitType $organizationUnitType)
    {
        //
    }
}
