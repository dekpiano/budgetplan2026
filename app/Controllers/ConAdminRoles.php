<?php

namespace App\Controllers;

class ConAdminRoles extends BaseController
{
    public function DataMain()
    {
        $data['full_url'] = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http') . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
        $data['uri'] = service('uri');
        return $data;
    }

    public function index()
    {
        if (!auth_is_superadmin()) {
            return redirect()->to(base_url('Admin/Home'))->with('error', 'เฉพาะผู้ดูแลระบบสูงสุด (Super Admin) เท่านั้นที่สามารถเข้าถึงหน้านี้ได้');
        }

        $data = $this->DataMain();
        $data['title'] = "จัดการและกำหนดสิทธิ์ผู้ใช้งาน";

        $DB_Personnel = \Config\Database::connect('personnel');
        $DB_Budget = \Config\Database::connect();
        $DB_Skj = \Config\Database::connect('skj');

        $systems = $DB_Budget->table('tb_admin_systems')
            ->where('admin_system_active', 1)
            ->whereIn('admin_system_code', [
                'budget_ledger', 'budget_ordinance', 'registry_purchase', 'registry_hire',
                'procurement', 'money_receipt', 'budget_structure'
            ])
            ->orderBy('admin_system_level', 'ASC')
            ->orderBy('admin_system_id', 'ASC')
            ->get()->getResult();

        $accessRows = $DB_Budget->table('tb_admin_system_access')
            ->orderBy('admin_access_userid', 'ASC')
            ->get()->getResult();

        $userSystemMap = [];
        foreach ($accessRows as $row) {
            if (empty($row->admin_access_userid) || empty($row->admin_access_system_code)) continue;
            if (!isset($userSystemMap[$row->admin_access_userid])) $userSystemMap[$row->admin_access_userid] = [];
            $userSystemMap[$row->admin_access_userid][$row->admin_access_system_code] = [
                'view' => (int)$row->admin_access_view,
                'create' => (int)$row->admin_access_create,
                'update' => (int)$row->admin_access_update,
                'delete' => (int)$row->admin_access_delete,
                'approve' => (int)$row->admin_access_approve,
            ];
        }

        $systemMap = [];
        foreach ($systems as $system) $systemMap[$system->admin_system_code] = $system;

        // แปลงรหัสตำแหน่ง/กลุ่มสาระจาก Personnel DB เป็นชื่อภาษาไทยจากฐานข้อมูลกลาง SKJ
        $positionMap = [];
        foreach ($DB_Skj->table('tb_position')->get()->getResult() as $position) {
            $positionMap[$position->posi_id] = $position->posi_name;
        }

        $learningMap = [];
        foreach ($DB_Skj->table('tb_learning')->get()->getResult() as $learning) {
            $learningMap[$learning->lear_id] = trim($learning->lear_namethai);
        }

        $allTeachers = $DB_Personnel->table('tb_personnel')
            ->select('pers_id, pers_prefix, pers_firstname, pers_lastname, pers_position, pers_learning, pers_img')
            ->where('pers_status', 'กำลังใช้งาน')
            ->orderBy('pers_firstname', 'ASC')
            ->get()->getResult();

        $teacherMap = [];
        foreach ($allTeachers as $teacher) $teacherMap[$teacher->pers_id] = $teacher;

        $assignedTeachers = [];
        foreach ($userSystemMap as $uid => $access) {
            $teacher = $teacherMap[$uid] ?? null;
            $assignedTeachers[] = [
                'pers_id' => $uid,
                'fullname' => $teacher ? ($teacher->pers_prefix . $teacher->pers_firstname . ' ' . $teacher->pers_lastname) : $uid,
                'position' => $teacher ? ($positionMap[$teacher->pers_position] ?? ($learningMap[$teacher->pers_learning] ?? 'บุคลากร')) : '-',
                'img' => $teacher ? $teacher->pers_img : '',
                'systems' => array_keys($access),
                'access' => $access,
            ];
        }

        $data['PositionMap'] = $positionMap;
        $data['LearningMap'] = $learningMap;
        $data['SystemDefs'] = $systems;
        $data['SystemMap'] = $systemMap;
        $data['AssignedTeachers'] = $assignedTeachers;
        $data['AllTeachers'] = $allTeachers;
        $data['TotalAssigned'] = count($assignedTeachers);
        $data['TotalSystems'] = count($systems);

        return view('Admin/AdminLeyout/AdminHeader', $data)
            . view('Admin/AdminLeyout/AdminMenuLeft', $data)
            . view('Admin/AdminRoles/AdminRolesMain', $data)
            . view('Admin/AdminLeyout/AdminFooter', $data);
    }

    /**
     * บันทึกการกำหนดสิทธิ์แบบ Checkbox รายบุคคล (เพิ่มหรือแก้ไข 1 คน มีได้หลายสิทธิ์)
     * POST params: pers_id, roles[]
     */
    public function savePersonnelRoles()
    {
        if (!auth_is_superadmin()) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'เฉพาะ Super Admin เท่านั้นที่สามารถแก้ไขสิทธิ์ได้']);
        }

        $persId = trim($this->request->getPost('pers_id') ?? '');
        $systems = $this->request->getPost('systems') ?? [];

        if ($persId === '') {
            return $this->response->setJSON(['status' => 'error', 'message' => 'กรุณาเลือกบุคลากรที่ต้องการกำหนดสิทธิ์']);
        }
        if (!is_array($systems)) $systems = !empty($systems) ? [$systems] : [];

        $db = \Config\Database::connect();
        $validSystems = $db->table('tb_admin_systems')
            ->select('admin_system_code')
            ->where('admin_system_active', 1)
            ->whereIn('admin_system_code', [
                'budget_ledger', 'budget_ordinance', 'registry_purchase', 'registry_hire',
                'procurement', 'money_receipt', 'budget_structure'
            ])
            ->get()->getResultArray();
        $validCodes = array_column($validSystems, 'admin_system_code');
        $systems = array_values(array_unique(array_intersect($systems, $validCodes)));

        $db->transStart();
        $db->table('tb_admin_system_access')->where('admin_access_userid', $persId)->delete();

        if (!empty($systems)) {
            $now = date('Y-m-d H:i:s');
            $insertData = [];
            foreach ($systems as $systemCode) {
                $insertData[] = [
                    'admin_access_userid' => $persId,
                    'admin_access_system_code' => $systemCode,
                    'admin_access_view' => 1,
                    'admin_access_create' => 1,
                    'admin_access_update' => 1,
                    'admin_access_delete' => 0,
                    'admin_access_approve' => 0,
                    'created_at' => $now,
                    'updated_at' => $now,
                ];
            }
            $db->table('tb_admin_system_access')->insertBatch($insertData);
        }
        $db->transComplete();

        if ($db->transStatus() === false) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'ไม่สามารถบันทึกสิทธิ์ระบบได้']);
        }

        return $this->response->setJSON([
            'status' => 'success',
            'message' => 'บันทึกระบบที่บุคลากรสามารถใช้งานได้เรียบร้อยแล้ว (' . count($systems) . ' ระบบ)'
        ]);
    }

    /**
     * ลบสิทธิ์ทั้งหมดของบุคลากรท่านนั้นออกจากระบบ (Remove user roles)
     */
    public function deletePersonnelRoles($persId)
    {
        if (!auth_is_superadmin()) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'เฉพาะ Super Admin เท่านั้นที่สามารถลบสิทธิ์ได้']);
        }

        $persId = trim($persId);
        if (empty($persId)) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'ไม่พบรหัสบุคลากร']);
        }

        $db = \Config\Database::connect();
        $db->table('tb_admin_system_access')->where('admin_access_userid', $persId)->delete();
        // ล้างข้อมูล Role เดิมด้วย เพื่อไม่ให้สิทธิ์แบบเก่าค้างอยู่
        $db->table('tb_admin_rloes')->where('admin_rloes_userid', $persId)->delete();

        return $this->response->setJSON([
            'status'  => 'success',
            'message' => 'ลบสิทธิ์การใช้งานของบุคลากรท่านนี้เรียบร้อยแล้ว'
        ]);
    }

    /**
     * บันทึกการกำหนดผู้ใช้งานให้กับ Role (รองรับหลายคนต่อ 1 สิทธิ์)
     */
    public function saveRoleAssignments()
    {
        if (!auth_is_superadmin()) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'เฉพาะ Super Admin เท่านั้นที่สามารถแก้ไขสิทธิ์ได้']);
        }

        $status   = trim($this->request->getPost('status') ?? '');
        $nanetype = trim($this->request->getPost('nanetype') ?? '');
        $level    = (int)($this->request->getPost('level') ?? 2);
        $userIds  = $this->request->getPost('user_ids') ?? [];

        if ($status === '') {
            return $this->response->setJSON(['status' => 'error', 'message' => 'ไม่พบรหัสสิทธิ์']);
        }

        if (!is_array($userIds)) {
            $userIds = !empty($userIds) ? [$userIds] : [];
        }

        $db = \Config\Database::connect();
        $db->table('tb_admin_rloes')->where('admin_rloes_status', $status)->delete();

        if (!empty($userIds)) {
            $insertData = [];
            foreach ($userIds as $uid) {
                $uid = trim($uid);
                if (!empty($uid)) {
                    $insertData[] = [
                        'admin_rloes_userid'   => $uid,
                        'admin_rloes_status'   => $status,
                        'admin_rloes_nanetype' => $nanetype,
                        'admin_rloes_level'    => $level,
                        'created_at'           => date('Y-m-d H:i:s'),
                        'updated_at'           => date('Y-m-d H:i:s'),
                    ];
                }
            }
            if (!empty($insertData)) {
                $db->table('tb_admin_rloes')->insertBatch($insertData);
            }
        } else {
            $db->table('tb_admin_rloes')->insert([
                'admin_rloes_userid'   => '',
                'admin_rloes_status'   => $status,
                'admin_rloes_nanetype' => $nanetype,
                'admin_rloes_level'    => $level,
                'created_at'           => date('Y-m-d H:i:s'),
                'updated_at'           => date('Y-m-d H:i:s'),
            ]);
        }

        return $this->response->setJSON([
            'status'  => 'success',
            'message' => 'บันทึกการกำหนดผู้ใช้งานเรียบร้อยแล้ว (' . count($userIds) . ' คน)'
        ]);
    }

    /**
     * Assign personnel to a role (backward compatibility).
     */
    public function RloesSettingManager()
    {
        if (!auth_is_superadmin()) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'เฉพาะ Super Admin เท่านั้น']);
        }

        $rloesId = $this->request->getVar('RloesID');
        $teachId = $this->request->getVar('TeachID');
        $keytype = $this->request->getVar('Keytype');

        $db = \Config\Database::connect();
        $result = $db->table('tb_admin_rloes')->where('admin_rloes_id', $rloesId)->update([
            'admin_rloes_userid'   => $teachId,
            'admin_rloes_nanetype' => $keytype,
            'updated_at'           => date('Y-m-d H:i:s'),
        ]);

        return $this->response->setJSON(['status' => $result ? 'success' : 'error']);
    }

    /**
     * เพิ่มสิทธิ์ใหม่ (Add Role)
     */
    public function addRole()
    {
        if (!auth_is_superadmin()) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'เฉพาะ Super Admin เท่านั้น']);
        }

        $status   = trim($this->request->getPost('status') ?? '');
        $nanetype = trim($this->request->getPost('nanetype') ?? '');
        $level    = (int)($this->request->getPost('level') ?? 2);

        if ($status === '' || $nanetype === '') {
            return $this->response->setJSON(['status' => 'error', 'message' => 'กรุณากรอกข้อมูลให้ครบถ้วน']);
        }

        $db = \Config\Database::connect();

        $exists = $db->table('tb_admin_rloes')->where('admin_rloes_status', $status)->countAllResults();
        if ($exists > 0) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'รหัสสิทธิ์นี้มีอยู่ในระบบแล้ว']);
        }

        $db->table('tb_admin_rloes')->insert([
            'admin_rloes_userid'   => '',
            'admin_rloes_status'   => $status,
            'admin_rloes_nanetype' => $nanetype,
            'admin_rloes_level'    => $level,
            'created_at'           => date('Y-m-d H:i:s'),
            'updated_at'           => date('Y-m-d H:i:s'),
        ]);

        return $this->response->setJSON(['status' => 'success', 'message' => 'เพิ่มสิทธิ์เรียบร้อยแล้ว']);
    }

    /**
     * แก้ไขข้อมูลสิทธิ์ (Update Role)
     */
    public function updateRole($oldStatus)
    {
        if (!auth_is_superadmin()) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'เฉพาะ Super Admin เท่านั้น']);
        }

        $status   = trim($this->request->getPost('status') ?? '');
        $nanetype = trim($this->request->getPost('nanetype') ?? '');
        $level    = (int)($this->request->getPost('level') ?? 2);

        if ($status === '' || $nanetype === '') {
            return $this->response->setJSON(['status' => 'error', 'message' => 'กรุณากรอกข้อมูลให้ครบถ้วน']);
        }

        $db = \Config\Database::connect();
        
        $db->table('tb_admin_rloes')
            ->groupStart()
                ->where('admin_rloes_status', $oldStatus)
                ->orWhere('admin_rloes_id', $oldStatus)
            ->groupEnd()
            ->update([
                'admin_rloes_status'   => $status,
                'admin_rloes_nanetype' => $nanetype,
                'admin_rloes_level'    => $level,
                'updated_at'           => date('Y-m-d H:i:s'),
            ]);

        return $this->response->setJSON(['status' => 'success', 'message' => 'แก้ไขสิทธิ์เรียบร้อยแล้ว']);
    }

    /**
     * ลบสิทธิ์ทั้งหมดใน status นั้น (Delete Role)
     */
    public function deleteRole($status)
    {
        if (!auth_is_superadmin()) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'เฉพาะ Super Admin เท่านั้น']);
        }

        $db = \Config\Database::connect();
        $db->table('tb_admin_rloes')
            ->groupStart()
                ->where('admin_rloes_status', $status)
                ->orWhere('admin_rloes_id', $status)
            ->groupEnd()
            ->delete();

        return $this->response->setJSON(['status' => 'success', 'message' => 'ลบสิทธิ์เรียบร้อยแล้ว']);
    }
}


