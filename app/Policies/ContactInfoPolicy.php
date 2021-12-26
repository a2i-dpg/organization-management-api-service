<?php

namespace App\Policies;

use App\Models\ContactInfo;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class ContactInfoPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any contactInfos.
     *
     * @param User $authUser
     * @return mixed
     */
    public function viewAny(User $authUser)
    {
        return $authUser->hasPermission('view_any_contact_info');
    }

    /**
     * Determine whether the user can view the contactInfo.
     *
     * @param User $authUser
     * @param ContactInfo $contactInfo
     * @return mixed
     */
    public function view(User $authUser, ContactInfo $contactInfo)
    {
        return $authUser->hasPermission('view_single_contact_info');
    }

    /**
     * Determine whether the user can create contactInfos.
     *
     * @param User $authUser
     * @return mixed
     */
    public function create(User $authUser)
    {
        return $authUser->hasPermission('create_contact_info');
    }

    /**
     * Determine whether the user can update the contactInfo.
     *
     * @param User $authUser
     * @param ContactInfo $contactInfo
     * @return mixed
     */
    public function update(User $authUser, ContactInfo $contactInfo)
    {
        return $authUser->hasPermission('update_contact_info');
    }

    /**
     * Determine whether the user can delete the contactInfo.
     *
     * @param User $authUser
     * @param ContactInfo $contactInfo
     * @return mixed
     */
    public function delete(User $authUser, ContactInfo $contactInfo)
    {
        return $authUser->hasPermission('delete_contact_info');
    }
}
