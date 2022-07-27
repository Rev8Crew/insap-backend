<?php

namespace Database\Seeders;

use App\Enums\Permission;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class RolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        foreach (Permission::variants() as $permission) {
            \Spatie\Permission\Models\Permission::create(['name' => $permission]);
        }

        /** @var Role $superAdmin */
        $superAdmin = Role::create([ 'name' => 'super-admin' ]);
        $superAdmin->givePermissionTo(Permission::variants());

        $testRole = Role::create(['name' => 'test']);
        $userRole = Role::create(['name' => 'user']);
    }
}
