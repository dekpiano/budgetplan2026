# Project Budget Period & Time Status Tracking Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** ให้ระบบสามารถกำหนดห้วงเวลาการใช้เงินโครงการ (วันที่เริ่มต้น - สิ้นสุด) และแสดงป้ายเตือนสถานะเมื่ออยู่ในช่วงโครงการ หรือเลยกำหนดเวลาใช้เงินโครงการแล้ว

**Architecture:** 
1. เพิ่มคอลัมน์ `start_date` (DATE NULL) และ `end_date` (DATE NULL) ในตาราง `tb_budget_projects`
2. สร้างฟังก์ชัน Helper กลาง `get_project_time_status(?string $startDate, ?string $endDate)` ใน `app/Common.php` เพื่อคำนวณสถานะ (`active`, `expired`, `upcoming`, `unspecified`)
3. รองรับการบันทึก/แก้ไขใน `ConAdminBudgetLedger::saveProject()` และฟอร์ม Modal ใน `StructureSetting.php`
4. แสดงผลป้ายสถานะและแถบแจ้งเตือนใน `ProjectList.php`, `LedgerView.php`, และ `UserBudgetSummary.php`

**Tech Stack:** PHP 8.2+, CodeIgniter 4, MySQL, Bootstrap 5, Sneat Admin Template

**Spec:** `docs/superpowers/specs/2026-11-12-project-period-tracking-design.md`

## Global Constraints
- วันที่ในฐานข้อมูลเก็บเป็นฟอร์แมต `YYYY-MM-DD` หรือ `NULL`
- การเลยกำหนดเวลาเป็นการเตือนด้วยป้ายสี (`badge`) และ Alert เท่านั้น ต้องไม่บล็อกการบันทึกรายการในทะเบียนคุม
- ป้ายสถานะต้องรองรับการแสดงผลภาษาไทยที่เข้าใจง่าย เช่น "อยู่ในช่วงโครงการ", "เลยกำหนดเวลาแล้ว", "ยังไม่ถึงกำหนด"

## Review Focus
1. กรณีไม่ได้ระบุวันเริ่มต้น หรือไม่ได้ระบุวันสิ้นสุด (NULL) ต้องไม่เกิด PHP Warning หรือพัง และให้คืนสถานะ `unspecified`
2. วันที่สิ้นสุดตรงกับวันปัจจุบัน (วันนี้เป็นวันสุดท้าย) ต้องถือว่ายัง `active` (อยู่ในช่วงโครงการ)
3. การแสดงผลวันที่ต้องแปลงเป็น พ.ศ. (Thai Buddhist Era) สวยงาม อ่านง่าย

---

### Task 1: Database Schema Migration
**Files:**
- Create: `database/migrations/2026-11-12_add_period_dates_to_projects.sql`
- Modify: `database/BudgetPlan2026_Clean_Install.sql`

- [ ] **Step 1: Write migration SQL script**
- [ ] **Step 2: Execute ALTER TABLE on database via docker**
- [ ] **Step 3: Update `BudgetPlan2026_Clean_Install.sql` schema**
- [ ] **Step 4: Verify columns exist using database describe query**

---

### Task 2: Core Helper Function `get_project_time_status()`
**Files:**
- Modify: `app/Common.php`
- Create: `writable/test_time_status.php` (temporary test script)

- [ ] **Step 1: Write unit test script covering 4 cases (`active`, `expired`, `upcoming`, `unspecified`)**
- [ ] **Step 2: Implement `get_project_time_status(?string $startDate, ?string $endDate): array` in `app/Common.php`**
- [ ] **Step 3: Run test script to verify all 4 test cases pass**
- [ ] **Step 4: Clean up temporary test script**

---

### Task 3: Backend Controller Integration
**Files:**
- Modify: `app/Controllers/ConAdminBudgetLedger.php`

- [ ] **Step 1: Update `saveProject()` in `ConAdminBudgetLedger.php` to receive and sanitize `start_date` and `end_date`**
- [ ] **Step 2: Run PHP lint to verify syntax**

---

### Task 4: Admin Structure Setting View & Modal
**Files:**
- Modify: `app/Views/Admin/AdminBudgetLedger/StructureSetting.php`

- [ ] **Step 1: Add input fields `start_date` and `end_date` to `#modalProject` form**
- [ ] **Step 2: Update JavaScript `openModalProject` to populate `start_date` and `end_date` when editing existing projects, and clear them on new project**
- [ ] **Step 3: Update project card display in `StructureSetting.php` to show time period badges**

---

### Task 5: Admin Project List & Ledger View Integration
**Files:**
- Modify: `app/Views/Admin/AdminBudgetLedger/ProjectList.php`
- Modify: `app/Views/Admin/AdminBudgetLedger/LedgerView.php`

- [ ] **Step 1: Add time status badge column/indicator in `ProjectList.php`**
- [ ] **Step 2: Add time status alert banner in `LedgerView.php` (header of ledger)**

---

### Task 6: User Budget Summary View Integration
**Files:**
- Modify: `app/Views/User/UserHome/UserBudgetSummary.php`

- [ ] **Step 1: Add time status badge next to project name or under project info in `UserBudgetSummary.php`**

---

### Task 7: Final Verification & Git Commit
- [ ] **Step 1: Lint all modified PHP files**
- [ ] **Step 2: Verify in browser that dates can be saved and badges display accurately**
- [ ] **Step 3: Git commit with descriptive Thai message and push**

