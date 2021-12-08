<?php

namespace App\Traits\Scopes;

use App\Models\BaseModel;
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
        $authUser = Auth::user();
        $tableName = $this->getTable();

        if ($authUser && $authUser->isOrganizationUser()) {  //Organization User
            if (Schema::hasColumn($tableName, 'organization_id')) {
                $query = $query->where($tableName . '.organization_id', $authUser->organization_id);
            }
        }
        return $query;
    }

}
