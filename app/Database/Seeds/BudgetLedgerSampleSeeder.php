<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class BudgetLedgerSampleSeeder extends Seeder
{
    public function run()
    {
        $db = \Config\Database::connect();

        // Check if sample project exists
        $existing = $db->table('tb_budget_projects')->where('project_code', '3.1.7')->where('budget_year', '2569')->get()->getRow();
        
        $projectId = null;
        if (!$existing) {
            $db->table('tb_budget_projects')->insert([
                'budget_year'      => '2569',
                'strategy_name'    => 'ยุทธศาสตร์การพัฒนากระบวนการจัดการเรียนรู้ที่เน้นผู้เรียนเป็นสำคัญ',
                'strategy_sub'     => '3.1 กลยุทธ์การพัฒนา 1 คน 1 ความสามารถ ตามอัจฉริยภาพของผู้เรียน',
                'project_code'     => '3.1.7',
                'project_name'     => 'โครงการกิจกรรมพัฒนากลุ่มสาระการเรียนรู้สุขศึกษาและพลศึกษา',
                'budget_source'    => 'เงินงบประมาณรายจ่ายประจำปีงบประมาณ พ.ศ. 2569 (เงินพัฒนาการจัดการศึกษา - กิจกรรมพัฒนาผู้เรียน)',
                'allocated_budget' => 550000.00,
                'status'           => 'active',
                'created_at'       => date('Y-m-d H:i:s'),
                'updated_at'       => date('Y-m-d H:i:s'),
            ]);
            $projectId = $db->insertID();
        } else {
            $projectId = $existing->id;
        }

        // Add sample ledger items if empty
        $count = $db->table('tb_budget_ledger')->where('project_id', $projectId)->countAllResults();
        if ($count == 0) {
            $samples = [
                [
                    'project_id'             => $projectId,
                    'doc_date'               => null,
                    'dika_no'                => null,
                    'doc_no'                 => null,
                    'description'            => 'ตามงบประมาณรายจ่ายประจำปีงบประมาณ พ.ศ. 2569 (เงินพัฒนาการจัดการศึกษา - กิจกรรมพัฒนาผู้เรียน)',
                    'amount_budget_adjust'   => 550000.00,
                    'amount_borrow'          => 0.00,
                    'amount_return_cash'     => 0.00,
                    'amount_return_receipt'  => 0.00,
                    'amount_direct_withdraw' => 0.00,
                    'balance'                => 550000.00,
                    'created_at'             => date('Y-m-d H:i:s'),
                ],
                [
                    'project_id'             => $projectId,
                    'doc_date'               => '2025-11-14',
                    'dika_no'                => null,
                    'doc_no'                 => '15/2569',
                    'description'            => 'ค่าใช้จ่ายในการเดินทางไปราชการเพื่อเข้าร่วมการแข่งขันฟุตซอล U18 (7,12 พ.ย. 68)',
                    'amount_budget_adjust'   => 0.00,
                    'amount_borrow'          => 0.00,
                    'amount_return_cash'     => 0.00,
                    'amount_return_receipt'  => 0.00,
                    'amount_direct_withdraw' => 11040.00,
                    'balance'                => 538960.00,
                    'created_at'             => date('Y-m-d H:i:s'),
                ],
                [
                    'project_id'             => $projectId,
                    'doc_date'               => '2025-11-14',
                    'dika_no'                => null,
                    'doc_no'                 => '16/2569',
                    'description'            => 'เงินยืมทดรองจ่าย ค่าใช้จ่ายในการเดินทางไปราชการเพื่อเข้าร่วมการแข่งขันฟุตซอล U18 (18,20,26 พ.ย. 68)',
                    'amount_budget_adjust'   => 0.00,
                    'amount_borrow'          => 16560.00,
                    'amount_return_cash'     => 0.00,
                    'amount_return_receipt'  => 0.00,
                    'amount_direct_withdraw' => 0.00,
                    'balance'                => 522400.00,
                    'created_at'             => date('Y-m-d H:i:s'),
                ],
                [
                    'project_id'             => $projectId,
                    'doc_date'               => '2025-11-18',
                    'dika_no'                => null,
                    'doc_no'                 => '22/2569',
                    'description'            => "เงินยืมทดรองจ่าย ค่าใช้จ่ายในการเดินทางไปราชการเพื่อเข้าร่วมการแข่งขันกีฬาพานักเรียนฯ รอบคัดเลือก (28 พ.ย. - 8 ธ.ค. 68) จ.สุโขทัย",
                    'amount_budget_adjust'   => 0.00,
                    'amount_borrow'          => 84840.00,
                    'amount_return_cash'     => 0.00,
                    'amount_return_receipt'  => 0.00,
                    'amount_direct_withdraw' => 0.00,
                    'balance'                => 437560.00,
                    'created_at'             => date('Y-m-d H:i:s'),
                ],
                [
                    'project_id'             => $projectId,
                    'doc_date'               => '2025-12-08',
                    'dika_no'                => null,
                    'doc_no'                 => '54/2569',
                    'description'            => 'ส่งใช้เงินยืมทดรองจ่ายในการเดินทางไปราชการเพื่อเข้าร่วมการแข่งขันฟุตซอล U18',
                    'amount_budget_adjust'   => 0.00,
                    'amount_borrow'          => 0.00,
                    'amount_return_cash'     => 2160.00,
                    'amount_return_receipt'  => 14400.00,
                    'amount_direct_withdraw' => 0.00,
                    'balance'                => 439720.00,
                    'created_at'             => date('Y-m-d H:i:s'),
                ],
                [
                    'project_id'             => $projectId,
                    'doc_date'               => '2025-12-08',
                    'dika_no'                => null,
                    'doc_no'                 => '56/2569',
                    'description'            => 'ส่งใช้เงินยืมทดรองจ่าย ค่าใช้จ่ายในการเดินทางไปราชการเพื่อเข้าร่วมการแข่งขันกีฬาพานักเรียนฯ',
                    'amount_budget_adjust'   => 0.00,
                    'amount_borrow'          => 0.00,
                    'amount_return_cash'     => 19740.00,
                    'amount_return_receipt'  => 65100.00,
                    'amount_direct_withdraw' => 0.00,
                    'balance'                => 459460.00,
                    'created_at'             => date('Y-m-d H:i:s'),
                ],
                [
                    'project_id'             => $projectId,
                    'doc_date'               => '2025-12-09',
                    'dika_no'                => null,
                    'doc_no'                 => '61/2569',
                    'description'            => 'เงินยืมทดรองจ่ายค่าใช้จ่ายในการเข้าร่วมการแข่งขันกีฬาเยาวชนแห่งชาติ (รอบชิงชนะเลิศ) 14-19 ธ.ค. 68',
                    'amount_budget_adjust'   => 0.00,
                    'amount_borrow'          => 24480.00,
                    'amount_return_cash'     => 0.00,
                    'amount_return_receipt'  => 0.00,
                    'amount_direct_withdraw' => 0.00,
                    'balance'                => 434980.00,
                    'created_at'             => date('Y-m-d H:i:s'),
                ],
            ];

            foreach ($samples as $s) {
                $db->table('tb_budget_ledger')->insert($s);
            }
        }
    }
}
