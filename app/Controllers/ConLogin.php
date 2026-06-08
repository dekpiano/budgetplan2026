<?php

namespace App\Controllers;

class ConLogin extends BaseController
{
    private $GoogleButton = "";

    function __construct(){
        $config = config('Google');
        $params = [
            'client_id'     => $config->clientId,
            'redirect_uri'  => $config->redirectUri,
            'response_type' => 'code',
            'scope'         => 'email profile openid',
            'access_type'   => 'online',
            'prompt'        => 'select_account'
        ];
        $authUrl = 'https://accounts.google.com/o/oauth2/v2/auth?' . http_build_query($params);
        $this->GoogleButton = '<a href="'.$authUrl.'" class="btn btn-primary me-3 w-auto"><i class="tf-icons bx bxl-google-plus"></i> Login by Google </a>';
    }

    public function DataMain(){
        $data['full_url'] = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http') . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
        $data['GoogleButton'] = $this->GoogleButton;
        $data['uri'] = service('uri'); 
        return $data;
    }

    public function LoginOfficerPersonnel(){
        $data = $this->DataMain();
        $data['title']="หน้าแรก";
        $data['description']="เข้าสู่ระบบ";
        $data['UrlMenuMain'] = 'LoginOfficerPersonnel';
        $data['UrlMenuSub'] = '';

        $session = session();

        // ถ้า login แล้ว redirect ไปหน้า admin เลย
        if ($session->get('logged_in')) {
            if (in_array($session->get('status'), ['admin', 'manager', 'superadmin'])) {
                return redirect()->to(base_url('Admin/Home'));
            }
        }

        if($this->request->getVar("return_to") == ""){

        }else{
            session()->set('Return',$this->request->getVar("return_to"));
        }

        // ตรวจสอบว่ามี code จาก Google OAuth หรือไม่
        $code = $this->request->getVar('code');
        log_message('info', '[GoogleLogin] Request URI: ' . current_url() . ' | code exists: ' . ($code ? 'YES' : 'NO'));

        if($code){
            log_message('info', '[GoogleLogin] Processing OAuth code...');
            $config = config('Google');
            $curl = \Config\Services::curlrequest();

            try {
                // 1. Exchange code for access_token
                log_message('info', '[GoogleLogin] Exchanging code for token. redirect_uri=' . $config->redirectUri);
                $response = $curl->post('https://oauth2.googleapis.com/token', [
                    'version' => 1.1,
                    'http_errors' => false,
                    'verify' => false,
                    'form_params' => [
                        'code'          => $code,
                        'client_id'     => $config->clientId,
                        'client_secret' => $config->clientSecret,
                        'redirect_uri'  => $config->redirectUri,
                        'grant_type'    => 'authorization_code',
                    ],
                ]);

                $tokenBody = $response->getBody();
                $tokenStatus = $response->getStatusCode();
                log_message('info', '[GoogleLogin] Token response status: ' . $tokenStatus);
                log_message('info', '[GoogleLogin] Token response body: ' . $tokenBody);

                if ($tokenStatus !== 200) {
                    $errorData = json_decode($tokenBody, true);
                    $errorMsg = $errorData['error_description'] ?? ($errorData['error'] ?? 'Unknown OAuth Error');
                    log_message('error', '[GoogleLogin] Token exchange failed: ' . $errorMsg);
                    $session->setFlashdata('Error', 'Google OAuth Error: ' . $errorMsg);
                    return redirect()->to(base_url('LoginOfficerPersonnel'));
                }

                $tokens = json_decode($tokenBody, true);

                if (!isset($tokens['access_token'])) {
                    log_message('error', '[GoogleLogin] No access_token in response');
                    $session->setFlashdata('Error', 'ไม่ได้รับ access_token จาก Google');
                    return redirect()->to(base_url('LoginOfficerPersonnel'));
                }

                // 2. ดึงข้อมูลผู้ใช้จาก id_token (JWT) - ไม่ต้องเรียก API เพิ่ม
                $googleData = null;
                if (isset($tokens['id_token'])) {
                    log_message('info', '[GoogleLogin] Decoding id_token JWT...');
                    $parts = explode('.', $tokens['id_token']);
                    if (count($parts) === 3) {
                        $payload = base64_decode(strtr($parts[1], '-_', '+/'));
                        $googleData = json_decode($payload, true);
                        log_message('info', '[GoogleLogin] JWT decoded: ' . json_encode($googleData));
                    }
                }

                // Fallback: ถ้า id_token ไม่มีหรือ decode ไม่ได้ ใช้ native PHP curl
                if (!$googleData || !isset($googleData['email'])) {
                    log_message('info', '[GoogleLogin] Fallback: using native PHP curl for userinfo...');
                    $ch = curl_init();
                    curl_setopt_array($ch, [
                        CURLOPT_URL => 'https://www.googleapis.com/oauth2/v3/userinfo',
                        CURLOPT_RETURNTRANSFER => true,
                        CURLOPT_HTTPHEADER => [
                            'Authorization: Bearer ' . $tokens['access_token'],
                        ],
                        CURLOPT_SSL_VERIFYPEER => false,
                        CURLOPT_SSL_VERIFYHOST => false,
                        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                        CURLOPT_TIMEOUT => 10,
                    ]);
                    $profileBody = curl_exec($ch);
                    $curlError = curl_error($ch);
                    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
                    curl_close($ch);
                    log_message('info', '[GoogleLogin] Native curl userinfo status: ' . $httpCode);
                    log_message('info', '[GoogleLogin] Native curl userinfo body: ' . $profileBody);
                    if ($curlError) {
                        log_message('error', '[GoogleLogin] Native curl error: ' . $curlError);
                    }
                    $googleData = json_decode($profileBody, true);
                    log_message('info', '[GoogleLogin] User profile parsed: ' . json_encode($googleData));
                }

            } catch (\Exception $e) {
                log_message('error', '[GoogleLogin] Exception: ' . $e->getMessage());
                $session->setFlashdata('Error', 'การเชื่อมต่อกับ Google ล้มเหลว: ' . $e->getMessage());
                return redirect()->to(base_url('LoginOfficerPersonnel'));
            }

            if ($googleData && isset($googleData['email'])) {
                $email = $googleData['email'];
                $google_id = $googleData['sub'];
                log_message('info', '[GoogleLogin] Email: ' . $email . ' | Google ID: ' . $google_id);

                // ใช้ Database connect ใหม่ทุกครั้ง เพื่อหลีกเลี่ยงปัญหา query builder state
                $db = \Config\Database::connect('personnel');
                $dbBudget = \Config\Database::connect(); // skjacth_budgetplan

                // ตรวจสอบว่า email มีในระบบหรือไม่
                $userRow = $db->table('tb_personnel')->where('pers_username', $email)->get()->getRowArray();
                log_message('info', '[GoogleLogin] DB lookup result: ' . ($userRow ? 'FOUND (pers_id=' . $userRow['pers_id'] . ')' : 'NOT FOUND'));

                if($userRow){
                    // อัพเดท login_oauth_uid
                    $db->table('tb_personnel')->where('pers_username', $email)->update([
                        'login_oauth_uid' => $google_id,
                        'updated_at' => date('Y-m-d H:i:s')
                    ]);

                    // ดึงข้อมูล roles จาก skjacth_budgetplan.tb_admin_rloes
                    $User2 = $dbBudget->table('tb_admin_rloes')
                        ->select('admin_rloes_status, GROUP_CONCAT(admin_rloes_nanetype) AS rloesAll')
                        ->where('admin_rloes_userid', $userRow['pers_id'])
                        ->get()
                        ->getRowArray();
                    
                    log_message('info', '[GoogleLogin] Roles: ' . json_encode($User2));

                    $status = (isset($User2['admin_rloes_status']) && $User2['admin_rloes_status'] != "") 
                        ? $User2['admin_rloes_status'] 
                        : "Member";

                    $newdata = [
                        'username'  => $userRow['pers_prefix'].$userRow['pers_firstname'].' '.$userRow['pers_lastname'],
                        'id'        => $userRow['pers_id'],
                        'logged_in' => true,
                        'rloes'     => $User2['rloesAll'] ?? '',
                        'status'    => $status
                    ];
                    $session->set($newdata);
                    log_message('info', '[GoogleLogin] Session set: ' . json_encode($newdata));
                    
                    if (in_array($status, ['admin', 'manager', 'superadmin'])) {
                        log_message('info', '[GoogleLogin] Redirecting to Admin/Home');
                        return redirect()->to(base_url('Admin/Home'));
                    }

                    // ถ้าไม่ใช่ admin/manager ให้ redirect ตาม Return หรือ หน้าหลัก
                    $returnUrl = $session->get('Return');
                    if ($returnUrl) {
                        return redirect()->to("https://".$returnUrl);
                    }
                    return redirect()->to(base_url());

                } else {
                    log_message('warning', '[GoogleLogin] Email not found in system: ' . $email);
                    $session->setFlashdata('Error', 'ไม่พบ Email: ' . $email . ' ในระบบ กรุณาติดต่อผู้ดูแลระบบ!');
                    return redirect()->to(base_url('LoginOfficerPersonnel'));
                }
            } else {
                log_message('error', '[GoogleLogin] No email in Google response');
                $session->setFlashdata('Error', 'ไม่สามารถดึงข้อมูลอีเมลจาก Google ได้');
                return redirect()->to(base_url('LoginOfficerPersonnel'));
            }
        }

        return view('User/UserLeyout/UserHeader',$data)
        .view('User/UserLeyout/UserMenuLeft')
        .view('Login/LoginGoogle')
        .view('User/UserLeyout/UserFooter');
    }

    public function LogoutOfficerPersonnel(){
        $session = session();
        $session->destroy();
        return redirect()->to(base_url());
    }
}
