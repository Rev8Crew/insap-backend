<?php

namespace App\Modules\Access\Services;

use App\Models\User;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class AccessService
{
    public function createRole(string $role) {
        return Role::create(['name' => $role]);
    }

    public function createPermission(string $permission) {
        return Permission::create(['name' => $permission]);
    }

    public function addPermissionToRole(Permission $permission, Role $role): Permission
    {
        return $permission->assignRole($role);
    }

    public function removePermissionFromRole(Permission $permission, Role $role): Permission
    {
        return $permission->removeRole($role);
    }

    public function addRoleToUser(Role $role, User $user): User
    {
        return $user->assignRole($role);
    }

    public function removeRoleFromUser(Role $role, User $user) {
        return $user->removeRole($role);
    }
}
