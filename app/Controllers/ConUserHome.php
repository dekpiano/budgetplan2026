<?php

namespace App\Controllers;

class ConUserHome extends BaseController
{  

    function __construct(){
       
    }


    public function DataMain(){
        $data['full_url'] = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http') . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
   
        $data['uri'] = service('uri'); 
        return $data;
    }

    public function index()
    {
        $session = session();
        $database = \Config\Database::connect('personnel');
        $builder = $database->table('tb_personnel');

        $data = $this->DataMain();
        $data['title']="หน้าแรก";
        $data['description']="หน้าแรกระบบบริหารงานงบประมาณและแผน";
        $data['UrlMenuMain'] = 'Main';
        $data['UrlMenuSub'] = '';

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

        //$data['DictationAll'] = $builder->countAll();
       
       

        return view('User/UserLeyout/UserHeader',$data)
                .view('User/UserLeyout/UserMenuLeft')
                .view('User/UserHome/UserPageHome')
                .view('User/UserLeyout/UserFooter');
    }


  

    
}
