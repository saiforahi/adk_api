<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\PermissionRegistrar;
class RoleAndPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        // create permissions

        // create roles and assign existing permissions
        $role1 = Role::create(['name' => 'admin']);
        $role2 = Role::create(['name' => 'user']);
        // $role2->givePermissionTo('publish articles');
        // $role2->givePermissionTo('unpublish articles');
        //$role3 = Role::create(['name' => 'user']);
        // gets all permissions via Gate::before rule; see AuthServiceProvider

        // create demo users
        $user = \App\Models\User::factory()->create([
            'name' => 'Mr Admin',
            'email' => 'admin@mail.com',
            'phone'=> '01XXXXXXXXX',
            'password' => Hash::make('123456'),
            //'last_login_ip' => 'Male',
        ]);
        $user->assignRole($role1);

        $user = \App\Models\User::factory()->create([
            'name' => 'Mr User',
            'email' => 'user@mail.com',
            'password' => Hash::make('123456'),
            //'gender' => 'Male',
        ]);
        $user->assignRole($role2);
    }
}