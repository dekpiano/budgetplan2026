<?php
// app/Helpers/auth_helper.php

if (!function_exists('auth_user_roles')) {
    /**
     * Get all roles of the current user as an array.
     */
    function auth_user_roles(): array
    {
        $session = session();
        $roles = $session->get('roles');
        if (is_array($roles) && !empty($roles)) {
            return $roles;
        }
        $status = $session->get('status');
        return $status ? [$status] : ['guest'];
    }
}

if (!function_exists('auth_user_role')) {
    /**
     * Get the primary role of the current user.
     */
    function auth_user_role(): string
    {
        $roles = auth_user_roles();
        return $roles[0] ?? (session()->get('status') ?: 'guest');
    }
}

if (!function_exists('auth_has_role')) {
    /**
     * Check if user has ANY of the specified roles.
     * Superadmin and Admin automatically match any operational role.
     */
    function auth_has_role(...$allowedRoles): bool
    {
        $userRoles = auth_user_roles();
        if (in_array('superadmin', $userRoles, true) || in_array('admin', $userRoles, true)) {
            return true;
        }
        foreach ($allowedRoles as $role) {
            if (is_array($role)) {
                if (!empty(array_intersect($role, $userRoles))) {
                    return true;
                }
            } elseif (in_array($role, $userRoles, true)) {
                return true;
            }
        }
        return false;
    }
}

if (!function_exists('auth_is_admin')) {
    /**
     * Check if current user has any administrative access.
     * Covers all staff/officer roles.
     */
    function auth_is_admin(): bool
    {
        $roles = auth_user_roles();
        if (in_array('superadmin', $roles, true) || in_array('admin', $roles, true)) return true;

        $persId = session()->get('pers_id');
        if (!$persId) return false;

        try {
            $db = \Config\Database::connect();
            return (int)$db->table('tb_admin_system_access')
                ->where('admin_access_userid', $persId)
                ->where('admin_access_view', 1)
                ->countAllResults() > 0;
        } catch (\Throwable $e) {
            return in_array($roles[0] ?? '', ['director', 'planner', 'finance', 'procurement', 'manager'], true);
        }
    }
}

if (!function_exists('auth_is_superadmin')) {
    /**
     * Check if current user is superadmin.
     */
    function auth_is_superadmin(): bool
    {
        return in_array('superadmin', auth_user_roles(), true);
    }
}

if (!function_exists('auth_is_director')) {
    /**
     * Check if current user is Director / Executive.
     */
    function auth_is_director(): bool
    {
        return auth_has_role('director');
    }
}

if (!function_exists('auth_can_access_system')) {
    /**
     * Check whether the current user is allowed to access a system from the dynamic system catalog.
     * Super Admin and Admin always have access.
     */
    function auth_can_access_system(string $systemCode): bool
    {
        static $cache = [];
        $roles = auth_user_roles();
        if (in_array('superadmin', $roles, true) || in_array('admin', $roles, true)) return true;
        if (array_key_exists($systemCode, $cache)) return $cache[$systemCode];

        $persId = session()->get('pers_id');
        if (!$persId) return $cache[$systemCode] = false;

        try {
            $db = \Config\Database::connect();
            $row = $db->table('tb_admin_system_access')
                ->where('admin_access_userid', $persId)
                ->where('admin_access_system_code', $systemCode)
                ->where('admin_access_view', 1)
                ->get()->getRow();
            return $cache[$systemCode] = (bool)$row;
        } catch (\Throwable $e) {
            // Keep legacy role authorization working until the migration is installed.
            return $cache[$systemCode] = false;
        }
    }
}

if (!function_exists('auth_can_manage_plan')) {
    /**
     * Check if current user can manage strategic structure, tactics, and projects.
     * Allowed: superadmin, admin, planner.
     */
    function auth_can_manage_plan(): bool
    {
        return auth_can_access_system('budget_structure') || auth_can_access_system('budget_plan');
    }
}

if (!function_exists('auth_can_manage_finance')) {
    /**
     * Check if current user can manage budget ledger transactions (Add, Edit, Delete ledger items).
     * Allowed: superadmin, admin, finance.
     */
    function auth_can_manage_finance(): bool
    {
        return auth_can_access_system('budget_ledger');
    }
}

if (!function_exists('auth_can_manage_procurement')) {
    /**
     * Check if current user can manage purchase & hire order registry.
     * Allowed: superadmin, admin, procurement.
     */
    function auth_can_manage_procurement(): bool
    {
        return auth_can_access_system('procurement');
    }
}

