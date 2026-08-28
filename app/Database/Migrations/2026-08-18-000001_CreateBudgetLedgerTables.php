<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateBudgetLedgerTables extends Migration
{
    public function up()
    {
        // 1. Table: tb_budget_projects
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'budget_year' => [
                'type'       => 'VARCHAR',
                'constraint' => 10,
                'comment'    => 'ปีงบประมาณ เช่น 2569',
            ],
            'strategy_name' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,
                'comment'    => 'ยุทธศาสตร์ เช่น ยุทธศาสตร์การพัฒนากระบวนการจัดการเรียนรู้...',
            ],
            'strategy_sub' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,
                'comment'    => 'กลยุทธ์ เช่น กลยุทธ์การพัฒนา 1 คน 1 ความสามารถ...',
            ],
            'project_code' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
                'null'       => true,
                'comment'    => 'รหัสหมวด/โครงการ เช่น 3.1.7',
            ],
            'project_name' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'comment'    => 'ชื่อโครงการ เช่น โครงการกิจกรรมพัฒนากลุ่มสาระการเรียนรู้สุขศึกษาและพลศึกษา',
            ],
            'budget_source' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,
                'comment'    => 'แหล่งงบประมาณ เช่น เงินงบประมาณรายจ่ายประจำปี พ.ศ. 2569 (เงินพัฒนาการจัดการศึกษา - กิจกรรมพัฒนาผู้เรียน)',
            ],
            'allocated_budget' => [
                'type'       => 'DECIMAL',
                'constraint' => '15,2',
                'default'    => 0.00,
                'comment'    => 'ยอดงบประมาณตั้งต้น',
            ],
            'status' => [
                'type'       => 'ENUM',
                'constraint' => ['active', 'closed'],
                'default'    => 'active',
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addKey('budget_year');
        $this->forge->createTable('tb_budget_projects', true);

        // 2. Table: tb_budget_ledger
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'project_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'doc_date' => [
                'type' => 'DATE',
                'null' => true,
                'comment' => 'วัน/เดือน/ปี',
            ],
            'dika_no' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'null'       => true,
                'comment'    => 'ที่วางฎีกา',
            ],
            'doc_no' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'null'       => true,
                'comment'    => 'ที่เอกสาร เช่น 15/2569',
            ],
            'description' => [
                'type' => 'TEXT',
                'null' => true,
                'comment' => 'รายการ',
            ],
            'amount_budget_adjust' => [
                'type'       => 'DECIMAL',
                'constraint' => '15,2',
                'default'    => 0.00,
                'comment'    => 'เพิ่ม/ลด',
            ],
            'amount_borrow' => [
                'type'       => 'DECIMAL',
                'constraint' => '15,2',
                'default'    => 0.00,
                'comment'    => 'ยืมเงิน',
            ],
            'amount_return_cash' => [
                'type'       => 'DECIMAL',
                'constraint' => '15,2',
                'default'    => 0.00,
                'comment'    => 'ส่งใช้เงินยืม: ส่งใช้เงินสด',
            ],
            'amount_return_receipt' => [
                'type'       => 'DECIMAL',
                'constraint' => '15,2',
                'default'    => 0.00,
                'comment'    => 'ส่งใช้เงินยืม: ส่งใช้ใบสำคัญ',
            ],
            'amount_direct_withdraw' => [
                'type'       => 'DECIMAL',
                'constraint' => '15,2',
                'default'    => 0.00,
                'comment'    => 'เบิก (เบิกตรง/จ่ายตรง)',
            ],
            'balance' => [
                'type'       => 'DECIMAL',
                'constraint' => '15,2',
                'default'    => 0.00,
                'comment'    => 'คงเหลือ',
            ],
            'notes' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,
                'comment'    => 'หมายเหตุ',
            ],
            'created_by' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
                'null'       => true,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addKey('project_id');
        $this->forge->addKey('doc_date');
        $this->forge->addForeignKey('project_id', 'tb_budget_projects', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('tb_budget_ledger', true);
    }

    public function down()
    {
        $this->forge->dropTable('tb_budget_ledger', true);
        $this->forge->dropTable('tb_budget_projects', true);
    }
}
