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
                    <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
                        <div>
                            <h4 class="fw-bold mb-1" style="font-family: 'Plus Jakarta Sans', 'Sarabun', sans-serif;">
                                <i class="bx bx-shield-quarter text-warning me-2"></i>จัดการสิทธิ์การใช้งาน
                            </h4>
                            <p class="text-muted fs-7 mb-0">เพิ่ม แก้ไข และกำหนดสิทธิ์ผู้ใช้งานในระบบ</p>
                        </div>
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb mb-0 bg-transparent p-0">
                                <li class="breadcrumb-item"><a href="<?= base_url('Admin/Home'); ?>" class="text-warning">หน้าแรก</a></li>
                                <li class="breadcrumb-item active text-secondary" aria-current="page">จัดการสิทธิ์</li>
                            </ol>
                        </nav>
                    </div>
                </div>
            </div>

            <!-- Stats Mini Cards -->
            <div class="row mb-4">
                <div class="col-md-4 mb-3">
                    <div class="card border-0 shadow-sm p-3" style="border-radius: 12px; background: #fff;">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <span class="text-muted fs-8">จำนวนสิทธิ์ทั้งหมด</span>
                                <h3 class="fw-bold mb-0 text-orange mt-1" id="totalRoles">0</h3>
                            </div>
                            <div class="avatar bg-warning-soft rounded p-2 text-warning">
                                <i class="bx bx-shield fs-3"></i>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 mb-3">
                    <div class="card border-0 shadow-sm p-3" style="border-radius: 12px; background: #fff;">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <span class="text-muted fs-8">มีผู้ใช้งานแล้ว</span>
                                <h3 class="fw-bold mb-0 text-success mt-1" id="totalAssigned">0</h3>
                            </div>
                            <div class="avatar bg-success-soft rounded p-2 text-success">
                                <i class="bx bx-user-check fs-3"></i>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 mb-3">
                    <div class="card border-0 shadow-sm p-3" style="border-radius: 12px; background: #fff;">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <span class="text-muted fs-8">ยังไม่กำหนดผู้ใช้</span>
                                <h3 class="fw-bold mb-0 text-danger mt-1" id="totalUnassigned">0</h3>
                            </div>
                            <div class="avatar bg-danger-soft rounded p-2 text-danger">
                                <i class="bx bx-user-x fs-3"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tabs -->
            <ul class="nav nav-pills mb-4" role="tablist">
                <li class="nav-item" role="presentation">
                    <button type="button" class="nav-link active d-flex align-items-center" role="tab" data-bs-toggle="tab" data-bs-tab="roles">
                        <i class="bx bx-shield me-1"></i> รายการสิทธิ์
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button type="button" class="nav-link d-flex align-items-center" role="tab" data-bs-toggle="tab" data-bs-tab="assignments">
                        <i class="bx bx-user-plus me-1"></i> กำหนดผู้ใช้งาน
                    </button>
                </li>
            </ul>

            <!-- Tab Content -->
            <div class="tab-content p-0">

                <!-- Tab 1: Role List -->
                <div class="tab-pane fade show active" id="roles">
                    <div class="card border-0 shadow-sm" style="border-radius: 16px;">
                        <div class="card-header bg-white border-0 pt-4 px-4 d-flex align-items-center justify-content-between flex-wrap gap-3">
                            <div>
                                <h5 class="mb-1" style="font-weight: 700;"><i class="bx bx-list-ul me-2 text-warning"></i>รายการสิทธิ์</h5>
                                <p class="text-muted fs-8 mb-0">จัดการระดับสิทธิ์การใช้งานในระบบ</p>
                            </div>
                            <button type="button" class="btn btn-warning rounded-pill shadow-sm text-white" onclick="openRoleModal()">
                                <i class="bx bx-plus me-1"></i> เพิ่มสิทธิ์
                            </button>
                        </div>
                        <div class="card-body px-4 pb-4">
                            <div class="table-responsive">
                                <table id="roleTable" class="table table-hover align-middle" style="width:100%">
                                    <thead>
                                        <tr class="table-light text-secondary">
                                            <th style="width: 60px;">ลำดับ</th>
                                            <th>สิทธิ์</th>
                                            <th>ชื่อเรียก</th>
                                            <th style="width: 80px;">ระดับ</th>
                                            <th>ผู้ใช้งานปัจจุบัน</th>
                                            <?php if (auth_is_superadmin()): ?>
                                            <th style="width: 120px;">จัดการ</th>
                                            <?php endif; ?>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($Manager as $key => $role): ?>
                                        <tr>
                                            <td class="text-center"><span class="badge bg-label-secondary rounded-pill"><?= $key + 1 ?></span></td>
                                            <td>
                                                <?php
                                                switch ($role->admin_rloes_status) {
                                                    case 'superadmin': $badgeClass = 'bg-label-danger'; break;
                                                    case 'admin':      $badgeClass = 'bg-label-warning'; break;
                                                    case 'manager':    $badgeClass = 'bg-label-info'; break;
                                                    case 'Member':     $badgeClass = 'bg-label-success'; break;
                                                    default:           $badgeClass = 'bg-label-secondary'; break;
                                                }
                                                ?>
                                                <span class="badge <?= $badgeClass ?> fw-semibold"><?= esc($role->admin_rloes_status) ?></span>
                                            </td>
                                            <td class="fw-semibold"><?= esc($role->admin_rloes_nanetype) ?></td>
                                            <td class="text-center"><span class="badge bg-label-dark"><?= $role->admin_rloes_level ?></span></td>
                                            <td>
                                                <?php if ($role->admin_rloes_userid && $role->admin_rloes_userid !== 'system'): ?>
                                                    <span class="text-success"><i class="bx bx-user-check me-1"></i> <?= esc($role->admin_rloes_userid) ?></span>
                                                <?php else: ?>
                                                    <span class="text-muted"><i class="bx bx-user-x me-1"></i> ยังไม่กำหนด</span>
                                                <?php endif; ?>
                                            </td>
                                            <?php if (auth_is_superadmin()): ?>
                                            <td>
                                                <div class="d-flex gap-1">
                                                    <button type="button" class="btn btn-sm btn-icon btn-outline-warning rounded" onclick="editRole(<?= $role->admin_rloes_id ?>, '<?= esc($role->admin_rloes_status) ?>', '<?= esc($role->admin_rloes_nanetype) ?>', <?= $role->admin_rloes_level ?>)" title="แก้ไข">
                                                        <i class="bx bx-edit-alt"></i>
                                                    </button>
                                                    <button type="button" class="btn btn-sm btn-icon btn-outline-danger rounded" onclick="deleteRole(<?= $role->admin_rloes_id ?>, '<?= esc($role->admin_rloes_nanetype) ?>')" title="ลบ">
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
                        </div>
                    </div>
                </div>

                <!-- Tab 2: Personnel Assignment -->
                <div class="tab-pane fade" id="assignments">
                    <div class="card border-0 shadow-sm" style="border-radius: 16px;">
                        <div class="card-header bg-white border-0 pt-4 px-4">
                            <h5 class="mb-1" style="font-weight: 700;"><i class="bx bx-user-plus me-2 text-warning"></i>กำหนดผู้ใช้งาน</h5>
                            <p class="text-muted fs-8 mb-0">เลือกผู้ใช้งานสำหรับแต่ละสิทธิ์</p>
                        </div>
                        <div class="card-body px-4 pb-4">
                            <div class="table-responsive">
                                <table id="assignmentTable" class="table table-hover align-middle" style="width:100%">
                                    <thead>
                                        <tr class="table-light text-secondary">
                                            <th style="width: 60px;">ลำดับ</th>
                                            <th>สิทธิ์</th>
                                            <th>ชื่อเรียก</th>
                                            <th style="width: 35%;">ผู้ใช้งานปัจจุบัน</th>
                                            <th style="width: 100px;">บันทึก</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($Manager as $key => $role): ?>
                                        <tr>
                                            <td class="text-center"><span class="badge bg-label-secondary rounded-pill"><?= $key + 1 ?></span></td>
                                            <td>
                                                <?php
                                                switch ($role->admin_rloes_status) {
                                                    case 'superadmin': $badgeClass = 'bg-label-danger'; break;
                                                    case 'admin':      $badgeClass = 'bg-label-warning'; break;
                                                    case 'manager':    $badgeClass = 'bg-label-info'; break;
                                                    case 'Member':     $badgeClass = 'bg-label-success'; break;
                                                    default:           $badgeClass = 'bg-label-secondary'; break;
                                                }
                                                ?>
                                                <span class="badge <?= $badgeClass ?> fw-semibold"><?= esc($role->admin_rloes_status) ?></span>
                                            </td>
                                            <td class="fw-semibold"><?= esc($role->admin_rloes_nanetype) ?></td>
                                            <td>
                                                <select class="select2-assignment"
                                                        data-rloes-id="<?= $role->admin_rloes_id ?>"
                                                        data-rloes-nanetype="<?= esc($role->admin_rloes_nanetype) ?>">
                                                    <option value="">-- เลือกเจ้าหน้าที่ --</option>
                                                    <?php foreach ($NameTeacher as $teacher): ?>
                                                        <option value="<?= esc($teacher->pers_id) ?>"
                                                            <?= $role->admin_rloes_userid === $teacher->pers_id ? 'selected' : '' ?>>
                                                            <?= esc($teacher->pers_prefix . $teacher->pers_firstname . ' ' . $teacher->pers_lastname) ?>
                                                        </option>
                                                    <?php endforeach; ?>
                                                </select>
                                            </td>
                                            <td class="text-center">
                                                <button type="button" class="btn btn-sm btn-warning rounded-pill text-white" onclick="saveAssignment(this)">
                                                    <i class="bx bx-save me-1"></i> บันทึก
                                                </button>
                                            </td>
                                        </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
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

<style>
/* Select2 inside table - fix overflow & width */
.table .select2-container {
    min-width: 100%;
    display: block;
}
.table .select2-container .select2-selection--single {
    height: calc(2.60em + 0.875rem + 2px);
    border: 1px solid #d9dee3;
    border-radius: 0.375rem;
}
.table .select2-container--default .select2-selection--single .select2-selection__rendered {
    line-height: 2.4rem;
    padding-left: 0.875rem;
    color: #697a8d;
}
.table .select2-container--default .select2-selection--single .select2-selection__arrow {
    height: calc(2.60em + 0.875rem);
}
.table .select2-container--default .select2-selection--single .select2-selection__arrow b {
    border-color: #697a8d transparent transparent transparent;
}
/* Fix dropdown z-index inside modal */
.modal .select2-container--open .select2-dropdown {
    z-index: 1056;
}
/* Stats cards color fix */
.text-orange { color: #ff9800 !important; }
.bg-warning-soft { background-color: rgba(255, 152, 0, 0.12) !important; }
.bg-success-soft { background-color: rgba(40, 199, 111, 0.12) !important; }
.bg-danger-soft  { background-color: rgba(234, 84, 85, 0.12) !important; }
/* Fix tabs spacing */
.nav-pills .nav-link.active {
    background-color: #ff9800 !important;
    color: #fff !important;
}
.nav-pills .nav-link {
    color: #697a8d;
}
.nav-pills .nav-link:hover:not(.active) {
    color: #ff9800;
}
</style>

<!-- Modal: Add/Edit Role -->
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
                        <label class="form-label fw-semibold text-dark" for="role_status">สิทธิ์ (status) <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="status" id="role_status" placeholder="เช่น admin, manager, superadmin, Member" required>
                        <div class="invalid-feedback">กรุณาระบุชื่อสิทธิ์</div>
                        <div class="form-text text-muted">ระบุเป็นภาษาอังกฤษ เช่น admin, manager, superadmin</div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold text-dark" for="role_nanetype">ชื่อเรียกสิทธิ์ <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="nanetype" id="role_nanetype" placeholder="เช่น ผู้ดูแลระบบ" required>
                        <div class="invalid-feedback">กรุณาระบุชื่อเรียกสิทธิ์</div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold text-dark" for="role_level">ระดับความสำคัญ (level) <span class="text-danger">*</span></label>
                        <input type="number" class="form-control" name="level" id="role_level" placeholder="เช่น 1 (น้อย = สูงสุด)" min="0" required>
                        <div class="invalid-feedback">กรุณาระบุระดับความสำคัญ</div>
                        <div class="form-text text-muted">ตัวเลขน้อย = สำคัญมาก เช่น superadmin = 1, admin = 2</div>
                    </div>
                </div>
                <div class="modal-footer bg-light px-4 py-3 border-top" style="border-radius: 0 0 16px 16px;">
                    <button type="button" class="btn btn-outline-secondary rounded-pill" data-bs-dismiss="modal">ยกเลิก</button>
                    <button type="submit" class="btn btn-warning rounded-pill px-4 text-white shadow-sm">
                        <i class="bx bx-save me-1"></i> บันทึก
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal: Delete Confirmation -->
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
                <p class="mb-1 fw-semibold">คุณต้องการลบสิทธิ์นี้?</p>
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
