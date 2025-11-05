<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CitiesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Skip if cities already exist (idempotent seeder)
        if (DB::table('cities')->count() > 0) {
            echo "⚠ Cities already seeded, skipping...\n";
            return;
        }

        $cities = [
            // California (state_id: 5)
            ['city' => 'Los Angeles', 'state_id' => 5],
            ['city' => 'San Francisco', 'state_id' => 5],
            ['city' => 'San Diego', 'state_id' => 5],
            ['city' => 'Sacramento', 'state_id' => 5],
            // Florida (state_id: 9)
            ['city' => 'Miami', 'state_id' => 9],
            ['city' => 'Orlando', 'state_id' => 9],
            ['city' => 'Tampa', 'state_id' => 9],
            ['city' => 'Jacksonville', 'state_id' => 9],
            // New York (state_id: 33)
            ['city' => 'New York City', 'state_id' => 33],
            ['city' => 'Buffalo', 'state_id' => 33],
            ['city' => 'Rochester', 'state_id' => 33],
            // Texas (state_id: 44)
            ['city' => 'Houston', 'state_id' => 44],
            ['city' => 'Dallas', 'state_id' => 44],
            ['city' => 'Austin', 'state_id' => 44],
            ['city' => 'San Antonio', 'state_id' => 44],
            // Washington (state_id: 48)
            ['city' => 'Seattle', 'state_id' => 48],
            ['city' => 'Spokane', 'state_id' => 48],
            ['city' => 'Tacoma', 'state_id' => 48],
        ];

        foreach ($cities as $city) {
            DB::table('cities')->insert([
                'city' => $city['city'],
                'state_id' => $city['state_id'],
                'slug' => Str::slug($city['city']),
                'status' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
