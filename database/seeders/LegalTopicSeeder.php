<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\LegalTopic;
use Illuminate\Support\Facades\DB;

class LegalTopicSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * Seeds common property management legal topics that will be used
     * throughout the platform for organizing state law content.
     *
     * @return void
     */
    public function run()
    {
        // Skip if legal topics already exist (idempotent seeder)
        if (LegalTopic::count() > 0) {
            echo "⚠ Legal topics already seeded, skipping...\n";
            return;
        }

        // Define legal topics with descriptions
        $topics = [
            [
                'name' => 'Eviction Laws',
                'description' => 'Comprehensive information about eviction procedures, notice requirements, and tenant protections across different states.',
                'sort_order' => 10,
                'is_active' => true,
            ],
            [
                'name' => 'Security Deposits',
                'description' => 'State-specific regulations governing security deposit limits, handling, storage, and return requirements.',
                'sort_order' => 20,
                'is_active' => true,
            ],
            [
                'name' => 'Lease Agreements',
                'description' => 'Legal requirements for residential lease agreements, mandatory clauses, and prohibited terms.',
                'sort_order' => 30,
                'is_active' => true,
            ],
            [
                'name' => 'Fair Housing',
                'description' => 'Federal and state fair housing laws, protected classes, and anti-discrimination requirements.',
                'sort_order' => 40,
                'is_active' => true,
            ],
            [
                'name' => 'Tenant Rights',
                'description' => 'Overview of tenant rights including habitability, privacy, retaliation protection, and access to utilities.',
                'sort_order' => 50,
                'is_active' => true,
            ],
            [
                'name' => 'Property Maintenance',
                'description' => 'Landlord obligations for property maintenance, repairs, habitability standards, and tenant responsibilities.',
                'sort_order' => 60,
                'is_active' => true,
            ],
            [
                'name' => 'Rent Control',
                'description' => 'State and local rent control ordinances, rent increase limitations, and exemptions.',
                'sort_order' => 70,
                'is_active' => true,
            ],
            [
                'name' => 'Landlord Entry Rights',
                'description' => 'Legal requirements for landlord entry into rental properties including notice requirements and permissible reasons.',
                'sort_order' => 80,
                'is_active' => true,
            ],
            [
                'name' => 'Lead Paint Disclosure',
                'description' => 'Federal and state requirements for lead-based paint disclosure in pre-1978 properties.',
                'sort_order' => 90,
                'is_active' => true,
            ],
            [
                'name' => 'Pet Policies',
                'description' => 'Laws governing pet deposits, pet rent, service animals, and emotional support animals in rental housing.',
                'sort_order' => 100,
                'is_active' => true,
            ],
        ];

        // Insert topics using the model (will auto-generate slugs)
        foreach ($topics as $topic) {
            LegalTopic::create($topic);
        }

        $this->command->info('Created ' . count($topics) . ' legal topics successfully.');
    }
}
