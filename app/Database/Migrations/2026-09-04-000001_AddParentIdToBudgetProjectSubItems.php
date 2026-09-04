<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddParentIdToBudgetProjectSubItems extends Migration
{
    public function up()
    {
        $this->forge->addColumn('tb_budget_project_sub_items', [
            'parent_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => true,
                'default'    => null,
                'comment'    => 'รหัสกิจกรรมหลัก (แม่) สำหรับกิจกรรมย่อย',
                'after'      => 'project_id',
            ],
        ]);
        $this->forge->addKey('parent_id');
    }

    public function down()
    {
        $this->forge->dropColumn('tb_budget_project_sub_items', 'parent_id');
    }
}
