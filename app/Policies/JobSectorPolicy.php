<?php

namespace App\Policies;

use App\Models\JobSector;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class JobSectorPolicy
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
        return $authUser->hasPermission('view_any_job_sector');

    }

    /**
     * Determine whether the user can view the jobSector.
     *
     * @param User $authUser
     * @param JobSector $jobSector
     * @return bool
     */
    public function view(User $authUser, JobSector $jobSector): bool
    {
        return $authUser->hasPermission('view_single_job_sector');

    }

    /**
     * Determine whether the user can create jobSectors.
     *
     * @param User $authUser
     * @return bool
     */
    public function create(User $authUser): bool
    {
        return $authUser->hasPermission('create_job_sector');
    }

    /**
     * Determine whether the user can update the jobSector.
     *
     * @param User $authUser
     * @param JobSector $jobSector
     * @return bool
     */
    public function update(User $authUser, JobSector $jobSector): bool
    {
        return $authUser->hasPermission('update_job_sector');
    }

    /**
     * Determine whether the user can delete the jobSector.
     *
     * @param User $authUser
     * @param JobSector $jobSector
     * @return bool
     */
    public function delete(User $authUser, JobSector $jobSector): bool
    {
        return $authUser->hasPermission('delete_job_sector');
    }
}
