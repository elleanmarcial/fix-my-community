<?php

namespace Database\Seeders;

use App\Models\Report;
use App\Models\User;
use Illuminate\Database\Seeder;
use Faker\Factory as Faker;

class ReportSeeder extends Seeder
{
    public function run(): void
    {
        $faker = Faker::create();

        $user = User::where('role', 'resident')->first();

        $statuses = [
            'Submitted',
            'Verified',
            'In Progress',
            'Resolved',
        ];

        $categories = [
            'Garbage',
            'Street Light',
            'Road Damage',
            'Drainage',
            'Water Supply',
            'Public Safety',
            'Flooding',
            'Infrastructure',
        ];

        for ($i = 0; $i < 10; $i++) {

            $status = $statuses[$i % count($statuses)];

            Report::create([
                'user_id' => $user->id,
                'title' => $faker->sentence(4),
                'description' => $faker->paragraph(),
                'category' => $categories[$i % count($categories)],
                'location' => 'Purok ' . $faker->numberBetween(1, 6),
                'is_anonymous' => $faker->boolean(20),
                'status' => $status,
                'assigned_to' => $status === 'In Progress' || $status === 'Resolved'
                    ? $faker->name()
                    : null,
                'admin_notes' => $status !== 'Submitted'
                    ? $faker->sentence()
                    : null,
                'reported_at' => $faker->dateTimeBetween('-30 days', 'now'),
                'resolved_at' => $status === 'Resolved'
                    ? $faker->dateTimeBetween('-10 days', 'now')
                    : null,
            ]);
        }
    }
}