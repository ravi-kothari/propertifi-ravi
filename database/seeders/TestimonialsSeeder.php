<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TestimonialsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Skip if testimonials already exist (idempotent seeder)
        if (DB::table('testimonials')->count() > 0) {
            echo "⚠ Testimonials already seeded, skipping...\n";
            return;
        }

        $testimonials = [
            [
                'heading' => 'Sarah Mitchell',
                'designation' => 'Property Owner, Los Angeles, CA',
                'description' => 'Propertifi helped me find an amazing property manager within days. The platform made it so easy to compare different managers and their services. I am extremely satisfied with the quality of service I am receiving.',
            ],
            [
                'heading' => 'Michael Chen',
                'designation' => 'Real Estate Investor, Miami, FL',
                'description' => 'As someone who owns multiple rental properties, finding reliable property managers used to be a nightmare. Propertifi streamlined the entire process and connected me with verified professionals who truly understand the local market.',
            ],
            [
                'heading' => 'Jennifer Rodriguez',
                'designation' => 'First-Time Landlord, Austin, TX',
                'description' => 'I was nervous about renting out my first property, but Propertifi made everything simple. The property manager they matched me with has been fantastic, handling everything from tenant screening to maintenance with complete professionalism.',
            ],
            [
                'heading' => 'David Thompson',
                'designation' => 'Property Owner, Seattle, WA',
                'description' => 'The transparency and ease of comparing property managers on Propertifi is unmatched. I was able to review fees, services, and reviews all in one place. Best decision I made for my rental business.',
            ],
            [
                'heading' => 'Lisa Patel',
                'designation' => 'Multi-Unit Owner, New York, NY',
                'description' => 'Managing 15 units across different neighborhoods was overwhelming until I used Propertifi. They connected me with a property management company that specializes in multi-family properties. My occupancy rate has never been higher.',
            ],
            [
                'heading' => 'Robert Johnson',
                'designation' => 'Vacation Rental Owner, Orlando, FL',
                'description' => 'Propertifi understands that different properties need different management approaches. They found me a manager who specializes in short-term rentals and vacation properties. My rental income has increased by 30% since making the switch.',
            ],
        ];

        foreach ($testimonials as $testimonial) {
            DB::table('testimonials')->insert([
                'heading' => $testimonial['heading'],
                'designation' => $testimonial['designation'],
                'description' => $testimonial['description'],
                'status' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
