<?php
namespace App\Database\Migrations;
use CodeIgniter\Database\Migration;

class CreateNewsAndPublicDocumentsTables extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id'=>['type'=>'INT','constraint'=>11,'unsigned'=>true,'auto_increment'=>true],
            'title'=>['type'=>'VARCHAR','constraint'=>255],
            'content'=>['type'=>'TEXT','null'=>true],
            'category'=>['type'=>'VARCHAR','constraint'=>100,'default'=>'ประชาสัมพันธ์'],
            'cover_image'=>['type'=>'VARCHAR','constraint'=>255,'null'=>true],
            'published_at'=>['type'=>'DATETIME','null'=>true],
            'expires_at'=>['type'=>'DATETIME','null'=>true],
            'status'=>['type'=>'ENUM','constraint'=>['draft','published','closed'],'default'=>'draft'],
            'view_count'=>['type'=>'INT','constraint'=>11,'unsigned'=>true,'default'=>0],
            'created_by'=>['type'=>'VARCHAR','constraint'=>100,'null'=>true],
            'created_at'=>['type'=>'DATETIME','null'=>true],
            'updated_at'=>['type'=>'DATETIME','null'=>true],
        ]);
        $this->forge->addKey('id',true);
        $this->forge->addKey(['status','published_at']);
        $this->forge->createTable('tb_public_news',true);

        $this->forge->addField([
            'id'=>['type'=>'INT','constraint'=>11,'unsigned'=>true,'auto_increment'=>true],
            'news_id'=>['type'=>'INT','constraint'=>11,'unsigned'=>true],
            'original_name'=>['type'=>'VARCHAR','constraint'=>255],
            'stored_name'=>['type'=>'VARCHAR','constraint'=>255],
            'file_path'=>['type'=>'VARCHAR','constraint'=>500],
            'mime_type'=>['type'=>'VARCHAR','constraint'=>150,'null'=>true],
            'file_size'=>['type'=>'BIGINT','constraint'=>20,'unsigned'=>true,'default'=>0],
            'download_count'=>['type'=>'INT','constraint'=>11,'unsigned'=>true,'default'=>0],
            'created_at'=>['type'=>'DATETIME','null'=>true],
        ]);
        $this->forge->addKey('id',true);
        $this->forge->addKey('news_id');
        $this->forge->createTable('tb_public_news_files',true);
    }

    public function down()
    {
        $this->forge->dropTable('tb_public_news_files',true);
        $this->forge->dropTable('tb_public_news',true);
    }
}