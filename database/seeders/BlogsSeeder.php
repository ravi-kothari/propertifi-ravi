<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class BlogsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Skip if blogs already exist (idempotent seeder)
        if (DB::table('blogs')->where('slug', 'top-10-questions-to-ask-before-hiring-a-property-manager')->exists()) {
            echo "⚠ Blogs already seeded, skipping...\n";
            return;
        }

        $blogs = [
            [
                'heading' => 'Top 10 Questions to Ask Before Hiring a Property Manager',
                'description' => "Choosing the right property manager is one of the most important decisions you'll make as a rental property owner. A good property manager can save you time, reduce stress, and maximize your investment returns. However, not all property managers are created equal.\n\nBefore signing a management agreement, make sure to ask about their experience, fee structure, tenant screening process, maintenance procedures, and communication methods. Understanding these key areas will help you make an informed decision and find a property manager who aligns with your goals and expectations.",
                'featured' => 1,
                'post_date' => now()->subDays(5),
            ],
            [
                'heading' => 'Understanding Property Management Fees: A Complete Breakdown',
                'description' => "Property management fees can vary significantly depending on location, property type, and services offered. Understanding what you're paying for is crucial to evaluating the value proposition of any property management company.\n\nTypical fees include monthly management fees (usually 8-12% of rent collected), leasing fees (50-100% of first month's rent), maintenance markups, and various administrative charges. In this guide, we break down each fee type and explain what constitutes fair market pricing in today's property management industry.",
                'featured' => 1,
                'post_date' => now()->subDays(10),
            ],
            [
                'heading' => '5 Red Flags When Choosing a Property Manager',
                'description' => "Not every property manager will be the right fit for your investment property. Knowing the warning signs can save you from costly mistakes and headaches down the road.\n\nWatch out for managers who lack proper licensing, have poor online reviews, offer unusually low fees, can't provide references, or are vague about their processes. These red flags often indicate potential problems with communication, financial management, or overall competence in handling your valuable asset.",
                'featured' => 0,
                'post_date' => now()->subDays(15),
            ],
            [
                'heading' => 'The Ultimate Guide to Rental Property Maintenance',
                'description' => "Proper maintenance is essential for protecting your investment and keeping tenants happy. A well-maintained property attracts quality tenants, commands higher rents, and experiences fewer costly emergency repairs.\n\nSuccessful property maintenance involves preventive care, quick response to tenant requests, seasonal preparations, and regular property inspections. Learn how professional property managers handle maintenance efficiently and how you can implement similar systems whether you self-manage or hire professional help.",
                'featured' => 0,
                'post_date' => now()->subDays(20),
            ],
            [
                'heading' => 'Maximizing ROI: How Professional Property Management Pays for Itself',
                'description' => "Many property owners hesitate to hire professional management due to the cost. However, experienced property managers often pay for themselves through reduced vacancy rates, better tenant retention, and optimized rental pricing.\n\nProfessional managers also save owners significant time and stress while protecting against costly legal mistakes. When you factor in their expertise in tenant screening, maintenance coordination, and market knowledge, the return on investment often far exceeds the management fees paid.",
                'featured' => 0,
                'post_date' => now()->subDays(25),
            ],
        ];

        foreach ($blogs as $blog) {
            DB::table('blogs')->insert([
                'heading' => $blog['heading'],
                'description' => $blog['description'],
                'slug' => Str::slug($blog['heading']),
                'status' => 1,
                'featured' => $blog['featured'],
                'post_date' => $blog['post_date'],
                'seo_title' => $blog['heading'],
                'seo_description' => Str::limit(strip_tags($blog['description']), 160),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
