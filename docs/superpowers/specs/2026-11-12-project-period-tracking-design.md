# Design Document: Project Budget Period & Time Status Tracking

## 1. Intent & Context
ผู้ใช้งานต้องการให้ระบบสามารถกำหนด "ห้วงเวลาการใช้เงิน/ดำเนินโครงการ" ให้กับแต่ละโครงการได้ เพื่อติดตามสถานะว่าโครงการใดอยู่ในช่วงเวลาดำเนินโครงการ และโครงการใดเลยกำหนดเวลาการใช้เงินแล้ว โดยแสดงป้ายเตือนและสีแจ้งเตือนอย่างชัดเจน แต่ไม่บล็อกการลงบันทึกเบิกจ่ายย้อนหลังของ Admin

## 2. Requirements & Goals
- **เป้าหมายหลัก (Goals):**
  - รองรับการกำหนด `start_date` (วันที่เริ่มต้น) และ `end_date` (วันที่สิ้นสุด) ของแต่ละโครงการ (NULLable)
  - คำนวณและแสดงผลสถานะห้วงเวลาเทียบกับวันที่ปัจจุบัน (`today`):
    - `active`: อยู่ในช่วงเวลาดำเนินโครงการ (สีเขียว `bg-success-soft text-success`)
    - `expired`: เลยกำหนดเวลาใช้เงินโครงการแล้ว (สีแดง `bg-danger-soft text-danger`)
    - `upcoming`: ยังไม่ถึงกำหนดเวลา (สีน้ำเงิน/เทา `bg-info-soft text-info`)
    - `unspecified`: ไม่ได้ระบุห้วงเวลา (สีเทาอ่อน)
  - แสดงป้ายสถานะในหน้ารายการโครงการฝั่ง Admin (`ProjectList.php`), หน้าทะเบียนคุม (`LedgerView.php`), และหน้าสรุปโครงการฝั่ง User (`UserBudgetSummary.php`)
  - อัปเดตฟอร์มบันทึก/แก้ไขโครงการในหน้าโครงสร้างแผน (`StructureSetting.php`) ให้มี Date Picker หรือ Input Date สำหรับวันเริ่มและสิ้นสุด
- **สิ่งที่ไม่ทำ (Non-Goals):**
  - ไม่บล็อกหรือห้ามการบันทึกเบิกจ่ายในทะเบียนคุม (Admin ยังลงรายการย้อนหลังได้ตามปกติ)

## 3. Approaches Considered
- **Approach 1: ระบุช่วงวันที่เริ่มต้น – วันที่สิ้นสุด (Start Date & End Date) [เลือกแนวทางนี้]**
  - ยืดหยุ่นสูงสุด รองรับทั้งโครงการระยะสั้นและระยะยาว คำนวณสถานะ 3 ระดับได้แม่นยำ
- **Approach 2: ระบุเฉพาะวันสิ้นสุดเดดไลน์ (End Date Only)**
  - กรอกช่องเดียวแต่ขาดความยืดหยุ่นสำหรับโครงการที่ยังไม่เริ่ม
- **Approach 3: ผูกกับเทอม/ไตรมาสอัตโนมัติ**
  - ขาดความคล่องตัวสำหรับโครงการที่จัดงานเฉพาะกิจ

## 4. Technical Architecture & Database
### 4.1 Database Changes (`tb_budget_projects`)
```sql
ALTER TABLE tb_budget_projects 
ADD COLUMN start_date DATE NULL DEFAULT NULL AFTER budget_source,
ADD COLUMN end_date DATE NULL DEFAULT NULL AFTER start_date;
```

### 4.2 Helper Function (`app/Common.php`)
สร้างฟังก์ชันกลาง `get_project_time_status(?string $startDate, ?string $endDate): array`
คืนค่า:
```php
[
    'status'     => 'active' | 'expired' | 'upcoming' | 'unspecified',
    'label'      => 'อยู่ในช่วงโครงการ' | 'เลยกำหนดเวลาแล้ว' | 'ยังไม่ถึงกำหนดเวลา' | 'ไม่ระบุช่วงเวลา',
    'badge_class'=> 'bg-success-soft text-success' | 'bg-danger-soft text-danger' | ...,
    'icon'       => 'bx-check-circle' | 'bx-time-five' | 'bx-calendar' | ...,
    'date_text'  => 'DD/MM/YYYY - DD/MM/YYYY',
    'days_left'  => int (จำนวนวันที่เหลือ หรือจำนวนวันที่เลยกำหนด)
]
```

### 4.3 UI Integrations
1. **`app/Views/Admin/AdminBudgetLedger/StructureSetting.php`**:
   - เพิ่มฟิลด์ `start_date` และ `end_date` ใน `#modalProject`
   - ปรับ JavaScript ให้รองรับการอ่านและเซฟข้อมูล
2. **`app/Controllers/ConAdminBudgetLedger.php`**:
   - อัปเดต `saveProject()` ให้รับ `start_date` และ `end_date`
3. **`app/Views/Admin/AdminBudgetLedger/ProjectList.php`**:
   - แสดง Badge สถานะห้วงเวลาโครงการและช่วงวันที่ในคอลัมน์ชื่อโครงการ
4. **`app/Views/Admin/AdminBudgetLedger/LedgerView.php`**:
   - แสดง Alert แถบเตือนด้านบนหัวทะเบียนคุม แสดงสถานะห้วงเวลาอย่างเด่นชัด
5. **`app/Views/User/UserHome/UserBudgetSummary.php`**:
   - แสดง Badge สถานะห้วงเวลาในตารางโครงการฝั่งผู้ใช้ทั่วไป

