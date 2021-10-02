<?php

namespace App\Policies;

use App\Models\HumanResource;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class HumanResourcePolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any humanResources.
     *
     * @param User $authUser
     * @return mixed
     */
    public function viewAny(User $authUser)
    {
        return $authUser->hasPermission('view_any_human_resource');

    }

    /**
     * Determine whether the user can view the humanResource.
     *
     * @param User $authUser
     * @param HumanResource $humanResource
     * @return mixed
     */
    public function view(User $authUser, HumanResource $humanResource)
    {
        return $authUser->hasPermission('view_single_human_resource');

    }

    /**
     * Determine whether the user can create humanResources.
     *
     * @param User $authUser
     * @return mixed
     */
    public function create(User $authUser)
    {
        return $authUser->hasPermission('create_human_resource');

    }

    /**
     * Determine whether the user can update the humanResource.
     *
     * @param User $authUser
     * @param HumanResource $humanResource
     * @return bool
     */
    public function update(User $authUser, HumanResource $humanResource): bool
    {
        return $authUser->hasPermission('update_human_resource');

    }

    /**
     * Determine whether the user can delete the humanResource.
     *
     * @param User $authUser
     * @param HumanResource $humanResource
     * @return bool
     */
    public function delete(User $authUser, HumanResource $humanResource)
    {
        return $authUser->hasPermission('delete_human_resource');

    }
}
