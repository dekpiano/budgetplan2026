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
        $session = session();
        $data = $this->DataMain();
        $data['title'] = "หน้าแรก (แดชบอร์ดงบประมาณ)";
        
        $db = \Config\Database::connect();
        $builder = $db->table('tb_purchase_hire_orders');
        
        // Fetch 5 recent orders
        $data['recent_orders'] = $builder->orderBy('order_date', 'DESC')->orderBy('id', 'DESC')->limit(5)->get()->getResult();
        
        // Calculate stats
        $totalAmountResult = $db->query("SELECT SUM(amount) AS total FROM tb_purchase_hire_orders")->getRow();
        $data['total_amount'] = $totalAmountResult ? ($totalAmountResult->total ?: 0) : 0;
        
        $data['total_orders'] = $builder->countAllResults(false);
        
        $sentResult = $db->query("SELECT COUNT(*) AS total FROM tb_purchase_hire_orders WHERE system_sent_status = 'ส่งลงระบบ/ทำฎีกาแล้ว'")->getRow();
        $data['total_sent'] = $sentResult ? $sentResult->total : 0;
        
        // Monthly chart data (budget utilization)
        $chartQuery = $db->query("SELECT MONTH(order_date) as month, SUM(amount) as total FROM tb_purchase_hire_orders GROUP BY MONTH(order_date) ORDER BY MONTH(order_date) ASC")->getResult();
        $monthlyTotals = array_fill(1, 12, 0);
        foreach ($chartQuery as $row) {
            $monthlyTotals[intval($row->month)] = floatval($row->total);
        }
        $data['monthly_totals'] = array_values($monthlyTotals);
      
        return view('Admin/AdminLeyout/AdminHeader',$data)
                .view('Admin/AdminLeyout/AdminMenuLeft')
                .view('Admin/AdminHome/AdminPageHome')
                .view('Admin/AdminLeyout/AdminFooter');
    }

    public function registry($type = 'purchase')
    {
        $session = session();
        $data = $this->DataMain();
        
        $type = strtolower($type);
        $typeName = ($type === 'hire') ? 'สั่งจ้าง' : 'สั่งซื้อ';
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
                .view('Admin/AdminLeyout/AdminMenuLeft')
                .view('Admin/AdminHome/AdminPageRegistry')
                .view('Admin/AdminLeyout/AdminFooter');
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
