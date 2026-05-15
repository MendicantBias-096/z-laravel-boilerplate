<?php

return [

    // Dashboard
    'dashboard' => 'Dashboard',
    'welcome' => 'Welcome, :name',
    'card_role' => 'Role',
    'card_permissions' => 'Permissions',
    'card_environment' => 'Environment',

    // User form
    'user_photo' => 'Profile photo',
    'user_photo_preview' => 'Preview',
    'user_first_name' => 'First name',
    'user_first_name_ph' => 'e.g. John',
    'user_last_name' => 'Last name',
    'user_last_name_ph' => 'e.g. Smith',
    'user_username' => 'Username',
    'user_username_ph' => 'e.g. john_smith',
    'user_email' => 'Email',
    'user_email_ph' => 'email@example.com',
    'user_password' => 'Password',
    'user_new_password' => 'New password',
    'user_password_ph' => 'Minimum 8 characters',
    'user_password_hint' => 'Leave empty to keep current password',
    'user_confirm' => 'Confirm password',
    'user_confirm_ph' => 'Repeat password',
    'user_role' => 'Role',
    'user_role_ph' => 'No role assigned',
    'user_role_hint_new' => 'Selecting a role loads its permissions automatically.',
    'user_role_hint_edit' => 'Changing the role will replace current permissions.',
    'user_permissions' => 'User permissions',
    'user_permissions_desc' => 'Customize access for this user.',
    'user_restore_perms' => 'Restore original permissions',
    'user_btn_create' => 'Create user',
    'user_btn_update' => 'Update',

    // Role form
    'role_name' => 'Role name',
    'role_name_ph' => 'e.g. Warehouse Manager',
    'role_name_hint' => 'Enter the name as it will appear in the interface.',
    'role_identifier' => 'Identifier',
    'role_permissions' => 'Role permissions',
    'role_permissions_desc' => 'Select the access this role will have.',
    'role_btn_save' => 'Save',

    // User toasts
    'user_perms_restored' => 'Permissions restored',
    'user_perms_restored_desc' => 'Original user permissions have been restored.',
    'user_perms_loaded' => 'Permissions loaded',
    'user_perms_loaded_desc' => 'Loaded permissions from ":name".',
    'user_saved' => 'Success',
    'user_created_desc' => 'User created successfully.',
    'user_updated_desc' => 'User updated successfully.',

    // Common actions
    'cancel' => 'Cancel',
    'success' => 'Success',
    'soft_deleted' => ':model deleted successfully.',
    'restored' => ':model restored successfully.',
    'user_deactivated' => 'User deactivated successfully.',
    'user_activated' => 'User activated successfully.',
    'user_status' => 'Account status',
    'user_status_active' => 'Active',
    'user_status_hint' => 'A deactivated user will not be able to log in.',

    // Role dialogs
    'role_delete_title' => 'Delete role?',
    'role_delete_desc' => 'The role ":name" will be permanently deleted.',
    'role_delete_confirm' => 'Delete',
    'role_delete_cancel' => 'Cancel',
    'role_delete_error' => 'Cannot delete',
    'role_delete_has_users' => 'The role ":name" has assigned users.',
    'role_deleted' => 'Role deleted',
    'role_deleted_desc' => 'The role was deleted successfully.',

];
