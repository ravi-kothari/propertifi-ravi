<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\DocumentCategory;
use Illuminate\Support\Facades\DB;

class DocumentCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * Seeds document categories for organizing property management templates.
     * Categories will be referenced by document templates.
     *
     * @return void
     */
    public function run()
    {
        // Skip if document categories already exist (idempotent seeder)
        if (DocumentCategory::count() > 0) {
            echo "⚠ Document categories already seeded, skipping...\n";
            return;
        }

        // Define document categories
        $categories = [
            [
                'name' => 'Lease Agreements',
                'description' => 'Residential and commercial lease agreements, rental contracts, and lease addendums for various property types and situations.',
                'sort_order' => 10,
                'is_active' => true,
            ],
            [
                'name' => 'Eviction Notices',
                'description' => 'State-specific eviction notices including pay or quit notices, cure or quit notices, and unconditional quit notices.',
                'sort_order' => 20,
                'is_active' => true,
            ],
            [
                'name' => 'Rental Applications',
                'description' => 'Tenant screening forms, rental applications, background check authorizations, and income verification documents.',
                'sort_order' => 30,
                'is_active' => true,
            ],
            [
                'name' => 'Move-In/Move-Out Forms',
                'description' => 'Property inspection checklists, condition reports, inventory lists, and move-in/move-out documentation.',
                'sort_order' => 40,
                'is_active' => true,
            ],
            [
                'name' => 'Pet Agreements',
                'description' => 'Pet addendums, pet policies, pet deposit agreements, and service/emotional support animal documentation.',
                'sort_order' => 50,
                'is_active' => true,
            ],
            [
                'name' => 'Maintenance & Repairs',
                'description' => 'Maintenance request forms, repair authorizations, contractor agreements, and work order templates.',
                'sort_order' => 60,
                'is_active' => true,
            ],
            [
                'name' => 'Financial Documents',
                'description' => 'Rent receipts, late payment notices, security deposit itemization forms, and lease renewal notices.',
                'sort_order' => 70,
                'is_active' => true,
            ],
            [
                'name' => 'Addendums & Disclosures',
                'description' => 'Lease addendums, required state disclosures, lead paint disclosures, and property condition statements.',
                'sort_order' => 80,
                'is_active' => true,
            ],
        ];

        // Insert categories using the model (will auto-generate slugs)
        foreach ($categories as $category) {
            DocumentCategory::create($category);
        }

        $this->command->info('Created ' . count($categories) . ' document categories successfully.');
    }
}
