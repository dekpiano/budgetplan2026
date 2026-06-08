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
}
