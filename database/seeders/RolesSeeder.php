<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Roles;
use Carbon\Carbon;

class RolesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $now = Carbon::now();

        $roles = [
            [
                'name' => 'super_admin',
                'title' => 'Super Administrator',
                'description' => 'Full system access with all permissions',
                'permissions' => $this->getAllPermissions(),
                'status' => 1,
                'is_admin' => true,
                'is_default' => false,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'name' => 'account_manager',
                'title' => 'Account Manager',
                'description' => 'Manages user accounts and basic analytics',
                'permissions' => [
                    'manage_users',
                    'create_users',
                    'edit_users',
                    'verify_users',
                    'view_all_leads',
                    'view_analytics',
                ],
                'status' => 1,
                'is_admin' => false,
                'is_default' => false,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'name' => 'content_manager',
                'title' => 'Content Manager',
                'description' => 'Manages website content including blogs, templates, and legal content',
                'permissions' => [
                    'manage_legal_content',
                    'manage_templates',
                    'manage_blogs',
                    'manage_testimonials',
                ],
                'status' => 1,
                'is_admin' => false,
                'is_default' => false,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'name' => 'lead_manager',
                'title' => 'Lead Manager',
                'description' => 'Manages lead distribution and property manager performance',
                'permissions' => [
                    'view_all_leads',
                    'manage_leads',
                    'distribute_leads',
                    'export_leads',
                    'view_analytics',
                ],
                'status' => 1,
                'is_admin' => false,
                'is_default' => false,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'name' => 'property_manager',
                'title' => 'Property Manager',
                'description' => 'Default role for property managers (no admin access)',
                'permissions' => [],
                'status' => 1,
                'is_admin' => false,
                'is_default' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'name' => 'analytics_viewer',
                'title' => 'Analytics Viewer',
                'description' => 'View-only access to analytics and reports',
                'permissions' => [
                    'view_analytics',
                    'view_revenue_analytics',
                ],
                'status' => 1,
                'is_admin' => false,
                'is_default' => false,
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ];

        foreach ($roles as $role) {
            Roles::updateOrCreate(
                ['name' => $role['name']],
                $role
            );
        }

        $this->command->info('Default roles created successfully!');
    }

    /**
     * Get all available permissions.
     *
     * @return array
     */
    private function getAllPermissions()
    {
        return [
            // User Management
            'manage_users',
            'create_users',
            'edit_users',
            'delete_users',
            'verify_users',

            // Role Management
            'manage_roles',
            'create_roles',
            'edit_roles',
            'delete_roles',

            // Lead Management
            'view_all_leads',
            'manage_leads',
            'distribute_leads',
            'export_leads',

            // Content Management
            'manage_legal_content',
            'manage_templates',
            'manage_blogs',
            'manage_testimonials',

            // Analytics
            'view_analytics',
            'view_revenue_analytics',
            'export_analytics',

            // System
            'manage_settings',
            'manage_tiers',
            'clear_cache',
            'view_logs',
        ];
    }
}
