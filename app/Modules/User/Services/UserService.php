<?php

namespace App\Modules\User\Services;

use App\helpers\IsActiveHelper;
use App\Models\User;
use App\Models\UserInfo;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class UserService
{
    /**
     *  Generate User model with userInfo and role
     * @param array $userParams
     * @param array $userInfoParams
     * @return User
     */
    public function create(array $userParams, array $userInfoParams = []): User
    {

        $userParams['password'] = Hash::make($userParams['password']);
        $user = User::create( $userParams );

        UserInfo::create([
            'user_id' => $user->id,
            'info' => $userInfoParams
        ]);

        // Add default role
        $user->assignRole(['super-admin']);
        return $user;
    }

    /**
     * @param User $user
     * @param array $userParams
     * @param array $userInfoParams
     */
    public function update(User $user, array $userParams = [], array $userInfoParams = [])
    {
        $user->update($userParams);
        $user->user_info->update($userInfoParams);
    }

    /**
     * @param User $user
     */
    public function delete(User $user) {
        $userInfo = UserInfo::where('user_id', $user->id);
        $userInfo->delete();

        $user->delete();
    }

    /**
     * @param User $user
     * @param array $roles
     */
    public function attachRoles(User $user, array $roles) {
        $user->roles()->attach( Role::whereIn('name', $roles)->get());
    }

    /**
     * @param User $user
     * @param array $roles
     */
    public function removeRoles(User $user, array $roles) {
        $user->roles()->detach(Role::whereIn('name', $roles)->get());
    }

    /**
     * @param User $user
     */
    public function activate(User $user) {
        $user->update(['is_active' => IsActiveHelper::ACTIVE_ACTIVE]);
    }

    /**
     * @param User $user
     */
    public function deactivate(User $user) {
        $user->update(['is_active' => IsActiveHelper::ACTIVE_INACTIVE]);
    }
}
