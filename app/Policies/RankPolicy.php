<?php

namespace App\Policies;

use App\Models\Rank;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class RankPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any ranks.
     *
     * @param User $authUser
     * @return bool
     */
    public function viewAny(User $authUser): bool
    {
        return $authUser->hasPermission('view_any_rank');
    }

    /**
     * Determine whether the user can view the rank.
     *
     * @param User $authUser
     * @param Rank $rank
     * @return bool
     */
    public function view(User $authUser, Rank $rank): bool
    {
        return $authUser->hasPermission('view_single_rank');
    }

    /**
     * Determine whether the user can create ranks.
     *
     * @param User $authUser
     * @return bool
     */
    public function create(User $authUser): bool
    {
        return $authUser->hasPermission('create_rank');
    }

    /**
     * Determine whether the user can update the rank.
     *
     * @param User $authUser
     * @param Rank $rank
     * @return bool
     */
    public function update(User $authUser, Rank $rank): bool
    {
        return $authUser->hasPermission('update_rank');
    }

    /**
     * Determine whether the user can delete the rank.
     *
     * @param User $authUser
     * @param Rank $rank
     * @return bool
     */
    public function delete(User $authUser, Rank $rank): bool
    {
        return $authUser->hasPermission('delete_rank');
    }
}
