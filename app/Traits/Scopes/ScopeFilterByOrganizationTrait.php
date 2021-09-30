<?php

namespace App\Traits\Scopes;

use App\Models\BaseModel;
use Illuminate\Support\Facades\Auth;

trait ScopeFilterByOrganizationTrait
{
    public function scopeByOrganization($query)
    {
        $authUser = Auth::user();
        if ($authUser->user_type == BaseModel::ORGANIZATION_USER && $authUser->organization_id) {  //Organization User
            return $query->where('organization_id', $authUser->organization_id);
        }
        return $query;
    }

}
