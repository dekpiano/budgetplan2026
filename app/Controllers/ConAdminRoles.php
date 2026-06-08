<?php

namespace App\Controllers;

class ConAdminRoles extends BaseController
{
    public function DataMain(){
        $data['full_url'] = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http') . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";

        $data['uri'] = service('uri');
        return $data;
    }

    public function index()
    {
        $data = $this->DataMain();
        $data['title'] = "จัดการข้อมูลกำหนดสิทธิ์การใช้งาน";
        $DB_Personnel = \Config\Database::connect('personnel');
        $DB_Budget = \Config\Database::connect(); // skjacth_budgetplan
        $tb_admin_rloes = $DB_Budget->table('tb_admin_rloes');
        $DBPers = $DB_Personnel->table('tb_personnel');

        $data['Manager'] = $tb_admin_rloes->select('admin_rloes_userid,admin_rloes_id,admin_rloes_nanetype,admin_rloes_status,admin_rloes_level')
            ->orderBy('admin_rloes_level','ASC')
            ->get()->getResult();

        $data['NameTeacher'] = $DBPers->select('pers_id,pers_prefix,pers_firstname,pers_lastname,pers_position,pers_learning')
            ->where('pers_status','กำลังใช้งาน')
            ->orderBy('pers_position','ASC')
            ->get()->getResult();

        return view('Admin/AdminLeyout/AdminHeader',$data)
                .view('Admin/AdminLeyout/AdminMenuLeft')
                .view('Admin/AdminRoles/AdminRolesMain')
                .view('Admin/AdminLeyout/AdminFooter');
    }

    /**
     * Assign personnel to a role (existing functionality).
     * POST params: TeachID, RloesID, Keytype
     */
    public function RloesSettingManager()
    {
        if (!auth_is_superadmin()) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'เฉพาะ Super Admin เท่านั้นที่สามารถแก้ไขสิทธิ์ได้']);
        }

        $DB_Budget = \Config\Database::connect();
        $DBrloes = $DB_Budget->table('tb_admin_rloes');

        $result = $DBrloes->where('admin_rloes_id', $this->request->getVar('RloesID'))->update([
            'admin_rloes_userid'  => $this->request->getVar('TeachID'),
            'admin_rloes_nanetype'=> $this->request->getVar('Keytype'),
        ]);

        return $this->response->setJSON(['status' => $result ? 'success' : 'error']);
    }

    /**
     * Add a new role.
     * POST params: status, nanetype, level
     */
    public function addRole()
    {
        if (!auth_is_superadmin()) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'เฉพาะ Super Admin เท่านั้น']);
        }

        $status   = trim($this->request->getPost('status'));
        $nanetype = trim($this->request->getPost('nanetype'));
        $level    = (int) $this->request->getPost('level');

        if ($status === '' || $nanetype === '') {
            return $this->response->setJSON(['status' => 'error', 'message' => 'กรุณากรอกข้อมูลให้ครบถ้วน']);
        }

        $db = \Config\Database::connect();
        $db->table('tb_admin_rloes')->insert([
            'admin_rloes_userid'   => 'system',
            'admin_rloes_status'   => $status,
            'admin_rloes_nanetype' => $nanetype,
            'admin_rloes_level'    => $level,
            'created_at'           => date('Y-m-d H:i:s'),
            'updated_at'           => date('Y-m-d H:i:s'),
        ]);

        return $this->response->setJSON(['status' => 'success', 'message' => 'เพิ่มสิทธิ์เรียบร้อยแล้ว']);
    }

    /**
     * Update an existing role.
     * POST params: status, nanetype, level
     */
    public function updateRole($id)
    {
        if (!auth_is_superadmin()) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'เฉพาะ Super Admin เท่านั้น']);
        }

        $status   = trim($this->request->getPost('status'));
        $nanetype = trim($this->request->getPost('nanetype'));
        $level    = (int) $this->request->getPost('level');

        if ($status === '' || $nanetype === '') {
            return $this->response->setJSON(['status' => 'error', 'message' => 'กรุณากรอกข้อมูลให้ครบถ้วน']);
        }

        $db = \Config\Database::connect();
        $db->table('tb_admin_rloes')->where('admin_rloes_id', $id)->update([
            'admin_rloes_status'   => $status,
            'admin_rloes_nanetype' => $nanetype,
            'admin_rloes_level'    => $level,
            'updated_at'           => date('Y-m-d H:i:s'),
        ]);

        return $this->response->setJSON(['status' => 'success', 'message' => 'แก้ไขสิทธิ์เรียบร้อยแล้ว']);
    }

    /**
     * Delete a role.
     */
    public function deleteRole($id)
    {
        if (!auth_is_superadmin()) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'เฉพาะ Super Admin เท่านั้น']);
        }

        $db = \Config\Database::connect();
        $db->table('tb_admin_rloes')->where('admin_rloes_id', $id)->delete();

        return $this->response->setJSON(['status' => 'success', 'message' => 'ลบสิทธิ์เรียบร้อยแล้ว']);
    }
}
