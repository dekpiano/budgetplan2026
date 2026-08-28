<?php

namespace App\Controllers;

class ConUserPlan extends BaseController
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
        $data = $this->DataMain();
        $data['title'] = "โครงสร้างแผนยุทธศาสตร์และโครงการ";
        $data['description'] = "เรียนรู้และศึกษาโครงสร้างยุทธศาสตร์ กลยุทธ์ โครงการ และงบประมาณ โรงเรียนสวนกุหลาบวิทยาลัย (จิรประวัติ) นครสวรรค์";
        
        $data['UrlMenuMain'] = 'Plan';
        $data['UrlMenuSub'] = 'Plan';

        $selectedYear = $this->request->getGet('year') ?? '2569';
        $data['selected_year'] = $selectedYear;

        // ปีงบประมาณทั้งหมด
        $yearsQuery = $this->db->query("SELECT DISTINCT budget_year FROM tb_budget_projects UNION SELECT DISTINCT budget_year FROM tb_budget_strategies ORDER BY budget_year DESC")->getResult();
        $data['years'] = !empty($yearsQuery) ? $yearsQuery : [(object)['budget_year' => '2569']];

        // ดึงยุทธศาสตร์ (ระดับ 1)
        $strategies = $this->db->table('tb_budget_strategies')
            ->where('budget_year', $selectedYear)
            ->orderBy('strategy_no', 'ASC')
            ->get()
            ->getResult();

        // ดึงโครงการทั้งหมด พร้อมรายการย่อย
        $projects = $this->db->table('tb_budget_projects')
            ->where('budget_year', $selectedYear)
            ->orderBy('project_code', 'ASC')
            ->get()
            ->getResult();

        foreach ($projects as &$pr) {
            $pr->sub_items = $this->db->table('tb_budget_project_sub_items')
                ->where('project_id', $pr->id)
                ->orderBy('id', 'ASC')
                ->get()
                ->getResult();
        }

        $totalAllocated = 0.0;
        $totalProjectsCount = count($projects);

        // เชื่อมโยง Hierarchy Tree: ยุทธศาสตร์ -> กลยุทธ์ -> โครงการ
        foreach ($strategies as &$st) {
            $tactics = $this->db->table('tb_budget_tactics')
                ->where('strategy_id', $st->id)
                ->orderBy('tactic_no', 'ASC')
                ->get()
                ->getResult();

            $stTotalBudget = 0.0;
            $stProjectCount = 0;

            foreach ($tactics as &$tc) {
                $tcProjects = array_filter($projects, function ($pr) use ($tc, $st) {
                    return $pr->tactic_id == $tc->id || (empty($pr->tactic_id) && $pr->strategy_name == $st->strategy_name && strpos($pr->strategy_sub, $tc->tactic_no) !== false);
                });

                $tcBudget = 0.0;
                foreach ($tcProjects as $p) {
                    $tcBudget += (float)$p->allocated_budget;
                }

                $tc->projects = array_values($tcProjects);
                $tc->total_budget = $tcBudget;
                $tc->project_count = count($tcProjects);

                $stTotalBudget += $tcBudget;
                $stProjectCount += count($tcProjects);
            }

            $st->tactics = $tactics;
            $st->total_budget = $stTotalBudget;
            $st->project_count = $stProjectCount;
            $totalAllocated += $stTotalBudget;
        }

        $data['strategies'] = $strategies;
        $data['total_allocated'] = $totalAllocated;
        $data['total_strategies'] = count($strategies);
        $data['total_projects'] = $totalProjectsCount;

        // ดึงไฟล์เอกสารแผนปฏิบัติการ
        $planDir = ROOTPATH . 'uploads/plan';
        $planFiles = [];
        if (is_dir($planDir)) {
            $files = scandir($planDir);
            foreach ($files as $file) {
                if ($file !== '.' && $file !== '..') {
                    $planFiles[] = $file;
                }
            }
        }
        $data['plan_files'] = $planFiles;

        return view('User/UserLeyout/UserHeader', $data)
            . view('User/UserLeyout/UserMenuLeft', $data)
            . view('User/UserPlan/UserPlanPage', $data)
            . view('User/UserLeyout/UserFooter', $data);
    }
}
