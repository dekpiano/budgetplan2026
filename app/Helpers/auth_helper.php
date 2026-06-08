<?php
// app/Helpers/auth_helper.php

if (!function_exists('auth_user_role')) {
    /**
     * Get the current user's role from session.
     * Returns 'guest' if not logged in.
     */
    function auth_user_role(): string
    {
        return session()->get('status') ?: 'guest';
    }
}

if (!function_exists('auth_is_admin')) {
    /**
     * Check if current user has admin-level access.
     * Covers admin, manager, and superadmin roles.
     */
    function auth_is_admin(): bool
    {
        return in_array(auth_user_role(), ['admin', 'manager', 'superadmin'], true);
    }
}

if (!function_exists('auth_is_superadmin')) {
    /**
     * Check if current user is superadmin.
     * Use this for sensitive operations like role management.
     */
    function auth_is_superadmin(): bool
    {
        return auth_user_role() === 'superadmin';
    }
}
