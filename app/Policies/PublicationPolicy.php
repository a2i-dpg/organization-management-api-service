<?php

namespace App\Policies;

use App\Models\Publication;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class PublicationPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any publications.
     *
     * @param User $authUser
     * @return bool
     */
    public function viewAny(User $authUser): bool
    {
        return $authUser->hasPermission('view_any_publication');
    }

    /**
     * Determine whether the user can view the publication.
     *
     * @param User $authUser
     * @param Publication $publication
     * @return mixed
     */
    public function view(User $authUser, Publication $publication)
    {
        return $authUser->hasPermission('view_single_publication');
    }

    /**
     * Determine whether the user can create publications.
     *
     * @param User $authUser
     * @return bool
     */
    public function create(User $authUser)
    {
        return $authUser->hasPermission('create_publication');
    }

    /**
     * Determine whether the user can update the publication.
     *
     * @param User $authUser
     * @param Publication $publication
     * @return bool
     */
    public function update(User $authUser, Publication $publication)
    {
        return $authUser->hasPermission('update_publication');
    }

    /**
     * Determine whether the user can delete the publication.
     *
     * @param User $authUser
     * @param Publication $publication
     * @return bool
     */
    public function delete(User $authUser, Publication $publication)
    {
        return $authUser->hasPermission('delete_publication');
    }
}
