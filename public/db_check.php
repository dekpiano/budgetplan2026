<?php
define('FCPATH', __DIR__ . DIRECTORY_SEPARATOR);
chdir(FCPATH . '../');
require FCPATH . '../app/Config/Paths.php';
$paths = new Config\Paths();
require rtrim($paths->systemDirectory, '\\/ ') . DIRECTORY_SEPARATOR . 'bootstrap.php';

$db = \Config\Database::connect();
$check = $db->table('tb_admin_systems')->where('admin_system_code', 'budget_ordinance')->get()->getRow();
if (!$check) {
    $db->table('tb_admin_systems')->insert([
        'admin_system_code' => 'budget_ordinance',
        'admin_system_name' => 'ทะเบียนคุมงบประมาณเงินข้อบัญญัติ',
        'admin_system_description' => 'ทะเบียนคุมงบประมาณเงินข้อบัญญัติ (ค่าน้ำมัน, เบี้ยเลี้ยงเดินทาง, ค่าลงทะเบียน)',
        'admin_system_icon' => 'bx-book-bookmark',
        'admin_system_url' => 'Admin/BudgetOrdinance/Oil',
        'admin_system_level' => 2,
        'admin_system_active' => 1,
        'created_at' => date('Y-m-d H:i:s'),
        'updated_at' => date('Y-m-d H:i:s'),
    ]);
    echo "Inserted budget_ordinance\n";
} else {
    echo "Already exists: " . json_encode($check, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "\n";
}
$all = $db->table('tb_admin_systems')->get()->getResultArray();
echo json_encode($all, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);

