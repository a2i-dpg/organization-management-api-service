<?php

namespace App\Traits\Scopes;

use App\Models\BaseModel;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;

trait ScopeAcl
{
    /**
     * @param $query
     * @return mixed
     */
    public function scopeAcl($query): mixed
    {
        /** @var User $authUser */
        $authUser = Auth::user();
        $tableName = $this->getTable();

        if ($authUser && $authUser->isOrganizationUser()) {  //Organization User
            if (Schema::hasColumn($tableName, 'organization_id')) {
                $query = $query->where($tableName . '.organization_id', $authUser->organization_id);
            }
        } else if ($authUser && $authUser->isIndustryAssociationUser()) {  //IndustryAssociation User
            if (Schema::hasColumn($tableName, 'industry_association_id')) {
                $query = $query->where($tableName . '.industry_association_id', $authUser->industry_association_id);
            }
        } else if ($authUser && $authUser->isInstituteUser()) {  //Institute User
            if (Schema::hasColumn($tableName, 'institute_id')) {
                $query = $query->where($tableName . '.institute_id', $authUser->institute_id);
            }
        }
        return $query;
    }

}
