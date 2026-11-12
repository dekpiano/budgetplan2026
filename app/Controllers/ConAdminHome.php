<?php

namespace App\Controllers;

class ConAdminHome extends BaseController
{
    public function DataMain(){
        $data['full_url'] = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http') . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
        
        $data['uri'] = service('uri'); 
        return $data;
    }

    public function index()
    {
        helper('auth');
        if (!auth_is_admin()) {
            return redirect()->to(base_url())->with('error', 'คุณไม่มีสิทธิ์เข้าถึงระบบผู้ดูแล');
        }
        $session = session();
        $data = $this->DataMain();
        $data['title'] = "หน้าแรก (แดชบอร์ดงบประมาณและทะเบียนคุม)";
        
        $db = \Config\Database::connect();
        
        $selectedYear = $this->request->getGet('year') ?? '2569';
        $data['selected_year'] = $selectedYear;

        // รายชื่อปีงบประมาณทั้งหมด
        $yearsQuery = $db->query("SELECT DISTINCT budget_year FROM tb_budget_projects UNION SELECT DISTINCT budget_year FROM tb_budget_strategies ORDER BY budget_year DESC")->getResult();
        $data['years'] = !empty($yearsQuery) ? $yearsQuery : [(object)['budget_year' => '2569']];

        // =============================================
        // 1. ระบบทะเบียนคุมเงินงบประมาณ (Budget Ledger)
        // =============================================
        $projects = $db->table('tb_budget_projects')
            ->where('budget_year', $selectedYear)
            ->orderBy('project_code', 'ASC')
            ->get()
            ->getResult();
        sort_projects_naturally($projects, 'project_code');

        $totalAllocated = 0;
        $totalBalance   = 0;
        $totalSpent     = 0;
        $totalBorrow    = 0;
        $totalReturnCash = 0;
        $totalReturnReceipt = 0;

        foreach ($projects as &$p) {
            $lastItem = $db->table('tb_budget_ledger')
                ->where('project_id', $p->id)
                ->orderBy('doc_date', 'DESC')
                ->orderBy('id', 'DESC')
                ->get()
                ->getRow();

            $p->current_balance = $lastItem ? (float)$lastItem->balance : (float)$p->allocated_budget;

            $stats = $db->query("
                SELECT 
                    SUM(amount_budget_adjust) AS total_adjust,
                    SUM(amount_borrow) AS total_borrow,
                    SUM(amount_return_cash) AS total_return_cash,
                    SUM(amount_return_receipt) AS total_return_receipt,
                    SUM(amount_direct_withdraw) AS total_direct
                FROM tb_budget_ledger 
                WHERE project_id = ?
            ", [$p->id])->getRow();

            $spent = $stats ? ((float)$stats->total_return_receipt + (float)$stats->total_direct) : 0;
            $p->total_spent = $spent;
            $p->total_borrow = $stats ? (float)$stats->total_borrow : 0;
            $p->used_percent = $p->allocated_budget > 0 ? min(100, round(($p->total_spent / $p->allocated_budget) * 100, 1)) : 0;
            $p->item_count = $db->table('tb_budget_ledger')->where('project_id', $p->id)->countAllResults();

            $totalAllocated += (float)$p->allocated_budget;
            $totalBalance   += (float)$p->current_balance;
            $totalSpent     += (float)$p->total_spent;
            $totalBorrow    += (float)$p->total_borrow;
            $totalReturnCash += (float)($stats->total_return_cash ?? 0);
            $totalReturnReceipt += (float)($stats->total_return_receipt ?? 0);

            // ข้อมูลเงินยืมและการส่งใช้ระดับโครงการ
            $p->return_cash = (float)($stats->total_return_cash ?? 0);
            $p->return_receipt = (float)($stats->total_return_receipt ?? 0);
            $p->total_returned = $p->return_cash + $p->return_receipt;
            $p->active_borrow = max(0, (float)$p->total_borrow - $p->total_returned);
        }

        // คัดกรองเฉพาะโครงการที่มีเงินยืมค้างชำระ / รอส่งใช้
        $activeBorrowProjects = [];
        foreach ($projects as $p) {
            if (($p->active_borrow ?? 0) > 0) {
                $p->borrow_items = $db->table('tb_budget_ledger')
                    ->where('project_id', $p->id)
                    ->where('amount_borrow >', 0)
                    ->orderBy('doc_date', 'DESC')
                    ->orderBy('id', 'DESC')
                    ->get()
                    ->getResult();
                $activeBorrowProjects[] = $p;
            }
        }
        $data['active_borrow_projects'] = $activeBorrowProjects;

        $data['projects']         = $projects;
        $data['total_projects']   = count($projects);
        $data['total_allocated']  = $totalAllocated;
        $data['total_balance']    = $totalBalance;
        $data['total_spent']      = $totalSpent;
        $data['total_borrow']     = $totalBorrow;
        $data['active_borrow']    = max(0, $totalBorrow - $totalReturnCash - $totalReturnReceipt);
        $data['spent_percent']    = $totalAllocated > 0 ? min(100, round(($totalSpent / $totalAllocated) * 100, 1)) : 0;

        // 8 รายการล่าสุดในทะเบียนคุมเงินงบประมาณ
        $recentLedgers = $db->table('tb_budget_ledger')
            ->select('tb_budget_ledger.*, tb_budget_projects.project_code, tb_budget_projects.project_name')
            ->join('tb_budget_projects', 'tb_budget_projects.id = tb_budget_ledger.project_id', 'left')
            ->orderBy('tb_budget_ledger.doc_date', 'DESC')
            ->orderBy('tb_budget_ledger.id', 'DESC')
            ->limit(8)
            ->get()
            ->getResult();
        $data['recent_ledgers'] = $recentLedgers;

        // ข้อมูลกราฟการเบิกจ่ายรายเดือนจากทะเบียนคุม (Budget Ledger Monthly Trend)
        $monthlyLedgerTotals = array_fill(1, 12, 0);
        $chartLedgerQuery = $db->query("
            SELECT MONTH(doc_date) as month, SUM(amount_return_receipt + amount_direct_withdraw) as total 
            FROM tb_budget_ledger 
            WHERE doc_date IS NOT NULL AND doc_date != '0000-00-00'
            GROUP BY MONTH(doc_date) 
            ORDER BY MONTH(doc_date) ASC
        ")->getResult();
        foreach ($chartLedgerQuery as $row) {
            if ($row->month >= 1 && $row->month <= 12) {
                $monthlyLedgerTotals[intval($row->month)] = floatval($row->total);
            }
        }
        $data['monthly_ledger_totals'] = array_values($monthlyLedgerTotals);

        // สรุปยอดตามยุทธศาสตร์ (Strategy Summary)
        $strategies = $db->table('tb_budget_strategies')
            ->where('budget_year', $selectedYear)
            ->orderBy('strategy_no', 'ASC')
            ->get()
            ->getResult();
        sort_projects_naturally($strategies, 'strategy_no');
        
        foreach ($strategies as &$st) {
            $stAllocated = 0;
            $stSpent = 0;
            foreach ($projects as $pr) {
                if ($pr->strategy_id == $st->id || (empty($pr->strategy_id) && strpos($pr->strategy_name, "{$st->strategy_no}") !== false)) {
                    $stAllocated += (float)$pr->allocated_budget;
                    $stSpent += (float)$pr->total_spent;
                }
            }
            $st->allocated = $stAllocated;
            $st->spent = $stSpent;
            $st->percent = $stAllocated > 0 ? min(100, round(($stSpent / $stAllocated) * 100, 1)) : 0;
        }
        $data['strategies_summary'] = $strategies;

        // =============================================
        // 2. ทะเบียนสั่งซื้อ / สั่งจ้าง (Orders)
        // =============================================
        $builder = $db->table('tb_purchase_hire_orders');
        $data['recent_orders'] = $builder->orderBy('order_date', 'DESC')->orderBy('id', 'DESC')->limit(5)->get()->getResult();
        
        $totalAmountResult = $db->query("SELECT SUM(amount) AS total FROM tb_purchase_hire_orders")->getRow();
        $data['total_orders_amount'] = $totalAmountResult ? ($totalAmountResult->total ?: 0) : 0;
        $data['total_orders'] = $builder->countAllResults(false);
        
        $sentResult = $db->query("SELECT COUNT(*) AS total FROM tb_purchase_hire_orders WHERE system_sent_status = 'ส่งลงระบบ/ทำฎีกาแล้ว'")->getRow();
        $data['total_sent'] = $sentResult ? $sentResult->total : 0;
      
        return view('Admin/AdminLeyout/AdminHeader', $data)
            . view('Admin/AdminLeyout/AdminMenuLeft', $data)
            . view('Admin/AdminHome/AdminPageHome', $data)
            . view('Admin/AdminLeyout/AdminFooter', $data);
    }

    public function registry($type = 'purchase')
    {
        helper('auth');
        $type = strtolower($type);
        $typeName = ($type === 'hire') ? 'สั่งจ้าง' : 'สั่งซื้อ';
        $systemCode = ($type === 'hire') ? 'registry_hire' : 'registry_purchase';
        if (!auth_can_access_system($systemCode)) {
            return redirect()->to(base_url('Admin/Home'))->with('error', 'คุณไม่มีสิทธิ์เข้าถึงทะเบียนคุมใบ' . $typeName);
        }

        $session = session();
        $data = $this->DataMain();
        
        $data['order_type'] = $typeName;
        $data['type_slug'] = $type;
        $data['title'] = "ทะเบียนคุมใบ" . $typeName;
        
        $db = \Config\Database::connect();
        $builder = $db->table('tb_purchase_hire_orders');
        
        $data['orders'] = $builder->where('order_type', $typeName)->orderBy('order_date', 'DESC')->orderBy('id', 'DESC')->get()->getResult();
        
        // Calculate stats for registry page header
        $totalAmountResult = $db->query("SELECT SUM(amount) AS total FROM tb_purchase_hire_orders WHERE order_type = '$typeName'")->getRow();
        $data['total_amount'] = $totalAmountResult ? ($totalAmountResult->total ?: 0) : 0;
        
        $data['total_orders'] = $builder->where('order_type', $typeName)->countAllResults(false);
        
        $sentResult = $db->query("SELECT COUNT(*) AS total FROM tb_purchase_hire_orders WHERE order_type = '$typeName' AND system_sent_status = 'ส่งลงระบบ/ทำฎีกาแล้ว'")->getRow();
        $data['total_sent'] = $sentResult ? $sentResult->total : 0;

        // Fetch teachers list from personnel database
        $dbPersonnel = \Config\Database::connect('personnel');
        $data['teachers'] = $dbPersonnel->table('tb_personnel')
            ->select('pers_prefix, pers_firstname, pers_lastname')
            ->where('pers_status', 'กำลังใช้งาน')
            ->orderBy('pers_firstname', 'ASC')
            ->get()->getResult();
        
        return view('Admin/AdminLeyout/AdminHeader',$data)
                .view('Admin/AdminLeyout/AdminMenuLeft', $data)
                .view('Admin/AdminHome/AdminPageRegistry', $data)
                .view('Admin/AdminLeyout/AdminFooter', $data);
    }

    public function saveOrder()
    {
        $db = \Config\Database::connect();
        $builder = $db->table('tb_purchase_hire_orders');

        $id = $this->request->getPost('id');
        
        $data = [
            'order_type'         => $this->request->getPost('order_type') ?: 'สั่งซื้อ',
            'order_number'       => $this->request->getPost('order_number'),
            'order_date'         => $this->request->getPost('order_date'),
            'description'        => $this->request->getPost('description'),
            'amount'             => floatval(str_replace(',', '', $this->request->getPost('amount'))),
            'signatory'          => $this->request->getPost('signatory'),
            'contractor'         => $this->request->getPost('contractor'),
            'delivery_date'      => $this->request->getPost('delivery_date') ?: null,
            'remarks'            => $this->request->getPost('remarks'),
            'inspection_status'  => $this->request->getPost('inspection_status'),
            'system_sent_status' => $this->request->getPost('system_sent_status'),
        ];

        // Handle Image Uploads
        $uploadedImages = [];
        $imageFiles = $this->request->getFiles();
        if ($imageFiles && isset($imageFiles['images'])) {
            $upload_server_url = getenv('upload.server.url');
            if (!$upload_server_url) {
                return $this->response->setJSON(['status' => 'error', 'message' => 'Upload server URL is not configured.']);
            }
            $client = \Config\Services::curlrequest();
            $order_date = $this->request->getPost('order_date') ?: date('Y-m-d');
            $date_folder = date('Y-m-d', strtotime($order_date));

            foreach ($imageFiles['images'] as $img) {
                if ($img->isValid() && !$img->hasMoved()) {
                    $local_temp_path = $img->getTempName();
                    $mimeType = $img->getMimeType();
                    $originalName = $img->getName();

                    try {
                        // Strictly allow only English alphanumeric characters
                        $nameWithoutExt = pathinfo($originalName, PATHINFO_FILENAME);
                        $sanitizedName = preg_replace('/[^\w-]/', '_', $nameWithoutExt);
                        $sanitizedName = trim(preg_replace('/_+/', '_', $sanitizedName), '_');
                        $finalName = ($sanitizedName ?: 'image') . '-' . uniqid() . '.jpg';

                        $response = $client->request('POST', $upload_server_url, [
                            'headers' => ['X-Auth-Token' => 'Dekpiano2025!!'],
                            'verify' => false,
                            'multipart' => [
                                'file' => new \CURLFile($local_temp_path, $mimeType, $finalName),
                                'path' => 'budgetplan/orders/' . $date_folder,
                                'desired_filename' => $finalName,
                            ]
                        ]);

                        if ($response->getStatusCode() === 200) {
                            $body = json_decode($response->getBody());
                            if ($body && isset($body->status) && $body->status === 'success' && isset($body->filename)) {
                                $uploadedImages[] = 'budgetplan/orders/' . $date_folder . '/' . $body->filename;
                            } else {
                                log_message('error', 'File upload to remote server failed: ' . $response->getBody());
                                return $this->response->setJSON(['status' => 'error', 'message' => 'File upload to remote server failed', 'details' => $response->getBody()]);
                            }
                        } else {
                             log_message('error', 'File upload to remote server failed with status code: ' . $response->getStatusCode());
                             return $this->response->setJSON(['status' => 'error', 'message' => 'Remote server error', 'details' => $response->getBody()]);
                        }
                    } catch (\Exception $e) {
                        log_message('error', 'Exception during file upload: ' . $e->getMessage());
                        return $this->response->setJSON(['status' => 'error', 'message' => $e->getMessage()]);
                    }
                }
            }
        }

        // Image Management (Edit Mode)
        if ($id) {
            // Fetch current order to get existing images
            $currentOrder = $builder->where('id', $id)->get()->getRow();
            if ($currentOrder) {
                $existingImages = $currentOrder->images ? json_decode($currentOrder->images, true) : [];
                
                // Get retained images from form
                $retainedImagesRaw = $this->request->getPost('retained_images');
                $retainedImages = [];
                if ($retainedImagesRaw) {
                    $retainedImages = is_array($retainedImagesRaw) ? $retainedImagesRaw : json_decode($retainedImagesRaw, true);
                }

                // Check which images were deleted
                $upload_server_delete_url = getenv('upload.server.delete.url');
                $deletedRemoteImagesByPath = [];

                foreach ($existingImages as $img) {
                    if (!in_array($img, $retainedImages)) {
                        if (strpos($img, 'uploads/') === 0) {
                            // Local image
                            if (file_exists(FCPATH . $img)) {
                                @unlink(FCPATH . $img);
                            }
                        } else {
                            // Remote image
                            $lastSlash = strrpos($img, '/');
                            if ($lastSlash !== false) {
                                $path = substr($img, 0, $lastSlash);
                                $filename = substr($img, $lastSlash + 1);
                                $deletedRemoteImagesByPath[$path][] = $filename;
                            }
                        }
                    }
                }

                // Delete remote images if any
                if (!empty($deletedRemoteImagesByPath) && $upload_server_delete_url) {
                    $client = \Config\Services::curlrequest();
                    foreach ($deletedRemoteImagesByPath as $path => $files) {
                        try {
                            $response = $client->request('POST', $upload_server_delete_url, [
                                'headers' => ['X-Auth-Token' => 'Dekpiano2025!!'],
                                'verify' => false,
                                'json' => [
                                    'files' => $files,
                                    'path' => $path
                                ]
                            ]);
                            if ($response->getStatusCode() !== 200) {
                                log_message('error', 'Failed to delete remote images: ' . $response->getBody());
                            }
                        } catch (\Throwable $e) {
                            log_message('error', 'Exception during remote old image deletion: ' . $e->getMessage());
                        }
                    }
                }

                // Final image array
                $finalImages = array_merge($retainedImages, $uploadedImages);
                $data['images'] = json_encode($finalImages);

                // Update
                $builder->where('id', $id)->update($data);
                return $this->response->setJSON(['status' => 'success', 'message' => 'บันทึกการแก้ไขข้อมูลเรียบร้อยแล้ว']);
            }
            return $this->response->setJSON(['status' => 'error', 'message' => 'ไม่พบข้อมูลที่ต้องการแก้ไข']);
        } else {
            // Create
            $data['images'] = json_encode($uploadedImages);
            $builder->insert($data);
            return $this->response->setJSON(['status' => 'success', 'message' => 'เพิ่มข้อมูลทะเบียนคุมเรียบร้อยแล้ว']);
        }
    }

    public function deleteOrder($id)
    {
        $db = \Config\Database::connect();
        $builder = $db->table('tb_purchase_hire_orders');

        $order = $builder->where('id', $id)->get()->getRow();
        if ($order) {
            $images = $order->images ? json_decode($order->images, true) : [];
            $deletedRemoteImagesByPath = [];
            foreach ($images as $img) {
                if (strpos($img, 'uploads/') === 0) {
                    if (file_exists(FCPATH . $img)) {
                        @unlink(FCPATH . $img);
                    }
                } else {
                    $lastSlash = strrpos($img, '/');
                    if ($lastSlash !== false) {
                        $path = substr($img, 0, $lastSlash);
                        $filename = substr($img, $lastSlash + 1);
                        $deletedRemoteImagesByPath[$path][] = $filename;
                    }
                }
            }

            // Delete remote images
            $upload_server_delete_url = getenv('upload.server.delete.url');
            if (!empty($deletedRemoteImagesByPath) && $upload_server_delete_url) {
                $client = \Config\Services::curlrequest();
                foreach ($deletedRemoteImagesByPath as $path => $files) {
                    try {
                        $client->request('POST', $upload_server_delete_url, [
                            'headers' => ['X-Auth-Token' => 'Dekpiano2025!!'],
                            'verify' => false,
                            'json' => [
                                'files' => $files,
                                'path' => $path
                            ]
                        ]);
                    } catch (\Throwable $e) {
                        log_message('error', 'Exception during remote image deletion: ' . $e->getMessage());
                    }
                }
            }

            $builder->where('id', $id)->delete();
            return $this->response->setJSON(['status' => 'success', 'message' => 'ลบข้อมูลเรียบร้อยแล้ว']);
        }
        return $this->response->setJSON(['status' => 'error', 'message' => 'ไม่พบข้อมูลที่ต้องการลบ']);
    }

    public function printOrder($id)
    {
        $db = \Config\Database::connect();
        $order = $db->table('tb_purchase_hire_orders')->where('id', $id)->get()->getRow();

        if (!$order) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('ไม่พบรายการที่ระบุ: ' . $id);
        }

        $data['order'] = $order;
        $data['title'] = 'พิมพ์รายงานใบ' . $order->order_type;
        $data['logo_school'] = 'https://skj.ac.th/uploads/logoSchool/LogoSKJ_4.png';

        return view('Admin/AdminHome/PrintOrderReport', $data);
    }

    public function User()
    {
        $session = session();
        $data = $this->DataMain();
        $data['title']="หน้าแรก";

        

        return view('User/UserLeyout/UserHeader',$data)
                .view('User/UserLeyout/UserMenuLeft')
                .view('User/UserHome/UserPageHome')
                .view('User/UserLeyout/UserFooter');
    }    

}
