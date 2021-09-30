<?php

namespace App\Policies;

use App\OrganizationType;
use App\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class OrganizationTypePolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any organizationTypes.
     *
     * @param  App\User  $user
     * @return mixed
     */
    public function viewAny(User $user)
    {
        //
    }

    /**
     * Determine whether the user can view the organizationType.
     *
     * @param  App\User  $user
     * @param  App\OrganizationType  $organizationType
     * @return mixed
     */
    public function view(User $user, OrganizationType $organizationType)
    {
        //
    }

    /**
     * Determine whether the user can create organizationTypes.
     *
     * @param  App\User  $user
     * @return mixed
     */
    public function create(User $user)
    {
        //
    }

    /**
     * Determine whether the user can update the organizationType.
     *
     * @param  App\User  $user
     * @param  App\OrganizationType  $organizationType
     * @return mixed
     */
    public function update(User $user, OrganizationType $organizationType)
    {
        //
    }

    /**
     * Determine whether the user can delete the organizationType.
     *
     * @param  App\User  $user
     * @param  App\OrganizationType  $organizationType
     * @return mixed
     */
    public function delete(User $user, OrganizationType $organizationType)
    {
        //
    }
}
