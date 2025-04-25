<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Role;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Crea un utente admin
        $adminUser = User::firstOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name' => 'Admin User',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ]
        );

        // Trova il ruolo admin
        $adminRole = Role::where('name', 'admin')->first();

        // Assegna il ruolo admin all'utente
        if ($adminRole && !$adminUser->hasRole('admin')) {
            $adminUser->roles()->attach($adminRole);
        }
    }
}