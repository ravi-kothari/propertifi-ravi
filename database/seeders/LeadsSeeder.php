<?php

namespace Database\Seeders;

use App\Models\Lead;
use App\Models\User;
use App\Models\UserLeads;
use Illuminate\Database\Seeder;

class LeadsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Skip if leads already exist (idempotent seeder)
        if (Lead::where('email', 'michael.anderson@example.com')->exists()) {
            echo "⚠ Leads already seeded, skipping...\n";
            return;
        }

        // Get property managers for lead distribution
        $propertyManagers = User::where('type', User::TYPE_PM)->get();

        if ($propertyManagers->isEmpty()) {
            echo "⚠ No property managers found. Please run UsersSeeder first.\n";
            return;
        }

        // Lead 1 - High quality, distributed
        $lead1 = Lead::create([
            'name' => 'Michael Anderson',
            'email' => 'michael.anderson@example.com',
            'phone' => '+1234567900',
            'address' => '123 Maple Street',
            'city' => 'Los Angeles',
            'state' => 'CA',
            'zipcode' => '90015',
            'property_type' => 'Multi Family',
            'number_of_units' => 8,
            'square_footage' => 6400,
            'year_built' => 2010,
            'amenities' => json_encode(['Parking', 'Laundry', 'Pool']),
            'price' => 950000,
            'price_range' => '$900K - $1M',
            'category' => 'buy',
            'additional_services' => 'Full management services including maintenance and tenant screening',
            'preferred_contact' => 'email',
            'source' => 'website',
            'utm_source' => 'google',
            'utm_medium' => 'organic',
            'status' => 'new',
            'quality_score' => 90,
            'distribution_count' => 3,
            'last_distributed_at' => now()->subDays(2),
            'viewed_count' => 2,
            'first_viewed_at' => now()->subDays(1),
            'unique_id' => 'LEAD-' . now()->timestamp . '-001',
            'confirmation_number' => 'CONF-' . strtoupper(substr(md5(uniqid()), 0, 8)),
            'questions' => json_encode([
                ['question' => 'What is your timeline?', 'answer' => 'Ready to hire within 2 weeks'],
                ['question' => 'Current management situation?', 'answer' => 'Self-managed, need professional help'],
            ]),
        ]);

        // Distribute lead1 to 3 property managers
        foreach ($propertyManagers->take(3) as $pm) {
            UserLeads::create([
                'lead_id' => $lead1->id,
                'user_id' => $pm->id,
                'status' => 'viewed',
                'notes' => 'High-quality lead in target area',
            ]);
        }

        // Lead 2 - Medium quality, pending
        $lead2 = Lead::create([
            'name' => 'Susan Williams',
            'email' => 'susan.w@example.com',
            'phone' => '+1234567901',
            'address' => '456 Oak Avenue',
            'city' => 'San Francisco',
            'state' => 'CA',
            'zipcode' => '94102',
            'property_type' => 'Single Family',
            'number_of_units' => 1,
            'square_footage' => 2200,
            'year_built' => 2015,
            'price' => 1200000,
            'price_range' => '$1M - $1.5M',
            'category' => 'rent',
            'additional_services' => 'Tenant placement and rent collection',
            'preferred_contact' => 'phone',
            'source' => 'website',
            'utm_source' => 'facebook',
            'utm_medium' => 'cpc',
            'utm_campaign' => 'summer2025',
            'status' => 'pending',
            'quality_score' => 70,
            'distribution_count' => 1,
            'last_distributed_at' => now()->subDays(1),
            'viewed_count' => 1,
            'first_viewed_at' => now()->subHours(12),
            'unique_id' => 'LEAD-' . now()->timestamp . '-002',
            'confirmation_number' => 'CONF-' . strtoupper(substr(md5(uniqid()), 0, 8)),
        ]);

        // Distribute lead2 to 1 property manager
        UserLeads::create([
            'lead_id' => $lead2->id,
            'user_id' => $propertyManagers->where('city', 'San Francisco')->first()->id ?? $propertyManagers->first()->id,
            'status' => 'viewed',
        ]);

        // Lead 3 - High quality, new, not distributed
        $lead3 = Lead::create([
            'name' => 'James Martinez',
            'email' => 'j.martinez@example.com',
            'phone' => '+1234567902',
            'address' => '789 Pine Boulevard',
            'city' => 'Austin',
            'state' => 'TX',
            'zipcode' => '78701',
            'property_type' => 'Multi Family',
            'number_of_units' => 12,
            'square_footage' => 9600,
            'year_built' => 2018,
            'amenities' => json_encode(['Parking', 'Gym', 'Pool', 'Security']),
            'price' => 1750000,
            'price_range' => '$1.5M - $2M',
            'category' => 'manage',
            'additional_services' => 'Full-service property management',
            'preferred_contact' => 'email',
            'source' => 'referral',
            'status' => 'new',
            'quality_score' => 95,
            'distribution_count' => 0,
            'viewed_count' => 0,
            'unique_id' => 'LEAD-' . now()->timestamp . '-003',
            'confirmation_number' => 'CONF-' . strtoupper(substr(md5(uniqid()), 0, 8)),
            'questions' => json_encode([
                ['question' => 'What is your timeline?', 'answer' => 'Immediate'],
                ['question' => 'Current management situation?', 'answer' => 'Previous management contract ending'],
                ['question' => 'Budget for management fees?', 'answer' => '8-10% of monthly rent'],
            ]),
        ]);

        // Lead 4 - Commercial property in NYC
        $lead4 = Lead::create([
            'name' => 'Corporate Properties LLC',
            'email' => 'contact@corpproperties.com',
            'phone' => '+1234567903',
            'address' => '555 Madison Avenue',
            'city' => 'New York',
            'state' => 'NY',
            'zipcode' => '10001',
            'property_type' => 'Commercial',
            'number_of_units' => 25,
            'square_footage' => 45000,
            'year_built' => 2005,
            'amenities' => json_encode(['Parking', 'Security', 'Elevator']),
            'price' => 8500000,
            'price_range' => '$8M+',
            'category' => 'manage',
            'additional_services' => 'Commercial property management with tenant services',
            'preferred_contact' => 'email',
            'source' => 'website',
            'utm_source' => 'linkedin',
            'utm_medium' => 'cpc',
            'status' => 'contacted',
            'quality_score' => 85,
            'distribution_count' => 2,
            'last_distributed_at' => now()->subDays(3),
            'viewed_count' => 2,
            'first_viewed_at' => now()->subDays(2),
            'unique_id' => 'LEAD-' . now()->timestamp . '-004',
            'confirmation_number' => 'CONF-' . strtoupper(substr(md5(uniqid()), 0, 8)),
        ]);

        // Distribute lead4 to NYC property manager
        $nycPm = $propertyManagers->where('city', 'New York')->first();
        if ($nycPm) {
            UserLeads::create([
                'lead_id' => $lead4->id,
                'user_id' => $nycPm->id,
                'status' => 'contacted',
                'notes' => 'Commercial property - high value lead',
            ]);
        }

        // Lead 5 - Association property
        $lead5 = Lead::create([
            'name' => 'Sunset HOA Board',
            'email' => 'board@sunsethoa.com',
            'phone' => '+1234567904',
            'address' => '777 Sunset Commons',
            'city' => 'Miami',
            'state' => 'FL',
            'zipcode' => '33139',
            'property_type' => 'Association',
            'number_of_units' => 45,
            'square_footage' => 72000,
            'year_built' => 2012,
            'amenities' => json_encode(['Pool', 'Tennis Court', 'Clubhouse', 'Gym']),
            'category' => 'manage',
            'additional_services' => 'HOA management, financial services, community events',
            'preferred_contact' => 'email',
            'source' => 'website',
            'utm_source' => 'google',
            'utm_medium' => 'organic',
            'status' => 'new',
            'quality_score' => 80,
            'distribution_count' => 0,
            'viewed_count' => 0,
            'unique_id' => 'LEAD-' . now()->timestamp . '-005',
            'confirmation_number' => 'CONF-' . strtoupper(substr(md5(uniqid()), 0, 8)),
        ]);

        // Lead 6 - Low quality lead
        $lead6 = Lead::create([
            'name' => 'Quick Question',
            'email' => 'question@example.com',
            'phone' => '+1234567905',
            'address' => 'Not Provided',
            'city' => 'Los Angeles',
            'state' => 'CA',
            'zipcode' => '90001',
            'property_type' => 'Single Family',
            'category' => 'inquiry',
            'preferred_contact' => 'email',
            'source' => 'website',
            'status' => 'new',
            'quality_score' => 30,
            'distribution_count' => 0,
            'viewed_count' => 0,
            'unique_id' => 'LEAD-' . now()->timestamp . '-006',
            'confirmation_number' => 'CONF-' . strtoupper(substr(md5(uniqid()), 0, 8)),
        ]);

        // Lead 7 - Converted lead
        $lead7 = Lead::create([
            'name' => 'Patricia Brown',
            'email' => 'patricia.brown@example.com',
            'phone' => '+1234567906',
            'address' => '321 Elm Street',
            'city' => 'Los Angeles',
            'state' => 'CA',
            'zipcode' => '90025',
            'property_type' => 'Multi Family',
            'number_of_units' => 6,
            'square_footage' => 4800,
            'year_built' => 2016,
            'amenities' => json_encode(['Parking', 'Laundry']),
            'price' => 850000,
            'price_range' => '$800K - $900K',
            'category' => 'manage',
            'additional_services' => 'Full property management',
            'preferred_contact' => 'phone',
            'source' => 'website',
            'status' => 'converted',
            'quality_score' => 100,
            'distribution_count' => 2,
            'last_distributed_at' => now()->subDays(10),
            'viewed_count' => 5,
            'first_viewed_at' => now()->subDays(9),
            'unique_id' => 'LEAD-' . now()->timestamp . '-007',
            'confirmation_number' => 'CONF-' . strtoupper(substr(md5(uniqid()), 0, 8)),
        ]);

        // Distribute lead7 to LA property manager (converted)
        $laPm = $propertyManagers->where('city', 'Los Angeles')->first();
        if ($laPm) {
            UserLeads::create([
                'lead_id' => $lead7->id,
                'user_id' => $laPm->id,
                'status' => 'converted',
                'notes' => 'Signed contract for full property management services. 12-month agreement.',
            ]);
        }

        // Lead 8 - Lost lead
        $lead8 = Lead::create([
            'name' => 'Richard Taylor',
            'email' => 'rtaylor@example.com',
            'phone' => '+1234567907',
            'address' => '999 Broadway',
            'city' => 'San Francisco',
            'state' => 'CA',
            'zipcode' => '94110',
            'property_type' => 'Single Family',
            'number_of_units' => 1,
            'square_footage' => 1800,
            'year_built' => 2008,
            'price' => 980000,
            'category' => 'sell',
            'preferred_contact' => 'phone',
            'source' => 'website',
            'status' => 'lost',
            'quality_score' => 60,
            'distribution_count' => 2,
            'last_distributed_at' => now()->subDays(15),
            'viewed_count' => 3,
            'first_viewed_at' => now()->subDays(14),
            'unique_id' => 'LEAD-' . now()->timestamp . '-008',
            'confirmation_number' => 'CONF-' . strtoupper(substr(md5(uniqid()), 0, 8)),
        ]);

        // Distribute lead8 to SF property manager (lost)
        $sfPm = $propertyManagers->where('city', 'San Francisco')->first();
        if ($sfPm) {
            UserLeads::create([
                'lead_id' => $lead8->id,
                'user_id' => $sfPm->id,
                'status' => 'lost',
                'notes' => 'Chose another property management company. Lower fees offered.',
            ]);
        }

        echo "✓ Created 8 leads with various statuses and quality scores\n";
        echo "✓ Distributed leads to property managers\n";
    }
}
