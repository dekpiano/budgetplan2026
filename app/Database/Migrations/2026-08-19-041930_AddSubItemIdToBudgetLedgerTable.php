<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddSubItemIdToBudgetLedgerTable extends Migration
{
    public function up()
    {
        $this->forge->addColumn('tb_budget_ledger', [
            'sub_item_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => true,
                'comment'    => 'รหัสรายการข้อย่อย (tb_budget_project_sub_items)',
                'after'      => 'project_id',
            ],
        ]);
        $this->forge->addKey('sub_item_id');
    }

    public function down()
    {
        $this->forge->dropColumn('tb_budget_ledger', 'sub_item_id');
    }
}
