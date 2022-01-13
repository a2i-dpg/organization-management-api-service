<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class HrDemandInstitutePolicy
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
        return $authUser->hasPermission('view_any_hr_demand_institute');
    }

    /**
     * Determine whether the user can view the HrDemand.
     *
     * @param User $authUser
     * @return bool
     */
    public function view(User $authUser): bool
    {
        return $authUser->hasPermission('view_single_hr_demand_institute');
    }

    /**
     * Determine whether the user can update the HrDemand.
     *
     * @param User $authUser
     * @return bool
     */
    public function update(User $authUser): bool
    {
        return $authUser->hasPermission('update_hr_demand_institute');
    }

    /**
     * Determine whether the institute user can update HrDemand.
     *
     * @param User $authUser
     * @return bool
     */
    public function updateByInstitute(User $authUser): bool
    {
        return $authUser->hasPermission('update_hr_demand_institute_by_institute');
    }
}
