<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Admin account
        User::factory()->create([
            'name' => 'Juan Dela Cruz',
            'email' => 'juan@example.com',
            'address' => 'Barangay Main Road',
            'role' => 'admin',
            'password' => Hash::make('Password123!'),
        ]);

        // Resident account
        User::factory()->create([
            'name' => 'Sample Resident',
            'email' => 'sample1@example.com',
            'address' => 'Purok 1, Barangay Main Road',
            'role' => 'resident',
            'password' => Hash::make('Password123!'),
        ]);

        // Seed 10 sample community reports
        $this->call([
            ReportSeeder::class,
        ]);
    }
}