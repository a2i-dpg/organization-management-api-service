<?php

namespace App\Policies;

use App\Occupation;
use App\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class OccupationPlocy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any occupations.
     *
     * @param  App\User  $user
     * @return mixed
     */
    public function viewAny(User $user)
    {
        //
    }

    /**
     * Determine whether the user can view the occupation.
     *
     * @param  App\User  $user
     * @param  App\Occupation  $occupation
     * @return mixed
     */
    public function view(User $user, Occupation $occupation)
    {
        //
    }

    /**
     * Determine whether the user can create occupations.
     *
     * @param  App\User  $user
     * @return mixed
     */
    public function create(User $user)
    {
        //
    }

    /**
     * Determine whether the user can update the occupation.
     *
     * @param  App\User  $user
     * @param  App\Occupation  $occupation
     * @return mixed
     */
    public function update(User $user, Occupation $occupation)
    {
        //
    }

    /**
     * Determine whether the user can delete the occupation.
     *
     * @param  App\User  $user
     * @param  App\Occupation  $occupation
     * @return mixed
     */
    public function delete(User $user, Occupation $occupation)
    {
        //
    }
}
