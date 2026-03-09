<?php

return [
    'actions' => [
        'edit'    => 'Edit',
        'delete'  => 'Delete',
        'restore' => 'Restore',
    ],
    'tooltips' => [
        'edit'    => 'Edit :model',
        'delete'  => 'Delete :model',
        'restore' => 'Restore :model',
    ],
    'confirm' => [
        'delete'  => 'Delete this :model?',
        'restore' => 'Restore this :model?',
    ],
    'dialog' => [
        'delete' => [
            'title'       => 'Delete :model?',
            'description' => 'This action cannot be undone.',
            'confirm'     => 'Yes, delete',
        ],
        'restore' => [
            'title'       => 'Restore :model?',
            'description' => 'The record will become active again.',
            'confirm'     => 'Yes, restore',
        ],
        'cancel' => 'Cancel',
    ],
    'new'       => 'New :model',
    'search'    => 'Search...',
    'filters'   => 'Filters',
    'active'    => 'active',
    'clear'     => 'Clear filters',
    'showing'   => 'Showing',
    'results'   => 'results of',
    'permission' => 'permission|permissions',

    // Users
    'users' => [
        'headers' => [
            'username'    => 'Username',
            'name'        => 'Name',
            'email'       => 'Email',
            'role'        => 'Role',
            'permissions' => 'Permissions',
            'status'      => 'Status',
            'actions'     => 'Actions',
        ],
        'filter_email' => 'Email',
        'filter_email_placeholder' => 'Filter by email...',
        'status_active'      => 'Active',
        'status_inactive'    => 'Deactivated',
        'status_deleted'     => 'Deleted',
    ],

    // Roles
    'roles' => [
        'headers' => [
            'name'        => 'Name',
            'identifier'  => 'Identifier',
            'permissions' => 'Permissions',
            'users'       => 'Users',
            'actions'     => 'Actions',
        ],
    ],
];
