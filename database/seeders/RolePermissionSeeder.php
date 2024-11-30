<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Contracts\Permission;
use Spatie\Permission\Contracts\Role;

class RolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $permissions = [
            'view courses',
            'create courses',
            'edit courses',
            'delete courses'
        ];

        foreach ($permissions as $permisson) {
            \Spatie\Permission\Models\Permission::create([
                'name' => $permisson
            ]);
        }

        $teacherRole = \Spatie\Permission\Models\Role::create([
            'name' => 'teacher'
        ]);

        $teacherRole->givePermissionTo([
            'view courses',
            'create courses',
            'edit courses',
            'delete courses'
        ]);

        $studentRole = \Spatie\Permission\Models\Role::create([
            'name' => 'student'
        ]);

        $studentRole->givePermissionTo([
            'view courses'
        ]);

        $user = User::create([
            'name' => 'admin',
            'email' => 'admin@mail.com',
            'password' => bcrypt('password')
        ]);

        $user->assignRole($teacherRole);


    }
}
