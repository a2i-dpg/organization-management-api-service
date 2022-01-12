<?php

namespace App\Policies;

use App\Models\IndustryAssociation;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class IndustryAssociationPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any industryAssociations.
     *
     * @param User $authUser
     * @return bool
     */
    public function viewAny(User $authUser): bool
    {
        return $authUser->hasPermission('view_any_industry_association');
    }

    /**
     * Determine whether the user can view the industryAssociation.
     * @param User $authUser
     * @param IndustryAssociation $industryAssociation
     * @return bool
     */
    public function view(User $authUser, IndustryAssociation $industryAssociation): bool
    {
        return $authUser->hasPermission('view_single_industry_association');

    }

    /**
     * Determine whether the user can create industryAssociations.
     *
     * @param User $authUser
     * @return bool
     */
    public function create(User $authUser): bool
    {
        return $authUser->hasPermission('create_industry_association');
    }

    /**
     * Determine whether the user can update the industryAssociation.
     *
     * @param User $authUser
     * @param IndustryAssociation $industryAssociation
     * @return bool
     */
    public function update(User $authUser, IndustryAssociation $industryAssociation): bool
    {
        return $authUser->hasPermission('update_industry_association');
    }

    /**
     * Determine whether the user can delete the industryAssociation.
     *
     * @param User $authUser
     * @param IndustryAssociation $industryAssociation
     * @return bool
     */
    public function delete(User $authUser, IndustryAssociation $industryAssociation): bool
    {
        return $authUser->hasPermission('delete_industry_association');
    }


    /**
     * Determine whether the user can view any industryAssociation Member.
     * @param User $authUser
     * @return bool
     */
    public function viewAnyMember(User $authUser): bool
    {
        return $authUser->hasPermission('view_any_industry_association_member');
    }


    /**
     * Determine whether the user can view the industryAssociation.
     * @param User $authUser
     * @return bool
     */
    public function viewMember(User $authUser): bool
    {
        return $authUser->hasPermission('view_single_industry_association_member');

    }
}
