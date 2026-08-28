<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateStrategicHierarchyTables extends Migration
{
    public function up()
    {
        // 1. Table: tb_budget_strategies (ระดับที่ 1: ยุทธศาสตร์ เช่น 1, 2, 3)
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
            'strategy_no' => [
                'type'       => 'VARCHAR',
                'constraint' => 20,
                'comment'    => 'ลำดับยุทธศาสตร์ เช่น 1, 2, 3',
            ],
            'strategy_name' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'comment'    => 'ชื่อยุทธศาสตร์ เช่น ยุทธศาสตร์การพัฒนากระบวนการจัดการเรียนรู้ที่เน้นผู้เรียนเป็นสำคัญ',
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
        $this->forge->addKey(['budget_year', 'strategy_no']);
        $this->forge->createTable('tb_budget_strategies', true);

        // 2. Table: tb_budget_tactics (ระดับที่ 2: กลยุทธ์ เช่น 1.1, 1.2, 3.1)
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'strategy_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'tactic_no' => [
                'type'       => 'VARCHAR',
                'constraint' => 20,
                'comment'    => 'ลำดับกลยุทธ์ เช่น 1.1, 3.1',
            ],
            'tactic_name' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'comment'    => 'ชื่อกลยุทธ์ เช่น กลยุทธ์การพัฒนา 1 คน 1 ความสามารถ ตามอัจฉริยภาพของผู้เรียน',
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
        $this->forge->addKey('strategy_id');
        $this->forge->addForeignKey('strategy_id', 'tb_budget_strategies', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('tb_budget_tactics', true);

        // Add tactic_id and strategy_id to tb_budget_projects if not exists
        $db = \Config\Database::connect();
        $fields = $db->getFieldNames('tb_budget_projects');
        if (!in_array('tactic_id', $fields)) {
            $this->forge->addColumn('tb_budget_projects', [
                'strategy_id' => [
                    'type'       => 'INT',
                    'constraint' => 11,
                    'unsigned'   => true,
                    'null'       => true,
                    'after'      => 'budget_year',
                ],
                'tactic_id' => [
                    'type'       => 'INT',
                    'constraint' => 11,
                    'unsigned'   => true,
                    'null'       => true,
                    'after'      => 'strategy_id',
                ],
            ]);
        }
    }

    public function down()
    {
        $this->forge->dropTable('tb_budget_tactics', true);
        $this->forge->dropTable('tb_budget_strategies', true);
    }
}
