<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class AdminRloesSeeder extends Seeder
{
    public function run()
    {
        $data = [
            [
                'admin_rloes_userid'   => 'system',
                'admin_rloes_status'   => 'superadmin',
                'admin_rloes_nanetype' => 'ผู้ดูแลระบบสูงสุด (Super Admin)',
                'admin_rloes_level'    => 1,
                'created_at'           => date('Y-m-d H:i:s'),
                'updated_at'           => date('Y-m-d H:i:s'),
            ],
            [
                'admin_rloes_userid'   => 'system',
                'admin_rloes_status'   => 'admin',
                'admin_rloes_nanetype' => 'ผู้ดูแลระบบ (Admin)',
                'admin_rloes_level'    => 2,
                'created_at'           => date('Y-m-d H:i:s'),
                'updated_at'           => date('Y-m-d H:i:s'),
            ],
            [
                'admin_rloes_userid'   => 'system',
                'admin_rloes_status'   => 'manager',
                'admin_rloes_nanetype' => 'ผู้จัดการ (Manager)',
                'admin_rloes_level'    => 3,
                'created_at'           => date('Y-m-d H:i:s'),
                'updated_at'           => date('Y-m-d H:i:s'),
            ],
            [
                'admin_rloes_userid'   => 'system',
                'admin_rloes_status'   => 'Member',
                'admin_rloes_nanetype' => 'ผู้ใช้งานทั่วไป (Member)',
                'admin_rloes_level'    => 99,
                'created_at'           => date('Y-m-d H:i:s'),
                'updated_at'           => date('Y-m-d H:i:s'),
            ],
        ];

        // Use the default database (skjacth_budgetplan)
        $this->db->table('tb_admin_rloes')->insertBatch($data);
    }
}
