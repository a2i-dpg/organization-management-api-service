<?php


namespace App\Policies;

use App\Models\User;

abstract class BasePolicy
{

    public function before($user, $ability)
    {
        /** @var User $user */
        if ($user->row_status != User::ROW_STATUS_ACTIVE) {
            return false;
        }
    }
}
