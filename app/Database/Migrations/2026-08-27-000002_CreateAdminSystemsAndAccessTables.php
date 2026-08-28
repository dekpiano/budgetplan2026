<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateAdminSystemsAndAccessTables extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'admin_system_id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'admin_system_code' => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => false],
            'admin_system_name' => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => false],
            'admin_system_description' => ['type' => 'VARCHAR', 'constraint' => 500, 'null' => true],
            'admin_system_icon' => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true],
            'admin_system_url' => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'admin_system_level' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'default' => 1],
            'admin_system_active' => ['type' => 'TINYINT', 'constraint' => 1, 'default' => 1],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
            'updated_at' => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('admin_system_id', true);
        $this->forge->addUniqueKey('admin_system_code');
        $this->forge->createTable('tb_admin_systems', true);

        $this->forge->addField([
            'admin_access_id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'admin_access_userid' => ['type' => 'VARCHAR', 'constraint' => 50, 'null' => false],
            'admin_access_system_code' => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => false],
            'admin_access_view' => ['type' => 'TINYINT', 'constraint' => 1, 'default' => 1],
            'admin_access_create' => ['type' => 'TINYINT', 'constraint' => 1, 'default' => 0],
            'admin_access_update' => ['type' => 'TINYINT', 'constraint' => 1, 'default' => 0],
            'admin_access_delete' => ['type' => 'TINYINT', 'constraint' => 1, 'default' => 0],
            'admin_access_approve' => ['type' => 'TINYINT', 'constraint' => 1, 'default' => 0],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
            'updated_at' => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('admin_access_id', true);
        $this->forge->addKey('admin_access_userid');
        $this->forge->addKey('admin_access_system_code');
        $this->forge->addUniqueKey(['admin_access_userid', 'admin_access_system_code']);
        $this->forge->createTable('tb_admin_system_access', true);

        $now = date('Y-m-d H:i:s');
        $systems = [
            ['budget_ledger', 'ทะเบียนคุมเงินงบประมาณ', 'ทะเบียนคุมเงินงบประมาณ', 'bx-book-content', 'Admin/BudgetLedger', 1],
            ['registry_purchase', 'ทะเบียนคุมใบสั่งซื้อ', 'ทะเบียนคุมใบสั่งซื้อ', 'bx-cart-alt', 'Admin/Registry/Purchase', 2],
            ['registry_hire', 'ทะเบียนคุมใบสั่งจ้าง', 'ทะเบียนคุมใบสั่งจ้าง', 'bx-spreadsheet', 'Admin/Registry/Hire', 3],
            ['procurement', 'ขั้นตอนจัดซื้อ / จัดจ้าง', 'ขั้นตอนจัดซื้อและจัดจ้าง', 'bx-cart', 'User/Procurement/Process', 4],
            ['money_receipt', 'ใบสำคัญรับเงินตอบแทน', 'ใบสำคัญรับเงินตอบแทน', 'bx-file', 'User/Procurement/MoneyReceipt', 5],
            ['budget_structure', 'ตั้งค่ายุทธศาสตร์และโครงการ', 'ตั้งค่ายุทธศาสตร์และโครงการ', 'bx-sitemap', 'Admin/BudgetLedger/Structure', 6],
        ];

        foreach ($systems as $system) {
            $this->db->table('tb_admin_systems')->insert([
                'admin_system_code' => $system[0],
                'admin_system_name' => $system[1],
                'admin_system_description' => $system[2],
                'admin_system_icon' => $system[3],
                'admin_system_url' => $system[4],
                'admin_system_level' => $system[5],
                'admin_system_active' => $system[6],
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }

        // Migrate existing Role assignments into system access so current users do not lose access.
        if ($this->db->query("SHOW TABLES LIKE 'tb_admin_rloes'")->getNumRows() > 0) {
            $legacyRows = $this->db->table('tb_admin_rloes')
                ->select('admin_rloes_userid, admin_rloes_status')
                ->where('admin_rloes_userid !=', '')
                ->get()->getResult();

            $allSystemCodes = array_column($systems, 0);
            $roleSystemMap = [
                'planner' => ['budget_plan'],
                'finance' => ['budget_ledger'],
                'procurement' => ['procurement'],
                'director' => ['budget_home'],
                'manager' => $allSystemCodes,
                'admin' => $allSystemCodes,
                'superadmin' => $allSystemCodes,
            ];

            $legacyAccess = [];
            foreach ($legacyRows as $legacy) {
                $codes = $roleSystemMap[$legacy->admin_rloes_status] ?? [];
                foreach ($codes as $code) {
                    $legacyAccess[$legacy->admin_rloes_userid . '|' . $code] = [
                        'admin_access_userid' => $legacy->admin_rloes_userid,
                        'admin_access_system_code' => $code,
                        'admin_access_view' => 1,
                        'admin_access_create' => 1,
                        'admin_access_update' => 1,
                        'admin_access_delete' => 0,
                        'admin_access_approve' => 0,
                        'created_at' => $now,
                        'updated_at' => $now,
                    ];
                }
            }
            if (!empty($legacyAccess)) {
                $this->db->table('tb_admin_system_access')->insertBatch(array_values($legacyAccess));
            }
        }
    }

    public function down()
    {
        $this->forge->dropTable('tb_admin_system_access', true);
        $this->forge->dropTable('tb_admin_systems', true);
    }
}
