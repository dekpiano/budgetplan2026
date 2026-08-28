<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateBudgetProjectSubItemsTable extends Migration
{
    public function up()
    {
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
            'item_no' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
                'null'       => true,
                'comment'    => 'ลำดับข้อย่อย เช่น ๘.๑ หรือ 8.1',
            ],
            'item_name' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'comment'    => 'ชื่อกิจกรรม/ข้อย่อย เช่น ค่าอุปกรณ์การเรียน',
            ],
            'allocated_budget' => [
                'type'       => 'DECIMAL',
                'constraint' => '15,2',
                'default'    => 0.00,
                'comment'    => 'งบประมาณข้อย่อย',
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
        $this->forge->addForeignKey('project_id', 'tb_budget_projects', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('tb_budget_project_sub_items', true);
    }

    public function down()
    {
        $this->forge->dropTable('tb_budget_project_sub_items', true);
    }
}
