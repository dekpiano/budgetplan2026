<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class StrategicHierarchySeeder extends Seeder
{
    public function run()
    {
        $db = \Config\Database::connect();

        // 1. เพิ่มยุทธศาสตร์ที่ 3
        $st = $db->table('tb_budget_strategies')->where('budget_year', '2569')->where('strategy_no', '3')->get()->getRow();
        $strategyId = null;
        if (!$st) {
            $db->table('tb_budget_strategies')->insert([
                'budget_year'   => '2569',
                'strategy_no'   => '3',
                'strategy_name' => 'ยุทธศาสตร์การพัฒนากระบวนการจัดการเรียนรู้ที่เน้นผู้เรียนเป็นสำคัญ',
                'created_at'    => date('Y-m-d H:i:s'),
            ]);
            $strategyId = $db->insertID();
        } else {
            $strategyId = $st->id;
        }

        // 2. เพิ่มกลยุทธ์ที่ 3.1
        $tc = $db->table('tb_budget_tactics')->where('strategy_id', $strategyId)->where('tactic_no', '3.1')->get()->getRow();
        $tacticId = null;
        if (!$tc) {
            $db->table('tb_budget_tactics')->insert([
                'strategy_id' => $strategyId,
                'tactic_no'   => '3.1',
                'tactic_name' => 'กลยุทธ์การพัฒนา 1 คน 1 ความสามารถ ตามอัจฉริยภาพของผู้เรียน',
                'created_at'  => date('Y-m-d H:i:s'),
            ]);
            $tacticId = $db->insertID();
        } else {
            $tacticId = $tc->id;
        }

        // 3. ผูกโครงการ 3.1.7 เข้ากับ strategy_id และ tactic_id
        $db->table('tb_budget_projects')
            ->where('budget_year', '2569')
            ->where('project_code', '3.1.7')
            ->update([
                'strategy_id' => $strategyId,
                'tactic_id'   => $tacticId
            ]);
    }
}
