<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Application Permissions
    |--------------------------------------------------------------------------
    |
    | This file contains all available permissions in the application.
    | Permissions are grouped by category for better organization.
    |
    */

    'categories' => [
        'user_management' => [
            'label' => 'User Management',
            'description' => 'Permissions related to managing users',
            'permissions' => [
                'manage_users' => [
                    'label' => 'Manage Users',
                    'description' => 'Full access to user management',
                ],
                'create_users' => [
                    'label' => 'Create Users',
                    'description' => 'Create new users',
                ],
                'edit_users' => [
                    'label' => 'Edit Users',
                    'description' => 'Edit existing users',
                ],
                'delete_users' => [
                    'label' => 'Delete Users',
                    'description' => 'Delete users',
                ],
                'verify_users' => [
                    'label' => 'Verify Users',
                    'description' => 'Verify and approve property managers',
                ],
            ],
        ],

        'role_management' => [
            'label' => 'Role Management',
            'description' => 'Permissions related to managing roles and permissions',
            'permissions' => [
                'manage_roles' => [
                    'label' => 'Manage Roles',
                    'description' => 'Full access to role management',
                ],
                'create_roles' => [
                    'label' => 'Create Roles',
                    'description' => 'Create new roles',
                ],
                'edit_roles' => [
                    'label' => 'Edit Roles',
                    'description' => 'Edit existing roles',
                ],
                'delete_roles' => [
                    'label' => 'Delete Roles',
                    'description' => 'Delete roles',
                ],
            ],
        ],

        'lead_management' => [
            'label' => 'Lead Management',
            'description' => 'Permissions related to managing leads',
            'permissions' => [
                'view_all_leads' => [
                    'label' => 'View All Leads',
                    'description' => 'View all leads in the system',
                ],
                'manage_leads' => [
                    'label' => 'Manage Leads',
                    'description' => 'Full access to lead management',
                ],
                'distribute_leads' => [
                    'label' => 'Distribute Leads',
                    'description' => 'Manually distribute leads to property managers',
                ],
                'export_leads' => [
                    'label' => 'Export Leads',
                    'description' => 'Export lead data',
                ],
            ],
        ],

        'content_management' => [
            'label' => 'Content Management',
            'description' => 'Permissions related to managing website content',
            'permissions' => [
                'manage_legal_content' => [
                    'label' => 'Manage Legal Content',
                    'description' => 'Manage state laws and legal topics',
                ],
                'manage_templates' => [
                    'label' => 'Manage Templates',
                    'description' => 'Manage document templates',
                ],
                'manage_blogs' => [
                    'label' => 'Manage Blogs',
                    'description' => 'Create and edit blog posts',
                ],
                'manage_testimonials' => [
                    'label' => 'Manage Testimonials',
                    'description' => 'Manage customer testimonials',
                ],
            ],
        ],

        'analytics' => [
            'label' => 'Analytics & Reports',
            'description' => 'Permissions related to viewing analytics and reports',
            'permissions' => [
                'view_analytics' => [
                    'label' => 'View Analytics',
                    'description' => 'View general analytics and reports',
                ],
                'view_revenue_analytics' => [
                    'label' => 'View Revenue Analytics',
                    'description' => 'View revenue and financial analytics',
                ],
                'export_analytics' => [
                    'label' => 'Export Analytics',
                    'description' => 'Export analytics data',
                ],
            ],
        ],

        'system' => [
            'label' => 'System Settings',
            'description' => 'Permissions related to system configuration',
            'permissions' => [
                'manage_settings' => [
                    'label' => 'Manage Settings',
                    'description' => 'Access system settings',
                ],
                'manage_tiers' => [
                    'label' => 'Manage Tiers',
                    'description' => 'Manage subscription tiers',
                ],
                'clear_cache' => [
                    'label' => 'Clear Cache',
                    'description' => 'Clear application cache',
                ],
                'view_logs' => [
                    'label' => 'View Logs',
                    'description' => 'View system logs',
                ],
            ],
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Flatten all permissions into a simple array
    |--------------------------------------------------------------------------
    */

    'all' => function () {
        $permissions = [];
        foreach (config('permissions.categories') as $category) {
            foreach ($category['permissions'] as $key => $permission) {
                $permissions[] = $key;
            }
        }
        return $permissions;
    },
];
