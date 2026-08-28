<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddFieldsToStrategicHierarchy extends Migration
{
    public function up()
    {
        $this->forge->addColumn('tb_budget_tactics', [
            'objective' => [
                'type'    => 'TEXT',
                'null'    => true,
                'comment' => 'วัตถุประสงค์ (ข้อ ๆ)',
                'after'   => 'tactic_name',
            ],
            'actions' => [
                'type'    => 'TEXT',
                'null'    => true,
                'comment' => 'งานที่ทำ/แนวทางการดำเนินงาน (ข้อ ๆ)',
                'after'   => 'objective',
            ],
            'responsible_agency' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,
                'comment'    => 'หน่วยงานที่รับผิดชอบ',
                'after'      => 'actions',
            ],
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('tb_budget_tactics', ['objective', 'actions', 'responsible_agency']);
    }
}
