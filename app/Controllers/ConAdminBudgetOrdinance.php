<?php

namespace App\Controllers;

use App\Models\AdminBudgetOrdinance\OilModel;
use App\Models\AdminBudgetOrdinance\TravelModel;
use App\Models\AdminBudgetOrdinance\RegistrationModel;
use CodeIgniter\Controller;

class ConAdminBudgetOrdinance extends BaseController
{
    protected $db;
    protected $oilModel;
    protected $travelModel;
    protected $registrationModel;

    public function __construct()
    {
        $this->db = \Config\Database::connect();
        $this->oilModel = new OilModel();
        $this->travelModel = new TravelModel();
        $this->registrationModel = new RegistrationModel();
    }

    public function DataMain()
    {
        $data['full_url'] = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http') . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
        $data['uri'] = service('uri');
        return $data;
    }

    /**
     * หน้าแรกของทะเบียนคุมงบประมาณข้อบัญญัติ (Redirect ไปหน้าน้ำมัน)
     */
    public function index()
    {
        return redirect()->to(base_url('Admin/BudgetOrdinance/Oil'));
    }

    /* =========================================================================
     * SECTION 1: เบิกค่าน้ำมันเชื้อเพลิง (Oil Reimbursement)
     * ========================================================================= */

    /**
     * หน้าจอจัดการทะเบียนคุมเบิกค่าน้ำมันเชื้อเพลิง
     */
    public function oil()
    {
        if (!auth_is_superadmin() && !auth_can_access_system('budget_ordinance')) {
            return redirect()->to(base_url('Admin/Home'))->with('error', 'คุณไม่มีสิทธิ์เข้าถึงระบบเบิกค่าน้ำมัน');
        }

        $data = $this->DataMain();
        $data['title'] = "ทะเบียนคุมงบประมาณเงินข้อบัญญัติ - เบิกค่าน้ำมันเชื้อเพลิง";

        $selectedYear = $this->request->getGet('year') ?? get_budget_year();
        $data['selected_year'] = $selectedYear;

        // ดึงรายการปีงบประมาณทั้งหมด
        $data['years'] = $this->oilModel->getDistinctYears();

        // ดึงรายการเบิกจ่ายในปีงบประมาณที่เลือก
        $data['items'] = $this->oilModel->getItemsByYear($selectedYear);

        // ดึงสถิติสรุปยอด
        $data['stats'] = $this->oilModel->getSummaryStats($selectedYear);

        // ดึงข้อมูลรถยนต์จากฐานข้อมูล general (skjacth_general.tb_school_car)
        try {
            $dbGeneral = \Config\Database::connect('general');
            $cars = $dbGeneral->table('tb_school_car')
                ->select('car_ID, car_registration, car_province, car_category, car_brand, car_model, car_status')
                ->orderBy('car_ID', 'ASC')
                ->get()
                ->getResult();
            $data['cars'] = $cars;
            $data['schoolCars'] = $cars;
        } catch (\Throwable $e) {
            $data['cars'] = [];
            $data['schoolCars'] = [];
        }

        // ดึงรายชื่อพนักงานขับรถ
        try {
            $dbGeneral = \Config\Database::connect('general');
            $drivers = $dbGeneral->table('tb_car_driver')
                ->join('skjacth_personnel.tb_personnel', 'tb_personnel.pers_id = tb_car_driver.cardriver_userID', 'left')
                ->select('tb_personnel.pers_prefix, tb_personnel.pers_firstname, tb_personnel.pers_lastname')
                ->get()
                ->getResult();

            $driverList = [];
            foreach ($drivers as $d) {
                $driverList[] = trim($d->pers_prefix . $d->pers_firstname . ' ' . $d->pers_lastname);
            }
            $data['drivers'] = array_unique(array_filter($driverList));
        } catch (\Throwable $e) {
            $data['drivers'] = [];
        }

        // ดึงรายชื่อบุคลากรทั้งหมด (สำหรับผู้ขอเบิก)
        try {
            $dbPers = \Config\Database::connect('personnel');
            $data['personnel'] = $dbPers->table('tb_personnel')
                ->select('pers_id, pers_prefix, pers_firstname, pers_lastname, pers_position')
                ->where('pers_status', 'กำลังใช้งาน')
                ->orderBy('pers_firstname', 'ASC')
                ->get()
                ->getResult();
        } catch (\Throwable $e) {
            $data['personnel'] = [];
        }

        echo view('Admin/AdminLeyout/AdminHeader', $data);
        echo view('Admin/AdminLeyout/AdminMenuLeft', $data);
        echo view('Admin/AdminBudgetOrdinance/Oil/OilMain', $data);
        echo view('Admin/AdminLeyout/AdminFooter', $data);
    }

    /**
     * ดึงข้อมูลรายการเดี่ยวเพื่อนำไปแก้ไข (AJAX GET)
     */
    public function getOilData($id = null)
    {
        if (!$id) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'ไม่พบรหัสรายการ']);
        }

        $item = $this->oilModel->find($id);
        if (!$item) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'ไม่พบข้อมูล']);
        }

        return $this->response->setJSON([
            'status' => 'success',
            'data'   => $item
        ]);
    }

    /**
     * บันทึกข้อมูลเบิกค่าน้ำมัน / ปรับงบประมาณ (AJAX POST)
     */
    public function saveOil()
    {
        if (!auth_is_superadmin() && !auth_can_access_system('budget_ordinance')) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'คุณไม่มีสิทธิ์ทำรายการนี้']);
        }

        $id = $this->request->getPost('id');
        $entryType = $this->request->getPost('entry_type') ?? 'withdraw';

        // จัดการวันที่ (รองรับทั้งรูปแบบ พ.ศ. และ ค.ศ.)
        $docDate = $this->parseThaiBuddhistDateToSql($this->request->getPost('doc_date')) ?? date('Y-m-d');

        // คำนวณปีงบประมาณไทยอัตโนมัติตามวันที่เอกสาร
        $autoBudgetYear = get_budget_year($docDate);
        $budgetYear = $this->request->getPost('budget_year') ?: $autoBudgetYear;
        if (!empty($docDate)) {
            $budgetYear = get_budget_year($docDate);
        }

        // ตรวจสอบเงื่อนไข: ต้องมีการตั้งงบประมาณก่อนเสมอ จึงจะสามารถบันทึกรายการเบิกจ่ายได้
        if ($entryType === 'withdraw') {
            if (!$this->oilModel->hasBudgetSet($budgetYear)) {
                return $this->response->setJSON([
                    'status'  => 'error',
                    'message' => 'ไม่สามารถบันทึกรายการเบิกค่าน้ำมันได้ เนื่องจากยังไม่มีการตั้งงบประมาณสำหรับปีงบประมาณ พ.ศ. ' . $budgetYear . ' กรุณากดปุ่ม "ตั้งงบประมาณ" ก่อนทำการเบิกจ่าย'
                ]);
            }
        }

        // จัดการตัวเลขการเงิน (ตัดลูกน้ำออก)
        $amountBudgetAdjust = (float)str_replace(',', '', $this->request->getPost('amount_budget_adjust') ?? '0');
        $amountWithdraw = (float)str_replace(',', '', $this->request->getPost('amount_withdraw') ?? '0');
        $fuelLiters = (float)str_replace(',', '', $this->request->getPost('fuel_liters') ?? '0');
        $fuelPrice = (float)str_replace(',', '', $this->request->getPost('fuel_price_per_liter') ?? '0');

        $currentUser = session()->get('fullname') ?? 'Admin';

        $dataSave = [
            'budget_year'          => $budgetYear,
            'doc_date'             => $docDate,
            'dika_no'              => $this->request->getPost('dika_no'),
            'doc_no'               => $this->request->getPost('doc_no'),
            'order_no'             => $this->request->getPost('order_no'),
            'description'          => $this->request->getPost('description'),
            'destination'          => $this->request->getPost('destination'),
            'car_license'          => $this->request->getPost('car_license'),
            'car_type'             => $this->request->getPost('car_type'),
            'driver_name'          => $this->request->getPost('driver_name'),
            'requester_name'       => $this->request->getPost('requester_name'),
            'fuel_type'            => $this->request->getPost('fuel_type'),
            'fuel_liters'          => $fuelLiters,
            'fuel_price_per_liter' => $fuelPrice,
            'amount_budget_adjust' => ($entryType === 'adjust') ? $amountBudgetAdjust : 0.00,
            'amount_withdraw'      => ($entryType === 'withdraw') ? $amountWithdraw : 0.00,
            'entry_type'           => $entryType,
            'gas_station'          => $this->request->getPost('gas_station'),
            'receipt_no'           => $this->request->getPost('receipt_no'),
            'notes'                => $this->request->getPost('notes'),
            'created_by'           => $currentUser,
        ];

        $oldBudgetYear = null;
        if (!empty($id)) {
            $oldItem = $this->oilModel->find($id);
            if ($oldItem) {
                $oldBudgetYear = $oldItem->budget_year;
            }
            $this->oilModel->update($id, $dataSave);
            $msg = 'อัปเดตข้อมูลการเบิกค่าน้ำมันสำเร็จ';
        } else {
            $this->oilModel->insert($dataSave);
            $msg = 'บันทึกรายการเบิกค่าน้ำมันเรียบร้อยแล้ว';
        }

        // คำนวณยอดคงเหลือใหม่ทั้งปีงบประมาณ
        $this->oilModel->recalculateRunningBalance($budgetYear);

        // หากมีการเปลี่ยนปีงบประมาณ ให้คำนวณยอดคงเหลือของปีเดิมด้วย
        if (!empty($oldBudgetYear) && $oldBudgetYear !== $budgetYear) {
            $this->oilModel->recalculateRunningBalance($oldBudgetYear);
        }

        return $this->response->setJSON([
            'status'  => 'success',
            'message' => $msg
        ]);
    }

    /**
     * ลบรายการเบิกค่าน้ำมัน (AJAX POST)
     */
    public function deleteOil($id = null)
    {
        if (!auth_is_superadmin() && !auth_can_access_system('budget_ordinance')) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'คุณไม่มีสิทธิ์ทำรายการนี้']);
        }

        if (!$id) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'ไม่พบรหัสรายการที่ต้องการลบ']);
        }

        $item = $this->oilModel->find($id);
        if (!$item) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'ไม่พบข้อมูลในระบบ']);
        }

        $budgetYear = $item->budget_year;
        $this->oilModel->delete($id);

        // คำนวณยอดคงเหลือใหม่หลังลบ
        $this->oilModel->recalculateRunningBalance($budgetYear);

        return $this->response->setJSON([
            'status'  => 'success',
            'message' => 'ลบรายการเรียบร้อยแล้ว'
        ]);
    }

    /**
     * พิมพ์รายงานทะเบียนคุมค่าน้ำมัน
     */
    public function printOil()
    {
        $selectedYear = $this->request->getGet('year') ?? get_budget_year();
        $data['selected_year'] = $selectedYear;
        $data['items'] = $this->oilModel->getItemsByYear($selectedYear);
        $data['stats'] = $this->oilModel->getSummaryStats($selectedYear);

        return view('Admin/AdminBudgetOrdinance/Oil/OilPrint', $data);
    }

    /* =========================================================================
     * SECTION 2: ค่าใช้จ่ายเดินทางไปราชการ (Travel Expenses)
     * ========================================================================= */

    /**
     * หน้าจอจัดการทะเบียนคุมค่าใช้จ่ายเดินทางไปราชการ
     */
    public function travel()
    {
        if (!auth_is_superadmin() && !auth_can_access_system('budget_ordinance')) {
            return redirect()->to(base_url('Admin/Home'))->with('error', 'คุณไม่มีสิทธิ์เข้าถึงระบบค่าใช้จ่ายเดินทางไปราชการ');
        }

        $data = $this->DataMain();
        $data['title'] = "ทะเบียนคุมงบประมาณเงินข้อบัญญัติ - ค่าใช้จ่ายเดินทางไปราชการ";

        $selectedYear = $this->request->getGet('year') ?? get_budget_year();
        $data['selected_year'] = $selectedYear;

        // ดึงรายการปีงบประมาณทั้งหมด
        $data['years'] = $this->travelModel->getDistinctYears();

        // ดึงรายการเบิกจ่ายในปีงบประมาณที่เลือก
        $data['items'] = $this->travelModel->getItemsByYear($selectedYear);

        // ดึงสถิติสรุปยอด
        $data['stats'] = $this->travelModel->getSummaryStats($selectedYear);

        // ดึงรายชื่อบุคลากรทั้งหมด (สำหรับผู้ขอเบิกและคณะผู้เดินทาง)
        try {
            $dbPers = \Config\Database::connect('personnel');
            $data['personnel'] = $dbPers->table('tb_personnel')
                ->select('pers_id, pers_prefix, pers_firstname, pers_lastname, pers_position')
                ->where('pers_status', 'กำลังใช้งาน')
                ->orderBy('pers_firstname', 'ASC')
                ->get()
                ->getResult();
        } catch (\Throwable $e) {
            $data['personnel'] = [];
        }

        echo view('Admin/AdminLeyout/AdminHeader', $data);
        echo view('Admin/AdminLeyout/AdminMenuLeft', $data);
        echo view('Admin/AdminBudgetOrdinance/Travel/TravelMain', $data);
        echo view('Admin/AdminLeyout/AdminFooter', $data);
    }

    /**
     * ดึงข้อมูลรายการเดินทางเดี่ยวเพื่อนำไปแก้ไข (AJAX GET)
     */
    public function getTravelData($id = null)
    {
        if (!$id) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'ไม่พบรหัสรายการ']);
        }

        $item = $this->travelModel->find($id);
        if (!$item) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'ไม่พบข้อมูล']);
        }

        return $this->response->setJSON([
            'status' => 'success',
            'data'   => $item
        ]);
    }

    /**
     * แปลงวันที่ พ.ศ./ค.ศ. ให้อยู่ในรูปแบบ Y-m-d (MySQL) อย่างแม่นยำ
     */
    private function parseThaiBuddhistDateToSql(?string $inputDate): ?string
    {
        if (empty($inputDate)) return null;
        $cleaned = trim($inputDate);
        if (preg_match('/^(\d{4})-(\d{2})-(\d{2})$/', $cleaned, $matches)) {
            $y = (int)$matches[1];
            if ($y > 2400) $y -= 543;
            return sprintf('%04d-%02d-%02d', $y, $matches[2], $matches[3]);
        } elseif (preg_match('/^(\d{1,2})\/(\d{1,2})\/(\d{4})$/', $cleaned, $matches)) {
            $y = (int)$matches[3];
            if ($y > 2400) $y -= 543;
            return sprintf('%04d-%02d-%02d', $y, $matches[2], $matches[1]);
        }
        $t = strtotime($cleaned);
        return $t ? date('Y-m-d', $t) : null;
    }

    /**
     * บันทึกข้อมูลค่าใช้จ่ายเดินทางไปราชการ / ปรับงบประมาณ (AJAX POST)
     */
    public function saveTravel()
    {
        if (!auth_is_superadmin() && !auth_can_access_system('budget_ordinance')) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'คุณไม่มีสิทธิ์ทำรายการนี้']);
        }

        $id = $this->request->getPost('id');
        $entryType = $this->request->getPost('entry_type') ?? 'withdraw';

        // จัดการวันที่ (พ.ศ. -> MySQL Date)
        $docDate = $this->parseThaiBuddhistDateToSql($this->request->getPost('doc_date')) ?? date('Y-m-d');
        $travelStartDate = $this->parseThaiBuddhistDateToSql($this->request->getPost('travel_start_date'));
        $travelEndDate = $this->parseThaiBuddhistDateToSql($this->request->getPost('travel_end_date'));

        // คำนวณปีงบประมาณไทยอัตโนมัติตามวันที่เอกสาร (หรือวันเริ่มเดินทาง)
        $referenceDate = !empty($docDate) ? $docDate : (!empty($travelStartDate) ? $travelStartDate : date('Y-m-d'));
        $autoBudgetYear = get_budget_year($referenceDate);
        $budgetYear = $this->request->getPost('budget_year') ?: $autoBudgetYear;
        if (!empty($referenceDate)) {
            $budgetYear = get_budget_year($referenceDate);
        }

        // ตรวจสอบเงื่อนไข: ต้องมีการตั้งงบประมาณก่อนเสมอ จึงจะสามารถบันทึกรายการเบิกจ่ายได้
        if ($entryType === 'withdraw') {
            if (!$this->travelModel->hasBudgetSet($budgetYear)) {
                return $this->response->setJSON([
                    'status'  => 'error',
                    'message' => 'ไม่สามารถบันทึกรายการค่าใช้จ่ายเดินทางได้ เนื่องจากยังไม่มีการตั้งงบประมาณสำหรับปีงบประมาณ พ.ศ. ' . $budgetYear . ' กรุณากดปุ่ม "ตั้งงบประมาณ" ก่อนทำการเบิกจ่าย'
                ]);
            }
        }

        // หากไม่มีวันสิ้นสุด ให้ใช้วันเดียวกับวันเริ่มต้น
        if (!empty($travelStartDate) && empty($travelEndDate)) {
            $travelEndDate = $travelStartDate;
        }

        // ตัวเลขการเงิน
        $allowanceAmount = (float)str_replace(',', '', $this->request->getPost('allowance_amount') ?? '0');
        $accommodationAmount = (float)str_replace(',', '', $this->request->getPost('accommodation_amount') ?? '0');
        $transportAmount = (float)str_replace(',', '', $this->request->getPost('transport_amount') ?? '0');
        $otherExpensesAmount = (float)str_replace(',', '', $this->request->getPost('other_expenses_amount') ?? '0');

        $amountBudgetAdjust = (float)str_replace(',', '', $this->request->getPost('amount_budget_adjust') ?? '0');
        $amountWithdraw = (float)str_replace(',', '', $this->request->getPost('amount_withdraw') ?? '0');

        // หากเป็นการเบิกจ่าย และไม่ได้กรอกยอดรวม ให้คำนวณจากยอดแจกแจง
        if ($entryType === 'withdraw' && $amountWithdraw <= 0) {
            $amountWithdraw = $allowanceAmount + $accommodationAmount + $transportAmount + $otherExpensesAmount;
        }

        $travelDays = (int)($this->request->getPost('travel_days') ?? 1);
        if ($travelDays < 1) $travelDays = 1;
        $travelerCount = (int)($this->request->getPost('traveler_count') ?? 1);
        if ($travelerCount < 1) $travelerCount = 1;

        $currentUser = session()->get('fullname') ?? 'Admin';

        $dataSave = [
            'budget_year'           => $budgetYear,
            'doc_date'              => $docDate,
            'dika_no'               => $this->request->getPost('dika_no'),
            'doc_no'                => $this->request->getPost('doc_no'),
            'order_no'              => $this->request->getPost('order_no'),
            'description'           => $this->request->getPost('description'),
            'destination'           => $this->request->getPost('destination'),
            'travel_start_date'     => $travelStartDate,
            'travel_end_date'       => $travelEndDate,
            'travel_days'           => $travelDays,
            'requester_name'        => $this->request->getPost('requester_name'),
            'traveler_names'        => $this->request->getPost('traveler_names'),
            'traveler_count'        => $travelerCount,
            'allowance_amount'      => $allowanceAmount,
            'accommodation_amount'  => $accommodationAmount,
            'transport_amount'      => $transportAmount,
            'other_expenses_amount' => $otherExpensesAmount,
            'amount_budget_adjust'  => ($entryType === 'adjust') ? $amountBudgetAdjust : 0.00,
            'amount_withdraw'       => ($entryType === 'withdraw') ? $amountWithdraw : 0.00,
            'entry_type'            => $entryType,
            'notes'                 => $this->request->getPost('notes'),
            'created_by'            => $currentUser,
        ];

        $oldBudgetYear = null;
        if (!empty($id)) {
            $oldItem = $this->travelModel->find($id);
            if ($oldItem) {
                $oldBudgetYear = $oldItem->budget_year;
            }
            $this->travelModel->update($id, $dataSave);
            $msg = 'อัปเดตข้อมูลค่าใช้จ่ายเดินทางไปราชการสำเร็จ';
        } else {
            $this->travelModel->insert($dataSave);
            $msg = 'บันทึกรายการค่าใช้จ่ายเดินทางไปราชการเรียบร้อยแล้ว';
        }

        // คำนวณยอดคงเหลือใหม่ทั้งปีงบประมาณ
        $this->travelModel->recalculateRunningBalance($budgetYear);

        // หากมีการเปลี่ยนปีงบประมาณ ให้คำนวณยอดคงเหลือของปีเดิมด้วย
        if (!empty($oldBudgetYear) && $oldBudgetYear !== $budgetYear) {
            $this->travelModel->recalculateRunningBalance($oldBudgetYear);
        }

        return $this->response->setJSON([
            'status'  => 'success',
            'message' => $msg
        ]);
    }

    /**
     * ลบรายการค่าใช้จ่ายเดินทางไปราชการ (AJAX POST)
     */
    public function deleteTravel($id = null)
    {
        if (!auth_is_superadmin() && !auth_can_access_system('budget_ordinance')) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'คุณไม่มีสิทธิ์ทำรายการนี้']);
        }

        if (!$id) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'ไม่พบรหัสรายการที่ต้องการลบ']);
        }

        $item = $this->travelModel->find($id);
        if (!$item) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'ไม่พบข้อมูลในระบบ']);
        }

        $budgetYear = $item->budget_year;
        $this->travelModel->delete($id);

        // คำนวณยอดคงเหลือใหม่หลังลบ
        $this->travelModel->recalculateRunningBalance($budgetYear);

        return $this->response->setJSON([
            'status'  => 'success',
            'message' => 'ลบรายการเรียบร้อยแล้ว'
        ]);
    }

    /**
     * พิมพ์รายงานทะเบียนคุมค่าใช้จ่ายเดินทางไปราชการ
     */
    public function printTravel()
    {
        $selectedYear = $this->request->getGet('year') ?? get_budget_year();
        $data['selected_year'] = $selectedYear;
        $data['items'] = $this->travelModel->getItemsByYear($selectedYear);
        $data['stats'] = $this->travelModel->getSummaryStats($selectedYear);

        return view('Admin/AdminBudgetOrdinance/Travel/TravelPrint', $data);
    }

    /* =========================================================================
     * SECTION 3: ค่าลงทะเบียนอบรม / สัมมนา (Registration Fees)
     * ========================================================================= */

    /**
     * หน้าจอจัดการทะเบียนคุมค่าลงทะเบียนอบรม/สัมมนา
     */
    public function registration()
    {
        if (!auth_is_superadmin() && !auth_can_access_system('budget_ordinance')) {
            return redirect()->to(base_url('Admin/Home'))->with('error', 'คุณไม่มีสิทธิ์เข้าถึงระบบค่าลงทะเบียน');
        }

        $data = $this->DataMain();
        $data['title'] = "ทะเบียนคุมงบประมาณเงินข้อบัญญัติ - ค่าลงทะเบียนอบรม/สัมมนา";

        $selectedYear = $this->request->getGet('year') ?? get_budget_year();
        $data['selected_year'] = $selectedYear;

        // ดึงรายการปีงบประมาณทั้งหมด
        $data['years'] = $this->registrationModel->getDistinctYears();

        // ดึงรายการเบิกจ่ายในปีงบประมาณที่เลือก
        $data['items'] = $this->registrationModel->getItemsByYear($selectedYear);

        // ดึงสถิติสรุปยอด
        $data['stats'] = $this->registrationModel->getSummaryStats($selectedYear);

        // ดึงรายชื่อบุคลากรทั้งหมด (สำหรับผู้ขอเบิกและคณะผู้เข้าร่วมอบรม)
        try {
            $dbPers = \Config\Database::connect('personnel');
            $data['personnel'] = $dbPers->table('tb_personnel')
                ->select('pers_id, pers_prefix, pers_firstname, pers_lastname, pers_position')
                ->where('pers_status', 'กำลังใช้งาน')
                ->orderBy('pers_firstname', 'ASC')
                ->get()
                ->getResult();
        } catch (\Throwable $e) {
            $data['personnel'] = [];
        }

        echo view('Admin/AdminLeyout/AdminHeader', $data);
        echo view('Admin/AdminLeyout/AdminMenuLeft', $data);
        echo view('Admin/AdminBudgetOrdinance/Registration/RegistrationMain', $data);
        echo view('Admin/AdminLeyout/AdminFooter', $data);
    }

    /**
     * ดึงข้อมูลรายการค่าลงทะเบียนเดี่ยวเพื่อนำไปแก้ไข (AJAX GET)
     */
    public function getRegistrationData($id = null)
    {
        if (!$id) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'ไม่พบรหัสรายการ']);
        }

        $item = $this->registrationModel->find($id);
        if (!$item) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'ไม่พบข้อมูล']);
        }

        return $this->response->setJSON([
            'status' => 'success',
            'data'   => $item
        ]);
    }

    /**
     * บันทึกข้อมูลค่าลงทะเบียน / ปรับงบประมาณ (AJAX POST)
     */
    public function saveRegistration()
    {
        if (!auth_is_superadmin() && !auth_can_access_system('budget_ordinance')) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'คุณไม่มีสิทธิ์ทำรายการนี้']);
        }

        $id = $this->request->getPost('id');
        $entryType = $this->request->getPost('entry_type') ?? 'withdraw';

        // จัดการวันที่ (พ.ศ. -> MySQL Date)
        $docDate = $this->parseThaiBuddhistDateToSql($this->request->getPost('doc_date')) ?? date('Y-m-d');
        $trainingStartDate = $this->parseThaiBuddhistDateToSql($this->request->getPost('training_start_date'));
        $trainingEndDate = $this->parseThaiBuddhistDateToSql($this->request->getPost('training_end_date'));

        // คำนวณปีงบประมาณไทยอัตโนมัติตามวันที่เอกสาร (หรือวันเริ่มอบรม)
        $referenceDate = !empty($docDate) ? $docDate : (!empty($trainingStartDate) ? $trainingStartDate : date('Y-m-d'));
        $autoBudgetYear = get_budget_year($referenceDate);
        $budgetYear = $this->request->getPost('budget_year') ?: $autoBudgetYear;
        if (!empty($referenceDate)) {
            $budgetYear = get_budget_year($referenceDate);
        }

        // ตรวจสอบเงื่อนไข: ต้องมีการตั้งงบประมาณก่อนเสมอ จึงจะสามารถบันทึกรายการเบิกจ่ายได้
        if ($entryType === 'withdraw') {
            if (!$this->registrationModel->hasBudgetSet($budgetYear)) {
                return $this->response->setJSON([
                    'status'  => 'error',
                    'message' => 'ไม่สามารถบันทึกรายการค่าลงทะเบียนได้ เนื่องจากยังไม่มีการตั้งงบประมาณสำหรับปีงบประมาณ พ.ศ. ' . $budgetYear . ' กรุณากดปุ่ม "ตั้งงบประมาณ" ก่อนทำการเบิกจ่าย'
                ]);
            }
        }

        // หากไม่มีวันสิ้นสุด ให้ใช้วันเดียวกับวันเริ่มต้น
        if (!empty($trainingStartDate) && empty($trainingEndDate)) {
            $trainingEndDate = $trainingStartDate;
        }

        // ตัวเลขการเงิน
        $feePerPerson = (float)str_replace(',', '', $this->request->getPost('fee_per_person') ?? '0');
        $amountBudgetAdjust = (float)str_replace(',', '', $this->request->getPost('amount_budget_adjust') ?? '0');
        $amountWithdraw = (float)str_replace(',', '', $this->request->getPost('amount_withdraw') ?? '0');

        $trainingDays = (int)($this->request->getPost('training_days') ?? 1);
        if ($trainingDays < 1) $trainingDays = 1;
        $attendeeCount = (int)($this->request->getPost('attendee_count') ?? 1);
        if ($attendeeCount < 1) $attendeeCount = 1;

        // หากเป็นการเบิกจ่าย และไม่ได้กรอกยอดรวม ให้คำนวณจาก ค่าลงทะเบียนต่อคน * จำนวนคน
        if ($entryType === 'withdraw' && $amountWithdraw <= 0 && $feePerPerson > 0) {
            $amountWithdraw = $feePerPerson * $attendeeCount;
        }

        $currentUser = session()->get('fullname') ?? 'Admin';

        $dataSave = [
            'budget_year'          => $budgetYear,
            'doc_date'             => $docDate,
            'dika_no'              => $this->request->getPost('dika_no'),
            'doc_no'               => $this->request->getPost('doc_no'),
            'order_no'             => $this->request->getPost('order_no'),
            'description'          => $this->request->getPost('description'),
            'organizer'            => $this->request->getPost('organizer'),
            'location'             => $this->request->getPost('location'),
            'training_start_date'  => $trainingStartDate,
            'training_end_date'    => $trainingEndDate,
            'training_days'        => $trainingDays,
            'requester_name'       => $this->request->getPost('requester_name'),
            'attendee_names'       => $this->request->getPost('attendee_names'),
            'attendee_count'       => $attendeeCount,
            'fee_per_person'       => $feePerPerson,
            'amount_budget_adjust' => ($entryType === 'adjust') ? $amountBudgetAdjust : 0.00,
            'amount_withdraw'      => ($entryType === 'withdraw') ? $amountWithdraw : 0.00,
            'receipt_no'           => $this->request->getPost('receipt_no'),
            'entry_type'           => $entryType,
            'notes'                => $this->request->getPost('notes'),
            'created_by'           => $currentUser,
        ];

        $oldBudgetYear = null;
        if (!empty($id)) {
            $oldItem = $this->registrationModel->find($id);
            if ($oldItem) {
                $oldBudgetYear = $oldItem->budget_year;
            }
            $this->registrationModel->update($id, $dataSave);
            $msg = 'อัปเดตข้อมูลค่าลงทะเบียนสำเร็จ';
        } else {
            $this->registrationModel->insert($dataSave);
            $msg = 'บันทึกรายการค่าลงทะเบียนเรียบร้อยแล้ว';
        }

        // คำนวณยอดคงเหลือใหม่ทั้งปีงบประมาณ
        $this->registrationModel->recalculateRunningBalance($budgetYear);

        // หากมีการเปลี่ยนปีงบประมาณ ให้คำนวณยอดคงเหลือของปีเดิมด้วย
        if (!empty($oldBudgetYear) && $oldBudgetYear !== $budgetYear) {
            $this->registrationModel->recalculateRunningBalance($oldBudgetYear);
        }

        return $this->response->setJSON([
            'status'  => 'success',
            'message' => $msg
        ]);
    }

    /**
     * ลบรายการค่าลงทะเบียน (AJAX POST)
     */
    public function deleteRegistration($id = null)
    {
        if (!auth_is_superadmin() && !auth_can_access_system('budget_ordinance')) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'คุณไม่มีสิทธิ์ทำรายการนี้']);
        }

        if (!$id) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'ไม่พบรหัสรายการที่ต้องการลบ']);
        }

        $item = $this->registrationModel->find($id);
        if (!$item) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'ไม่พบข้อมูลในระบบ']);
        }

        $budgetYear = $item->budget_year;
        $this->registrationModel->delete($id);

        // คำนวณยอดคงเหลือใหม่หลังลบ
        $this->registrationModel->recalculateRunningBalance($budgetYear);

        return $this->response->setJSON([
            'status'  => 'success',
            'message' => 'ลบรายการเรียบร้อยแล้ว'
        ]);
    }

    /**
     * พิมพ์รายงานทะเบียนคุมค่าลงทะเบียน
     */
    public function printRegistration()
    {
        $selectedYear = $this->request->getGet('year') ?? get_budget_year();
        $data['selected_year'] = $selectedYear;
        $data['items'] = $this->registrationModel->getItemsByYear($selectedYear);
        $data['stats'] = $this->registrationModel->getSummaryStats($selectedYear);

        return view('Admin/AdminBudgetOrdinance/Registration/RegistrationPrint', $data);
    }
}
