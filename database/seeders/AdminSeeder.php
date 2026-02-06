<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'admin@bhdml.com'],
            [
                'name' => 'Administrateur BHDML',
                'first_name' => 'Admin',
                'last_name' => 'BHDML',
                'email' => 'admin@bhdml.com',
                'phone' => '+33123456789',
                'password' => Hash::make('Admin@2024!'),
                'email_verified_at' => now(),
                'is_active' => true,
                'is_verified' => true,
                'is_admin' => true,
                'is_moderator' => false,
                'member_type' => 'admin',
                'gender' => 'male',
                'address' => '123 Avenue des Champs-Élysées',
                'city' => 'Paris',
                'country' => 'cote_ivoire',
                'postal_code' => '75008',
                'company_name' => 'BHDML Administration',
                'job_title' => 'Administrateur Système',
                'accepts_newsletter' => false,
                'accepts_notifications' => true,
                'member_status' => 'active',
            ]
        );

        $this->command->info('✅ Admin créé : admin@bhdml.com / Admin@2024!');
    }
}
