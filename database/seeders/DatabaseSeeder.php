<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {

        // agency
        \App\Models\Agency::create([
            'name' => 'Agence Principale',
            'code' => 'AG001',
            'phone' => '+221782902512',
            'whatsapp_number' => '+221782902512',
            'email' => 'agence@example.com',
            'address' => '123 Rue de l\'Agence',
            'city' => 'Dakar',
            'country' => 'Sénégal',
            'is_active' => true,
            
        ]);


        User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password',
            'role' => 'admin',
            'agency_id' => 1,
        ]);
    }
}
