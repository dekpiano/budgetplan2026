<?php

namespace App\Controllers;

class ConPublicNews extends BaseController
{
    protected $db;

    public function __construct()
    {
        $this->db = \Config\Database::connect();
    }

    private function layoutData($title)
    {
        return [
            'title' => $title,
            'description' => 'ประชาสัมพันธ์และเอกสารเผยแพร่ ระบบบริหารงานงบประมาณและแผน',
            'UrlMenuMain' => 'News',
            'UrlMenuSub' => 'News',
            'uri' => service('uri'),
        ];
    }

    public function index()
    {
        $q = trim((string) $this->request->getGet('q'));
        $category = trim((string) $this->request->getGet('category'));
        $now = date('Y-m-d H:i:s');

        $b = $this->db->table('tb_public_news')
            ->where('status', 'published')
            ->groupStart()
                ->where('published_at <=', $now)
                ->orWhere('published_at IS NULL', null, false)
            ->groupEnd()
            ->groupStart()
                ->where('expires_at >=', $now)
                ->orWhere('expires_at IS NULL', null, false)
            ->groupEnd();

        if ($q !== '') {
            $b->groupStart()->like('title', $q)->orLike('content', $q)->groupEnd();
        }
        if ($category !== '') {
            $b->where('category', $category);
        }

        $news = $b->orderBy('published_at', 'DESC')->orderBy('id', 'DESC')->get()->getResult();

        // ดึงประเภทไฟล์แนบของแต่ละข่าวเพื่อนำไปแสดง Badge บอกประเภทไฟล์
        $fileTypesByNews = [];
        $fileCountsByNews = [];
        $filesByNews = [];
        $files = $this->db->table('tb_public_news_files')->get()->getResult();
        foreach ($files as $f) {
            $nid = (int) $f->news_id;
            $fileCountsByNews[$nid] = ($fileCountsByNews[$nid] ?? 0) + 1;
            $typeInfo = get_file_type_info($f->original_name);
            
            $f->type_info = $typeInfo;
            $filesByNews[$nid][] = $f;

            if (!isset($fileTypesByNews[$nid])) {
                $fileTypesByNews[$nid] = [];
            }
            if (!in_array($typeInfo, $fileTypesByNews[$nid], true)) {
                $fileTypesByNews[$nid][] = $typeInfo;
            }
        }

        $data = $this->layoutData('ประชาสัมพันธ์และเอกสารเผยแพร่ - ระบบงบประมาณและแผน');
        $data['news'] = $news;
        $data['keyword'] = $q;
        $data['category'] = $category;
        $data['fileTypesByNews'] = $fileTypesByNews;
        $data['fileCountsByNews'] = $fileCountsByNews;
        $data['filesByNews'] = $filesByNews;
        $data['categories'] = $this->db->table('tb_public_news')
            ->select('category')
            ->distinct()
            ->where('status', 'published')
            ->orderBy('category')
            ->get()
            ->getResult();

        return view('User/UserLeyout/UserHeader', $data)
            . view('User/UserLeyout/UserMenuLeft', $data)
            . view('User/UserNews/PublicNewsMain', $data)
            . view('User/UserLeyout/UserFooter', $data);
    }

    public function view($id)
    {
        $now = date('Y-m-d H:i:s');
        $news = $this->db->table('tb_public_news')
            ->where('id', (int) $id)
            ->where('status', 'published')
            ->groupStart()
                ->where('published_at <=', $now)
                ->orWhere('published_at IS NULL', null, false)
            ->groupEnd()
            ->groupStart()
                ->where('expires_at >=', $now)
                ->orWhere('expires_at IS NULL', null, false)
            ->groupEnd()
            ->get()
            ->getRow();

        if (!$news) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('ไม่พบเอกสารประชาสัมพันธ์');
        }

        $this->db->table('tb_public_news')->where('id', $news->id)->set('view_count', 'view_count + 1', false)->update();

        $newsFiles = $this->db->table('tb_public_news_files')
            ->where('news_id', $news->id)
            ->orderBy('id', 'ASC')
            ->get()
            ->getResult();

        foreach ($newsFiles as $f) {
            $f->url = remote_upload_url($f->file_path);
            $f->type_info = get_file_type_info($f->original_name);
        }
        $news->files = $newsFiles;

        $data = $this->layoutData($news->title);
        $data['news'] = $news;

        return view('User/UserLeyout/UserHeader', $data)
            . view('User/UserLeyout/UserMenuLeft', $data)
            . view('User/UserNews/PublicNewsView', $data)
            . view('User/UserLeyout/UserFooter', $data);
    }

    public function download($fileId)
    {
        $file = $this->db->table('tb_public_news_files f')
            ->select('f.*, n.status, n.published_at, n.expires_at')
            ->join('tb_public_news n', 'n.id = f.news_id')
            ->where('f.id', (int) $fileId)
            ->get()
            ->getRow();

        $now = date('Y-m-d H:i:s');
        if (
            !$file ||
            $file->status !== 'published' ||
            ($file->published_at && $file->published_at > $now) ||
            ($file->expires_at && $file->expires_at < $now)
        ) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('ไม่พบไฟล์');
        }

        $downloadUrl = remote_upload_url($file->file_path);
        if (empty($downloadUrl)) {
            log_message('error', '[NewsDownload] remote_upload_url is empty.');
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('ไม่พบแหล่งจัดเก็บไฟล์');
        }

        $this->db->table('tb_public_news_files')
            ->where('id', $file->id)
            ->set('download_count', 'download_count + 1', false)
            ->update();

        return redirect()->to($downloadUrl);
    }
}