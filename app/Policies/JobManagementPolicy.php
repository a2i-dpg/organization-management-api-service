<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class JobManagementPolicy extends BasePolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any Job.
     * @param User $authUser
     * @return bool
     */
    public function viewAny(User $authUser): bool
    {
        return $authUser->hasPermission('view_any_job');
    }

    /**
     * Determine whether the user can view specific job.
     * @param User $authUser
     * @return bool
     */
    public function view(User $authUser): bool
    {
        return $authUser->hasPermission('view_single_job');
    }


    /**
     * Determine whether the user can create industryAssociations.
     * @param User $authUser
     * @return bool
     */
    public function create(User $authUser): bool
    {
        return $authUser->hasPermission('create_job');

    }
}
