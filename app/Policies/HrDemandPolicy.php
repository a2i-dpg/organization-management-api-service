<?php

namespace App\Policies;

use App\Models\HrDemand;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class HrDemandPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any HrDemand.
     *
     * @param User $authUser
     * @return bool
     */
    public function viewAny(User $authUser): bool
    {
        return $authUser->hasPermission('view_any_hr_demand');
    }

    /**
     * Determine whether the TSP user can view any HrDemand.
     *
     * @param User $authUser
     * @return bool
     */
    public function viewAnyByInstitute(User $authUser): bool
    {
        return $authUser->hasPermission('view_any_hr_demand_by_institute');
    }

    /**
     * Determine whether the user can view the HrDemand.
     *
     * @param User $authUser
     * @return bool
     */
    public function view(User $authUser): bool
    {
        return $authUser->hasPermission('view_single_hr_demand');
    }

    /**
     * Determine whether the TSP user can view the HrDemand.
     *
     * @param User $authUser
     * @return bool
     */
    public function viewByInstitute(User $authUser): bool
    {
        return $authUser->hasPermission('view_single_hr_demand_by_institute');
    }

    /**
     * Determine whether the user can create HrDemand.
     *
     * @param User $authUser
     * @return bool
     */
    public function create(User $authUser): bool
    {
        return $authUser->hasPermission('create_hr_demand');
    }

    /**
     * Determine whether the user can update the HrDemand.
     *
     * @param User $authUser
     * @return bool
     */
    public function update(User $authUser): bool
    {
        return $authUser->hasPermission('update_hr_demand');
    }

    /**
     * Determine whether the user can delete the HrDemand.
     *
     * @param User $authUser
     * @return bool
     */
    public function delete(User $authUser): bool
    {
        return $authUser->hasPermission('delete_hr_demand');
    }
}
