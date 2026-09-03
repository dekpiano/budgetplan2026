<?php

namespace App\Controllers;

class ConUserHome extends BaseController
{  
    protected $db;

    public function __construct()
    {
        $this->db = \Config\Database::connect();
    }

    public function DataMain()
    {
        $data['full_url'] = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http') . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
        $data['uri'] = service('uri'); 
        return $data;
    }

    public function index()
    {
        $session = session();
        $data = $this->DataMain();
        $data['title'] = "หน้าแรก - ระบบบริหารงานงบประมาณและแผน";
        $data['description'] = "ระบบบริหารงานงบประมาณและแผน โรงเรียนสวนกุหลาบวิทยาลัย (จิรประวัติ) นครสวรรค์";
        $data['UrlMenuMain'] = 'Main';
        $data['UrlMenuSub'] = '';

        return view('User/UserLeyout/UserHeader', $data)
            . view('User/UserLeyout/UserMenuLeft', $data)
            . view('User/UserHome/UserPageHome', $data)
            . view('User/UserLeyout/UserFooter', $data);
    }

    /**
     * หน้าแสดงสรุปโครงการและงบประมาณ (สำหรับครูและบุคลากรทั่วไป)
     */
    public function BudgetSummary()
    {
        $session = session();
        $data = $this->DataMain();
        $data['title'] = "สรุปโครงการและงบประมาณ - ระบบบริหารงานงบประมาณและแผน";
        $data['description'] = "ตรวจสอบรายละเอียดงบประมาณและสถานะโครงการ โรงเรียนสวนกุหลาบวิทยาลัย (จิรประวัติ) นครสวรรค์";
        $data['UrlMenuMain'] = 'BudgetLedger';
        $data['UrlMenuSub'] = 'BudgetSummary';

        $selectedYear = $this->request->getGet('year') ?? '2569';
        $data['selected_year'] = $selectedYear;

        // ปีงบประมาณทั้งหมด
        $yearsQuery = $this->db->query("SELECT DISTINCT budget_year FROM tb_budget_projects UNION SELECT DISTINCT budget_year FROM tb_budget_strategies ORDER BY budget_year DESC")->getResult();
        $data['years'] = !empty($yearsQuery) ? $yearsQuery : [(object)['budget_year' => '2569']];

        // ดึงโครงการทั้งหมดในปีที่เลือก
        $projects = $this->db->table('tb_budget_projects')
            ->where('budget_year', $selectedYear)
            ->orderBy('project_code', 'ASC')
            ->get()
            ->getResult();
        sort_projects_naturally($projects, 'project_code');

        $totalAllocated = 0;
        $totalBalance   = 0;
        $totalPaid      = 0;

        foreach ($projects as &$p) {
            $lastItem = $this->db->table('tb_budget_ledger')
                ->where('project_id', $p->id)
                ->orderBy('doc_date', 'DESC')
                ->orderBy('id', 'DESC')
                ->get()
                ->getRow();

            $p->current_balance = $lastItem ? (float)$lastItem->balance : (float)$p->allocated_budget;

            $stats = $this->db->query("
                SELECT 
                    SUM(amount_budget_adjust) AS total_adjust,
                    SUM(amount_borrow) AS total_borrow,
                    SUM(amount_return_cash) AS total_return_cash,
                    SUM(amount_return_receipt) AS total_return_receipt,
                    SUM(amount_direct_withdraw) AS total_direct
                FROM tb_budget_ledger 
                WHERE project_id = ?
            ", [$p->id])->getRow();

            $totalSpent = $stats ? ((float)$stats->total_return_receipt + (float)$stats->total_direct) : 0;
            $p->total_paid = $totalSpent;
            $p->used_percent = $p->allocated_budget > 0 ? min(100, round(($p->total_paid / $p->allocated_budget) * 100, 1)) : 0;

            $totalAllocated += (float)$p->allocated_budget;
            $totalBalance   += (float)$p->current_balance;
            $totalPaid      += (float)$p->total_paid;
        }

        $data['projects']        = $projects;
        $data['total_projects']  = count($projects);
        $data['total_allocated'] = $totalAllocated;
        $data['total_balance']   = $totalBalance;
        $data['total_paid']      = $totalPaid;
        $data['used_percent']    = $totalAllocated > 0 ? min(100, round(($totalPaid / $totalAllocated) * 100, 1)) : 0;

        // ดึงยุทธศาสตร์
        $strategies = $this->db->table('tb_budget_strategies')
            ->where('budget_year', $selectedYear)
            ->orderBy('strategy_no', 'ASC')
            ->get()
            ->getResult();
        $data['strategies'] = $strategies;

        return view('User/UserLeyout/UserHeader', $data)
            . view('User/UserLeyout/UserMenuLeft', $data)
            . view('User/UserHome/UserBudgetSummary', $data)
            . view('User/UserLeyout/UserFooter', $data);
    }

    /**
     * ดึงรายการบัญชีในทะเบียนคุมของโครงการ (AJAX)
     */
    public function getProjectLedger($projectId)
    {
        $project = $this->db->table('tb_budget_projects')->where('id', $projectId)->get()->getRow();
        if (!$project) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'ไม่พบข้อมูลโครงการ']);
        }

        $ledgerItems = $this->db->table('tb_budget_ledger')
            ->where('project_id', $projectId)
            ->orderBy('doc_date', 'ASC')
            ->orderBy('id', 'ASC')
            ->get()
            ->getResult();

        return $this->response->setJSON([
            'status'  => 'success',
            'project' => $project,
            'ledger'  => $ledgerItems
        ]);
    }
}

