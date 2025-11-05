<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\DocumentTemplate;
use Illuminate\Support\Facades\DB;

class DocumentTemplateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * Seeds document templates with mix of state-specific and generic documents.
     * Includes both free and paid templates across various categories.
     *
     * @return void
     */
    public function run()
    {
        // Skip if document templates already exist (idempotent seeder)
        if (DocumentTemplate::count() > 0) {
            echo "⚠ Document templates already seeded, skipping...\n";
            return;
        }

        $templates = [
            // Lease Agreements - State Specific
            [
                'title' => 'California Residential Lease Agreement',
                'description' => 'Comprehensive residential lease agreement compliant with California law including AB 1482 provisions, required disclosures, and tenant protection clauses.',
                'state_code' => 'CA',
                'category_slug' => 'lease-agreements',
                'file_path' => '/storage/templates/ca-residential-lease.pdf',
                'file_size_mb' => 1.2,
                'is_free' => true,
                'requires_signup' => false,
                'is_active' => true,
                'tags' => ['residential', 'california', 'ab-1482', 'comprehensive'],
            ],
            [
                'title' => 'New York Rent Stabilized Lease',
                'description' => 'DHCR-approved lease form for rent stabilized apartments in New York City with all required riders and disclosures.',
                'state_code' => 'NY',
                'category_slug' => 'lease-agreements',
                'file_path' => '/storage/templates/ny-rent-stabilized-lease.pdf',
                'file_size_mb' => 1.5,
                'is_free' => true,
                'requires_signup' => false,
                'is_active' => true,
                'tags' => ['residential', 'new-york', 'rent-stabilized', 'dhcr-approved'],
            ],
            [
                'title' => 'Texas Standard Residential Lease',
                'description' => 'TAR-approved residential lease agreement for Texas properties with standard terms and required state disclosures.',
                'state_code' => 'TX',
                'category_slug' => 'lease-agreements',
                'file_path' => '/storage/templates/tx-residential-lease.pdf',
                'file_size_mb' => 0.9,
                'is_free' => true,
                'requires_signup' => true,
                'is_active' => true,
                'tags' => ['residential', 'texas', 'tar-approved'],
            ],
            [
                'title' => 'Florida Residential Lease Agreement',
                'description' => 'Florida-specific residential lease with required disclosures including mold, radon, and flood zone information.',
                'state_code' => 'FL',
                'category_slug' => 'lease-agreements',
                'file_path' => '/storage/templates/fl-residential-lease.pdf',
                'file_size_mb' => 1.1,
                'is_free' => false,
                'requires_signup' => true,
                'is_active' => true,
                'tags' => ['residential', 'florida', 'comprehensive'],
            ],

            // Generic Lease Agreement
            [
                'title' => 'Month-to-Month Rental Agreement',
                'description' => 'Generic month-to-month rental agreement suitable for most states. Customize to meet your state\'s specific requirements.',
                'state_code' => null,
                'category_slug' => 'lease-agreements',
                'file_path' => '/storage/templates/month-to-month-lease.pdf',
                'file_size_mb' => 0.7,
                'is_free' => true,
                'requires_signup' => false,
                'is_active' => true,
                'tags' => ['month-to-month', 'generic', 'customizable'],
            ],

            // Eviction Notices - State Specific
            [
                'title' => 'California 3-Day Notice to Pay Rent or Quit',
                'description' => 'California-compliant 3-day notice for non-payment of rent with proper formatting and required language.',
                'state_code' => 'CA',
                'category_slug' => 'eviction-notices',
                'file_path' => '/storage/templates/ca-3-day-notice.pdf',
                'file_size_mb' => 0.3,
                'is_free' => true,
                'requires_signup' => false,
                'is_active' => true,
                'tags' => ['eviction', 'california', '3-day-notice', 'non-payment'],
            ],
            [
                'title' => 'New York 14-Day Rent Demand',
                'description' => 'New York 14-day notice demanding payment of rent with proper formatting for housing court filing.',
                'state_code' => 'NY',
                'category_slug' => 'eviction-notices',
                'file_path' => '/storage/templates/ny-14-day-demand.pdf',
                'file_size_mb' => 0.4,
                'is_free' => false,
                'requires_signup' => true,
                'is_active' => true,
                'tags' => ['eviction', 'new-york', '14-day-notice', 'non-payment'],
            ],
            [
                'title' => 'Texas 3-Day Notice to Vacate',
                'description' => 'Texas notice to vacate for non-payment of rent or lease violations with proper legal language.',
                'state_code' => 'TX',
                'category_slug' => 'eviction-notices',
                'file_path' => '/storage/templates/tx-3-day-notice.pdf',
                'file_size_mb' => 0.3,
                'is_free' => true,
                'requires_signup' => false,
                'is_active' => true,
                'tags' => ['eviction', 'texas', '3-day-notice'],
            ],

            // Rental Applications
            [
                'title' => 'Comprehensive Rental Application',
                'description' => 'Detailed rental application including employment history, references, income verification, and background check authorization.',
                'state_code' => null,
                'category_slug' => 'rental-applications',
                'file_path' => '/storage/templates/rental-application-comprehensive.pdf',
                'file_size_mb' => 0.6,
                'is_free' => true,
                'requires_signup' => false,
                'is_active' => true,
                'tags' => ['application', 'screening', 'generic'],
            ],
            [
                'title' => 'California Rental Application with Fair Housing Compliance',
                'description' => 'California-specific rental application with fair housing compliant questions and FEHA protections.',
                'state_code' => 'CA',
                'category_slug' => 'rental-applications',
                'file_path' => '/storage/templates/ca-rental-application.pdf',
                'file_size_mb' => 0.7,
                'is_free' => false,
                'requires_signup' => true,
                'is_active' => true,
                'tags' => ['application', 'california', 'fair-housing'],
            ],

            // Move-In/Move-Out Forms
            [
                'title' => 'Property Inspection Checklist',
                'description' => 'Room-by-room property condition checklist for documenting property condition at move-in and move-out.',
                'state_code' => null,
                'category_slug' => 'move-in-move-out-forms',
                'file_path' => '/storage/templates/inspection-checklist.pdf',
                'file_size_mb' => 0.5,
                'is_free' => true,
                'requires_signup' => false,
                'is_active' => true,
                'tags' => ['inspection', 'move-in', 'move-out', 'checklist'],
            ],
            [
                'title' => 'California Pre-Move-Out Inspection Notice',
                'description' => 'Required California notice offering tenants pre-move-out inspection as mandated by Civil Code 1950.5.',
                'state_code' => 'CA',
                'category_slug' => 'move-in-move-out-forms',
                'file_path' => '/storage/templates/ca-pre-moveout-notice.pdf',
                'file_size_mb' => 0.3,
                'is_free' => true,
                'requires_signup' => false,
                'is_active' => true,
                'tags' => ['california', 'move-out', 'inspection', 'required'],
            ],

            // Pet Agreements
            [
                'title' => 'Pet Addendum to Lease',
                'description' => 'Comprehensive pet addendum covering pet deposits, monthly pet rent, damage liability, and behavior requirements.',
                'state_code' => null,
                'category_slug' => 'pet-agreements',
                'file_path' => '/storage/templates/pet-addendum.pdf',
                'file_size_mb' => 0.4,
                'is_free' => true,
                'requires_signup' => false,
                'is_active' => true,
                'tags' => ['pet', 'addendum', 'generic'],
            ],
            [
                'title' => 'Service Animal Reasonable Accommodation Request',
                'description' => 'Form for tenants to request reasonable accommodation for service or emotional support animals under fair housing laws.',
                'state_code' => null,
                'category_slug' => 'pet-agreements',
                'file_path' => '/storage/templates/service-animal-request.pdf',
                'file_size_mb' => 0.4,
                'is_free' => false,
                'requires_signup' => true,
                'is_active' => true,
                'tags' => ['service-animal', 'reasonable-accommodation', 'fair-housing'],
            ],

            // Maintenance & Repairs
            [
                'title' => 'Maintenance Request Form',
                'description' => 'Standard maintenance request form for tenants to document repair needs with priority levels and photo upload options.',
                'state_code' => null,
                'category_slug' => 'maintenance-repairs',
                'file_path' => '/storage/templates/maintenance-request.pdf',
                'file_size_mb' => 0.3,
                'is_free' => true,
                'requires_signup' => false,
                'is_active' => true,
                'tags' => ['maintenance', 'repairs', 'request'],
            ],
            [
                'title' => 'Notice of Entry for Repairs',
                'description' => 'Multi-state notice template for landlord entry to conduct repairs with appropriate notice periods by state.',
                'state_code' => null,
                'category_slug' => 'maintenance-repairs',
                'file_path' => '/storage/templates/entry-notice-repairs.pdf',
                'file_size_mb' => 0.3,
                'is_free' => true,
                'requires_signup' => false,
                'is_active' => true,
                'tags' => ['entry', 'repairs', 'notice'],
            ],

            // Financial Documents
            [
                'title' => 'Rent Receipt Template',
                'description' => 'Professional rent receipt template with payment details, date, property address, and landlord signature.',
                'state_code' => null,
                'category_slug' => 'financial-documents',
                'file_path' => '/storage/templates/rent-receipt.pdf',
                'file_size_mb' => 0.2,
                'is_free' => true,
                'requires_signup' => false,
                'is_active' => true,
                'tags' => ['receipt', 'payment', 'financial'],
            ],
            [
                'title' => 'Security Deposit Itemization Form',
                'description' => 'Detailed security deposit return form with itemized deductions, receipts attachment, and state-specific timelines.',
                'state_code' => null,
                'category_slug' => 'financial-documents',
                'file_path' => '/storage/templates/deposit-itemization.pdf',
                'file_size_mb' => 0.4,
                'is_free' => false,
                'requires_signup' => true,
                'is_active' => true,
                'tags' => ['security-deposit', 'itemization', 'financial'],
            ],
            [
                'title' => 'Late Rent Notice',
                'description' => 'Professional late rent notice template reminding tenants of overdue rent and applicable late fees.',
                'state_code' => null,
                'category_slug' => 'financial-documents',
                'file_path' => '/storage/templates/late-rent-notice.pdf',
                'file_size_mb' => 0.3,
                'is_free' => true,
                'requires_signup' => false,
                'is_active' => true,
                'tags' => ['late-rent', 'notice', 'financial'],
            ],

            // Addendums & Disclosures
            [
                'title' => 'Lead-Based Paint Disclosure',
                'description' => 'Federally required lead-based paint disclosure for properties built before 1978 with EPA pamphlet.',
                'state_code' => null,
                'category_slug' => 'addendums-disclosures',
                'file_path' => '/storage/templates/lead-paint-disclosure.pdf',
                'file_size_mb' => 0.8,
                'is_free' => true,
                'requires_signup' => false,
                'is_active' => true,
                'tags' => ['disclosure', 'lead-paint', 'federal', 'required'],
            ],
            [
                'title' => 'California Bedbug Disclosure',
                'description' => 'Required California bedbug information sheet and disclosure form as mandated by state law.',
                'state_code' => 'CA',
                'category_slug' => 'addendums-disclosures',
                'file_path' => '/storage/templates/ca-bedbug-disclosure.pdf',
                'file_size_mb' => 0.5,
                'is_free' => true,
                'requires_signup' => false,
                'is_active' => true,
                'tags' => ['disclosure', 'california', 'bedbugs', 'required'],
            ],
        ];

        // Insert templates using the model (will auto-uppercase state codes)
        foreach ($templates as $template) {
            DocumentTemplate::create($template);
        }

        $this->command->info('Created ' . count($templates) . ' document templates successfully.');
    }
}
