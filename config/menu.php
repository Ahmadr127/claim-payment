<?php

return [
    [
        'title' => 'Dashboard',
        'icon' => 'fas fa-tachometer-alt',
        'route' => 'dashboard',
        'permission' => 'view_dashboard',
        'active' => 'dashboard',
    ],
    [
        'title' => 'Pengguna & Akses',
        'icon' => 'fas fa-users-cog',
        'permission' => ['manage_users', 'manage_roles', 'manage_permissions'],
        'active' => ['users.*', 'roles.*', 'permissions.*'],
        'children' => [
            [
                'title' => 'Users',
                'icon' => 'fas fa-users',
                'route' => 'users.index',
                'permission' => 'manage_users',
                'active' => 'users.*',
            ],
            [
                'title' => 'Roles',
                'icon' => 'fas fa-user-shield',
                'route' => 'roles.index',
                'permission' => 'manage_roles',
                'active' => 'roles.*',
            ],
            [
                'title' => 'Permissions',
                'icon' => 'fas fa-key',
                'route' => 'permissions.index',
                'permission' => 'manage_permissions',
                'active' => 'permissions.*',
            ],
        ],
    ],
    [
        'title' => 'Organisasi',
        'icon' => 'fas fa-building',
        'permission' => ['manage_organization_types', 'manage_organization_units'],
        'active' => ['organization-types.*', 'organization-units.*'],
        'children' => [
            [
                'title' => 'Tipe Organisasi',
                'icon' => 'fas fa-sitemap',
                'route' => 'organization-types.index',
                'permission' => 'manage_organization_types',
                'active' => 'organization-types.*',
            ],
            [
                'title' => 'Unit Organisasi',
                'icon' => 'fas fa-diagram-project',
                'route' => 'organization-units.index',
                'permission' => 'manage_organization_units',
                'active' => 'organization-units.*',
            ],
        ],
    ],
    [
        'title' => 'Tarif Umum',
        'icon' => 'fas fa-project-diagram',
        'permission' => 'manage_clinical_pathway',
        'active' => ['diagnoses.*'],
        'children' => [
            [
                'title' => 'Daftar Tarif Umum',
                'icon' => 'fas fa-list',
                'route' => 'diagnoses.index',
                'permission' => 'manage_clinical_pathway',
                'active' => 'diagnoses.*',
            ],
        ],
    ],
];
