<?php

namespace App\Policies;

use App\Models\HrDemand;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Support\Facades\Log;

class HrDemandPolicy
{
    use HandlesAuthorization;

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
     * Determine whether the user can update the humanResource.
     *
     * @param User $authUser
     * @param HrDemand $hrDemand
     * @return bool
     */
    public function update(User $authUser, HrDemand $hrDemand): bool
    {
        return $authUser->hasPermission('update_hr_demand');
    }
}
