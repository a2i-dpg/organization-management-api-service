<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class HrDemandYouthPolicy
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
        return $authUser->hasPermission('view_any_hr_demand_youth');
    }
}
