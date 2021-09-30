<?php

namespace App\Policies;

use App\OrganizationUnit;
use App\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class OrganizationUnitPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any organizationUnits.
     *
     * @param  App\User  $user
     * @return mixed
     */
    public function viewAny(User $user)
    {
        //
    }

    /**
     * Determine whether the user can view the organizationUnit.
     *
     * @param  App\User  $user
     * @param  App\OrganizationUnit  $organizationUnit
     * @return mixed
     */
    public function view(User $user, OrganizationUnit $organizationUnit)
    {
        //
    }

    /**
     * Determine whether the user can create organizationUnits.
     *
     * @param  App\User  $user
     * @return mixed
     */
    public function create(User $user)
    {
        //
    }

    /**
     * Determine whether the user can update the organizationUnit.
     *
     * @param  App\User  $user
     * @param  App\OrganizationUnit  $organizationUnit
     * @return mixed
     */
    public function update(User $user, OrganizationUnit $organizationUnit)
    {
        //
    }

    /**
     * Determine whether the user can delete the organizationUnit.
     *
     * @param  App\User  $user
     * @param  App\OrganizationUnit  $organizationUnit
     * @return mixed
     */
    public function delete(User $user, OrganizationUnit $organizationUnit)
    {
        //
    }
}
