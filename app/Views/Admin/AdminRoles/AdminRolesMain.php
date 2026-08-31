<!-- Layout container -->
<div class="layout-page">
    <?php echo view('Admin/AdminLeyout/AdminNavbar'); ?>

    <!-- Content wrapper -->
    <div class="content-wrapper">
        <!-- Content -->
        <div class="container-xxl flex-grow-1 container-p-y">

            <!-- Title Header -->
            <div class="row mb-4">
                <div class="col-12">
                    <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
                        <div>
                            <h4 class="fw-bold mb-1" style="font-family: 'Plus Jakarta Sans', 'Sarabun', sans-serif;">
                                <i class="bx bx-shield-quarter text-warning me-2"></i>จัดการและกำหนดสิทธิ์ผู้ใช้งาน
                            </h4>
                            <p class="text-muted fs-7 mb-0">เลือกเพิ่มบุคลากรและกำหนดสิทธิ์การทำงาน (1 คน สามารถทำงานได้หลายเมนู/หลายบทบาท)</p>
                        </div>
                        <div class="d-flex align-items-center gap-2">
                            <?php if (auth_is_superadmin()): ?>
                            <button type="button" class="btn btn-primary rounded-pill px-3 shadow-sm" onclick="openAddPersonnelModal()">
                                <i class="bx bx-user-plus me-1"></i> เพิ่มผู้ใช้งานและกำหนดสิทธิ์
                            </button>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Stats 3 Mini Cards -->
            <div class="row mb-4 g-3">
                <div class="col-12 col-sm-6 col-md-4">
                    <div class="card border-0 shadow-sm p-3 h-100" style="border-radius: 14px; background: #fff;">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <span class="text-muted fs-8 fw-semibold">ผู้ใช้งานที่ได้รับสิทธิ์ในระบบ</span>
                                <h3 class="fw-bold mb-0 text-primary mt-1"><?= count($AssignedTeachers) ?> <span class="fs-7 text-muted fw-normal">คน</span></h3>
                            </div>
                            <div class="avatar bg-primary-soft rounded p-2 text-primary">
                                <i class="bx bx-user-check fs-3"></i>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-sm-6 col-md-4">
                    <div class="card border-0 shadow-sm p-3 h-100" style="border-radius: 14px; background: #fff;">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <span class="text-muted fs-8 fw-semibold">ระบบที่กำหนดสิทธิ์ได้ทั้งหมด</span>
                                <h3 class="fw-bold mb-0 text-warning mt-1"><?= $TotalSystems ?> <span class="fs-7 text-muted fw-normal">ระบบ</span></h3>
                            </div>
                            <div class="avatar bg-warning-soft rounded p-2 text-warning">
                                <i class="bx bx-layer fs-3"></i>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-sm-12 col-md-4">
                    <div class="card border-0 shadow-sm p-3 h-100" style="border-radius: 14px; background: #fff;">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <span class="text-muted fs-8 fw-semibold">บุคลากรทั้งหมดในโรงเรียน</span>
                                <h3 class="fw-bold mb-0 text-secondary mt-1"><?= count($AllTeachers) ?> <span class="fs-7 text-muted fw-normal">คน</span></h3>
                            </div>
                            <div class="avatar bg-light rounded p-2 text-secondary">
                                <i class="bx bx-group fs-3"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Navigation Tabs -->
            <ul class="nav nav-pills mb-4" role="tablist">
                <li class="nav-item" role="presentation">
                    <button type="button" class="nav-link active d-flex align-items-center" role="tab" data-bs-toggle="tab" data-bs-tab="tab-assigned-personnel">
                        <i class="bx bx-user-check me-1"></i> รายชื่อผู้ได้รับสิทธิ์ในระบบ (<?= count($AssignedTeachers) ?>)
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button type="button" class="nav-link d-flex align-items-center" role="tab" data-bs-toggle="tab" data-bs-tab="tab-system-summary">
                        <i class="bx bx-layer me-1"></i> ระบบที่เปิดให้กำหนดสิทธิ์
                    </button>
                </li>
            </ul>

            <!-- Tab Content -->
            <div class="tab-content p-0">

                <!-- ==================== TAB 1: ASSIGNED PERSONNEL LIST ==================== -->
                <div class="tab-pane fade show active" id="tab-assigned-personnel">
                    <div class="card border-0 shadow-sm" style="border-radius: 16px;">
                        <div class="card-header bg-white border-0 pt-4 px-4 d-flex align-items-center justify-content-between flex-wrap gap-3">
                            <div>
                                <h5 class="mb-1 fw-bold text-dark"><i class="bx bx-id-card me-2 text-primary"></i>ผู้ใช้งานที่มีสิทธิ์ในระบบ</h5>
                                <p class="text-muted fs-8 mb-0">แสดงเฉพาะบุคลากรที่ได้รับการกำหนดสิทธิ์การใช้งาน สามารถแก้ไขสิทธิ์หรือยกเลิกสิทธิ์ได้ตลอดเวลา</p>
                            </div>
                            <?php if (auth_is_superadmin()): ?>
                            <button type="button" class="btn btn-sm btn-primary rounded-pill px-3 shadow-sm" onclick="openAddPersonnelModal()">
                                <i class="bx bx-user-plus me-1"></i> เพิ่มผู้ใช้งาน
                            </button>
                            <?php endif; ?>
                        </div>
                        <div class="card-body px-4 pb-4">
                            <?php if (empty($AssignedTeachers)): ?>
                                <div class="text-center py-5">
                                    <div class="avatar avatar-xl bg-light rounded-circle mx-auto mb-3 text-muted d-flex align-items-center justify-content-center" style="width: 70px; height: 70px;">
                                        <i class="bx bx-user-x fs-1"></i>
                                    </div>
                                    <h6 class="fw-bold text-dark mb-1">ยังไม่มีผู้ใช้งานที่ได้รับสิทธิ์ในระบบ</h6>
                                    <p class="text-muted fs-8 mb-3">กดปุ่มด้านล่างเพื่อเลือกบุคลากรและกำหนดสิทธิ์การทำงาน</p>
                                    <?php if (auth_is_superadmin()): ?>
                                    <button type="button" class="btn btn-primary rounded-pill px-4 shadow-sm" onclick="openAddPersonnelModal()">
                                        <i class="bx bx-user-plus me-1"></i> เพิ่มผู้ใช้งานและกำหนดสิทธิ์
                                    </button>
                                    <?php endif; ?>
                                </div>
                            <?php else: ?>
                                <div class="table-responsive p-1 p-md-2">
                                    <table id="assignedPersonnelTable" class="table table-hover align-middle mb-0 table-fit-screen">
                                        <thead>
                                            <tr class="table-light text-secondary">
                                                <th style="width: 5%;" class="text-center">#</th>
                                                <th style="width: 25%;">ชื่อ - สกุล บุคลากร</th>
                                                <th style="width: 20%;">ตำแหน่ง / กลุ่มสาระฯ</th>
                                                <th style="width: 38%;">ระบบที่บุคลากรสามารถทำงานได้</th>
                                                <?php if (auth_is_superadmin()): ?>
                                                <th style="width: 12%;" class="text-center">จัดการ</th>
                                                <?php endif; ?>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($AssignedTeachers as $idx => $t): ?>
                                            <tr>
                                                <td class="text-center text-muted fs-8"><?= $idx + 1 ?></td>
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        <div class="avatar avatar-sm me-2" style="width:42px;height:42px;flex:0 0 42px;">
                                                            <?php if (!empty($t['img'])): ?>
                                                                <img src="https://personnel.skj.ac.th/uploads/admin/Personnal/<?= esc($t['img']) ?>" alt="<?= esc($t['fullname']) ?>" class="rounded-circle w-100 h-100" style="object-fit: cover;" loading="lazy" onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                                                                <span class="avatar-initial rounded-circle bg-label-primary font-weight-bold" style="display:none;align-items:center;justify-content:center;width:100%;height:100%;"><?= mb_substr($t['fullname'], 0, 1, 'UTF-8') ?></span>
                                                            <?php else: ?>
                                                                <span class="avatar-initial rounded-circle bg-label-primary font-weight-bold" style="display:flex;align-items:center;justify-content:center;width:100%;height:100%;">
                                                                    <?= mb_substr($t['fullname'], 0, 1, 'UTF-8') ?>
                                                                </span>
                                                            <?php endif; ?>
                                                        </div>
                                                        <div>
                                                            <span class="fw-bold text-dark d-block"><?= esc($t['fullname']) ?></span>
                                                            <small class="text-muted font-monospace fs-8">ID: <?= esc($t['pers_id']) ?></small>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td>
                                                    <span class="badge bg-label-secondary text-truncate" style="max-width: 190px;">
                                                        <?= esc($t['position']) ?>
                                                    </span>
                                                </td>
                                                <td>
                                                    <div class="d-flex flex-wrap gap-1 align-items-center">
                                                        <?php foreach ($t['systems'] as $systemCode): ?>
                                                            <?php $system = $SystemMap[$systemCode] ?? null; ?>
                                                            <?php if ($system): ?>
                                                            <span class="badge bg-label-primary rounded-pill py-1 px-2">
                                                                <i class="bx <?= esc($system->admin_system_icon ?: 'bx-grid-alt') ?> me-1"></i><?= esc($system->admin_system_name) ?>
                                                            </span>
                                                            <?php endif; ?>
                                                        <?php endforeach; ?>
                                                    </div>
                                                </td>
                                                <?php if (auth_is_superadmin()): ?>
                                                <td class="text-center">
                                                    <div class="d-inline-flex gap-1">
                                                        <button type="button" 
                                                            class="btn btn-sm btn-outline-primary rounded-pill px-2 shadow-none btn-edit-user-roles"
                                                            data-pers-id="<?= esc($t['pers_id']) ?>"
                                                            data-fullname="<?= esc($t['fullname']) ?>"
                                                            data-position="<?= esc($t['position']) ?>"
                                                            data-systems='<?= htmlspecialchars(json_encode($t['systems']), ENT_QUOTES, 'UTF-8') ?>'
                                                            title="แก้ไขสิทธิ์">
                                                            <i class="bx bx-edit-alt me-1"></i> แก้ไข
                                                        </button>
                                                        <button type="button" 
                                                            class="btn btn-sm btn-outline-danger rounded-circle btn-icon shadow-none btn-delete-user-roles"
                                                            data-pers-id="<?= esc($t['pers_id']) ?>"
                                                            data-fullname="<?= esc($t['fullname']) ?>"
                                                            title="ลบสิทธิ์ทั้งหมด">
                                                            <i class="bx bx-trash"></i>
                                                        </button>
                                                    </div>
                                                </td>
                                                <?php endif; ?>
                                            </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <!-- ==================== TAB 2: SYSTEM SUMMARY ==================== -->
                <div class="tab-pane fade" id="tab-system-summary">
                    <div class="card border-0 shadow-sm" style="border-radius: 16px;">
                        <div class="card-header bg-white border-0 pt-4 px-4">
                            <h5 class="mb-1 fw-bold text-dark"><i class="bx bx-layer me-2 text-warning"></i>ระบบทั้งหมดที่สามารถกำหนดสิทธิ์ได้</h5>
                            <p class="text-muted fs-8 mb-0">รายการนี้มาจากทะเบียนระบบกลาง เพิ่มระบบใหม่ภายหลังได้โดยไม่ต้องผูกกับ Role ของบุคลากร</p>
                        </div>
                        <div class="card-body px-4 pb-4">
                            <div class="row g-3">
                                <?php foreach ($SystemDefs as $system): ?>
                                <div class="col-12 col-md-6 col-xl-4">
                                    <div class="card h-100 border p-3" style="border-radius: 12px;">
                                        <div class="d-flex align-items-start gap-3">
                                            <div class="avatar bg-warning-soft rounded p-2 text-warning flex-shrink-0">
                                                <i class="bx <?= esc($system->admin_system_icon ?: 'bx-grid-alt') ?> fs-3"></i>
                                            </div>
                                            <div class="flex-grow-1">
                                                <div class="fw-bold text-dark"><?= esc($system->admin_system_name) ?></div>
                                                <div class="text-muted fs-8 mt-1"><?= esc($system->admin_system_description) ?></div>
                                                <span class="badge bg-label-secondary mt-2">ระบบ: <?= esc($system->admin_system_code) ?></span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
        <!-- / Content -->
        <div class="content-backdrop fade"></div>
    </div>
    <!-- Content wrapper -->
</div>
<!-- / Layout page -->

<!-- ==================== MODAL: ADD / EDIT PERSONNEL ROLES (CHECKBOXES) ==================== -->
<div class="modal fade" id="modalPersonnelRoles" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 18px;">
            <div class="modal-header bg-primary py-3 px-4 text-white">
                <div>
                    <h5 class="modal-title text-white fw-bold mb-0" id="modalPersonnelRolesTitle">
                        <i class="bx bx-user-plus me-2"></i>เพิ่มผู้ใช้งานและกำหนดสิทธิ์
                    </h5>
                    <small class="text-white-50" id="modalPersonnelRolesSubtitle">เลือกบุคลากรและเลือกสิทธิ์การใช้งานที่ต้องการมอบหมาย</small>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form id="formPersonnelRoles">
                <input type="hidden" name="pers_id" id="assignPersId">
                <div class="modal-body p-4">
                    
                    <!-- Teacher Selection Area (Visible when Adding) -->
                    <div id="sectionSelectTeacher" class="mb-4">
                        <label class="form-label fw-bold text-dark fs-7" for="selectTeacherDropdown">
                            <i class="bx bx-search me-1 text-primary"></i>เลือกบุคลากรที่ต้องการเพิ่มสิทธิ์ <span class="text-danger">*</span>
                        </label>
                        <select class="form-select select2-teacher" id="selectTeacherDropdown" style="width: 100%;">
                            <option value="">-- พิมพ์ค้นหา หรือเลือกชื่อบุคลากร --</option>
                            <?php foreach ($AllTeachers as $teacher): ?>
                                <option value="<?= esc($teacher->pers_id) ?>" data-name="<?= esc($teacher->pers_prefix . $teacher->pers_firstname . ' ' . $teacher->pers_lastname) ?>" data-position="<?= esc(($PositionMap[$teacher->pers_position] ?? ($LearningMap[$teacher->pers_learning] ?? 'บุคลากร'))) ?>">
                                    <?= esc($teacher->pers_prefix . $teacher->pers_firstname . ' ' . $teacher->pers_lastname) ?>
                                    (<?= esc(($PositionMap[$teacher->pers_position] ?? ($LearningMap[$teacher->pers_learning] ?? 'บุคลากร'))) ?>)
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <!-- Teacher Display Card (Visible when Editing) -->
                    <div id="sectionDisplayTeacher" class="mb-4 d-none">
                        <div class="card bg-light border-0 p-3" style="border-radius: 12px;">
                            <div class="d-flex align-items-center">
                                <div class="avatar avatar-md me-3">
                                    <span class="avatar-initial rounded-circle bg-primary text-white fw-bold" id="editTeacherInitial">U</span>
                                </div>
                                <div>
                                    <h6 class="fw-bold text-dark mb-0" id="editTeacherName">-</h6>
                                    <small class="text-muted" id="editTeacherMeta">-</small>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Quick action buttons -->
                    <div class="d-flex justify-content-between align-items-center mb-3 pb-2 border-bottom">
                        <span class="fw-bold text-dark fs-7">
                            <i class="bx bx-check-square me-1 text-primary"></i>เลือกระบบที่บุคลากรสามารถทำงานได้ (เลือกได้หลายระบบ):
                        </span>
                        <div class="d-flex gap-2">
                            <button type="button" class="btn btn-xs btn-outline-primary rounded-pill px-2" id="btnSelectAllSystems">
                                <i class="bx bx-check-double me-1"></i> เลือกทั้งหมด
                            </button>
                            <button type="button" class="btn btn-xs btn-outline-secondary rounded-pill px-2" id="btnClearAllSystems">
                                <i class="bx bx-x me-1"></i> ล้างค่า
                            </button>
                        </div>
                    </div>

                    <!-- System Checkbox Cards Grouped by Category -->
                    <?php
                    $systemCategories = [
                        'budget' => ['title' => 'ระบบงบประมาณ', 'icon' => 'bx-bar-chart-alt-2', 'codes' => ['budget_ledger', 'budget_structure']],
                        'procurement' => ['title' => 'ระบบจัดซื้อจัดจ้าง', 'icon' => 'bx-cart', 'codes' => ['procurement', 'registry_purchase', 'registry_hire']],
                        'finance' => ['title' => 'ระบบการเงิน', 'icon' => 'bx-money', 'codes' => ['money_receipt']],
                    ];
                    $systemsByCode = [];
                    foreach ($SystemDefs as $system) $systemsByCode[$system->admin_system_code] = $system;
                    ?>
                    <div id="systemCheckboxContainer">
                        <?php foreach ($systemCategories as $category): ?>
                            <?php
                            $categorySystems = [];
                            foreach ($category['codes'] as $code) {
                                if (isset($systemsByCode[$code])) $categorySystems[] = $systemsByCode[$code];
                            }
                            if (!$categorySystems) continue;
                            ?>
                            <div class="system-category mb-4">
                                <div class="d-flex align-items-center gap-2 px-3 py-2 mb-2 rounded-3 bg-light border">
                                    <i class="bx <?= esc($category['icon']) ?> text-primary fs-5"></i>
                                    <span class="fw-bold text-dark"><?= esc($category['title']) ?></span>
                                    <span class="badge bg-label-secondary ms-auto"><?= count($categorySystems) ?> ระบบ</span>
                                </div>
                                <div class="row g-3">
                                    <?php foreach ($categorySystems as $system): ?>
                                    <div class="col-md-6">
                                        <label class="role-checkbox-card d-flex align-items-start p-3 h-100 position-relative" for="chk_system_<?= esc($system->admin_system_code) ?>">
                                            <div class="form-check me-3 mt-1">
                                                <input class="form-check-input system-chk" type="checkbox" name="systems[]" value="<?= esc($system->admin_system_code) ?>" id="chk_system_<?= esc($system->admin_system_code) ?>">
                                            </div>
                                            <div class="flex-grow-1">
                                                <div class="d-flex align-items-center gap-2 mb-1">
                                                    <span class="badge bg-label-primary rounded-pill px-2 py-1 fs-8">
                                                        <i class="bx <?= esc($system->admin_system_icon ?: 'bx-grid-alt') ?> me-1"></i><?= esc($system->admin_system_name) ?>
                                                    </span>
                                                </div>
                                                <p class="text-muted fs-8 mb-0"><?= esc($system->admin_system_description) ?></p>
                                            </div>
                                        </label>
                                    </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>

                </div>
                <div class="modal-footer bg-light px-4 py-3 border-top d-flex justify-content-between" style="border-radius: 0 0 18px 18px;">
                    <button type="button" class="btn btn-outline-secondary rounded-pill px-3" data-bs-dismiss="modal">ยกเลิก</button>
                    <button type="submit" class="btn btn-primary rounded-pill px-4 shadow-sm" id="btnSavePersonnelRoles">
                        <i class="bx bx-save me-1"></i> บันทึกสิทธิ์
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- ==================== MODAL: ADD / EDIT ROLE METADATA ==================== -->
<div class="modal fade" id="roleModal" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 16px;">
            <div class="modal-header bg-warning py-3 px-4">
                <h5 class="modal-title text-white fw-bold" id="roleModalTitle"><i class="bx bx-shield-plus me-2"></i>เพิ่มสิทธิ์</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form id="roleForm" class="needs-validation" novalidate>
                <div class="modal-body p-4">
                    <input type="hidden" name="id" id="role_id">
                    <div class="mb-3">
                        <label class="form-label fw-semibold text-dark" for="role_status">รหัสสิทธิ์ (status) <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="status" id="role_status" placeholder="เช่น planner, finance, procurement" required>
                        <div class="invalid-feedback">กรุณาระบุรหัสสิทธิ์</div>
                        <div class="form-text text-muted">ภาษาอังกฤษตัวเล็ก ไม่มีเว้นวรรค เช่น finance, planner</div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold text-dark" for="role_nanetype">ชื่อเรียกบทบาท <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="nanetype" id="role_nanetype" placeholder="เช่น เจ้าหน้าที่การเงินและบัญชี" required>
                        <div class="invalid-feedback">กรุณาระบุชื่อเรียกสิทธิ์</div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold text-dark" for="role_level">ระดับความสำคัญ (Level) <span class="text-danger">*</span></label>
                        <input type="number" class="form-control" name="level" id="role_level" placeholder="เช่น 1 = สูงสุด, 2, 3..." min="1" required>
                        <div class="invalid-feedback">กรุณาระบุระดับความสำคัญ</div>
                    </div>
                </div>
                <div class="modal-footer bg-light px-4 py-3 border-top" style="border-radius: 0 0 16px 16px;">
                    <button type="button" class="btn btn-outline-secondary rounded-pill" data-bs-dismiss="modal">ยกเลิก</button>
                    <button type="submit" class="btn btn-warning rounded-pill px-4 text-white shadow-sm" id="btnSaveRoleMeta">
                        <i class="bx bx-save me-1"></i> บันทึก
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- ==================== MODAL: DELETE CONFIRMATION ==================== -->
<div class="modal fade" id="deleteConfirmModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 16px;">
            <div class="modal-header bg-danger py-3 px-4">
                <h5 class="modal-title text-white fw-bold"><i class="bx bx-trash me-2"></i>ยืนยันการลบ</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4 text-center">
                <div class="mb-3">
                    <i class="bx bx-error-circle text-danger" style="font-size: 3rem;"></i>
                </div>
                <p class="mb-1 fw-semibold">คุณต้องการลบประเภทสิทธิ์นี้?</p>
                <p class="text-muted mb-0" id="deleteRoleName"></p>
            </div>
            <div class="modal-footer bg-light px-4 py-3 border-top justify-content-center" style="border-radius: 0 0 16px 16px;">
                <button type="button" class="btn btn-outline-secondary rounded-pill" data-bs-dismiss="modal">ยกเลิก</button>
                <button type="button" class="btn btn-danger rounded-pill px-4 text-white shadow-sm" id="btnConfirmDelete">
                    <i class="bx bx-trash me-1"></i> ลบ
                </button>
            </div>
        </div>
    </div>
</div>

<style>
.role-checkbox-card {
    border: 1.5px solid #e0e4e8;
    border-radius: 12px;
    background: #fff;
    cursor: pointer;
    transition: all 0.2s ease-in-out;
}
.role-checkbox-card:hover {
    border-color: #696cff;
    background-color: #f8f9ff;
    box-shadow: 0 4px 12px rgba(105, 108, 255, 0.08);
}
.role-checkbox-card input[type="checkbox"]:checked ~ div {
    color: #566a7f;
}
.role-checkbox-card.is-active {
    border-color: #696cff;
    background-color: #f4f5ff;
}
.role-checkbox-card .form-check-input {
    width: 1.35em;
    height: 1.35em;
    cursor: pointer;
}
.bg-primary-soft { background-color: rgba(105, 108, 255, 0.1) !important; }
.bg-warning-soft { background-color: rgba(255, 171, 0, 0.12) !important; }
.bg-success-soft { background-color: rgba(113, 221, 55, 0.12) !important; }
.bg-danger-soft  { background-color: rgba(255, 62, 29, 0.12) !important; }
.nav-pills .nav-link.active {
    background-color: #696cff !important;
    color: #fff !important;
}
.nav-pills .nav-link {
    color: #697a8d;
}
.fs-7 { font-size: 0.85rem !important; }
.fs-8 { font-size: 0.78rem !important; }

/* Select2 in modal dropdown z-index */
.modal .select2-container {
    z-index: 1056;
}
.modal .select2-container--default .select2-selection--single {
    height: calc(2.5em + 0.75rem + 2px);
    border: 1px solid #d9dee3;
    border-radius: 0.375rem;
    padding: 0.375rem 0.75rem;
}
.modal .select2-container--default .select2-selection--single .select2-selection__rendered {
    line-height: 2.2rem;
    color: #566a7f;
}
.modal .select2-container--default .select2-selection--single .select2-selection__arrow {
    height: 100%;
}
</style>

