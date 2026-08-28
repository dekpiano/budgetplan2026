<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddBudgetPlanSystemDefinitions extends Migration
{
    public function up()
    {
        $rows = [
            ['budget_ledger', 'ทะเบียนคุมเงินงบประมาณ', 'ทะเบียนคุมเงินงบประมาณ', 'bx-book-content', 'Admin/BudgetLedger', 1],
            ['registry_purchase', 'ทะเบียนคุมใบสั่งซื้อ', 'ทะเบียนคุมใบสั่งซื้อ', 'bx-cart-alt', 'Admin/Registry/Purchase', 2],
            ['registry_hire', 'ทะเบียนคุมใบสั่งจ้าง', 'ทะเบียนคุมใบสั่งจ้าง', 'bx-spreadsheet', 'Admin/Registry/Hire', 3],
            ['procurement', 'ขั้นตอนจัดซื้อ / จัดจ้าง', 'ขั้นตอนจัดซื้อและจัดจ้าง', 'bx-cart', 'User/Procurement/Process', 4],
            ['money_receipt', 'ใบสำคัญรับเงินตอบแทน', 'ใบสำคัญรับเงินตอบแทน', 'bx-file', 'User/Procurement/MoneyReceipt', 5],
            ['budget_structure', 'ตั้งค่ายุทธศาสตร์และโครงการ', 'ตั้งค่ายุทธศาสตร์และโครงการ', 'bx-sitemap', 'Admin/BudgetLedger/Structure', 6],
        ];

        $table = $this->db->table('tb_admin_systems');
        foreach ($rows as $row) {
            $data = [
                'admin_system_code' => $row[0],
                'admin_system_name' => $row[1],
                'admin_system_description' => $row[2],
                'admin_system_icon' => $row[3],
                'admin_system_url' => $row[4],
                'admin_system_level' => $row[5],
                'admin_system_active' => 1,
            ];
            $existing = $table->where('admin_system_code', $row[0])->get()->getRowArray();
            if ($existing) {
                $table->where('admin_system_id', $existing['admin_system_id'])->update($data);
            } else {
                $table->insert($data);
            }
        }
    }

    public function down()
    {
        $this->db->table('tb_admin_systems')->whereIn('admin_system_code', [
            'budget_ledger', 'registry_purchase', 'registry_hire',
            'procurement', 'money_receipt', 'budget_structure'
        ])->delete();
    }
}
