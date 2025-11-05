<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call([
            // Base data - states, cities, etc.
            StatesSeeder::class,
            CitiesSeeder::class,

            // Roles must be seeded before Users
            RolesSeeder::class,

            // Users and Leads
            UsersSeeder::class,
            LeadsSeeder::class,

            // Content
            BlogsSeeder::class,
            TestimonialsSeeder::class,

            // Legal Content and Document Management Seeders
            // Order matters: Topics and States must be seeded before Law Contents
            LegalTopicSeeder::class,
            StateProfileSeeder::class,
            StateLawContentSeeder::class,

            // Categories must be seeded before Templates
            DocumentCategorySeeder::class,
            DocumentTemplateSeeder::class,
        ]);
    }
}
