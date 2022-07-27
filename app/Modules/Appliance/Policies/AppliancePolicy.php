<?php

namespace App\Modules\Appliance\Policies;

use App\Enums\Permission;
use App\Models\User;
use App\Modules\Appliance\Models\Appliance;
use Illuminate\Auth\Access\HandlesAuthorization;

class AppliancePolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return $user->can(Permission::APPLIANCE_VIEW_ALL);
    }

    public function view(?User $user, Appliance $appliance): bool
    {
        return $user->can(Permission::APPLIANCE_VIEW);
    }

    public function create(User $user): bool
    {
        return $user->can(Permission::APPLIANCE_CREATE);
    }

    public function update(User $user, Appliance $appliance): bool
    {
        return $user->can(Permission::APPLIANCE_EDIT);
    }

    public function delete(User $user, Appliance $appliance): bool
    {
        return $user->can(Permission::APPLIANCE_DELETE);
    }

    public function restore(User $user, Appliance $appliance)
    {
        //
    }

    public function forceDelete(User $user, Appliance $appliance)
    {
        //
    }
}
