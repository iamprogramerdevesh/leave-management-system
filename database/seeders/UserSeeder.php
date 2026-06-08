<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        User::query()->where('email', 'test@gmail.com')->delete();

        $users = [
            [
                'employee_id' => 'EMP001',
                'name' => 'System Admin',
                'email' => 'admin@gmail.com',
                'mobile' => '9876543210',
                'department' => 'HR',
                'designation' => 'System Administrator',
                'role' => User::ROLE_ADMIN,
                'status' => User::STATUS_ACTIVE,
                'password' => Hash::make('123456'),
            ],
            [
                'employee_id' => 'EMP002',
                'name' => 'Department Manager',
                'email' => 'manager@gmail.com',
                'mobile' => '9876543211',
                'department' => 'IT',
                'designation' => 'Manager',
                'role' => User::ROLE_MANAGER,
                'status' => User::STATUS_ACTIVE,
                'password' => Hash::make('123456'),
            ],
            [
                'employee_id' => 'EMP003',
                'name' => 'John Employee',
                'email' => 'employee@gmail.com',
                'mobile' => '9876543212',
                'department' => 'Finance',
                'designation' => 'Executive',
                'role' => User::ROLE_EMPLOYEE,
                'status' => User::STATUS_ACTIVE,
                'password' => Hash::make('123456'),
            ],
        ];

        foreach ($users as $user) {
            User::query()->updateOrCreate(
                ['email' => $user['email']],
                $user
            );
        }
    }
}
