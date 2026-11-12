<!-- Layout container -->
<div class="layout-page">
    <?php echo view('Admin/AdminLeyout/AdminNavbar'); ?>

    <!-- Content wrapper -->
    <div class="content-wrapper">
        <!-- Content -->
        <div class="container-xxl flex-grow-1 container-p-y">

            <!-- Hero Banner -->
            <div class="row mb-4">
                <div class="col-12">
                    <div class="card border-0 position-relative overflow-hidden luxury-admin-hero-card">
                        <div class="hero-glow-1"></div>
                        <div class="hero-glow-2"></div>
                        
                        <div class="card-body p-4 p-md-4 position-relative z-index-1">
                            <div class="row align-items-center">
                                <div class="col-lg-7">
                                    <div class="d-flex align-items-center mb-2">
                                        <span class="system-badge">BUDGET PLAN 2569</span>
                                        <div class="status-dot-pulse ms-2"></div>
                                    </div>
                                    <h2 class="hero-title mb-1">ทะเบียนคุมเงินงบประมาณรายจ่าย</h2>
                                    <p class="hero-subtitle mb-0">
                                        ตรวจสอบยอดงบประมาณจัดสรร ติดตามยอดเงินยืมทดรองจ่าย และยอดคงเหลือสุทธิ
                                    </p>
                                </div>
                                <div class="col-lg-5 text-end mt-3 mt-lg-0">
                                    <div class="d-inline-flex flex-wrap gap-2 justify-content-lg-end">
                                        <!-- Year Filter -->
                                        <form method="get" action="<?= base_url('Admin/BudgetLedger') ?>" class="d-inline-flex align-items-center">
                                            <div class="input-group input-group-sm">
                                                <span class="input-group-text bg-white border-0 text-muted"><i class="bx bx-calendar"></i></span>
                                                <select class="form-select border-0 shadow-sm fw-semibold" id="yearFilter" name="year" onchange="this.form.submit()" style="border-radius: 0 20px 20px 0;">
                                                    <?php foreach ($years as $y): ?>
                                                        <option value="<?= $y->budget_year ?>" <?= $y->budget_year == $selected_year ? 'selected' : '' ?>>
                                                            ปีงบฯ พ.ศ. <?= $y->budget_year ?>
                                                        </option>
                                                    <?php endforeach; ?>
                                                </select>
                                            </div>
                                        </form>

                                        <a href="<?= base_url('Admin/BudgetLedger/Structure') ?>" class="btn btn-warning rounded-pill px-3 shadow-sm text-white fw-semibold">
                                            <i class="bx bx-cog me-1"></i> ตั้งค่ายุทธศาสตร์ / โครงการ
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Summary Statistics Cards -->
            <?php
                $sumAllocated = 0;
                $sumBalance = 0;
                $sumSpent = 0;
                $sumBorrow = 0;
                foreach ($projects as $p) {
                    $sumAllocated += (float)$p->allocated_budget;
                    $sumBalance += (float)$p->current_balance;
                    $sumSpent += (float)$p->total_spent;
                    $sumBorrow += (float)$p->total_borrow;
                }
            ?>
            <div class="row mb-4 g-3">
                <div class="col-lg-4 col-md-6">
                    <div class="card luxury-stat-card border-0 shadow-sm h-100">
                        <div class="card-body p-4">
                            <div class="d-flex align-items-center justify-content-between mb-3">
                                <div class="stat-icon-container">
                                    <i class="bx bx-wallet-alt"></i>
                                </div>
                                <span class="badge bg-primary-soft text-primary px-3 py-1 rounded-pill fs-8">งบประมาณรวม</span>
                            </div>
                            <span class="stat-card-title text-muted">งบประมาณที่ได้รับจัดสรรทั้งสิ้น</span>
                            <h2 class="stat-card-number mt-1 mb-1"><?= number_format($sumAllocated, 2); ?> <span class="currency">บาท</span></h2>
                            <p class="stat-card-footer mb-0 text-muted"><i class="bx bx-calendar-check me-1"></i> ประจำปีงบประมาณ พ.ศ. <?= $selected_year ?></p>
                        </div>
                    </div>
                </div>

                <div class="col-lg-4 col-md-6">
                    <div class="card luxury-stat-card border-0 shadow-sm h-100">
                        <div class="card-body p-4">
                            <div class="d-flex align-items-center justify-content-between mb-3">
                                <div class="stat-icon-container success">
                                    <i class="bx bx-check-shield"></i>
                                </div>
                                <span class="badge bg-success-soft text-success px-3 py-1 rounded-pill fs-8">
                                    <?= $sumAllocated > 0 ? number_format(($sumBalance / $sumAllocated) * 100, 1) : 0 ?>% คงเหลือ
                                </span>
                            </div>
                            <span class="stat-card-title text-muted">งบประมาณคงเหลือใช้ได้จริง</span>
                            <h2 class="stat-card-number mt-1 mb-1 text-success"><?= number_format($sumBalance, 2); ?> <span class="currency">บาท</span></h2>
                            <div class="progress mb-2" style="height: 6px; border-radius: 10px;">
                                <div class="progress-bar bg-success" role="progressbar" style="width: <?= $sumAllocated > 0 ? ($sumBalance / $sumAllocated) * 100 : 0 ?>%;"></div>
                            </div>
                            <p class="stat-card-footer mb-0 text-success"><i class="bx bx-trending-up me-1"></i> พร้อมสำหรับการเบิกจ่าย</p>
                        </div>
                    </div>
                </div>

                <div class="col-lg-4 col-md-12">
                    <div class="card luxury-stat-card border-0 shadow-sm h-100">
                        <div class="card-body p-4">
                            <div class="d-flex align-items-center justify-content-between mb-3">
                                <div class="stat-icon-container warning">
                                    <i class="bx bx-receipt"></i>
                                </div>
                                <span class="badge bg-warning-soft text-warning px-3 py-1 rounded-pill fs-8">จ่ายจริงแล้ว</span>
                            </div>
                            <span class="stat-card-title text-muted">ยอดเบิกจ่ายสะสม (ส่งใบสำคัญ + เบิกตรง)</span>
                            <h2 class="stat-card-number mt-1 mb-1 text-orange"><?= number_format($sumSpent, 2); ?> <span class="currency">บาท</span></h2>
                            <p class="stat-card-footer mb-0 text-muted"><i class="bx bx-folder me-1"></i> รวมจาก <?= count($projects); ?> โครงการ</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Projects Ledger Table Card -->
            <div class="card border-0 shadow-sm" style="border-radius: 16px;">
                <div class="card-header bg-transparent d-flex justify-content-between align-items-center py-3 border-bottom flex-wrap gap-2">
                    <div>
                        <h5 class="card-title mb-0 fw-bold d-flex align-items-center">
                            <i class="bx bx-spreadsheet text-primary me-2 fs-4"></i>รายการทะเบียนคุมเงินงบประมาณรายโครงการ (พ.ศ. <?= $selected_year ?>)
                        </h5>
                        <small class="text-muted">คลิก "ดูทะเบียนคุม" เพื่อบันทึก/ตรวจสอบยอดเงินยืมทดรองและเงินงบประมาณ</small>
                    </div>
                    <a href="<?= base_url('Admin/BudgetLedger/Structure') ?>" class="btn btn-outline-primary btn-sm rounded-pill">
                        <i class="bx bx-sitemap me-1"></i> จัดการโครงสร้างยุทธศาสตร์
                    </a>
                </div>

                <div class="table-responsive p-1 p-md-2">
                    <table class="table table-hover align-middle mb-0 table-fit-screen">
                        <thead class="table-light">
                            <tr>
                                <th style="width: 6%;" class="text-center">รหัส</th>
                                <th style="width: 25%;">ชื่อโครงการ / กิจกรรม</th>
                                <th style="width: 15%;">ห้วงเวลาโครงการ</th>
                                <th style="width: 15%;">แหล่งเงินงบประมาณ</th>
                                <th class="text-end" style="width: 10%;">งบตั้งต้น</th>
                                <th class="text-end" style="width: 10%;">จ่ายจริงแล้ว</th>
                                <th class="text-end" style="width: 10%;">คงเหลือสุทธิ</th>
                                <th class="text-center" style="width: 4%;">รายการ</th>
                                <th class="text-center" style="width: 5%;">การจัดการ</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($projects)): ?>
                                <tr>
                                    <td colspan="9" class="text-center py-5 text-muted">
                                        <i class="bx bx-folder-open display-4 opacity-50 mb-2 d-block"></i>
                                        <h6 class="fw-bold">ยังไม่มีข้อมูลโครงการในปีงบประมาณ <?= $selected_year ?></h6>
                                        <p class="small">ไปที่หน้า <a href="<?= base_url('Admin/BudgetLedger/Structure') ?>">ตั้งค่ายุทธศาสตร์และโครงการ</a> เพื่อเพิ่มข้อมูล</p>
                                    </td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($projects as $p): ?>
                                    <tr>
                                        <td class="text-center" data-order="<?= natural_code_sort_key($p->project_code) ?>">
                                            <span class="badge bg-label-secondary fw-bold rounded-pill px-2 py-1"><?= esc($p->project_code ?: '-') ?></span>
                                        </td>
                                        <td>
                                            <a href="<?= base_url('Admin/BudgetLedger/view/' . $p->id) ?>" class="fw-bold text-dark text-hover-primary text-decoration-none d-block mb-1" style="line-height: 1.4;">
                                                <i class="bx bx-right-arrow-alt text-primary me-1"></i><?= esc($p->project_name) ?>
                                            </a>
                                            <?php if (!empty($p->strategy_name)): ?>
                                                <small class="text-muted d-block text-truncate" style="max-width: 380px;" title="<?= esc($p->strategy_name) ?>">
                                                    <i class="bx bx-target-lock text-secondary me-1"></i><?= esc($p->strategy_name) ?>
                                                </small>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php $timeStatus = get_project_time_status($p->start_date ?? null, $p->end_date ?? null); ?>
                                            <div class="mb-1">
                                                <?= $timeStatus['badge_html'] ?>
                                            </div>
                                            <?php if ($timeStatus['status'] !== 'unspecified'): ?>
                                                <small class="text-muted d-block fs-8" style="line-height: 1.35;">
                                                    <i class="bx bx-calendar-event text-primary me-1"></i><?= esc($timeStatus['date_range']) ?>
                                                </small>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <span class="small text-muted d-inline-block text-truncate" style="max-width: 260px;" title="<?= esc($p->budget_source) ?>">
                                                <i class="bx bx-coin text-warning me-1"></i><?= esc($p->budget_source ?: '-') ?>
                                            </span>
                                        </td>
                                        <td class="text-end fw-semibold text-secondary">
                                            <?= number_format($p->allocated_budget, 2) ?>
                                        </td>
                                        <td class="text-end text-orange fw-semibold">
                                            <?= number_format($p->total_spent, 2) ?>
                                        </td>
                                        <td class="text-end">
                                            <span class="badge <?= $p->current_balance < 0 ? 'bg-label-danger' : 'bg-label-success' ?> fs-7 px-2 py-1 rounded">
                                                <?= number_format($p->current_balance, 2) ?>
                                            </span>
                                        </td>
                                        <td class="text-center">
                                            <span class="badge bg-label-info rounded-pill">
                                                <i class="bx bx-receipt me-1"></i><?= $p->item_count ?>
                                            </span>
                                        </td>
                                        <td class="text-center">
                                            <a href="<?= base_url('Admin/BudgetLedger/view/' . $p->id) ?>" class="btn btn-sm btn-primary rounded-pill px-3 shadow-sm" title="เปิดทะเบียนคุม">
                                                <i class="bx bx-folder-open me-1"></i>ดูทะเบียนคุม
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>
</div>

<style>
.luxury-admin-hero-card {
    background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%);
    border-radius: 20px;
    box-shadow: 0 10px 25px -5px rgba(15, 23, 42, 0.3);
    color: #fff;
}
.hero-glow-1 {
    position: absolute;
    top: -50px;
    right: 10%;
    width: 250px;
    height: 250px;
    background: radial-gradient(circle, rgba(251, 140, 0, 0.25) 0%, rgba(251, 140, 0, 0) 70%);
    border-radius: 50%;
}
.hero-glow-2 {
    position: absolute;
    bottom: -50px;
    left: 20%;
    width: 200px;
    height: 200px;
    background: radial-gradient(circle, rgba(13, 110, 253, 0.2) 0%, rgba(13, 110, 253, 0) 70%);
    border-radius: 50%;
}
.system-badge {
    background: rgba(251, 140, 0, 0.2);
    border: 1px solid rgba(251, 140, 0, 0.4);
    color: #ffb74d;
    font-size: 0.75rem;
    font-weight: 700;
    padding: 3px 10px;
    border-radius: 30px;
    letter-spacing: 1px;
}
.status-dot-pulse {
    width: 8px;
    height: 8px;
    background: #4caf50;
    border-radius: 50%;
    animation: pulse-green 2s infinite;
}
@keyframes pulse-green {
    0% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(76, 175, 80, 0.7); }
    70% { transform: scale(1); box-shadow: 0 0 0 8px rgba(76, 175, 80, 0); }
    100% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(76, 175, 80, 0); }
}
.hero-title { font-weight: 800; color: #ffffff; font-size: 1.6rem; }
.hero-subtitle { color: #94a3b8; font-size: 0.95rem; }
.luxury-stat-card { border-radius: 16px; background: #ffffff; }
.stat-icon-container {
    width: 46px;
    height: 46px;
    border-radius: 12px;
    background: rgba(13, 110, 253, 0.1);
    color: #0d6efd;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.5rem;
}
.stat-icon-container.success { background: rgba(25, 135, 84, 0.1); color: #198754; }
.stat-icon-container.warning { background: rgba(253, 126, 20, 0.1); color: #fd7e14; }
.stat-card-title { font-size: 0.85rem; font-weight: 600; }
.stat-card-number { font-size: 1.75rem; font-weight: 800; color: #1e293b; }
.stat-card-number .currency { font-size: 0.95rem; font-weight: 500; color: #64748b; }
.stat-card-footer { font-size: 0.8rem; }
.bg-primary-soft { background-color: rgba(13, 110, 253, 0.1); }
.bg-success-soft { background-color: rgba(25, 135, 84, 0.1); }
.bg-warning-soft { background-color: rgba(253, 126, 20, 0.1); }
.text-orange { color: #fd7e14 !important; }
.text-hover-primary:hover { color: #0d6efd !important; }
.fs-7 { font-size: 0.85rem !important; }
.fs-8 { font-size: 0.78rem !important; }
</style>
