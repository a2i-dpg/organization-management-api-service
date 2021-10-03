<?php

namespace App\Policies;

use App\Models\Occupation;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class OccupationPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any occupations.
     *
     * @param User $authUser
     * @return bool
     */
    public function viewAny(User $authUser): bool
    {
        return $authUser->hasPermission('view_any_occupation');

    }

    /**
     * Determine whether the user can view the occupation.
     *
     * @param User $authUser
     * @param Occupation $occupation
     * @return bool
     */
    public function view(User $authUser, Occupation $occupation): bool
    {
        return $authUser->hasPermission('view_single_occupation');
    }

    /**
     * Determine whether the user can create occupations.
     *
     * @param User $authUser
     * @return bool
     */
    public function create(User $authUser): bool
    {
        return $authUser->hasPermission('create_occupation');
    }

    /**
     * Determine whether the user can update the occupation.
     *
     * @param User $authUser
     * @param Occupation $occupation
     * @return bool
     */
    public function update(User $authUser, Occupation $occupation): bool
    {
        return $authUser->hasPermission('update_occupation');

    }

    /**
     * Determine whether the user can delete the occupation.
     *
     * @param User $authUser
     * @param Occupation $occupation
     * @return bool
     */
    public function delete(User $authUser, Occupation $occupation): bool
    {
        return $authUser->hasPermission('delete_occupation');
    }
}
