<?php

namespace App\Traits\Scopes;

use App\Models\BaseModel;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

trait ScopeFilterByOrganization
{
    public function scopeByOrganization($query,$table)
    {
        $authUser = Auth::user();

    Log::info($authUser->user_type);
        if ($authUser->user_type == BaseModel::ORGANIZATION_TYPE && $authUser->organization_id) {  //Organization User
            return $query->where($table.'.organization_id', $authUser->organization_id);
        }
        return $query;
    }
}
