<?php

namespace App\Policies;

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
        return $authUser->hasPermission('view_any_industry_association_hr_demand');
    }

    /**
     * Determine whether the user can view the HrDemand.
     *
     * @param User $authUser
     * @return bool
     */
    public function view(User $authUser): bool
    {
        return $authUser->hasPermission('view_single_industry_association_hr_demand');
    }

    /**
     * Determine whether the user can create HrDemand.
     *
     * @param User $authUser
     * @return bool
     */
    public function create(User $authUser): bool
    {
        return $authUser->hasPermission('create_industry_association_hr_demand');
    }

    /**
     * Determine whether the user can update the HrDemand.
     *
     * @param User $authUser
     * @return bool
     */
    public function update(User $authUser): bool
    {
        return $authUser->hasPermission('update_industry_association_hr_demand');
    }

    /**
     * Determine whether the user can delete the HrDemand.
     *
     * @param User $authUser
     * @return bool
     */
    public function delete(User $authUser): bool
    {
        return $authUser->hasPermission('delete_industry_association_hr_demand');
    }
}
