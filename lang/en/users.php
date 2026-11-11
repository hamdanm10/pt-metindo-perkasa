<?php

declare(strict_types=1);

return [
    'kinds' => [
        'staff' => [
            'title' => 'Staff',
            'description' => 'Add staff, update their details, set new passwords, and turn access on or off.',
            'create_description' => 'Add a staff account with an initial password. The role follows this page.',
            'edit_description' => 'Update the details, set a new password, or turn access on or off for this account.',
            'create' => 'Add staff',
            'create_title' => 'Add staff',
            'edit_title' => 'Staff details',
            'empty' => 'No staff match.',
            'back' => 'Back to staff',
        ],
        'customer' => [
            'title' => 'Customers',
            'description' => 'Add customers, update their details, set new passwords, and turn access on or off.',
            'create_description' => 'Add a customer account with an initial password. The role follows this page.',
            'edit_description' => 'Update the details, set a new password, or turn access on or off for this account.',
            'create' => 'Add customer',
            'create_title' => 'Add customer',
            'edit_title' => 'Customer details',
            'empty' => 'No customers match.',
            'back' => 'Back to customers',
        ],
    ],
    'edit' => 'Edit',
    'search' => 'Search name or email',
    'all_statuses' => 'All statuses',
    'actions' => 'Actions',
    'save' => 'Save',
    'cancel' => 'Cancel',
    'fields' => [
        'name' => 'Name',
        'email' => 'Email',
        'status' => 'Status',
        'initial_password' => 'Initial password',
        'new_password' => 'New password',
    ],
    'status' => [
        'active' => 'Active',
        'inactive' => 'Inactive',
    ],
    'password_hint' => 'At least 8 characters with upper case, lower case and a number.',
    'reset_title' => 'Reset password',
    'reset_text' => 'Set a new password. The account is signed out everywhere.',
    'reset_password' => 'Set new password',
    'status_title' => 'Account status',
    'deactivate_text' => 'An inactive account cannot sign in and is signed out immediately.',
    'activate_text' => 'This account is inactive. Activate it to allow sign in again.',
    'deactivate' => 'Deactivate account',
    'activate' => 'Activate account',
    'confirm_deactivate' => 'Deactivate this account? It will be signed out immediately.',
    'messages' => [
        'created' => 'Account created.',
        'updated' => 'Account updated.',
        'password_reset' => 'Password updated.',
        'activated' => 'Account activated.',
        'deactivated' => 'Account deactivated.',
    ],
    'errors' => [
        'self_active' => 'You cannot deactivate your own account.',
        'last_admin_active' => 'The last active admin cannot be deactivated.',
    ],
];
