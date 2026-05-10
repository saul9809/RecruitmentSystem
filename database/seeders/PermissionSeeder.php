<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $permissions = [
            'users.view',
            'users.edit',
            'users.delete',
            'users.create',
            'roles.create',
            'roles.edit',
            'roles.delete',
            'roles.view',
        ];

        foreach ($permissions as $key => $value) {
            // Insert permission into the database
            Permission::create(['name' => $value]);
        }
    }
}
