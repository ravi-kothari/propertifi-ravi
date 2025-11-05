<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Roles;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UsersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Skip if users already exist (idempotent seeder)
        if (User::where('email', 'admin@propertifi.com')->exists()) {
            echo "⚠ Users already seeded, skipping...\n";
            return;
        }

        // Get role IDs if they exist
        $adminRole = Roles::where('name', 'Admin')->first();
        $pmRole = Roles::where('name', 'Property Manager')->first();
        $ownerRole = Roles::where('name', 'Owner')->first();

        // Create Admin User
        User::create([
            'type' => User::TYPE_ADMIN,
            'name' => 'Admin User',
            'email' => 'admin@propertifi.com',
            'password' => Hash::make('password123'),
            'mobile' => '+1234567890',
            'gender' => 'Male',
            'dob' => '1985-01-15',
            'address' => '123 Admin Street',
            'city' => 'Los Angeles',
            'state' => 'California',
            'zipcode' => '90001',
            'country' => 'USA',
            'email_verified_at' => now(),
            'status' => 1,
            'role_id' => $adminRole ? $adminRole->id : null,
            'is_verified' => true,
            'verified_at' => now(),
        ]);

        // Create Property Manager 1 - California, Los Angeles
        User::create([
            'type' => User::TYPE_PM,
            'name' => 'John Smith Property Management',
            'email' => 'john@smithpm.com',
            'password' => Hash::make('password123'),
            'mobile' => '+1234567891',
            'gender' => 'Male',
            'dob' => '1980-05-20',
            'address' => '456 PM Boulevard',
            'city' => 'Los Angeles',
            'state' => 'California',
            'zipcode' => '90015',
            'country' => 'USA',
            'company_name' => 'Smith Property Management',
            'about' => 'Premier property management services in Los Angeles. Over 20 years of experience managing residential and commercial properties.',
            'p_contact_name' => 'John Smith',
            'p_contact_no' => '+1234567891',
            'p_contact_email' => 'contact@smithpm.com',
            'position' => 'CEO & Founder',
            'website' => 'https://smithpm.com',
            'single_family' => 1,
            'multi_family' => 1,
            'association_property' => 1,
            'commercial_property' => 0,
            'units' => 250,
            'featured' => 1,
            'credits' => 100,
            'email_verified_at' => now(),
            'status' => 1,
            'role_id' => $pmRole ? $pmRole->id : null,
            'is_verified' => true,
            'verified_at' => now(),
            'slug' => 'john-smith-property-management-los-angeles',
            'seo_title' => 'John Smith Property Management - Los Angeles CA',
            'seo_description' => 'Professional property management services in Los Angeles. Managing 250+ units with expertise in residential properties.',
            'seo_keywords' => 'property management, Los Angeles, residential management',
        ]);

        // Create Property Manager 2 - California, San Francisco
        User::create([
            'type' => User::TYPE_PM,
            'name' => 'Sarah Johnson Properties',
            'email' => 'sarah@johnsonproperties.com',
            'password' => Hash::make('password123'),
            'mobile' => '+1234567892',
            'gender' => 'Female',
            'dob' => '1982-08-14',
            'address' => '789 Market Street',
            'city' => 'San Francisco',
            'state' => 'California',
            'zipcode' => '94102',
            'country' => 'USA',
            'company_name' => 'Johnson Properties Inc',
            'about' => 'Full-service property management company specializing in multi-family and commercial properties in the Bay Area.',
            'p_contact_name' => 'Sarah Johnson',
            'p_contact_no' => '+1234567892',
            'p_contact_email' => 'info@johnsonproperties.com',
            'position' => 'Managing Director',
            'website' => 'https://johnsonproperties.com',
            'single_family' => 0,
            'multi_family' => 1,
            'association_property' => 1,
            'commercial_property' => 1,
            'units' => 500,
            'featured' => 1,
            'credits' => 150,
            'email_verified_at' => now(),
            'status' => 1,
            'role_id' => $pmRole ? $pmRole->id : null,
            'is_verified' => true,
            'verified_at' => now(),
            'slug' => 'sarah-johnson-properties-san-francisco',
            'seo_title' => 'Johnson Properties - San Francisco Property Management',
            'seo_description' => 'Bay Area property management experts. 500+ units managed with focus on multi-family and commercial properties.',
            'seo_keywords' => 'property management, San Francisco, Bay Area, multi-family',
        ]);

        // Create Property Manager 3 - Texas, Austin
        User::create([
            'type' => User::TYPE_PM,
            'name' => 'Austin Elite Property Management',
            'email' => 'info@austinelitepm.com',
            'password' => Hash::make('password123'),
            'mobile' => '+1234567893',
            'gender' => 'Male',
            'dob' => '1978-03-25',
            'address' => '321 Congress Avenue',
            'city' => 'Austin',
            'state' => 'Texas',
            'zipcode' => '78701',
            'country' => 'USA',
            'company_name' => 'Austin Elite Property Management',
            'about' => 'Award-winning property management services in Austin. We manage single-family, multi-family, and association properties.',
            'p_contact_name' => 'Mike Williams',
            'p_contact_no' => '+1234567893',
            'p_contact_email' => 'contact@austinelitepm.com',
            'position' => 'Owner',
            'website' => 'https://austinelitepm.com',
            'single_family' => 1,
            'multi_family' => 1,
            'association_property' => 1,
            'commercial_property' => 0,
            'units' => 180,
            'featured' => 1,
            'credits' => 80,
            'email_verified_at' => now(),
            'status' => 1,
            'role_id' => $pmRole ? $pmRole->id : null,
            'is_verified' => true,
            'verified_at' => now(),
            'slug' => 'austin-elite-property-management',
            'seo_title' => 'Austin Elite Property Management - Austin TX',
            'seo_description' => 'Top-rated property management in Austin, Texas. Specialized in residential property management.',
            'seo_keywords' => 'property management, Austin, Texas, residential',
        ]);

        // Create Property Manager 4 - New York, New York City
        User::create([
            'type' => User::TYPE_PM,
            'name' => 'Manhattan Property Experts',
            'email' => 'contact@manhattanpm.com',
            'password' => Hash::make('password123'),
            'mobile' => '+1234567894',
            'gender' => 'Female',
            'dob' => '1975-11-30',
            'address' => '555 5th Avenue',
            'city' => 'New York',
            'state' => 'New York',
            'zipcode' => '10001',
            'country' => 'USA',
            'company_name' => 'Manhattan Property Experts',
            'about' => 'Premier property management for high-end residential and commercial properties in Manhattan and surrounding boroughs.',
            'p_contact_name' => 'Emily Chen',
            'p_contact_no' => '+1234567894',
            'p_contact_email' => 'emily@manhattanpm.com',
            'position' => 'President',
            'website' => 'https://manhattanpm.com',
            'single_family' => 0,
            'multi_family' => 1,
            'association_property' => 0,
            'commercial_property' => 1,
            'units' => 750,
            'featured' => 1,
            'credits' => 200,
            'email_verified_at' => now(),
            'status' => 1,
            'role_id' => $pmRole ? $pmRole->id : null,
            'is_verified' => true,
            'verified_at' => now(),
            'slug' => 'manhattan-property-experts-nyc',
            'seo_title' => 'Manhattan Property Experts - NYC Property Management',
            'seo_description' => 'High-end property management in Manhattan. Over 750 units managed with focus on luxury residential and commercial.',
            'seo_keywords' => 'property management, Manhattan, NYC, luxury, commercial',
        ]);

        // Create Property Manager 5 - Florida, Miami (Unverified)
        User::create([
            'type' => User::TYPE_PM,
            'name' => 'Miami Coastal Properties',
            'email' => 'info@miamicoastal.com',
            'password' => Hash::make('password123'),
            'mobile' => '+1234567895',
            'gender' => 'Male',
            'dob' => '1988-07-10',
            'address' => '777 Ocean Drive',
            'city' => 'Miami',
            'state' => 'Florida',
            'zipcode' => '33139',
            'country' => 'USA',
            'company_name' => 'Miami Coastal Properties',
            'about' => 'New property management company specializing in beachfront and coastal properties.',
            'p_contact_name' => 'Carlos Rodriguez',
            'p_contact_no' => '+1234567895',
            'p_contact_email' => 'carlos@miamicoastal.com',
            'position' => 'Founder',
            'website' => 'https://miamicoastal.com',
            'single_family' => 1,
            'multi_family' => 1,
            'association_property' => 1,
            'commercial_property' => 0,
            'units' => 50,
            'featured' => 0,
            'credits' => 50,
            'email_verified_at' => now(),
            'status' => 1,
            'role_id' => $pmRole ? $pmRole->id : null,
            'is_verified' => false,
            'verified_at' => null,
            'slug' => 'miami-coastal-properties',
            'seo_title' => 'Miami Coastal Properties - Miami FL',
            'seo_description' => 'Coastal property management in Miami, Florida.',
            'seo_keywords' => 'property management, Miami, Florida, coastal',
        ]);

        // Create Owner 1
        User::create([
            'type' => User::TYPE_OWNER,
            'name' => 'Robert Davis',
            'email' => 'robert@example.com',
            'password' => Hash::make('password123'),
            'mobile' => '+1234567896',
            'gender' => 'Male',
            'dob' => '1970-02-28',
            'address' => '999 Property Lane',
            'city' => 'Los Angeles',
            'state' => 'California',
            'zipcode' => '90025',
            'country' => 'USA',
            'portfolio_type' => 'Single Family',
            'units' => 3,
            'email_verified_at' => now(),
            'status' => 1,
            'role_id' => $ownerRole ? $ownerRole->id : null,
        ]);

        // Create Owner 2
        User::create([
            'type' => User::TYPE_OWNER,
            'name' => 'Jennifer Martinez',
            'email' => 'jennifer@example.com',
            'password' => Hash::make('password123'),
            'mobile' => '+1234567897',
            'gender' => 'Female',
            'dob' => '1985-09-12',
            'address' => '888 Investment Drive',
            'city' => 'San Francisco',
            'state' => 'California',
            'zipcode' => '94110',
            'country' => 'USA',
            'portfolio_type' => 'Multi Family',
            'units' => 12,
            'email_verified_at' => now(),
            'status' => 1,
            'role_id' => $ownerRole ? $ownerRole->id : null,
        ]);

        // Create Owner 3
        User::create([
            'type' => User::TYPE_OWNER,
            'name' => 'David Thompson',
            'email' => 'david@example.com',
            'password' => Hash::make('password123'),
            'mobile' => '+1234567898',
            'gender' => 'Male',
            'dob' => '1968-06-05',
            'address' => '444 Investor Boulevard',
            'city' => 'Austin',
            'state' => 'Texas',
            'zipcode' => '78702',
            'country' => 'USA',
            'portfolio_type' => 'Mixed',
            'units' => 25,
            'email_verified_at' => now(),
            'status' => 1,
            'role_id' => $ownerRole ? $ownerRole->id : null,
        ]);

        echo "✓ Created 3 admin users, 5 property managers, and 3 owners\n";
    }
}
