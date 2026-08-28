<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class RestrictBudgetPlanSystems extends Migration
{
    public function up()
    {
        $allowed = [
            'budget_ledger',
            'registry_purchase',
            'registry_hire',
            'procurement',
            'money_receipt',
            'budget_structure',
        ];

        $this->db->table('tb_admin_systems')
            ->whereNotIn('admin_system_code', $allowed)
            ->delete();
    }

    public function down()
    {
        // Intentionally empty: removed system definitions are not recreated automatically.
    }
}
