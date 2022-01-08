<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class HrDemandInstitutePolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the institute user can update HrDemand.
     *
     * @param User $authUser
     * @return bool
     */
    public function updateByInstitute(User $authUser): bool
    {
        return $authUser->hasPermission('update_hr_demand_by_institute');
    }
}
