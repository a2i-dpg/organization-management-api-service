<?php

namespace App\Policies;


use App\Models\FourIRSector;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class FourIRSectorPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any jobSectors.
     *
     * @param User $authUser
     * @return bool
     */
    public function viewAny(User $authUser): bool
    {
        return $authUser->hasPermission('view_any_4ir_sectors');

    }

    /**
     * Determine whether the user can view the jobSector.
     *
     * @param User $authUser
     * @param FourIRSector $fourIRSector
     * @return bool
     */
    public function view(User $authUser, FourIRSector $fourIRSector): bool
    {
        return $authUser->hasPermission('view_single_4ir_sectors');

    }

    /**
     * Determine whether the user can create jobSectors.
     *
     * @param User $authUser
     * @return bool
     */
    public function create(User $authUser): bool
    {
        return $authUser->hasPermission('create_4ir_sectors');
    }

    /**
     * Determine whether the user can update the jobSector.
     *
     * @param User $authUser
     * @param FourIRSector $fourIRSector
     * @return bool
     */
    public function update(User $authUser, FourIRSector $fourIRSector): bool
    {
        return $authUser->hasPermission('update_4ir_sectors');
    }

    /**
     * Determine whether the user can delete the jobSector.
     *
     * @param User $authUser
     * @param FourIRSector $fourIRSector
     * @return bool
     */
    public function delete(User $authUser, FourIRSector $fourIRSector): bool
    {
        return $authUser->hasPermission('delete_4ir_sectors');
    }
}
