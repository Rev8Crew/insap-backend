<?php

namespace App\Modules\User\Services;

use App\Models\User;
use App\Models\UserInfo;

class UserService
{
    public function create( array $params ) {
        $user = User::create( $params );

        UserInfo::create([
           'user_id' => $user->id
        ]);

        return $user;
    }
}