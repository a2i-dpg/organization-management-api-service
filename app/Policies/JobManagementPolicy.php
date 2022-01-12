<?php

namespace App\Policies;

use App\Models\PrimaryJobInformation;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Response;

class JobManagementPolicy extends BasePolicy
{
    use HandlesAuthorization;


    /**
     * Determine whether the user can view specific job.
     * @param User $authUser
     * @param PrimaryJobInformation $primaryJobInformation
     * @param Model $model
     * @return bool
     */

    public function view(User $authUser, PrimaryJobInformation $primaryJobInformation, Model $model): bool
    {

        if ($authUser->isIndustryAssociationUser()) {
            return $authUser->hasPermission('view_single_job') && ($authUser->industry_association_id == $primaryJobInformation->industry_association_id) && ($primaryJobInformation->job_id == $model->job_id);
        } else if ($authUser->isOrganizationUser()) {
            return $authUser->hasPermission('view_single_job') &&
                ($authUser->organization_id == $primaryJobInformation->organization_id) && $primaryJobInformation->job_id == $model->job_id;
        } else if ($authUser->isInstituteUser()) {
            return $authUser->hasPermission('view_single_job') &&
                ($authUser->institute_id == $primaryJobInformation->institute_id) && $primaryJobInformation->job_id == $model->job_id;
        } else
            return false;
    }


    /**
     * Determine whether the user can create industryAssociations.
     * @param User $authUser
     * @return bool
     */
    public function create(User $authUser): bool
    {
        return $authUser->hasPermission('create_job');

    }
}
