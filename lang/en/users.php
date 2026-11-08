<?php

declare(strict_types=1);

return [
    'title' => 'Accounts',
    'create' => 'Add account',
    'create_title' => 'Add account',
    'edit_title' => 'Account details',
    'edit' => 'Edit',
    'search' => 'Search name or email',
    'all_roles' => 'All roles',
    'all_statuses' => 'All statuses',
    'actions' => 'Actions',
    'empty' => 'No accounts match.',
    'save' => 'Save',
    'cancel' => 'Cancel',
    'back' => 'Back to accounts',
    'fields' => [
        'name' => 'Name',
        'email' => 'Email',
        'role' => 'Role',
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
        'self_role' => 'You cannot remove your own admin role.',
        'last_admin' => 'The last active admin cannot lose the admin role.',
        'self_active' => 'You cannot deactivate your own account.',
        'last_admin_active' => 'The last active admin cannot be deactivated.',
    ],
];
