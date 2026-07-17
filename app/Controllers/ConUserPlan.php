<?php

namespace App\Controllers;

class ConUserPlan extends BaseController
{  

    public function DataMain(){
        $data['full_url'] = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http') . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
        $data['uri'] = service('uri'); 
        return $data;
    }

    public function index()
    {
        $data = $this->DataMain();
        $data['title'] = "แผนดำเนินงานและแผนปฏิบัติการ";
        $data['description'] = "หน้าแสดงเอกสารแผนดำเนินงานและแผนปฏิบัติการ";
        
        $data['UrlMenuMain'] = '';
        $data['UrlMenuSub'] = 'Plan';

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
                .view('User/UserLeyout/UserMenuLeft', $data)
                .view('User/UserPlan/UserPlanPage', $data)
                .view('User/UserLeyout/UserFooter', $data);
    }
}
