<?php


namespace App\Policies;

use App\Models\User;

abstract class BasePolicy
{

    public function before($authUser, $ability) : bool
    {
        /** @var User $authUser */
        if ($authUser && $authUser->row_status != User::ROW_STATUS_ACTIVE) {
            return false;
        }
    }
}
