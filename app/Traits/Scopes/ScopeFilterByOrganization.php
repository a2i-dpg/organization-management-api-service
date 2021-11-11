<?php

namespace App\Traits\Scopes;

use App\Models\BaseModel;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

trait ScopeFilterByOrganization
{
    public function scopeByOrganization($query)
    {
        $authUser = Auth::user();
        $tableName = $this->getTable();
        if ($authUser && $authUser->user_type == BaseModel::ORGANIZATION_USER_TYPE && $authUser->organization_id) {  //Organization User
            return $query->where($tableName.'.organization_id', $authUser->organization_id);
        }
        return $query;
    }
}
