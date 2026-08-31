<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddEntryTypeToOilTable extends Migration
{
    public function up()
    {
        $fields = [
            'entry_type' => [
                'type'       => 'ENUM',
                'constraint' => ['adjust', 'withdraw'],
                'default'    => 'withdraw',
                'after'      => 'notes',
            ],
            'updated_by' => [
                'type'       => 'VARCHAR',
                'constraint' => '100',
                'null'       => true,
                'after'      => 'created_by',
            ],
        ];

        // ตรวจสอบก่อนเพิ่มคอลัมน์
        if (!$this->db->fieldExists('entry_type', 'tb_budget_ordinance_oil')) {
            $this->forge->addColumn('tb_budget_ordinance_oil', $fields);
        }
    }

    public function down()
    {
        if ($this->db->fieldExists('entry_type', 'tb_budget_ordinance_oil')) {
            $this->forge->dropColumn('tb_budget_ordinance_oil', 'entry_type');
        }
        if ($this->db->fieldExists('updated_by', 'tb_budget_ordinance_oil')) {
            $this->forge->dropColumn('tb_budget_ordinance_oil', 'updated_by');
        }
    }
}
