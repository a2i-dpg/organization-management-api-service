<?php

namespace App\Policies;

use App\HumanResourceTemplate;
use App\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class HumanResourceTemplatePolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any humanResourceTemplates.
     *
     * @param  App\User  $user
     * @return mixed
     */
    public function viewAny(User $user)
    {
        //
    }

    /**
     * Determine whether the user can view the humanResourceTemplate.
     *
     * @param  App\User  $user
     * @param  App\HumanResourceTemplate  $humanResourceTemplate
     * @return mixed
     */
    public function view(User $user, HumanResourceTemplate $humanResourceTemplate)
    {
        //
    }

    /**
     * Determine whether the user can create humanResourceTemplates.
     *
     * @param  App\User  $user
     * @return mixed
     */
    public function create(User $user)
    {
        //
    }

    /**
     * Determine whether the user can update the humanResourceTemplate.
     *
     * @param  App\User  $user
     * @param  App\HumanResourceTemplate  $humanResourceTemplate
     * @return mixed
     */
    public function update(User $user, HumanResourceTemplate $humanResourceTemplate)
    {
        //
    }

    /**
     * Determine whether the user can delete the humanResourceTemplate.
     *
     * @param  App\User  $user
     * @param  App\HumanResourceTemplate  $humanResourceTemplate
     * @return mixed
     */
    public function delete(User $user, HumanResourceTemplate $humanResourceTemplate)
    {
        //
    }
}
