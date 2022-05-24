<?php

namespace App\Policies;


use App\Models\FourIRInitiative;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class FourIRInitiativePolicy
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

        return $authUser->hasPermission('view_any_4ir_initiatives');

    }

    /**
     * Determine whether the user can view the jobSector.
     *
     * @param User $authUser
     * @param FourIRInitiative $fourIRInitiative
     * @return bool
     */
    public function view(User $authUser, FourIRInitiative $fourIRInitiative): bool
    {
        return $authUser->hasPermission('view_single_4ir_initiatives');

    }

    /**
     * Determine whether the user can create jobSectors.
     *
     * @param User $authUser
     * @return bool
     */
    public function create(User $authUser): bool
    {
        return $authUser->hasPermission('create_4ir_initiatives');
    }

    /**
     * Determine whether the user can update the jobSector.
     *
     * @param User $authUser
     * @param FourIRInitiative $fourIRInitiative
     * @return bool
     */
    public function update(User $authUser, FourIRInitiative $fourIRInitiative): bool
    {
        return $authUser->hasPermission('update_4ir_initiatives');
    }

    /**
     * Determine whether the user can delete the jobSector.
     *
     * @param User $authUser
     * @param FourIRInitiative $fourIRInitiative
     * @return bool
     */
    public function delete(User $authUser, FourIRInitiative $fourIRInitiative): bool
    {
        return $authUser->hasPermission('delete_4ir_initiatives');
    }

    /**
     * @param User $authUser
     * @return bool
     */
    public function viewAnyInitiativeStep(User $authUser): bool
    {
        return $authUser->hasPermission('view_any_4ir_initiative_step');

    }

    /**
     * @param User $authUser
     * @return bool
     */
    public function viewSingleInitiativeStep(User $authUser): bool
    {
        return $authUser->hasPermission('view_single_4ir_initiative_step');

    }

    /**
     * @param User $authUser
     * @return bool
     */
    public function creatInitiativeStep(User $authUser): bool
    {
        return $authUser->hasPermission('create_4ir_initiative_step');

    }

    /**
     * @param User $authUser
     * @return bool
     */
    public function updateInitiativeStep(User $authUser): bool
    {

        return $authUser->hasPermission('update_4ir_initiatives_step');

    }

    /**
     * @param User $authUser
     * @return bool
     */
    public function deleteInitiativeStep(User $authUser): bool
    {
        return $authUser->hasPermission('delete_4ir_initiative_step');

    }
}
