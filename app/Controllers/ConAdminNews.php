<?php

namespace App\Controllers;

class ConAdminNews extends BaseController
{
    protected $db;

    public function __construct()
    {
        $this->db = \Config\Database::connect();
    }

    private function guard()
    {
        return auth_can_access_system('admin_news') ? null : redirect()->to(base_url('LoginOfficerBudgetPlan'));
    }

    public function index()
    {
        if ($r = $this->guard()) {
            return $r;
        }

        $news = $this->db->table('tb_public_news')
            ->orderBy('id', 'DESC')
            ->get()
            ->getResult();

        $fileCounts = [];
        $filesByNews = [];
        $fileTypesByNews = [];

        $allFiles = $this->db->table('tb_public_news_files')
            ->orderBy('id', 'ASC')
            ->get()
            ->getResult();

        foreach ($allFiles as $row) {
            $nid = (int) $row->news_id;
            $fileCounts[$nid] = ($fileCounts[$nid] ?? 0) + 1;
            $typeInfo = get_file_type_info($row->original_name);

            $filesByNews[$nid][] = [
                'id' => (int) $row->id,
                'news_id' => $nid,
                'original_name' => $row->original_name,
                'stored_name' => $row->stored_name,
                'file_path' => $row->file_path,
                'file_size' => (int) $row->file_size,
                'file_size_formatted' => $this->formatFileSize((int) $row->file_size),
                'url' => remote_upload_url($row->file_path),
                'download_count' => (int) $row->download_count,
                'type_info' => $typeInfo,
            ];

            if (!isset($fileTypesByNews[$nid])) {
                $fileTypesByNews[$nid] = [];
            }
            if (!in_array($typeInfo, $fileTypesByNews[$nid], true)) {
                $fileTypesByNews[$nid][] = $typeInfo;
            }
        }

        $data = [
            'title' => 'จัดการประชาสัมพันธ์',
            'uri' => service('uri'),
            'news' => $news,
            'fileCounts' => $fileCounts,
            'filesByNews' => $filesByNews,
            'fileTypesByNews' => $fileTypesByNews,
        ];

        return view('Admin/AdminLeyout/AdminHeader', $data)
            . view('Admin/AdminLeyout/AdminMenuLeft', $data)
            . view('Admin/AdminNews/AdminNewsMain', $data)
            . view('Admin/AdminLeyout/AdminFooter', $data);
    }

    /**
     * ดึงรายการไฟล์แนบของข่าวรายการนั้นๆ (AJAX)
     */
    public function getFiles($id)
    {
        if (!auth_can_access_system('admin_news')) {
            return $this->response->setStatusCode(401)->setJSON(['status' => 'error', 'message' => 'Unauthorized']);
        }

        $files = $this->db->table('tb_public_news_files')
            ->where('news_id', (int) $id)
            ->orderBy('id', 'ASC')
            ->get()
            ->getResult();

        $result = [];
        foreach ($files as $f) {
            $typeInfo = get_file_type_info($f->original_name);
            $result[] = [
                'id' => (int) $f->id,
                'news_id' => (int) $f->news_id,
                'original_name' => $f->original_name,
                'file_path' => $f->file_path,
                'stored_name' => $f->stored_name,
                'file_size' => (int) $f->file_size,
                'file_size_formatted' => $this->formatFileSize((int) $f->file_size),
                'mime_type' => $f->mime_type,
                'download_count' => (int) $f->download_count,
                'url' => remote_upload_url($f->file_path),
                'type_info' => $typeInfo,
                'created_at' => !empty($f->created_at) ? date('d/m/Y H:i', strtotime($f->created_at)) : '-',
            ];
        }

        return $this->response->setJSON([
            'status' => 'success',
            'files' => $result,
        ]);
    }

    /**
     * ลบไฟล์แนบเดี่ยว (AJAX / POST)
     */
    public function deleteFile($fileId)
    {
        if (!auth_can_access_system('admin_news')) {
            return $this->response->setStatusCode(401)->setJSON(['status' => 'error', 'message' => 'Unauthorized']);
        }

        $file = $this->db->table('tb_public_news_files')
            ->where('id', (int) $fileId)
            ->get()
            ->getRow();

        if (!$file) {
            return $this->response->setStatusCode(404)->setJSON(['status' => 'error', 'message' => 'ไม่พบไฟล์ที่ระบุ']);
        }

        // ลบไฟล์จาก Remote Upload Server
        $remotePath = 'budgetplan/news/' . $file->news_id;
        $this->deleteFromRemoteServer($remotePath, [$file->stored_name]);

        // ลบข้อมูลออกจากฐานข้อมูล
        $this->db->table('tb_public_news_files')
            ->where('id', (int) $fileId)
            ->delete();

        return $this->response->setJSON([
            'status' => 'success',
            'message' => 'ลบไฟล์เรียบร้อยแล้ว',
        ]);
    }

    public function save()
    {
        if ($r = $this->guard()) {
            return $r;
        }

        $id = (int) $this->request->getPost('id');
        $now = date('Y-m-d H:i:s');
        $status = $this->request->getPost('status') ?: 'draft';

        if (!in_array($status, ['draft', 'published', 'closed'], true)) {
            $status = 'draft';
        }

        $publishedAt = $this->request->getPost('published_at');
        if ($publishedAt) {
            $publishedAt = date('Y-m-d H:i:s', strtotime($publishedAt));
        } else {
            $publishedAt = ($status === 'published') ? $now : null;
        }

        $expiresAt = $this->request->getPost('expires_at');
        if ($expiresAt) {
            $expiresAt = date('Y-m-d H:i:s', strtotime($expiresAt));
        } else {
            $expiresAt = null;
        }

        $payload = [
            'title' => trim((string) $this->request->getPost('title')),
            'content' => (string) $this->request->getPost('content'),
            'category' => trim((string) $this->request->getPost('category')) ?: 'ประชาสัมพันธ์',
            'published_at' => $publishedAt,
            'expires_at' => $expiresAt,
            'status' => $status,
            'updated_at' => $now,
        ];

        if ($payload['title'] === '') {
            return redirect()->back()->with('error', 'กรุณาระบุหัวข้อประชาสัมพันธ์');
        }

        // บันทึกข้อมูลข่าว
        if ($id > 0) {
            $this->db->table('tb_public_news')->where('id', $id)->update($payload);
        } else {
            $payload['created_at'] = $now;
            $payload['created_by'] = (string) (session()->get('pers_id') ?? session()->get('user_id') ?? session()->get('username') ?? '');
            $this->db->table('tb_public_news')->insert($payload);
            $id = (int) $this->db->insertID();
        }

        // จัดการไฟล์เอกสารแนบ (Attachments)
        $files = $this->request->getFileMultiple('files');
        $uploadedCount = 0;
        $failedCount = 0;

        if ($files && is_array($files)) {
            $allowed = ['pdf', 'doc', 'docx', 'xls', 'xlsx', 'ppt', 'pptx', 'jpg', 'jpeg', 'png', 'gif', 'webp', 'zip', 'rar', 'txt', 'csv'];
            $remotePath = 'budgetplan/news/' . $id;

            foreach ($files as $file) {
                if (
                    !$file ||
                    !$file->isValid() ||
                    $file->hasMoved() ||
                    $file->getSize() === 0 ||
                    $file->getSize() > 50 * 1024 * 1024
                ) {
                    continue;
                }

                $ext = strtolower($file->getExtension());
                if (!in_array($ext, $allowed, true)) {
                    continue;
                }

                $original = $file->getClientName();
                $size = $file->getSize();
                $mime = $file->getClientMimeType();

                $nameWithoutExt = pathinfo($original, PATHINFO_FILENAME);
                $sanitizedName = preg_replace('/[^\w-]/', '_', $nameWithoutExt);
                $sanitizedName = trim(preg_replace('/_+/', '_', $sanitizedName), '_');
                $stored = ($sanitizedName ?: 'doc') . '-' . uniqid() . '.' . $ext;

                $uploaded = $this->uploadToRemoteServer(
                    $file->getTempName(),
                    $mime,
                    $stored,
                    $remotePath
                );

                if ($uploaded) {
                    $remoteFilePath = $remotePath . '/' . $uploaded;
                    $this->db->table('tb_public_news_files')->insert([
                        'news_id' => $id,
                        'original_name' => $original,
                        'stored_name' => $uploaded,
                        'file_path' => $remoteFilePath,
                        'mime_type' => $mime,
                        'file_size' => $size,
                        'download_count' => 0,
                        'created_at' => $now,
                    ]);
                    $uploadedCount++;
                } else {
                    $failedCount++;
                }
            }
        }

        $msg = 'บันทึกประชาสัมพันธ์เรียบร้อยแล้ว';
        if ($uploadedCount > 0) {
            $msg .= " (อัปโหลดไฟล์แนบ {$uploadedCount} ไฟล์)";
        }
        if ($failedCount > 0) {
            $msg .= " แต่มีไฟล์อัปโหลดไม่สำเร็จ {$failedCount} ไฟล์";
        }

        if ($this->request->isAJAX()) {
            // Set session flashdata so that it shows upon reload
            session()->setFlashdata('success', $msg);
            return $this->response->setJSON([
                'status' => 'success',
                'message' => $msg,
                'news_id' => $id,
                'csrfToken' => csrf_hash()
            ]);
        }

        return redirect()->to(base_url('Admin/News'))->with('success', $msg);
    }

    public function uploadChunk()
    {
        if ($r = $this->guard()) return $r;

        $newsId = (int)$this->request->getPost('news_id');
        $fileName = $this->request->getPost('file_name');
        $chunkIndex = (int)$this->request->getPost('chunk_index');
        $totalChunks = (int)$this->request->getPost('total_chunks');
        
        $file = $this->request->getFile('chunk');

        if (!$file || !$file->isValid()) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Invalid chunk']);
        }

        $remotePath = 'budgetplan/news/' . $newsId;
        
        // กำหนดชื่อไฟล์เป้าหมาย (ทำครั้งเดียวให้เหมือนกันทุก Chunk)
        $ext = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
        $nameWithoutExt = pathinfo($fileName, PATHINFO_FILENAME);
        $sanitizedName = preg_replace('/[^\w-]/', '_', $nameWithoutExt);
        $sanitizedName = trim(preg_replace('/_+/', '_', $sanitizedName), '_');
        
        // ใช้ md5 ของ id และชื่อไฟล์เดิม เพื่อให้ทุก chunk ส่งชื่อไฟล์เดียวกันไปให้ remote server ประกอบ
        $stored = ($sanitizedName ?: 'doc') . '-' . md5($newsId . '_' . $fileName) . '.' . $ext;
        
        $mime = $file->getClientMimeType();
        $tempPath = $file->getTempName();

        $errorMessage = '';
        
        // ส่ง Chunk ไปให้ Remote Server ทำการประกอบไฟล์โดยตรง
        $uploaded = $this->uploadToRemoteServer($tempPath, $mime, $stored, $remotePath, $errorMessage, $chunkIndex, $totalChunks);

        if ($chunkIndex == $totalChunks - 1) {
            // ถ้านี่คือ Chunk สุดท้าย
            if ($uploaded && $uploaded !== 'chunk_saved') {
                $remoteFilePath = $remotePath . '/' . $uploaded;
                $this->db->table('tb_public_news_files')->insert([
                    'news_id' => $newsId,
                    'original_name' => $fileName,
                    'stored_name' => $uploaded,
                    'file_path' => $remoteFilePath,
                    'mime_type' => $mime,
                    'file_size' => 0, // ไม่ทราบขนาดรวมที่แท้จริงจาก Chunk สุดท้าย แต่ในหน้าเว็บแสดงจากฝั่ง Client ได้
                    'download_count' => 0,
                    'created_at' => date('Y-m-d H:i:s'),
                ]);
                return $this->response->setJSON(['status' => 'success', 'completed' => true, 'csrfToken' => csrf_hash()]);
            } else {
                return $this->response->setJSON(['status' => 'error', 'message' => 'Failed to upload final chunk: ' . $errorMessage, 'csrfToken' => csrf_hash()]);
            }
        } else {
            // ถ้าเป็น Chunk ทั่วไป
            if ($uploaded === 'chunk_saved') {
                return $this->response->setJSON(['status' => 'success', 'completed' => false, 'csrfToken' => csrf_hash()]);
            } else {
                return $this->response->setJSON(['status' => 'error', 'message' => 'Failed to upload chunk: ' . $errorMessage, 'csrfToken' => csrf_hash()]);
            }
        }
    }

    public function delete($id)
    {
        if ($r = $this->guard()) {
            return $r;
        }

        $newsId = (int) $id;
        $news = $this->db->table('tb_public_news')->where('id', $newsId)->get()->getRow();

        if (!$news) {
            return redirect()->to(base_url('Admin/News'))->with('error', 'ไม่พบประชาสัมพันธ์รายการนี้');
        }

        // 1. ดึงไฟล์แนบทั้งหมดของข่าวนี้
        $files = $this->db->table('tb_public_news_files')
            ->where('news_id', $newsId)
            ->get()
            ->getResult();

        if (!empty($files)) {
            $storedFilenames = [];
            foreach ($files as $f) {
                if (!empty($f->stored_name)) {
                    $storedFilenames[] = $f->stored_name;
                }
            }
            if (!empty($storedFilenames)) {
                $this->deleteFromRemoteServer('budgetplan/news/' . $newsId, $storedFilenames);
            }

            // ลบจากฐานข้อมูล
            $this->db->table('tb_public_news_files')->where('news_id', $newsId)->delete();
        }

        // 2. ลบข่าวออกจากตาราง
        $this->db->table('tb_public_news')->where('id', $newsId)->delete();

        return redirect()->to(base_url('Admin/News'))->with('success', 'ลบประชาสัมพันธ์และไฟล์ที่เกี่ยวข้องเรียบร้อยแล้ว');
    }

    /**
     * ฟังก์ชันอัปโหลดไฟล์ไปยัง Remote Upload Server ด้วย Native cURL
     */
    private function uploadToRemoteServer(string $tempPath, string $mimeType, string $storedFilename, string $remotePath, &$errorMessage = null, $chunkIndex = null, $totalChunks = null): ?string
    {
        $uploadUrl = env('upload.server.url') ?: getenv('upload.server.url') ?: 'https://skj.nsnpao.go.th/upload.php';
        $token = 'Dekpiano2025!!';

        if (!file_exists($tempPath)) {
            $errorMessage = "Temp file not found: {$tempPath}";
            log_message('error', "[NewsUpload] Temp file not found: {$tempPath}");
            return null;
        }

        $host = parse_url($uploadUrl, PHP_URL_HOST);
        $ip = gethostbyname($host);

        $ch = curl_init($uploadUrl);
        
        $postFields = [
            'file' => new \CURLFile($tempPath, $mimeType, $storedFilename),
            'path' => $remotePath,
            'desired_filename' => $storedFilename,
        ];
        
        if ($chunkIndex !== null && $totalChunks !== null) {
            $postFields['chunk_index'] = $chunkIndex;
            $postFields['total_chunks'] = $totalChunks;
            $postFields['filename'] = $storedFilename;
        }

        $options = [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER => ['X-Auth-Token: ' . $token],
            CURLOPT_POST => true,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_IPRESOLVE => CURL_IPRESOLVE_V4,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_TIMEOUT => 300,
            CURLOPT_CONNECTTIMEOUT => 60,
            CURLOPT_POSTFIELDS => $postFields,
        ];

        if ($ip && $ip !== $host) {
            $port = parse_url($uploadUrl, PHP_URL_PORT) ?: (parse_url($uploadUrl, PHP_URL_SCHEME) === 'https' ? 443 : 80);
            $options[CURLOPT_RESOLVE] = ["{$host}:{$port}:{$ip}"];
        }

        curl_setopt_array($ch, $options);

        $raw = curl_exec($ch);
        $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $err = curl_error($ch);
        curl_close($ch);

        if ($err) {
            $errorMessage = "cURL Error: {$err}";
            log_message('error', "[NewsUpload] cURL error: {$err}");
            return null;
        }

        if ($code !== 200) {
            // Check if it's Nginx 413
            if (strpos($raw, '413 Request Entity Too Large') !== false) {
                $errorMessage = "Remote Server Error: 413 Request Entity Too Large (ไฟล์มีขนาดใหญ่เกินกว่าที่เซิร์ฟเวอร์ปลายทางกำหนด)";
            } else {
                $errorMessage = "Remote Server HTTP {$code}: " . strip_tags($raw);
            }
            log_message('error', "[NewsUpload] Remote server returned HTTP {$code}: {$raw}");
            return null;
        }

        $res = json_decode((string) $raw, true);
        
        if (is_array($res) && isset($res['status']) && $res['status'] === 'chunk_saved') {
            return 'chunk_saved';
        }

        if (!is_array($res) || ($res['status'] ?? '') !== 'success' || empty($res['filename'])) {
            $errorMessage = "Remote Server JSON Error: " . (isset($res['message']) ? $res['message'] : strip_tags($raw));
            log_message('error', "[NewsUpload] Remote upload response failure: {$raw}");
            return null;
        }

        return (string) $res['filename'];
    }

    /**
     * ฟังก์ชันลบไฟล์จาก Remote Upload Server ด้วย Native cURL
     */
    private function deleteFromRemoteServer(string $remotePath, array $filenames): bool
    {
        if (empty($filenames)) {
            return true;
        }

        $deleteUrl = env('upload.server.delete.url') ?: getenv('upload.server.delete.url') ?: 'https://skj.nsnpao.go.th/delete.php';
        $token = 'Dekpiano2025!!';

        $host = parse_url($deleteUrl, PHP_URL_HOST);
        $ip = gethostbyname($host);

        $ch = curl_init($deleteUrl);
        $options = [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER => [
                'X-Auth-Token: ' . $token,
                'Content-Type: application/json',
            ],
            CURLOPT_POST => true,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_IPRESOLVE => CURL_IPRESOLVE_V4,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_POSTFIELDS => json_encode([
                'path' => $remotePath,
                'files' => array_values($filenames),
            ]),
        ];

        if ($ip && $ip !== $host) {
            $port = parse_url($deleteUrl, PHP_URL_PORT) ?: (parse_url($deleteUrl, PHP_URL_SCHEME) === 'https' ? 443 : 80);
            $options[CURLOPT_RESOLVE] = ["{$host}:{$port}:{$ip}"];
        }

        curl_setopt_array($ch, $options);

        $raw = curl_exec($ch);
        $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $err = curl_error($ch);
        curl_close($ch);

        if ($err || $code !== 200) {
            log_message('error', "[NewsDelete] Remote delete failed: code={$code}, err={$err}, body={$raw}");
            return false;
        }

        return true;
    }

    /**
     * แปลงขนาดไบต์เป็นรูปแบบที่อ่านง่าย (KB, MB)
     */
    private function formatFileSize(int $bytes): string
    {
        if ($bytes >= 1048576) {
            return number_format($bytes / 1048576, 2) . ' MB';
        }
        if ($bytes >= 1024) {
            return number_format($bytes / 1024, 1) . ' KB';
        }
        return $bytes . ' B';
    }
}
