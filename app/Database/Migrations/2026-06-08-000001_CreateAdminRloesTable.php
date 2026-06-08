<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateAdminRloesTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'admin_rloes_id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'admin_rloes_userid' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
                'null'       => false,
                'comment'    => 'Foreign key reference to tb_personnel.pers_id',
            ],
            'admin_rloes_status' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
                'null'       => false,
                'default'    => 'Member',
                'comment'    => 'Role status: admin, manager, superadmin, Member, etc.',
            ],
            'admin_rloes_nanetype' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,
                'comment'    => 'Human-readable role name/description',
            ],
            'admin_rloes_level' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => false,
                'default'    => 0,
                'comment'    => 'Hierarchical level for ordering roles (lower = higher priority)',
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

        $this->forge->addKey('admin_rloes_id', true);
        $this->forge->addKey('admin_rloes_userid');
        $this->forge->addKey('admin_rloes_status');

        $this->forge->createTable('tb_admin_rloes');
    }

    public function down()
    {
        $this->forge->dropTable('tb_admin_rloes');
    }
}
