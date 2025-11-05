<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\StateProfile;
use Illuminate\Support\Facades\DB;

class StateProfileSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * Seeds state profiles for major US states with rental market information.
     * These profiles will be referenced by state law content and document templates.
     *
     * @return void
     */
    public function run()
    {
        // Skip if state profiles already exist (idempotent seeder)
        if (StateProfile::count() > 0) {
            echo "⚠ State profiles already seeded, skipping...\n";
            return;
        }

        // Define state profiles with comprehensive information
        $states = [
            [
                'state_code' => 'CA',
                'name' => 'California',
                'abbreviation' => 'CA',
                'overview' => 'California has some of the strongest tenant protections in the nation, with comprehensive rent control laws, strict eviction procedures, and extensive security deposit regulations. The state\'s high-cost rental market has led to progressive tenant-friendly legislation.',
                'meta_data' => [
                    'population' => '39,538,223',
                    'rental_households' => '45%',
                    'median_rent' => '$2,800',
                    'key_cities' => ['Los Angeles', 'San Francisco', 'San Diego', 'San Jose'],
                ],
                'is_active' => true,
            ],
            [
                'state_code' => 'NY',
                'name' => 'New York',
                'abbreviation' => 'NY',
                'overview' => 'New York features extensive tenant protections, particularly in New York City with rent stabilization laws and strong anti-eviction measures. The state requires detailed lease disclosures and has strict security deposit handling requirements.',
                'meta_data' => [
                    'population' => '20,201,249',
                    'rental_households' => '46%',
                    'median_rent' => '$1,850',
                    'key_cities' => ['New York City', 'Buffalo', 'Rochester', 'Albany'],
                ],
                'is_active' => true,
            ],
            [
                'state_code' => 'TX',
                'name' => 'Texas',
                'abbreviation' => 'TX',
                'overview' => 'Texas is generally landlord-friendly with fewer tenant protections compared to coastal states. The state allows more flexibility in lease terms and has relatively straightforward eviction procedures, though major cities have begun implementing tenant protection ordinances.',
                'meta_data' => [
                    'population' => '29,145,505',
                    'rental_households' => '38%',
                    'median_rent' => '$1,350',
                    'key_cities' => ['Houston', 'Dallas', 'Austin', 'San Antonio'],
                ],
                'is_active' => true,
            ],
            [
                'state_code' => 'FL',
                'name' => 'Florida',
                'abbreviation' => 'FL',
                'overview' => 'Florida maintains a balanced approach to landlord-tenant law with clear statutes governing security deposits and evictions. The state\'s significant rental market, especially in vacation areas, has specific provisions for short-term rentals.',
                'meta_data' => [
                    'population' => '21,538,187',
                    'rental_households' => '35%',
                    'median_rent' => '$1,650',
                    'key_cities' => ['Miami', 'Tampa', 'Orlando', 'Jacksonville'],
                ],
                'is_active' => true,
            ],
            [
                'state_code' => 'IL',
                'name' => 'Illinois',
                'abbreviation' => 'IL',
                'overview' => 'Illinois, particularly Chicago, has comprehensive tenant protection laws including security deposit interest requirements and detailed notice requirements for lease termination. The state mandates habitability standards and limits on late fees.',
                'meta_data' => [
                    'population' => '12,812,508',
                    'rental_households' => '33%',
                    'median_rent' => '$1,250',
                    'key_cities' => ['Chicago', 'Aurora', 'Naperville', 'Rockford'],
                ],
                'is_active' => true,
            ],
            [
                'state_code' => 'PA',
                'name' => 'Pennsylvania',
                'abbreviation' => 'PA',
                'overview' => 'Pennsylvania landlord-tenant law varies significantly between Philadelphia and other areas. The state has specific requirements for security deposit escrow accounts and detailed provisions for property maintenance and habitability.',
                'meta_data' => [
                    'population' => '13,002,700',
                    'rental_households' => '30%',
                    'median_rent' => '$1,150',
                    'key_cities' => ['Philadelphia', 'Pittsburgh', 'Allentown', 'Erie'],
                ],
                'is_active' => true,
            ],
            [
                'state_code' => 'OH',
                'name' => 'Ohio',
                'abbreviation' => 'OH',
                'overview' => 'Ohio provides a straightforward framework for landlord-tenant relationships with clear eviction procedures and security deposit regulations. Local ordinances in major cities may provide additional tenant protections.',
                'meta_data' => [
                    'population' => '11,799,448',
                    'rental_households' => '32%',
                    'median_rent' => '$950',
                    'key_cities' => ['Columbus', 'Cleveland', 'Cincinnati', 'Toledo'],
                ],
                'is_active' => true,
            ],
            [
                'state_code' => 'GA',
                'name' => 'Georgia',
                'abbreviation' => 'GA',
                'overview' => 'Georgia has relatively landlord-friendly laws with streamlined eviction processes and flexible lease terms. The state requires security deposit accounting and has specific provisions for property access and maintenance.',
                'meta_data' => [
                    'population' => '10,711,908',
                    'rental_households' => '36%',
                    'median_rent' => '$1,400',
                    'key_cities' => ['Atlanta', 'Augusta', 'Columbus', 'Savannah'],
                ],
                'is_active' => true,
            ],
            [
                'state_code' => 'NC',
                'name' => 'North Carolina',
                'abbreviation' => 'NC',
                'overview' => 'North Carolina landlord-tenant law emphasizes clear documentation and timely notice requirements. The state has specific regulations for security deposits and provides detailed eviction procedures.',
                'meta_data' => [
                    'population' => '10,439,388',
                    'rental_households' => '34%',
                    'median_rent' => '$1,200',
                    'key_cities' => ['Charlotte', 'Raleigh', 'Greensboro', 'Durham'],
                ],
                'is_active' => true,
            ],
            [
                'state_code' => 'MI',
                'name' => 'Michigan',
                'abbreviation' => 'MI',
                'overview' => 'Michigan law provides balanced protections with detailed security deposit regulations including damage itemization requirements. The state has clear habitability standards and specific notice requirements for entry and lease termination.',
                'meta_data' => [
                    'population' => '10,077,331',
                    'rental_households' => '29%',
                    'median_rent' => '$1,050',
                    'key_cities' => ['Detroit', 'Grand Rapids', 'Warren', 'Ann Arbor'],
                ],
                'is_active' => true,
            ],
            [
                'state_code' => 'WA',
                'name' => 'Washington',
                'abbreviation' => 'WA',
                'overview' => 'Washington State has progressive tenant protection laws with strict security deposit requirements, detailed eviction procedures, and strong habitability standards. Seattle has additional tenant protections including just cause eviction requirements.',
                'meta_data' => [
                    'population' => '7,705,281',
                    'rental_households' => '37%',
                    'median_rent' => '$1,850',
                    'key_cities' => ['Seattle', 'Spokane', 'Tacoma', 'Vancouver'],
                ],
                'is_active' => true,
            ],
            [
                'state_code' => 'MA',
                'name' => 'Massachusetts',
                'abbreviation' => 'MA',
                'overview' => 'Massachusetts has comprehensive tenant protections including strict security deposit regulations, mandatory interest payments, and detailed habitability requirements. The state has strong anti-retaliation and anti-discrimination provisions.',
                'meta_data' => [
                    'population' => '7,029,917',
                    'rental_households' => '38%',
                    'median_rent' => '$2,200',
                    'key_cities' => ['Boston', 'Worcester', 'Springfield', 'Cambridge'],
                ],
                'is_active' => true,
            ],
        ];

        // Insert state profiles using the model (will auto-generate slugs)
        foreach ($states as $state) {
            StateProfile::create($state);
        }

        $this->command->info('Created ' . count($states) . ' state profiles successfully.');
    }
}
