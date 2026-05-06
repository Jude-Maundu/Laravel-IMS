<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create Admin users
        $admin = User::updateOrCreate(
            ['email' => 'admin@greyapple.co.ke'],
            [
                'name' => 'Admin User',
                'password' => bcrypt(env('ADMIN_SETUP_PASSWORD', 'password')),
                'email_verified_at' => now(),
            ]
        );
        $admin->assignRole('Admin');

        // Create Manager users
        $manager = User::updateOrCreate(
            ['email' => 'manager@greyapple.co.ke'],
            [
                'name' => 'Manager User',
                'password' => bcrypt(env('MANAGER_SETUP_PASSWORD', 'password')),
                'email_verified_at' => now(),
            ]
        );
        $manager->assignRole('Manager');

        // Create additional test users
        $testUsers = [
            [
                'name' => 'Alice Johnson',
                'email' => 'alice@example.com',
                'role' => 'Manager',
            ],
            [
                'name' => 'Bob Smith',
                'email' => 'bob@example.com',
                'role' => 'Manager',
            ],
            [
                'name' => 'Carol White',
                'email' => 'carol@example.com',
                'role' => 'Admin',
            ],
        ];

        foreach ($testUsers as $userData) {
            $user = User::firstOrCreate(
                ['email' => $userData['email']],
                [
                    'name' => $userData['name'],
                    'password' => bcrypt('password'),
                    'email_verified_at' => now(),
                ]
            );
            $user->assignRole($userData['role']);
        }
    }
}
