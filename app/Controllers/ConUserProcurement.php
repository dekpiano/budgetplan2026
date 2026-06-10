<?php

namespace App\Controllers;

class ConUserProcurement extends BaseController
{  

    function __construct(){
       
    }


    public function DataMain(){
        $data['full_url'] = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http') . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
   
        $data['uri'] = service('uri'); 
        return $data;
    }

    public function ProcurementProcess()
    {
        $session = session();
        $database = \Config\Database::connect('personnel');
        $builder = $database->table('tb_personnel');

        $data = $this->DataMain();
        $data['title']="ขั้นตอนการจัดซื้อ / จัดจ้าง";
        $data['description']="ระบบบริหารงานงบประมาณและแผน";
        $data['UrlMenuMain'] = 'Procurement';
        $data['UrlMenuSub'] = 'Process';

        return view('User/UserLeyout/UserHeader',$data)
                .view('User/UserLeyout/UserMenuLeft')
                .view('User/UserProcurement/ProcurementProcess')
                .view('User/UserLeyout/UserFooter');
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
        $data['description'] = "ระบบบริหารงานงบประมาณและแผน";
        $data['UrlMenuMain'] = 'Procurement';
        $data['UrlMenuSub'] = ($type === 'hire') ? 'Hire' : 'Purchase';
        
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

        // ส่งข้อมูล session ไปยัง view สำหรับตรวจสอบสิทธิ์ผู้ตรวจรับ
        $data['session_username'] = $session->get('username');
        $data['session_logged_in'] = $session->get('logged_in') === true;
        $data['session_login_type'] = $session->get('login_type') ?: 'admin';
        $data['auto_open_upload'] = $session->get('auto_open_upload') === true;
        // เคลียร์ flag หลังอ่านแล้ว เพื่อไม่ให้เปิด modal ซ้ำตอน refresh
        $session->remove('auto_open_upload');

        return view('User/UserLeyout/UserHeader',$data)
                .view('User/UserLeyout/UserMenuLeft')
                .view('User/UserProcurement/UserPageRegistry')
                .view('User/UserLeyout/UserFooter');
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

    /**
     * ลบไฟภาพเก่าทั้งหมดของรายการ (ทั้ง local และ remote)
     * เรียกก่อนอัปโหลดรูปใหม่ (replace mode)
     */
    public function replaceImages($orderId)
    {
        $session = session();

        if (!$session->get('logged_in')) {
            return $this->response->setJSON(['success' => false, 'message' => 'กรุณาเข้าสู่ระบบ']);
        }

        $db = \Config\Database::connect();
        $order = $db->table('tb_purchase_hire_orders')->where('id', $orderId)->get()->getRow();

        if (!$order) {
            return $this->response->setJSON(['success' => false, 'message' => 'ไม่พบรายการ']);
        }

        $sessionUsername = $session->get('username');
        if (!$sessionUsername || $order->inspection_status !== $sessionUsername) {
            return $this->response->setJSON(['success' => false, 'message' => 'ไม่มีสิทธิ์']);
        }

        // ลบไฟล์เก่าทั้งหมด
        $images = $order->images ? json_decode($order->images, true) : [];
        if (is_array($images)) {
            $deletedRemoteByPath = [];
            foreach ($images as $img) {
                if (strpos($img, 'uploads/') === 0) {
                    // Local file
                    $localPath = FCPATH . $img;
                    if (file_exists($localPath)) {
                        @unlink($localPath);
                    }
                } else {
                    // Remote file
                    $lastSlash = strrpos($img, '/');
                    if ($lastSlash !== false) {
                        $path = substr($img, 0, $lastSlash);
                        $filename = substr($img, $lastSlash + 1);
                        $deletedRemoteByPath[$path][] = $filename;
                    }
                }
            }

            // ลบ remote files
            $deleteUrl = getenv('upload.server.delete.url');
            if (!empty($deletedRemoteByPath) && $deleteUrl) {
                $client = \Config\Services::curlrequest();
                foreach ($deletedRemoteByPath as $path => $files) {
                    try {
                        $client->request('POST', $deleteUrl, [
                            'headers' => ['X-Auth-Token' => 'Dekpiano2025!!'],
                            'verify'  => false,
                            'json'    => ['files' => $files, 'path' => $path]
                        ]);
                    } catch (\Throwable $e) {
                        log_message('error', '[ReplaceImages] Delete remote failed: ' . $e->getMessage());
                    }
                }
            }
        }

        // เคลียร์ images ใน DB
        $db->table('tb_purchase_hire_orders')
            ->where('id', $orderId)
            ->update(['images' => json_encode([])]);

        return $this->response->setJSON(['success' => true, 'message' => 'ลบไฟล์เก่าเรียบร้อย']);
    }

    /**
     * อัปโหลดรูปภาพสำหรับรายการสั่งซื้อ/สั่งจ้าง
     * เฉพาะผู้ที่เป็นผู้ตรวจรับของรายการนั้นเท่านั้นที่สามารถอัปโหลดได้
     * Upload ไปยัง remote server เหมือนระบบ admin
     */
    public function uploadImage($orderId)
    {
        $session = session();

        // ตรวจสอบว่า login แล้ว
        if (!$session->get('logged_in')) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'กรุณาเข้าสู่ระบบก่อนอัปโหลดรูปภาพ'
            ]);
        }

        $db = \Config\Database::connect();
        $order = $db->table('tb_purchase_hire_orders')->where('id', $orderId)->get()->getRow();

        if (!$order) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'ไม่พบรายการที่ระบุ'
            ]);
        }

        // ตรวจสอบว่าเป็นผู้ตรวจรับของรายการนี้
        $sessionUsername = $session->get('username');
        if (!$sessionUsername || $order->inspection_status !== $sessionUsername) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'คุณไม่มีสิทธิ์อัปโหลดรูปภาพในรายการนี้ เฉพาะผู้ตรวจรับเท่านั้นที่สามารถอัปโหลดได้'
            ]);
        }

        // ตรวจสอบว่ามีไฟล์ที่อัปโหลด
        $file = $this->request->getFile('image');
        if (!$file || !$file->isValid()) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'กรุณาเลือกไฟล์รูปภาพ'
            ]);
        }

        // ตรวจสอบประเภทไฟล์
        $allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/webp'];
        if (!in_array($file->getMimeType(), $allowedTypes)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'รองรับเฉพาะไฟล์รูปภาพ (JPG, JPEG, PNG, WEBP) เท่านั้น'
            ]);
        }

        // ไม่จำกัดขนาด — จะ compress ฝั่ง client ก่อนอัปโหลด

        // ตรวจสอบ upload server URL
        $uploadServerUrl = getenv('upload.server.url');
        if (!$uploadServerUrl) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Upload server URL ไม่ได้ตั้งค่า'
            ]);
        }

        // สร้างชื่อไฟล์
        $nameWithoutExt = pathinfo($file->getName(), PATHINFO_FILENAME);
        $sanitizedName = preg_replace('/[^\w-]/', '_', $nameWithoutExt);
        $sanitizedName = trim(preg_replace('/_+/', '_', $sanitizedName), '_');
        $finalName = ($sanitizedName ?: 'image') . '-' . uniqid() . '.jpg';

        // Upload ไป remote server
        $client = \Config\Services::curlrequest();
        $orderDate = $order->order_date ?: date('Y-m-d');
        $dateFolder = date('Y-m-d', strtotime($orderDate));

        try {
            $response = $client->request('POST', $uploadServerUrl, [
                'headers' => ['X-Auth-Token' => 'Dekpiano2025!!'],
                'verify'  => false,
                'multipart' => [
                    'file'             => new \CURLFile($file->getTempName(), $file->getMimeType(), $finalName),
                    'path'             => 'budgetplan/orders/' . $dateFolder,
                    'desired_filename' => $finalName,
                ]
            ]);

            if ($response->getStatusCode() !== 200) {
                log_message('error', '[InspectorUpload] Remote server error: ' . $response->getBody());
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'เกิดข้อผิดพลาดจาก upload server'
                ]);
            }

            $body = json_decode($response->getBody());
            if (!$body || !isset($body->status) || $body->status !== 'success' || !isset($body->filename)) {
                log_message('error', '[InspectorUpload] Upload failed: ' . $response->getBody());
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'อัปโหลดรูปภาพไม่สำเร็จ'
                ]);
            }

            $imagePath = 'budgetplan/orders/' . $dateFolder . '/' . $body->filename;

        } catch (\Exception $e) {
            log_message('error', '[InspectorUpload] Exception: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => 'เกิดข้อผิดพลาดในการอัปโหลด: ' . $e->getMessage()
            ]);
        }

        // อัปเดต field images ใน DB
        $existingImages = $order->images ? json_decode($order->images, true) : [];
        if (!is_array($existingImages)) {
            $existingImages = [];
        }
        $existingImages[] = $imagePath;

        $db->table('tb_purchase_hire_orders')
            ->where('id', $orderId)
            ->update(['images' => json_encode($existingImages)]);

        return $this->response->setJSON([
            'success'      => true,
            'message'      => 'อัปโหลดรูปภาพสำเร็จ',
            'image_path'   => $imagePath,
            'total_images' => count($existingImages)
        ]);
    }
}
