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
        $data['title'] = "แผนและโครงการ";
        $data['description'] = "เอกสารแผนปฏิบัติการและประกาศแผนงาน โรงเรียนสวนกุหลาบวิทยาลัย (จิรประวัติ) นครสวรรค์";
        
        $data['UrlMenuMain'] = 'Plan';
        $data['UrlMenuSub'] = 'Plan';

        // ดึงเอกสารแผนและโครงการจากระบบประชาสัมพันธ์ (Public News) หมวด "แผนงานและโครงการ"
        $now = date('Y-m-d H:i:s');
        $planNews = $this->db->table('tb_public_news')
            ->where('status', 'published')
            ->where('category', 'แผนงานและโครงการ')
            ->groupStart()
                ->where('published_at <=', $now)
                ->orWhere('published_at IS NULL', null, false)
            ->groupEnd()
            ->groupStart()
                ->where('expires_at >=', $now)
                ->orWhere('expires_at IS NULL', null, false)
            ->groupEnd()
            ->orderBy('published_at', 'DESC')
            ->orderBy('id', 'DESC')
            ->get()
            ->getResult();

        $planNewsIds = array_map(function($item) { return (int)$item->id; }, $planNews);
        $filesByNews = [];
        if (!empty($planNewsIds)) {
            $planFilesQuery = $this->db->table('tb_public_news_files')
                ->whereIn('news_id', $planNewsIds)
                ->orderBy('id', 'ASC')
                ->get()
                ->getResult();
            foreach ($planFilesQuery as $f) {
                $f->type_info = get_file_type_info($f->original_name);
                $filesByNews[(int)$f->news_id][] = $f;
            }
        }
        $data['plan_news'] = $planNews;
        $data['plan_news_files'] = $filesByNews;

        // ดึงไฟล์เอกสารแผนปฏิบัติการ (Local folder fallback)
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
