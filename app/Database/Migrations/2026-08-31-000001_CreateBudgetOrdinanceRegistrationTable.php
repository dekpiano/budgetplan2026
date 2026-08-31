<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateBudgetOrdinanceRegistrationTable extends Migration
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
            'budget_year' => [
                'type'       => 'VARCHAR',
                'constraint' => '10',
                'default'    => '2569',
            ],
            'doc_date' => [
                'type' => 'DATE',
                'null' => true,
            ],
            'dika_no' => [
                'type'       => 'VARCHAR',
                'constraint' => '100',
                'null'       => true,
            ],
            'doc_no' => [
                'type'       => 'VARCHAR',
                'constraint' => '100',
                'null'       => true,
            ],
            'order_no' => [
                'type'       => 'VARCHAR',
                'constraint' => '100',
                'null'       => true,
            ],
            'description' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'organizer' => [
                'type'       => 'VARCHAR',
                'constraint' => '255',
                'null'       => true,
            ],
            'location' => [
                'type'       => 'VARCHAR',
                'constraint' => '255',
                'null'       => true,
            ],
            'training_start_date' => [
                'type' => 'DATE',
                'null' => true,
            ],
            'training_end_date' => [
                'type' => 'DATE',
                'null' => true,
            ],
            'training_days' => [
                'type'       => 'INT',
                'constraint' => 5,
                'default'    => 1,
            ],
            'requester_name' => [
                'type'       => 'VARCHAR',
                'constraint' => '150',
                'null'       => true,
            ],
            'attendee_names' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'attendee_count' => [
                'type'       => 'INT',
                'constraint' => 5,
                'default'    => 1,
            ],
            'fee_per_person' => [
                'type'       => 'DECIMAL',
                'constraint' => '15,2',
                'default'    => 0.00,
            ],
            'amount_budget_adjust' => [
                'type'       => 'DECIMAL',
                'constraint' => '15,2',
                'default'    => 0.00,
            ],
            'amount_withdraw' => [
                'type'       => 'DECIMAL',
                'constraint' => '15,2',
                'default'    => 0.00,
            ],
            'balance' => [
                'type'       => 'DECIMAL',
                'constraint' => '15,2',
                'default'    => 0.00,
            ],
            'receipt_no' => [
                'type'       => 'VARCHAR',
                'constraint' => '100',
                'null'       => true,
            ],
            'notes' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'entry_type' => [
                'type'       => 'ENUM',
                'constraint' => ['adjust', 'withdraw'],
                'default'    => 'withdraw',
            ],
            'created_by' => [
                'type'       => 'VARCHAR',
                'constraint' => '100',
                'null'       => true,
            ],
            'updated_by' => [
                'type'       => 'VARCHAR',
                'constraint' => '100',
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
        $this->forge->addKey('budget_year');
        $this->forge->addKey('doc_date');
        $this->forge->createTable('tb_budget_ordinance_registration', true);
    }

    public function down()
    {
        $this->forge->dropTable('tb_budget_ordinance_registration', true);
    }
}
