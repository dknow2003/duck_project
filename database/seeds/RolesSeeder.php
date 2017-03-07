<?php

use App\Permission;
use App\Role;
use Illuminate\Database\Seeder;

class RolesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $superAdmin = Role::create([
            'name' => 'superadmin',
            'display_name' => '超级管理员',
        ]);

        $permissions = Permission::all();
        foreach ($permissions as $permission) {
            $superAdmin->perms()->attach($permission->id);
        }
    }
}
