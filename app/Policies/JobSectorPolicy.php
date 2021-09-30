<?php

namespace App\Policies;

use App\JobSector;
use App\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class JobSectorPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any jobSectors.
     *
     * @param  App\User  $user
     * @return mixed
     */
    public function viewAny(User $user)
    {
        //
    }

    /**
     * Determine whether the user can view the jobSector.
     *
     * @param  App\User  $user
     * @param  App\JobSector  $jobSector
     * @return mixed
     */
    public function view(User $user, JobSector $jobSector)
    {
        //
    }

    /**
     * Determine whether the user can create jobSectors.
     *
     * @param  App\User  $user
     * @return mixed
     */
    public function create(User $user)
    {
        //
    }

    /**
     * Determine whether the user can update the jobSector.
     *
     * @param  App\User  $user
     * @param  App\JobSector  $jobSector
     * @return mixed
     */
    public function update(User $user, JobSector $jobSector)
    {
        //
    }

    /**
     * Determine whether the user can delete the jobSector.
     *
     * @param  App\User  $user
     * @param  App\JobSector  $jobSector
     * @return mixed
     */
    public function delete(User $user, JobSector $jobSector)
    {
        //
    }
}
