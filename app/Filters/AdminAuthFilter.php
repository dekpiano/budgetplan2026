<?php
// app/Filters/AdminAuthFilter.php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

/**
 * AdminAuthFilter - Protects all Admin/* routes.
 *
 * Requires user to be logged in AND have one of:
 *   - admin
 *   - manager
 *   - superadmin
 *
 * Non-admin users are redirected to home with a flash error message.
 */
class AdminAuthFilter implements FilterInterface
{
    /**
     * Before filter: runs before the controller.
     */
    public function before(RequestInterface $request, $arguments = null)
    {
        $session = session();

        // Must be logged in AND have an admin role
        if (!$session->get('logged_in') || !in_array($session->get('status'), ['admin', 'manager', 'superadmin'], true)) {
            $session->setFlashdata('Error', 'คุณไม่มีสิทธิ์เข้าถึงหน้านี้ กรุณาติดต่อผู้ดูแลระบบ');
            return redirect()->to(base_url());
        }
    }

    /**
     * After filter: runs after the controller (no-op here).
     */
    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // No post-processing needed
    }
}
