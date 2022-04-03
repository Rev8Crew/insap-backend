<?php

namespace App\Modules\User\Services;

use App\helpers\IsActiveHelper;
use App\Models\User;
use App\Models\UserInfo;
use App\Services\File\FileService;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class UserService
{
    private FileService $fileService;
    public function __construct(FileService $fileService)
    {
        $this->fileService = $fileService;
    }

    /**
     *  Generate User model with userInfo and role
     * @param array $userParams
     * @param array $userInfoParams
     * @param UploadedFile|null $file
     * @param User|null $user
     * @return User
     */
    public function create(array $userParams, array $userInfoParams = [], ?UploadedFile $file = null, ?User $user = null): User
    {
        if ($file) {
            $userParams['image_id'] = $this->fileService->buildFromUploadedFile($file, $user)->id;
        }

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
        $this->deleteImage($user);

        $userInfo = UserInfo::where('user_id', $user->id);
        $userInfo->delete();

        $user->delete();
    }

    /**
     * @param User $user
     * @param array $roles
     */
    public function attachRoles(User $user, array $roles) {
        $user->roles()->syncWithoutDetaching( Role::whereIn('name', $roles)->get());
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


    /**
     * @param User $user
     * @param UploadedFile $uploadedFile
     * @param User $userChange
     * @return bool
     * @throws \Exception
     */
    public function changeImage(User $user, UploadedFile $uploadedFile, User $userChange): bool
    {
        $this->deleteImage($user);

        $file = $this->fileService->buildFromUploadedFile($uploadedFile, $userChange);
        return $user->update(['image_id' => $file->id]);
    }

    /**
     * @param User $user
     * @throws \Exception
     */
    private function deleteImage(User $user) {
        // If exists
        if ($user->image_id) {
            $this->fileService->delete($user->imageFile);
        }
    }
}
