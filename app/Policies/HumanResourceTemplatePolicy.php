<?php

namespace App\Policies;

use App\Models\HumanResourceTemplate;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class HumanResourceTemplatePolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any humanResourceTemplates.
     *
     * @param User $authUser
     * @return bool
     */
    public function viewAny(User $authUser): bool
    {
        return $authUser->hasPermission('view_any_human_resource_template');
    }

    /**
     * Determine whether the user can view the humanResourceTemplate.
     *
     * @param User $authUser
     * @param HumanResourceTemplate $humanResourceTemplate
     * @return bool
     */
    public function view(User $authUser, HumanResourceTemplate $humanResourceTemplate): bool
    {
        return $authUser->hasPermission('view_single_human_resource_template');
    }

    /**
     * Determine whether the user can create humanResourceTemplates.
     *
     * @param User $authUser
     * @return bool
     */
    public function create(User $authUser): bool
    {
        return $authUser->hasPermission('create_human_resource_template');
    }

    /**
     * Determine whether the user can update the humanResourceTemplate.
     *
     * @param User $authUser
     * @param HumanResourceTemplate $humanResourceTemplate
     * @return bool
     */
    public function update(User $authUser, HumanResourceTemplate $humanResourceTemplate)
    {
        return $authUser->hasPermission('update_human_resource_template');

    }

    /**
     * Determine whether the user can delete the humanResourceTemplate.
     *
     * @param User $authUser
     * @param HumanResourceTemplate $humanResourceTemplate
     * @return bool
     */
    public function delete(User $authUser, HumanResourceTemplate $humanResourceTemplate)
    {
        return $authUser->hasPermission('delete_human_resource_template');

    }
}
