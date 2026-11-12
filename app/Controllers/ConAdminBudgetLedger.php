<?php

namespace App\Controllers;

use CodeIgniter\Controller;

class ConAdminBudgetLedger extends BaseController
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

    /**
     * หน้า 1: ทะเบียนคุมเงินงบประมาณรายจ่าย (Dashboard & Ledger Overview)
     */
    public function index()
    {
        if (!auth_can_access_system('budget_ledger')) {
            return redirect()->to(base_url('Admin/Home'))->with('error', 'คุณไม่มีสิทธิ์เข้าถึงระบบทะเบียนคุมงบประมาณ');
        }

        $data = $this->DataMain();
        $data['title'] = "ทะเบียนคุมเงินงบประมาณรายจ่าย";

        $selectedYear = $this->request->getGet('year') ?? '2569';
        $data['selected_year'] = $selectedYear;

        // ดึงรายชื่อปีงบประมาณทั้งหมด
        $yearsQuery = $this->db->query("SELECT DISTINCT budget_year FROM tb_budget_projects UNION SELECT DISTINCT budget_year FROM tb_budget_strategies ORDER BY budget_year DESC")->getResult();
        $data['years'] = !empty($yearsQuery) ? $yearsQuery : [(object)['budget_year' => '2569']];

        // ดึงโครงการทั้งหมดในปีที่เลือก พร้อมคำนวณยอดคงเหลือ
        $projects = $this->db->table('tb_budget_projects')
            ->where('budget_year', $selectedYear)
            ->orderBy('project_code', 'ASC')
            ->get()
            ->getResult();
        sort_projects_naturally($projects, 'project_code');

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

            $p->total_borrow = $stats ? (float)$stats->total_borrow : 0;
            $p->total_spent = $stats ? ((float)$stats->total_return_receipt + (float)$stats->total_direct) : 0;
            $p->item_count = $this->db->table('tb_budget_ledger')->where('project_id', $p->id)->countAllResults();
        }

        $data['projects'] = $projects;

        return view('Admin/AdminLeyout/AdminHeader', $data)
            . view('Admin/AdminLeyout/AdminMenuLeft', $data)
            . view('Admin/AdminBudgetLedger/ProjectList', $data)
            . view('Admin/AdminLeyout/AdminFooter', $data);
    }

    /**
     * หน้า 2: ตั้งค่าโครงสร้างยุทธศาสตร์ กลยุทธ์ และโครงการ (Structure Management)
     */
    public function structure()
    {
        helper('auth');
        if (!auth_can_manage_plan()) {
            return redirect()->to(base_url('Admin/Home'))->with('error', 'คุณไม่มีสิทธิ์เข้าถึงการตั้งค่าโครงสร้างยุทธศาสตร์และโครงการ');
        }

        $data = $this->DataMain();
        $data['title'] = "ตั้งค่าโครงสร้างยุทธศาสตร์ กลยุทธ์ และโครงการ";

        $selectedYear = $this->request->getGet('year') ?? '2569';
        $data['selected_year'] = $selectedYear;

        // รายชื่อปีงบประมาณ
        $yearsQuery = $this->db->query("SELECT DISTINCT budget_year FROM tb_budget_projects UNION SELECT DISTINCT budget_year FROM tb_budget_strategies ORDER BY budget_year DESC")->getResult();
        $data['years'] = !empty($yearsQuery) ? $yearsQuery : [(object)['budget_year' => '2569']];

        // ดึงยุทธศาสตร์ (ระดับ 1)
        $strategies = $this->db->table('tb_budget_strategies')
            ->where('budget_year', $selectedYear)
            ->orderBy('strategy_no', 'ASC')
            ->get()
            ->getResult();
        sort_projects_naturally($strategies, 'strategy_no');

        // ดึงโครงการทั้งหมดในปีที่เลือก พร้อมรายการย่อย (Sub-items)
        $projects = $this->db->table('tb_budget_projects')
            ->where('budget_year', $selectedYear)
            ->orderBy('project_code', 'ASC')
            ->get()
            ->getResult();
        sort_projects_naturally($projects, 'project_code');

        $this->ensureSubItemParentColumn();

        foreach ($projects as &$pr) {
            $subItems = $this->db->table('tb_budget_project_sub_items')
                ->where('project_id', $pr->id)
                ->orderBy('id', 'ASC')
                ->get()
                ->getResult();
            $pr->sub_items = build_sub_items_tree($subItems);
        }

        // เชื่อมโยง Hierarchy Tree
        foreach ($strategies as &$st) {
            $tactics = $this->db->table('tb_budget_tactics')
                ->where('strategy_id', $st->id)
                ->orderBy('tactic_no', 'ASC')
                ->get()
                ->getResult();
            sort_projects_naturally($tactics, 'tactic_no');

            foreach ($tactics as &$tc) {
                $tcProjects = array_filter($projects, function ($pr) use ($tc, $st) {
                    return $pr->tactic_id == $tc->id || (empty($pr->tactic_id) && $pr->strategy_name == $st->strategy_name && strpos($pr->strategy_sub, $tc->tactic_no) !== false);
                });
                $tcProjects = array_values($tcProjects);
                sort_projects_naturally($tcProjects, 'project_code');
                $tc->projects = $tcProjects;
            }
            $st->tactics = $tactics;
        }

        $data['strategies'] = $strategies;
        $data['projects'] = $projects;

        return view('Admin/AdminLeyout/AdminHeader', $data)
            . view('Admin/AdminLeyout/AdminMenuLeft', $data)
            . view('Admin/AdminBudgetLedger/StructureSetting', $data)
            . view('Admin/AdminLeyout/AdminFooter', $data);
    }

    /**
     * API: ดึงกลยุทธ์ย่อยตาม Strategy ID (สำหรับ Dropdown Cascading)
     */
    public function getTacticsByStrategy($strategyId)
    {
        $tactics = $this->db->table('tb_budget_tactics')
            ->where('strategy_id', $strategyId)
            ->orderBy('tactic_no', 'ASC')
            ->get()
            ->getResult();
        sort_projects_naturally($tactics, 'tactic_no');

        return $this->response->setJSON($tactics);
    }

    /**
     * บันทึก / แก้ไข ยุทธศาสตร์ (ระดับ 1)
     */
    public function saveStrategy()
    {
        if (!auth_can_manage_plan()) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'คุณไม่มีสิทธิ์จัดการข้อมูลยุทธศาสตร์']);
        }

        $id = $this->request->getPost('id');
        $data = [
            'budget_year'   => trim($this->request->getPost('budget_year') ?? ''),
            'strategy_no'   => trim($this->request->getPost('strategy_no') ?? ''),
            'strategy_name' => trim($this->request->getPost('strategy_name') ?? ''),
            'updated_at'    => date('Y-m-d H:i:s'),
        ];

        if (empty($id)) {
            $data['created_at'] = date('Y-m-d H:i:s');
            $this->db->table('tb_budget_strategies')->insert($data);
            return $this->response->setJSON(['status' => 'success', 'message' => 'เพิ่มยุทธศาสตร์สำเร็จ']);
        } else {
            $this->db->table('tb_budget_strategies')->where('id', $id)->update($data);
            return $this->response->setJSON(['status' => 'success', 'message' => 'แก้ไขยุทธศาสตร์สำเร็จ']);
        }
    }

    /**
     * ลบ ยุทธศาสตร์
     */
    public function deleteStrategy($id)
    {
        if (!auth_can_manage_plan()) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'คุณไม่มีสิทธิ์ลบข้อมูลยุทธศาสตร์']);
        }

        $this->db->table('tb_budget_strategies')->where('id', $id)->delete();
        return $this->response->setJSON(['status' => 'success', 'message' => 'ลบยุทธศาสตร์เรียบร้อย']);
    }

    /**
     * บันทึก / แก้ไข กลยุทธ์ (ระดับ 2)
     */
    public function saveTactic()
    {
        if (!auth_can_manage_plan()) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'คุณไม่มีสิทธิ์จัดการข้อมูลกลยุทธ์']);
        }

        $id = $this->request->getPost('id');
        $data = [
            'strategy_id'        => $this->request->getPost('strategy_id'),
            'tactic_no'          => trim($this->request->getPost('tactic_no') ?? ''),
            'tactic_name'        => trim($this->request->getPost('tactic_name') ?? ''),
            'objective'          => format_item_lines(trim($this->request->getPost('objective') ?? '')),
            'actions'            => format_item_lines(trim($this->request->getPost('actions') ?? '')),
            'responsible_agency' => trim($this->request->getPost('responsible_agency') ?? ''),
            'updated_at'         => date('Y-m-d H:i:s'),
        ];

        if (empty($id)) {
            $data['created_at'] = date('Y-m-d H:i:s');
            $this->db->table('tb_budget_tactics')->insert($data);
            return $this->response->setJSON(['status' => 'success', 'message' => 'เพิ่มกลยุทธ์สำเร็จ']);
        } else {
            $this->db->table('tb_budget_tactics')->where('id', $id)->update($data);
            return $this->response->setJSON(['status' => 'success', 'message' => 'แก้ไขกลยุทธ์สำเร็จ']);
        }
    }

    /**
     * ลบ กลยุทธ์
     */
    public function deleteTactic($id)
    {
        if (!auth_can_manage_plan()) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'คุณไม่มีสิทธิ์ลบข้อมูลกลยุทธ์']);
        }

        $this->db->table('tb_budget_tactics')->where('id', $id)->delete();
        return $this->response->setJSON(['status' => 'success', 'message' => 'ลบกลยุทธ์เรียบร้อย']);
    }

    private function getCurrentUserId()
    {
        $session = session();
        return $session->get('id') ?: ($session->get('pers_id') ?: ($session->get('username') ?: 'Admin'));
    }

    private function parseMoney($value)
    {
        if (empty($value)) return 0.0;
        if (is_numeric($value)) return (float)$value;
        $cleaned = str_replace(',', '', (string)$value);
        return is_numeric($cleaned) ? (float)$cleaned : 0.0;
    }

    private function parseDateInput($dateStr)
    {
        $dateStr = trim($dateStr ?? '');
        if (empty($dateStr) || $dateStr === '0000-00-00') {
            return null;
        }

        // Format YYYY-MM-DD
        if (preg_match('/^(\d{4})-(\d{1,2})-(\d{1,2})$/', $dateStr, $matches)) {
            $year = (int)$matches[1];
            $month = (int)$matches[2];
            $day = (int)$matches[3];

            // If year was sent in BE (e.g. 2569), convert to CE (2026)
            if ($year > 2400) {
                $year -= 543;
            }
            return sprintf('%04d-%02d-%02d', $year, $month, $day);
        }

        // Format DD/MM/YYYY or DD-MM-YYYY
        if (preg_match('/^(\d{1,2})[\/\-](\d{1,2})[\/\-](\d{4})$/', $dateStr, $matches)) {
            $day = (int)$matches[1];
            $month = (int)$matches[2];
            $year = (int)$matches[3];

            if ($year > 2400) {
                $year -= 543;
            }
            return sprintf('%04d-%02d-%02d', $year, $month, $day);
        }

        $time = strtotime($dateStr);
        if ($time !== false && $time > 0) {
            $y = (int)date('Y', $time);
            if ($y > 2400) {
                $y -= 543;
            }
            return sprintf('%04d-%s', $y, date('m-d', $time));
        }

        return null;
    }

    /**
     * บันทึก / แก้ไขข้อมูลโครงการ (ระดับ 3)
     */
    public function saveProject()
    {
        if (!auth_can_manage_plan()) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'คุณไม่มีสิทธิ์จัดการข้อมูลโครงการ']);
        }

        $id = $this->request->getPost('id');
        $strategyId = $this->request->getPost('strategy_id');
        $tacticId = $this->request->getPost('tactic_id');

        $strategy = $strategyId ? $this->db->table('tb_budget_strategies')->where('id', $strategyId)->get()->getRow() : null;
        $tactic = $tacticId ? $this->db->table('tb_budget_tactics')->where('id', $tacticId)->get()->getRow() : null;

        $strategyName = $strategy ? "ยุทธศาสตร์ที่ {$strategy->strategy_no} {$strategy->strategy_name}" : trim($this->request->getPost('strategy_name') ?? '');
        $strategySub = $tactic ? "กลยุทธ์ {$tactic->tactic_no} {$tactic->tactic_name}" : trim($this->request->getPost('strategy_sub') ?? '');

        $startDate = trim($this->request->getPost('start_date') ?? '');
        $endDate   = trim($this->request->getPost('end_date') ?? '');

        $data = [
            'budget_year'      => trim($this->request->getPost('budget_year') ?? ''),
            'strategy_id'      => !empty($strategyId) ? $strategyId : null,
            'tactic_id'        => !empty($tacticId) ? $tacticId : null,
            'strategy_name'    => $strategyName,
            'strategy_sub'     => $strategySub,
            'project_code'     => trim($this->request->getPost('project_code') ?? ''),
            'project_name'     => trim($this->request->getPost('project_name') ?? ''),
            'budget_source'    => trim($this->request->getPost('budget_source') ?? ''),
            'start_date'       => !empty($startDate) ? $startDate : null,
            'end_date'         => !empty($endDate) ? $endDate : null,
            'allocated_budget' => $this->parseMoney($this->request->getPost('allocated_budget')),
            'updated_at'       => date('Y-m-d H:i:s'),
        ];

        $this->ensureSubItemParentColumn();
        $subItems = $this->request->getPost('sub_items'); // array of items with optional children

        // คำนวณงบประมาณรวมจากกิจกรรมหลักและกิจกรรมย่อย โดยไม่เกิดการนับซ้ำ
        $totalFromSub = 0.0;
        $hasAnySub = false;

        if (!empty($subItems) && is_array($subItems)) {
            foreach ($subItems as $item) {
                $hasChildren = !empty($item['children']) && is_array($item['children']);
                if ($hasChildren) {
                    $childSum = 0.0;
                    foreach ($item['children'] as $ch) {
                        if (!empty($ch['item_name']) || !empty($ch['item_no'])) {
                            $hasAnySub = true;
                            $childSum += $this->parseMoney($ch['allocated_budget'] ?? 0);
                        }
                    }
                    $totalFromSub += $childSum;
                } else {
                    if (!empty($item['item_name']) || !empty($item['item_no'])) {
                        $hasAnySub = true;
                        $totalFromSub += $this->parseMoney($item['allocated_budget'] ?? 0);
                    }
                }
            }
            if ($hasAnySub) {
                $data['allocated_budget'] = $totalFromSub;
            }
        }

        // ฟังก์ชันช่วยบันทึกกิจกรรมหลักและกิจกรรมย่อยแบบลำดับชั้น
        $saveSubItemsHierarchy = function ($targetProjectId, $itemsList) {
            $keepIds = [];
            if (!empty($itemsList) && is_array($itemsList)) {
                foreach ($itemsList as $mainItem) {
                    $mNo = trim($mainItem['item_no'] ?? '');
                    $mName = trim($mainItem['item_name'] ?? '');
                    $mId = !empty($mainItem['id']) ? (int)$mainItem['id'] : null;
                    $children = !empty($mainItem['children']) && is_array($mainItem['children']) ? $mainItem['children'] : [];

                    if (empty($mNo) && empty($mName) && empty($children)) {
                        continue;
                    }

                    // คำนวณงบประมาณของกิจกรรมหลัก (ถ้ารวมจากกิจกรรมย่อย หรือกรอกเอง)
                    if (!empty($children)) {
                        $mBudget = 0.0;
                        foreach ($children as $ch) {
                            $mBudget += $this->parseMoney($ch['allocated_budget'] ?? 0);
                        }
                    } else {
                        $mBudget = $this->parseMoney($mainItem['allocated_budget'] ?? 0);
                    }

                    $mainData = [
                        'project_id'       => $targetProjectId,
                        'parent_id'        => null,
                        'item_no'          => $mNo,
                        'item_name'        => $mName,
                        'allocated_budget' => $mBudget,
                        'updated_at'       => date('Y-m-d H:i:s'),
                    ];

                    $existingMain = null;
                    if ($mId) {
                        $existingMain = $this->db->table('tb_budget_project_sub_items')
                            ->where('id', $mId)
                            ->where('project_id', $targetProjectId)
                            ->get()
                            ->getRow();
                    }

                    if ($existingMain) {
                        $this->db->table('tb_budget_project_sub_items')->where('id', $mId)->update($mainData);
                        $parentId = $mId;
                    } else {
                        $mainData['created_at'] = date('Y-m-d H:i:s');
                        $this->db->table('tb_budget_project_sub_items')->insert($mainData);
                        $parentId = $this->db->insertID();
                    }
                    $keepIds[] = $parentId;

                    // บันทึกกิจกรรมย่อย (ลูก)
                    foreach ($children as $child) {
                        $cNo = trim($child['item_no'] ?? '');
                        $cName = trim($child['item_name'] ?? '');
                        $cId = !empty($child['id']) ? (int)$child['id'] : null;

                        if (empty($cNo) && empty($cName)) {
                            continue;
                        }

                        $childData = [
                            'project_id'       => $targetProjectId,
                            'parent_id'        => $parentId,
                            'item_no'          => $cNo,
                            'item_name'        => $cName,
                            'allocated_budget' => $this->parseMoney($child['allocated_budget'] ?? 0),
                            'updated_at'       => date('Y-m-d H:i:s'),
                        ];

                        $existingChild = null;
                        if ($cId) {
                            $existingChild = $this->db->table('tb_budget_project_sub_items')
                                ->where('id', $cId)
                                ->where('project_id', $targetProjectId)
                                ->get()
                                ->getRow();
                        }

                        if ($existingChild) {
                            $this->db->table('tb_budget_project_sub_items')->where('id', $cId)->update($childData);
                            $keepIds[] = $cId;
                        } else {
                            $childData['created_at'] = date('Y-m-d H:i:s');
                            $this->db->table('tb_budget_project_sub_items')->insert($childData);
                            $keepIds[] = $this->db->insertID();
                        }
                    }
                }
            }

            // ลบรายการที่ถูกลบออกจากฟอร์ม
            $builder = $this->db->table('tb_budget_project_sub_items')->where('project_id', $targetProjectId);
            if (!empty($keepIds)) {
                $builder->whereNotIn('id', $keepIds);
            }
            $builder->delete();
        };

        if (empty($id)) {
            $data['created_at'] = date('Y-m-d H:i:s');
            $this->db->table('tb_budget_projects')->insert($data);
            $newId = $this->db->insertID();

            // Insert sub items hierarchy
            $saveSubItemsHierarchy($newId, $subItems);

            // สร้างแถวตั้งต้นใน ledger อัตโนมัติ
            $this->db->table('tb_budget_ledger')->insert([
                'project_id'           => $newId,
                'doc_date'             => null,
                'dika_no'              => null,
                'doc_no'               => null,
                'description'          => 'ตาม' . ($data['budget_source'] ?: 'งบประมาณรายจ่ายประจำปี'),
                'amount_budget_adjust' => $data['allocated_budget'],
                'balance'              => $data['allocated_budget'],
                'created_by'           => $this->getCurrentUserId(),
                'created_at'           => date('Y-m-d H:i:s'),
            ]);

            return $this->response->setJSON(['status' => 'success', 'message' => 'เพิ่มโครงการเรียบร้อยแล้ว']);
        } else {
            $this->db->table('tb_budget_projects')->where('id', $id)->update($data);

            // Sync sub items hierarchy
            $saveSubItemsHierarchy($id, $subItems);

            // Update the initial ledger entry adjust amount if it exists
            $firstLedger = $this->db->table('tb_budget_ledger')
                ->where('project_id', $id)
                ->orderBy('id', 'ASC')
                ->get(1)
                ->getRow();

            if ($firstLedger && empty($firstLedger->doc_no) && empty($firstLedger->dika_no)) {
                $this->db->table('tb_budget_ledger')
                    ->where('id', $firstLedger->id)
                    ->update([
                        'amount_budget_adjust' => $data['allocated_budget'],
                        'updated_at'           => date('Y-m-d H:i:s'),
                    ]);
            }

            $this->recalculateLedgerBalance($id);
            return $this->response->setJSON(['status' => 'success', 'message' => 'บันทึกการแก้ไขเรียบร้อย']);
        }
    }

    /**
     * ลบโครงการ
     */
    public function deleteProject($id)
    {
        if (!auth_can_manage_plan()) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'คุณไม่มีสิทธิ์ลบข้อมูลโครงการ']);
        }

        $this->db->table('tb_budget_projects')->where('id', $id)->delete();
        return $this->response->setJSON(['status' => 'success', 'message' => 'ลบโครงการเรียบร้อย']);
    }

    /**
     * หน้าแสดงตารางทะเบียนคุมของโครงการ (Ledger View)
     */
    public function viewLedger($projectId)
    {
        $data = $this->DataMain();
        $project = $this->db->table('tb_budget_projects')->where('id', $projectId)->get()->getRow();
        if (!$project) {
            return redirect()->to(base_url('Admin/BudgetLedger'))->with('error', 'ไม่พบข้อมูลโครงการ');
        }

        $data['project'] = $project;
        $data['title'] = "ทะเบียนคุมเงินงบประมาณ: " . $project->project_name;
        $data['can_edit_ledger'] = auth_can_manage_finance();

        $ledger = $this->db->table('tb_budget_ledger l')
            ->select('l.*, s.item_no as sub_item_no, s.item_name as sub_item_name')
            ->join('tb_budget_project_sub_items s', 's.id = l.sub_item_id', 'left')
            ->where('l.project_id', $projectId)
            ->orderBy('l.doc_date', 'ASC')
            ->orderBy('l.id', 'ASC')
            ->get()
            ->getResult();

        $data['ledger'] = $ledger;

        $totals = [
            'adjust'          => 0,
            'borrow'          => 0,
            'return_cash'     => 0,
            'return_receipt'  => 0,
            'direct_withdraw' => 0,
            'last_balance'    => 0,
        ];

        foreach ($ledger as $item) {
            $totals['adjust'] += (float)$item->amount_budget_adjust;
            $totals['borrow'] += (float)$item->amount_borrow;
            $totals['return_cash'] += (float)$item->amount_return_cash;
            $totals['return_receipt'] += (float)$item->amount_return_receipt;
            $totals['direct_withdraw'] += (float)$item->amount_direct_withdraw;
            $totals['last_balance'] = (float)$item->balance;
        }

        $data['totals'] = $totals;

        // ดึงรายการข้อย่อย (Sub-items) ของโครงการ
        $subItems = $this->db->table('tb_budget_project_sub_items')
            ->where('project_id', $projectId)
            ->orderBy('id', 'ASC')
            ->get()
            ->getResult();
        sort_projects_naturally($subItems, 'item_no');

        // คำนวณยอดใช้จ่ายแยกตามข้อย่อย
        foreach ($subItems as &$sub) {
            $subExpense = $this->db->query("
                SELECT 
                    SUM(amount_direct_withdraw) as direct_withdraw,
                    SUM(amount_return_receipt) as return_receipt
                FROM tb_budget_ledger
                WHERE project_id = ? AND sub_item_id = ?
            ", [$projectId, $sub->id])->getRow();

            $spent = ($subExpense ? (float)$subExpense->direct_withdraw + (float)$subExpense->return_receipt : 0.0);
            $sub->total_spent = $spent;
            $sub->remaining = (float)$sub->allocated_budget - $spent;
            $sub->percent = (float)$sub->allocated_budget > 0 ? min(100, round(($spent / (float)$sub->allocated_budget) * 100, 1)) : 0;
        }

        $data['sub_items'] = build_sub_items_tree($subItems);
        $data['all_sub_items_raw'] = $subItems;

        // สรุปสัญญายืมเงินทั้งหมดในโครงการนี้และสถานะการเคลียร์
        $borrowsList = [];
        foreach ($ledger as $item) {
            if ($item->amount_borrow > 0) {
                // คำนวณยอดที่ส่งใช้แล้ว (เฉพาะรายการที่มีการส่งใช้เงินสด หรือส่งใช้ใบสำคัญ และต้องไม่ใช่รายการยืมนี้เอง)
                $cleared = $this->db->table('tb_budget_ledger')
                    ->select('SUM(amount_return_cash) as cash, SUM(amount_return_receipt) as receipt')
                    ->where('project_id', $projectId)
                    ->where('id !=', $item->id)
                    ->groupStart()
                        ->where('amount_return_cash >', 0)
                        ->orWhere('amount_return_receipt >', 0)
                    ->groupEnd()
                    ->groupStart()
                        ->where('ref_borrow_id', $item->id)
                        ->orGroupStart()
                            ->where('ref_borrow_id IS NULL')
                            ->where('ref_doc_no', $item->doc_no)
                            ->where('ref_doc_no IS NOT NULL')
                            ->where('ref_doc_no !=', '')
                        ->groupEnd()
                    ->groupEnd()
                    ->get()
                    ->getRow();

                $cash = $cleared ? (float)$cleared->cash : 0;
                $receipt = $cleared ? (float)$cleared->receipt : 0;
                $totalCleared = $cash + $receipt;
                $remaining = max(0, (float)$item->amount_borrow - $totalCleared);

                // แสดงเฉพาะสัญญายืมเงินที่ยังมียอดค้างชำระ (เคลียร์ครบแล้วไม่ต้องแสดง)
                if ($remaining > 0) {
                    $borrowsList[] = (object)[
                        'id'               => $item->id,
                        'doc_no'           => $item->doc_no ?: "ยืมเงิน ID #{$item->id}",
                        'doc_date'         => $item->doc_date,
                        'description'      => $item->description,
                        'amount_borrow'    => (float)$item->amount_borrow,
                        'cleared_cash'     => $cash,
                        'cleared_receipt'  => $receipt,
                        'total_cleared'    => $totalCleared,
                        'remaining_borrow' => $remaining,
                        'percent'          => (float)$item->amount_borrow > 0 ? min(100, round(($totalCleared / (float)$item->amount_borrow) * 100, 1)) : 100,
                        'is_settled'       => false
                    ];
                }
            }
        }
        $data['borrows_list'] = $borrowsList;

        return view('Admin/AdminLeyout/AdminHeader', $data)
            . view('Admin/AdminLeyout/AdminMenuLeft', $data)
            . view('Admin/AdminBudgetLedger/LedgerView', $data)
            . view('Admin/AdminLeyout/AdminFooter', $data);
    }

    /**
     * ดึงรายการสัญญายืมเงินที่ยังมียอดค้างชำระ (AJAX)
     */
    public function getUnclearedBorrows($projectId)
    {
        $excludeId = (int)($this->request->getGet('exclude_id') ?? 0);
        
        $borrows = $this->db->table('tb_budget_ledger')
            ->where('project_id', $projectId)
            ->where('amount_borrow >', 0)
            ->orderBy('doc_date', 'ASC')
            ->orderBy('id', 'ASC')
            ->get()
            ->getResult();

        $result = [];
        foreach ($borrows as $b) {
            $clearedBuilder = $this->db->table('tb_budget_ledger')
                ->select('SUM(amount_return_cash) as cash_cleared, SUM(amount_return_receipt) as receipt_cleared')
                ->where('project_id', $projectId)
                ->where('id !=', $b->id)
                ->groupStart()
                    ->where('amount_return_cash >', 0)
                    ->orWhere('amount_return_receipt >', 0)
                ->groupEnd()
                ->groupStart()
                    ->where('ref_borrow_id', $b->id)
                    ->orGroupStart()
                        ->where('ref_borrow_id IS NULL')
                        ->where('ref_doc_no', $b->doc_no)
                        ->where('ref_doc_no IS NOT NULL')
                        ->where('ref_doc_no !=', '')
                    ->groupEnd()
                ->groupEnd();

            if ($excludeId > 0) {
                $clearedBuilder->where('id !=', $excludeId);
            }

            $cleared = $clearedBuilder->get()->getRow();
            $totalCash = $cleared ? (float)$cleared->cash_cleared : 0;
            $totalReceipt = $cleared ? (float)$cleared->receipt_cleared : 0;
            $totalCleared = $totalCash + $totalReceipt;
            $remaining = max(0, (float)$b->amount_borrow - $totalCleared);

            // Include if still has remaining balance or if editing an item that was linked to this borrow
            $currentRefId = (int)($this->request->getGet('current_ref_id') ?? 0);
            if ($remaining > 0 || $b->id == $currentRefId) {
                $result[] = [
                    'id'               => $b->id,
                    'doc_no'           => $b->doc_no ?: "ยืมเงิน ID #{$b->id}",
                    'doc_date'         => $b->doc_date,
                    'description'      => $b->description,
                    'amount_borrow'    => (float)$b->amount_borrow,
                    'cleared_cash'     => $totalCash,
                    'cleared_receipt'  => $totalReceipt,
                    'total_cleared'    => $totalCleared,
                    'remaining_borrow' => $remaining,
                    'is_settled'       => $remaining <= 0
                ];
            }
        }

        return $this->response->setJSON([
            'status'  => 'success',
            'borrows' => $result
        ]);
    }

    /**
     * ดึงข้อมูลทะเบียนคุมแบบ Real-time (JSON สำหรับ AJAX Refresh ตารางโดยไม่ต้องรีโหลดหน้า)
     */
    public function getLedgerData($projectId)
    {
        $project = $this->db->table('tb_budget_projects')->where('id', $projectId)->get()->getRow();
        if (!$project) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'ไม่พบข้อมูลโครงการ']);
        }

        $subItems = $this->db->table('tb_budget_project_sub_items')
            ->where('project_id', $projectId)
            ->orderBy('id', 'ASC')
            ->get()
            ->getResult();

        foreach ($subItems as &$sub) {
            $subExpense = $this->db->query("
                SELECT 
                    SUM(amount_direct_withdraw) as direct_withdraw,
                    SUM(amount_return_receipt) as return_receipt
                FROM tb_budget_ledger
                WHERE project_id = ? AND sub_item_id = ?
            ", [$projectId, $sub->id])->getRow();

            $spent = ($subExpense ? (float)$subExpense->direct_withdraw + (float)$subExpense->return_receipt : 0.0);
            $sub->total_spent = $spent;
            $sub->remaining = (float)$sub->allocated_budget - $spent;
            $sub->percent = (float)$sub->allocated_budget > 0 ? min(100, round(($spent / (float)$sub->allocated_budget) * 100, 1)) : 0;
        }

        $subItems = build_sub_items_tree($subItems);

        $ledger = $this->db->table('tb_budget_ledger l')
            ->select('l.*, s.item_no as sub_item_no, s.item_name as sub_item_name')
            ->join('tb_budget_project_sub_items s', 's.id = l.sub_item_id', 'left')
            ->where('l.project_id', $projectId)
            ->orderBy('l.doc_date', 'ASC')
            ->orderBy('l.id', 'ASC')
            ->get()
            ->getResult();

        $totals = [
            'adjust'          => 0.0,
            'borrow'          => 0.0,
            'return_cash'     => 0.0,
            'return_receipt'  => 0.0,
            'direct_withdraw' => 0.0,
            'last_balance'    => 0.0,
        ];

        $thaiMonths = ['', 'ม.ค.', 'ก.พ.', 'มี.ค.', 'เม.ย.', 'พ.ค.', 'มิ.ย.', 'ก.ค.', 'ส.ค.', 'ก.ย.', 'ต.ค.', 'พ.ย.', 'ธ.ค.'];
        $formattedLedger = [];

        foreach ($ledger as $item) {
            $totals['adjust'] += (float)$item->amount_budget_adjust;
            $totals['borrow'] += (float)$item->amount_borrow;
            $totals['return_cash'] += (float)$item->amount_return_cash;
            $totals['return_receipt'] += (float)$item->amount_return_receipt;
            $totals['direct_withdraw'] += (float)$item->amount_direct_withdraw;
            $totals['last_balance'] = (float)$item->balance;

            $displayDate = '-';
            if (!empty($item->doc_date) && $item->doc_date !== '0000-00-00') {
                $time = strtotime($item->doc_date);
                if ($time !== false && $time > 0) {
                    $d = date('j', $time);
                    $m = date('n', $time);
                    $y = (int)date('Y', $time);
                    if ($y < 2400) $y += 543;
                    $displayDate = $d . '-' . $thaiMonths[$m] . '-' . substr((string)$y, 2);
                }
            }

            $formattedLedger[] = [
                'id'                     => (int)$item->id,
                'project_id'             => (int)$item->project_id,
                'ref_borrow_id'          => $item->ref_borrow_id ? (int)$item->ref_borrow_id : null,
                'ref_doc_no'             => $item->ref_doc_no ?: '',
                'doc_date'               => $item->doc_date,
                'display_date'           => $displayDate,
                'dika_no'                => $item->dika_no ?: '',
                'doc_no'                 => $item->doc_no ?: '',
                'description'            => $item->description ?: '',
                'amount_budget_adjust'   => (float)$item->amount_budget_adjust,
                'amount_borrow'          => (float)$item->amount_borrow,
                'amount_return_cash'     => (float)$item->amount_return_cash,
                'amount_return_receipt'  => (float)$item->amount_return_receipt,
                'amount_direct_withdraw' => (float)$item->amount_direct_withdraw,
                'balance'                => (float)$item->balance,
                'notes'                  => $item->notes ?: '',
                'sub_item_id'            => $item->sub_item_id ? (int)$item->sub_item_id : null,
                'sub_item_no'            => $item->sub_item_no ?: '',
                'sub_item_name'          => $item->sub_item_name ?: '',
                'raw_json'               => json_encode($item)
            ];
        }

        // สรุปสัญญายืมเงิน
        $borrowsList = [];
        foreach ($ledger as $item) {
            if ($item->amount_borrow > 0) {
                $cleared = $this->db->table('tb_budget_ledger')
                    ->select('SUM(amount_return_cash) as cash, SUM(amount_return_receipt) as receipt')
                    ->where('project_id', $projectId)
                    ->where('id !=', $item->id)
                    ->groupStart()
                        ->where('amount_return_cash >', 0)
                        ->orWhere('amount_return_receipt >', 0)
                    ->groupEnd()
                    ->groupStart()
                        ->where('ref_borrow_id', $item->id)
                        ->orGroupStart()
                            ->where('ref_borrow_id IS NULL')
                            ->where('ref_doc_no', $item->doc_no)
                            ->where('ref_doc_no IS NOT NULL')
                            ->where('ref_doc_no !=', '')
                        ->groupEnd()
                    ->groupEnd()
                    ->get()
                    ->getRow();

                $cash = $cleared ? (float)$cleared->cash : 0;
                $receipt = $cleared ? (float)$cleared->receipt : 0;
                $totalCleared = $cash + $receipt;
                $remaining = max(0, (float)$item->amount_borrow - $totalCleared);

                // แสดงเฉพาะสัญญายืมเงินที่ยังมียอดค้างชำระ (เคลียร์ครบแล้วไม่ต้องแสดง)
                if ($remaining > 0) {
                    $borrowsList[] = [
                        'id'               => (int)$item->id,
                        'doc_no'           => $item->doc_no ?: "ยืมเงิน ID #{$item->id}",
                        'doc_date'         => $item->doc_date,
                        'description'      => $item->description,
                        'amount_borrow'    => (float)$item->amount_borrow,
                        'cleared_cash'     => $cash,
                        'cleared_receipt'  => $receipt,
                        'total_cleared'    => $totalCleared,
                        'remaining_borrow' => $remaining,
                        'percent'          => (float)$item->amount_borrow > 0 ? min(100, round(($totalCleared / (float)$item->amount_borrow) * 100, 1)) : 100,
                        'is_settled'       => false
                    ];
                }
            }
        }

        return $this->response->setJSON([
            'status'          => 'success',
            'ledger'          => $formattedLedger,
            'totals'          => $totals,
            'sub_items'       => $subItems,
            'borrows_list'    => $borrowsList,
            'can_edit_ledger' => auth_can_manage_finance(),
        ]);
    }

    /**
     * บันทึกรายการใหม่ หรือแก้ไขรายการในทะเบียนคุม
     */
    public function saveLedgerItem()
    {
        if (!auth_can_manage_finance()) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'เฉพาะเจ้าหน้าที่การเงินเท่านั้นที่สามารถบันทึกทะเบียนคุมได้']);
        }

        $id = $this->request->getPost('id');
        $projectId = $this->request->getPost('project_id');
        $rawDocDate = $this->request->getPost('doc_date');
        $docDate = $this->parseDateInput($rawDocDate);

        $docNo = trim($this->request->getPost('doc_no') ?? '');
        $description = trim($this->request->getPost('description') ?? '');

        if (empty($docDate) || $docDate === '0000-00-00') {
            return $this->response->setJSON(['status' => 'error', 'message' => 'กรุณาระบุ วัน/เดือน/ปี']);
        }
        if (empty($docNo)) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'กรุณาระบุ ที่เอกสาร']);
        }
        if (empty($description)) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'กรุณาระบุ รายการ / วัตถุประสงค์การใช้จ่าย']);
        }

        $amountAdjust = $this->parseMoney($this->request->getPost('amount_budget_adjust'));
        $amountBorrow = $this->parseMoney($this->request->getPost('amount_borrow'));
        $amountReturnCash = $this->parseMoney($this->request->getPost('amount_return_cash'));
        $amountReturnReceipt = $this->parseMoney($this->request->getPost('amount_return_receipt'));
        $amountDirectWithdraw = $this->parseMoney($this->request->getPost('amount_direct_withdraw'));

        $isReturnTransaction = ($amountReturnCash > 0 || $amountReturnReceipt > 0);

        $refBorrowId = (int)($this->request->getPost('ref_borrow_id') ?? 0);
        $refDocNo = trim($this->request->getPost('ref_doc_no') ?? '');

        // Only assign ref_borrow_id and ref_doc_no if it is truly a RETURN transaction!
        if (!$isReturnTransaction) {
            $refBorrowId = null;
            $refDocNo = null;
        } else {
            if ($refBorrowId > 0 && empty($refDocNo)) {
                $borrowRow = $this->db->table('tb_budget_ledger')->where('id', $refBorrowId)->get()->getRow();
                if ($borrowRow) {
                    $refDocNo = $borrowRow->doc_no;
                }
            }
            $refBorrowId = $refBorrowId > 0 ? $refBorrowId : null;
            $refDocNo = !empty($refDocNo) ? $refDocNo : null;
        }

        $subItemId = (int)($this->request->getPost('sub_item_id') ?? 0);

        $data = [
            'project_id'             => $projectId,
            'sub_item_id'            => $subItemId > 0 ? $subItemId : null,
            'ref_borrow_id'          => $refBorrowId,
            'ref_doc_no'             => $refDocNo,
            'doc_date'               => $docDate,
            'dika_no'                => trim($this->request->getPost('dika_no') ?? ''),
            'doc_no'                 => $docNo,
            'description'            => $description,
            'amount_budget_adjust'   => $amountAdjust,
            'amount_borrow'          => $amountBorrow,
            'amount_return_cash'     => $amountReturnCash,
            'amount_return_receipt'  => $amountReturnReceipt,
            'amount_direct_withdraw' => $amountDirectWithdraw,
            'notes'                  => trim($this->request->getPost('notes') ?? ''),
            'updated_at'             => date('Y-m-d H:i:s'),
        ];

        if (empty($id)) {
            $data['created_at'] = date('Y-m-d H:i:s');
            $data['created_by'] = $this->getCurrentUserId();
            $this->db->table('tb_budget_ledger')->insert($data);
        } else {
            $this->db->table('tb_budget_ledger')->where('id', $id)->update($data);
        }

        $this->recalculateLedgerBalance($projectId);

        return $this->response->setJSON([
            'status'  => 'success',
            'message' => empty($id) ? 'เพิ่มรายการสำเร็จ' : 'แก้ไขรายการสำเร็จ'
        ]);
    }

    /**
     * ลบรายการในทะเบียนคุม
     */
    public function deleteLedgerItem($id)
    {
        if (!auth_can_manage_finance()) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'เฉพาะเจ้าหน้าที่การเงินเท่านั้นที่สามารถลบรายการได้']);
        }

        $item = $this->db->table('tb_budget_ledger')->where('id', $id)->get()->getRow();
        if ($item) {
            $projectId = $item->project_id;
            $this->db->table('tb_budget_ledger')->where('id', $id)->delete();
            $this->recalculateLedgerBalance($projectId);
            return $this->response->setJSON(['status' => 'success', 'message' => 'ลบรายการเรียบร้อย']);
        }
        return $this->response->setJSON(['status' => 'error', 'message' => 'ไม่พบรายการที่ต้องการลบ']);
    }

    /**
     * พิมพ์ทะเบียนคุมงบประมาณรายจ่าย (Print Layout)
     */
    public function printLedger($projectId)
    {
        $project = $this->db->table('tb_budget_projects')->where('id', $projectId)->get()->getRow();
        if (!$project) {
            return "ไม่พบข้อมูลโครงการ";
        }

        $ledger = $this->db->table('tb_budget_ledger')
            ->where('project_id', $projectId)
            ->orderBy('doc_date', 'ASC')
            ->orderBy('id', 'ASC')
            ->get()
            ->getResult();

        $totals = [
            'adjust'          => 0,
            'borrow'          => 0,
            'return_cash'     => 0,
            'return_receipt'  => 0,
            'direct_withdraw' => 0,
            'last_balance'    => 0,
        ];

        foreach ($ledger as $item) {
            $totals['adjust'] += (float)$item->amount_budget_adjust;
            $totals['borrow'] += (float)$item->amount_borrow;
            $totals['return_cash'] += (float)$item->amount_return_cash;
            $totals['return_receipt'] += (float)$item->amount_return_receipt;
            $totals['direct_withdraw'] += (float)$item->amount_direct_withdraw;
            $totals['last_balance'] = (float)$item->balance;
        }

        $data = [
            'project' => $project,
            'ledger'  => $ledger,
            'totals'  => $totals,
            'title'   => 'พิมพ์ทะเบียนคุมเงินงบประมาณ - ' . $project->project_name
        ];

        return view('Admin/AdminBudgetLedger/LedgerPrint', $data);
    }

    /**
     * ฟังก์ชันคำนวณยอดคงเหลือใหม่เรียงตามลำดับ (Real-time Recalculate Balance)
     */
    private function recalculateLedgerBalance($projectId)
    {
        $items = $this->db->table('tb_budget_ledger')
            ->where('project_id', $projectId)
            ->orderBy('doc_date', 'ASC')
            ->orderBy('id', 'ASC')
            ->get()
            ->getResult();

        $currentBalance = 0.0;

        foreach ($items as $row) {
            $adjust = (float)$row->amount_budget_adjust;
            $borrow = (float)$row->amount_borrow;
            $returnCash = (float)$row->amount_return_cash;
            $directWithdraw = (float)$row->amount_direct_withdraw;

            $currentBalance = $currentBalance + $adjust - $borrow + $returnCash - $directWithdraw;

            $this->db->table('tb_budget_ledger')->where('id', $row->id)->update([
                'balance' => $currentBalance
            ]);
        }
    }

    /**
     * ตรวจสอบและสร้างฟิลด์ parent_id ในตาราง tb_budget_project_sub_items ให้อัตโนมัติหากยังไม่มี
     */
    private function ensureSubItemParentColumn()
    {
        try {
            $fields = $this->db->getFieldNames('tb_budget_project_sub_items');
            if (!in_array('parent_id', $fields)) {
                $this->db->query("ALTER TABLE tb_budget_project_sub_items ADD COLUMN parent_id INT UNSIGNED NULL DEFAULT NULL AFTER project_id");
                $this->db->query("ALTER TABLE tb_budget_project_sub_items ADD INDEX idx_parent_id (parent_id)");
            }
        } catch (\Throwable $e) {
            // ไม่ต้องแจ้ง error หากฟิลด์มีอยู่แล้ว
        }
    }
}
