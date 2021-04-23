<?php

namespace App\Modules\User\Services;

use App\Models\User;
use App\Models\UserInfo;
use Illuminate\Support\Facades\Hash;

class UserService
{
    /**
     *  Generate User model with userInfo and role
     * @param array $userParams
     * @param array $userInfoParams
     * @return User|\Illuminate\Database\Eloquent\Model
     */
    public function create(array $userParams, array $userInfoParams = []) {
        // Pre actions
        $userParams['password'] = Hash::make($userParams['password']);

        $user = User::create( $userParams );

        UserInfo::create([
            'user_id' => $user->id,
            'info' => $userInfoParams
        ]);

        // Add Role
        $user->assignRole(['super-admin']);
        return $user;
    }

    /**
     * @param User $user
     * @param array $userParams
     * @return bool
     */
    public function update(User $user, array $userParams = []): bool
    {
//        $email = $userParams['email'] ?? null;
//
//        if ($email && User::whereEmail($email)->first()) {
//            return false;
//        }

        $user->update($userParams);
        return true;
    }

}
