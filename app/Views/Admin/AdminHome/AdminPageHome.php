<!-- Layout container -->
<div class="layout-page">
    <?php echo view('Admin/AdminLeyout/AdminNavbar'); ?>

    <!-- Content wrapper -->
    <div class="content-wrapper">
        <!-- Content -->
        <div class="container-xxl flex-grow-1 container-p-y">

            <!-- Hero Welcome & Year Filter Card -->
            <div class="row mb-4 animate-fade-in-up">
                <div class="col-12">
                    <div class="card border-0 position-relative overflow-hidden luxury-admin-hero-card">
                        <div class="hero-glow-1"></div>
                        <div class="hero-glow-2"></div>
                        
                        <div class="card-body p-4 p-md-5 position-relative z-index-1">
                            <div class="row align-items-center">
                                <div class="col-lg-7">
                                    <div class="d-flex align-items-center mb-3 flex-wrap gap-2">
                                        <span class="system-badge">BUDGET CONTROL DASHBOARD</span>
                                        <div class="status-dot-pulse ms-1"></div>
                                        <span class="badge bg-white text-dark rounded-pill px-3 py-1 fw-bold fs-8 shadow-sm">
                                            ปีงบประมาณ พ.ศ. <?= esc($selected_year ?? '2569') ?>
                                        </span>
                                    </div>
                                    <h1 class="hero-title mb-2">ระบบควบคุมงบประมาณและแผนงาน</h1>
                                    <p class="hero-subtitle mb-3">
                                        แดชบอร์ดติดตามทะเบียนคุมเงินงบประมาณรายจ่าย โครงการ และการจัดซื้อจัดจ้าง โรงเรียนสวนกุหลาบวิทยาลัย (จิรประวัติ) นครสวรรค์
                                    </p>
                                    <div class="d-flex align-items-center gap-2 flex-wrap mt-3">
                                        <a href="<?= base_url('Admin/BudgetLedger?year=' . ($selected_year ?? '2569')); ?>" class="btn btn-warning rounded-pill px-3 text-dark fw-bold shadow-sm">
                                            <i class="bx bx-book-content me-1"></i> ทะเบียนคุมงบประมาณ
                                        </a>
                                        <a href="<?= base_url('Admin/BudgetLedger/Structure?year=' . ($selected_year ?? '2569')); ?>" class="btn btn-outline-light rounded-pill px-3 fw-semibold">
                                            <i class="bx bx-sitemap me-1"></i> ตั้งค่ายุทธศาสตร์/โครงการ
                                        </a>
                                    </div>
                                </div>
                                <div class="col-lg-5 text-lg-end mt-4 mt-lg-0">
                                    <form method="get" action="<?= base_url('Admin/Home') ?>" class="d-inline-block bg-white bg-opacity-25 p-3 rounded-4 backdrop-blur shadow-sm text-start">
                                        <label class="text-white small fw-bold mb-1 d-block"><i class="bx bx-calendar me-1"></i>เลือกปีงบประมาณ</label>
                                        <select class="form-select form-select-sm rounded-pill fw-semibold" name="year" onchange="this.form.submit()">
                                            <?php foreach ($years as $y): ?>
                                                <option value="<?= $y->budget_year ?>" <?= $y->budget_year == ($selected_year ?? '2569') ? 'selected' : '' ?>>
                                                    ปีงบประมาณ พ.ศ. <?= $y->budget_year ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- 1. Key Metric KPI Cards (Budget Ledger Core) -->
            <div class="row g-3 mb-4 animate-fade-in-up" style="animation-delay: 0.05s;">
                <!-- 1. Total Allocated Budget -->
                <div class="col-xl-3 col-sm-6">
                    <div class="card luxury-stat-card border-0 shadow-sm h-100">
                        <div class="card-body p-4">
                            <div class="d-flex align-items-center justify-content-between mb-3">
                                <div class="stat-icon-container">
                                    <i class="bx bx-wallet-alt"></i>
                                </div>
                                <span class="badge bg-primary-soft text-primary px-3 py-1 rounded-pill fs-8">งบจัดสรร</span>
                            </div>
                            <span class="stat-card-title text-muted">งบประมาณจัดสรรรวมทั้งหมด</span>
                            <h3 class="stat-card-number mt-1 mb-2 text-primary font-monospace">
                                ฿<?= number_format($total_allocated ?? 0, 2); ?>
                            </h3>
                            <p class="stat-card-footer mb-0 text-muted fs-8">
                                <i class="bx bx-layer me-1"></i> รวมจาก <?= number_format($total_projects ?? 0); ?> โครงการในแผน
                            </p>
                        </div>
                    </div>
                </div>

                <!-- 2. Total Spent / Disbursed -->
                <div class="col-xl-3 col-sm-6">
                    <div class="card luxury-stat-card border-0 shadow-sm h-100">
                        <div class="card-body p-4">
                            <div class="d-flex align-items-center justify-content-between mb-3">
                                <div class="stat-icon-container" style="background: rgba(234, 84, 85, 0.1); color: #ea5455;">
                                    <i class="bx bx-trending-up"></i>
                                </div>
                                <span class="badge bg-danger-soft text-danger px-3 py-1 rounded-pill fs-8">
                                    <?= $spent_percent ?? 0; ?>% เบิกจ่าย
                                </span>
                            </div>
                            <span class="stat-card-title text-muted">ยอดเบิกจ่าย / จ่ายตรงสะสม</span>
                            <h3 class="stat-card-number mt-1 mb-2 text-danger font-monospace">
                                ฿<?= number_format($total_spent ?? 0, 2); ?>
                            </h3>
                            <div class="progress mb-1" style="height: 6px;">
                                <div class="progress-bar bg-danger" role="progressbar" style="width: <?= $spent_percent ?? 0; ?>%;"></div>
                            </div>
                            <p class="stat-card-footer mb-0 text-muted fs-8">ตัดจ่ายตามใบเสร็จและเบิกจ่ายตรง</p>
                        </div>
                    </div>
                </div>

                <!-- 3. Active Borrow Amount -->
                <div class="col-xl-3 col-sm-6">
                    <div class="card luxury-stat-card border-0 shadow-sm h-100">
                        <div class="card-body p-4">
                            <div class="d-flex align-items-center justify-content-between mb-3">
                                <div class="stat-icon-container warning">
                                    <i class="bx bx-time-five"></i>
                                </div>
                                <span class="badge bg-warning-soft text-warning px-3 py-1 rounded-pill fs-8">เงินยืม</span>
                            </div>
                            <span class="stat-card-title text-muted">เงินยืมค้างชำระ / รอส่งใช้</span>
                            <h3 class="stat-card-number mt-1 mb-2 text-warning font-monospace">
                                ฿<?= number_format($active_borrow ?? 0, 2); ?>
                            </h3>
                            <p class="stat-card-footer mb-0 text-warning fs-8">
                                <i class="bx bx-info-circle me-1"></i> รอส่งใช้ใบเสร็จหรือคืนเงินสด
                            </p>
                        </div>
                    </div>
                </div>

                <!-- 4. Total Net Balance -->
                <div class="col-xl-3 col-sm-6">
                    <div class="card luxury-stat-card border-0 shadow-sm h-100">
                        <div class="card-body p-4">
                            <div class="d-flex align-items-center justify-content-between mb-3">
                                <div class="stat-icon-container" style="background: rgba(40, 199, 111, 0.1); color: #28c76f;">
                                    <i class="bx bx-check-shield"></i>
                                </div>
                                <span class="badge bg-success-soft text-success px-3 py-1 rounded-pill fs-8">คงเหลือสุทธิ</span>
                            </div>
                            <span class="stat-card-title text-muted">งบประมาณคงเหลือพร้อมใช้</span>
                            <h3 class="stat-card-number mt-1 mb-2 text-success font-monospace">
                                ฿<?= number_format($total_balance ?? 0, 2); ?>
                            </h3>
                            <p class="stat-card-footer mb-0 text-success fs-8">
                                <i class="bx bx-shield-quarter me-1"></i> พร้อมสำหรับการบริหารจัดการ
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- 2. Charts & Strategic Distribution Section -->
            <div class="row mb-4 animate-fade-in-up" style="animation-delay: 0.1s;">
                <!-- Monthly Ledger Expenditure Trend Chart -->
                <div class="col-lg-8 mb-4">
                    <div class="card border-0 shadow-sm h-100 rounded-4">
                        <div class="card-header bg-white border-0 pt-4 px-4 pb-0 d-flex align-items-center justify-content-between flex-wrap gap-2">
                            <div>
                                <h5 class="mb-1 fw-bold text-dark"><i class="bx bx-line-chart text-primary me-2"></i>แนวโน้มการเบิกจ่ายงบประมาณรายเดือน</h5>
                                <p class="text-muted fs-8 mb-0">แสดงมูลค่าการเบิกจ่ายจริงตามรายการในทะเบียนคุมงบประมาณ ประจำปี พ.ศ. <?= esc($selected_year ?? '2569') ?></p>
                            </div>
                            <span class="badge bg-label-primary rounded-pill px-3 py-1 fs-8 fw-semibold">
                                <i class="bx bx-calendar me-1"></i>ปีงบประมาณ <?= esc($selected_year ?? '2569') ?>
                            </span>
                        </div>
                        <div class="card-body px-4">
                            <div id="ledgerMonthlyChart" style="min-height: 290px;"></div>
                        </div>
                    </div>
                </div>

                <!-- Strategy Breakdown Progress List -->
                <div class="col-lg-4 mb-4">
                    <div class="card border-0 shadow-sm h-100 rounded-4">
                        <div class="card-header bg-white border-0 pt-4 px-4 pb-2 d-flex align-items-center justify-content-between">
                            <h5 class="mb-1 fw-bold text-dark"><i class="bx bx-pie-chart-alt text-warning me-2"></i>สัดส่วนยุทธศาสตร์</h5>
                            <a href="<?= base_url('Admin/BudgetLedger/Structure?year=' . ($selected_year ?? '2569')); ?>" class="btn btn-xs btn-outline-warning rounded-pill">ตั้งค่า</a>
                        </div>
                        <div class="card-body px-4 pb-4">
                            <?php if (!empty($strategies_summary)): ?>
                                <div class="d-flex flex-column gap-3">
                                    <?php foreach ($strategies_summary as $st): ?>
                                        <div class="p-2 border rounded-3 bg-light">
                                            <div class="d-flex align-items-center justify-content-between mb-1">
                                                <span class="fw-bold fs-8 text-dark text-truncate" style="max-width: 180px;" title="<?= esc($st->strategy_name) ?>">
                                                    ยุทธศาสตร์ที่ <?= esc($st->strategy_no) ?> <?= esc($st->strategy_name) ?>
                                                </span>
                                                <span class="fs-8 fw-bold text-primary"><?= $st->percent ?>%</span>
                                            </div>
                                            <div class="progress mb-2" style="height: 5px;">
                                                <div class="progress-bar <?= $st->percent > 80 ? 'bg-danger' : ($st->percent > 50 ? 'bg-warning' : 'bg-success') ?>" style="width: <?= $st->percent ?>%"></div>
                                            </div>
                                            <div class="d-flex justify-content-between fs-8 text-muted">
                                                <span>จัดสรร: ฿<?= number_format($st->allocated, 2) ?></span>
                                                <span>ใช้ไป: <b class="text-danger">฿<?= number_format($st->spent, 2) ?></b></span>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            <?php else: ?>
                                <div class="text-center py-4 text-muted">
                                    <i class="bx bx-sitemap fs-2 mb-2 d-block opacity-50"></i>
                                    ยังไม่มีข้อมูลงบประมาณยุทธศาสตร์
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>

            <!-- 3. Active Projects & Budget Ledgers Table -->
            <div class="row mb-4 animate-fade-in-up" style="animation-delay: 0.15s;">
                <div class="col-12">
                    <div class="card border-0 shadow-sm rounded-4">
                        <div class="card-header bg-white border-0 pt-4 px-4 pb-2 d-flex align-items-center justify-content-between flex-wrap gap-2">
                            <div>
                                <h5 class="fw-bold mb-1 text-dark">
                                    <i class="bx bx-book-content text-primary me-2"></i>สถานะโครงการในทะเบียนคุมเงินงบประมาณ
                                </h5>
                                <p class="text-muted fs-7 mb-0">คลิกที่ปุ่ม <b>"เปิดทะเบียนคุม"</b> เพื่อบันทึกรายการใช้จ่าย จัดการยอดเงิน หรือพิมพ์ใบทะเบียนคุม</p>
                            </div>
                            <a href="<?= base_url('Admin/BudgetLedger?year=' . ($selected_year ?? '2569')) ?>" class="btn btn-primary btn-sm rounded-pill px-3 shadow-sm">
                                <i class="bx bx-plus-circle me-1"></i> ไปหน้าจัดการทะเบียนคุมทั้งหมด
                            </a>
                        </div>
                        <div class="card-body px-4 pb-4">
                            <div class="table-responsive">
                                <table id="adminDashboardProjectsTable" class="table table-hover align-middle w-100">
                                    <thead class="table-light">
                                        <tr class="text-muted fs-7">
                                            <th style="width: 100px;">รหัสโครงการ</th>
                                            <th>ชื่อโครงการ / กิจกรรม</th>
                                            <th>แหล่งเงิน / ยุทธศาสตร์</th>
                                            <th class="text-end" style="width: 130px;">งบจัดสรร (บาท)</th>
                                            <th class="text-end" style="width: 130px;">เบิกจ่ายแล้ว (บาท)</th>
                                            <th class="text-end" style="width: 130px;">คงเหลือสุทธิ (บาท)</th>
                                            <th class="text-center" style="width: 100px;">ความก้าวหน้า</th>
                                            <th class="text-center" style="width: 130px;">จัดการทะเบียน</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if (!empty($projects)): ?>
                                            <?php foreach ($projects as $p): ?>
                                                <tr>
                                                    <td>
                                                        <span class="badge bg-label-primary font-monospace fs-8">
                                                            <?= esc($p->project_code ?: '-') ?>
                                                        </span>
                                                    </td>
                                                    <td>
                                                        <div class="fw-bold text-dark fs-7 mb-1"><?= esc($p->project_name ?? '-') ?></div>
                                                        <?php if (!empty($p->budget_source ?? null)): ?>
                                                            <div class="text-muted fs-8 text-truncate" style="max-width: 280px;" title="<?= esc($p->budget_source) ?>">
                                                                <i class="bx bx-wallet me-1 text-warning"></i><?= esc($p->budget_source) ?>
                                                            </div>
                                                        <?php endif; ?>
                                                    </td>
                                                    <td>
                                                        <span class="badge bg-label-secondary rounded-pill fs-8">
                                                            <?= esc($p->strategy_name ?? 'งบประมาณทั่วไป') ?>
                                                        </span>
                                                    </td>
                                                    <td class="text-end fw-bold text-dark font-monospace">
                                                        <?= number_format($p->allocated_budget ?? 0, 2) ?>
                                                    </td>
                                                    <td class="text-end text-danger font-monospace">
                                                        <?= number_format($p->total_spent ?? 0, 2) ?>
                                                    </td>
                                                    <td class="text-end fw-bold <?= ($p->current_balance ?? 0) > 0 ? 'text-success' : 'text-muted' ?> font-monospace">
                                                        <?= number_format($p->current_balance ?? 0, 2) ?>
                                                    </td>
                                                    <td class="text-center">
                                                        <div class="d-flex align-items-center justify-content-center gap-1">
                                                            <div class="progress" style="width: 45px; height: 6px;">
                                                                <div class="progress-bar <?= ($p->used_percent ?? 0) > 80 ? 'bg-danger' : (($p->used_percent ?? 0) > 50 ? 'bg-warning' : 'bg-success') ?>" style="width: <?= $p->used_percent ?? 0 ?>%"></div>
                                                            </div>
                                                            <small class="fs-8 fw-semibold"><?= $p->used_percent ?? 0 ?>%</small>
                                                        </div>
                                                    </td>
                                                    <td class="text-center">
                                                        <a href="<?= base_url('Admin/BudgetLedger/view/' . $p->id) ?>" class="btn btn-outline-primary btn-sm rounded-pill px-3 py-1">
                                                            <i class="bx bx-edit-alt me-1"></i> บันทึกคุม
                                                        </a>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        <?php else: ?>
                                            <tr>
                                                <td colspan="8" class="text-center py-5 text-muted">
                                                    <i class="bx bx-folder-open fs-1 mb-2 d-block opacity-50"></i>
                                                    ยังไม่มีข้อมูลโครงการในปีงบประมาณนี้
                                                </td>
                                            </tr>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- 4. Recent Ledger Transactions & Recent Orders -->
            <div class="row mb-5 animate-fade-in-up" style="animation-delay: 0.2s;">
                <!-- Recent 8 Ledger Transactions -->
                <div class="col-lg-7 mb-4">
                    <div class="card border-0 shadow-sm h-100 rounded-4">
                        <div class="card-header bg-white border-0 pt-4 px-4 d-flex align-items-center justify-content-between">
                            <div>
                                <h5 class="mb-1 fw-bold text-dark"><i class="bx bx-time-five text-primary me-2"></i>รายการล่าสุดในทะเบียนคุม</h5>
                                <p class="text-muted fs-8 mb-0">แสดงประวัติการทำรายการเบิกจ่าย/ยืมเงินล่าสุด</p>
                            </div>
                            <a href="<?= base_url('Admin/BudgetLedger'); ?>" class="btn btn-sm btn-outline-primary rounded-pill px-3">ดูทะเบียนคุม</a>
                        </div>
                        <div class="card-body px-4 pb-4">
                            <div class="table-responsive">
                                <table class="table table-hover align-middle mb-0">
                                    <thead class="table-light">
                                        <tr class="text-secondary fs-8">
                                            <th style="width: 85px;" class="text-center">วันที่</th>
                                            <th>โครงการ / รายการ</th>
                                            <th class="text-end" style="width: 110px;">จำนวนเงิน</th>
                                            <th class="text-end" style="width: 110px;">คงเหลือ</th>
                                        </tr>
                                    </thead>
                                    <tbody class="fs-8">
                                        <?php if (!empty($recent_ledgers)): ?>
                                            <?php foreach ($recent_ledgers as $lg): ?>
                                                <tr>
                                                    <td class="text-center text-nowrap">
                                                        <?php
                                                            if (!empty($lg->doc_date) && $lg->doc_date !== '0000-00-00') {
                                                                $d = explode('-', $lg->doc_date);
                                                                echo count($d) === 3 ? "{$d[2]}/{$d[1]}/" . ($d[0] + 543) : $lg->doc_date;
                                                            } else {
                                                                echo '-';
                                                            }
                                                        ?>
                                                    </td>
                                                    <td>
                                                        <div class="fw-bold text-dark text-truncate" style="max-width: 220px;" title="<?= esc($lg->project_name) ?>">
                                                            <span class="badge bg-label-secondary font-monospace fs-8 me-1"><?= esc($lg->project_code ?: '-') ?></span>
                                                            <?= esc($lg->project_name) ?>
                                                        </div>
                                                        <small class="text-muted text-truncate d-block" style="max-width: 220px;" title="<?= esc($lg->description) ?>">
                                                            <?= esc($lg->description ?: '-') ?>
                                                        </small>
                                                    </td>
                                                    <td class="text-end font-monospace">
                                                        <?php
                                                            $pay = (float)($lg->amount_return_receipt ?? 0) + (float)($lg->amount_direct_withdraw ?? 0);
                                                            $bor = (float)($lg->amount_borrow ?? 0);
                                                            $adj = (float)($lg->amount_budget_adjust ?? 0);
                                                            if ($pay > 0) {
                                                                echo '<span class="text-danger fw-bold">- ' . number_format($pay, 2) . '</span>';
                                                            } elseif ($bor > 0) {
                                                                echo '<span class="text-warning fw-bold">ยืม ' . number_format($bor, 2) . '</span>';
                                                            } elseif ($adj > 0) {
                                                                echo '<span class="text-primary fw-bold">+ ' . number_format($adj, 2) . '</span>';
                                                            } else {
                                                                echo '-';
                                                            }
                                                        ?>
                                                    </td>
                                                    <td class="text-end fw-bold font-monospace <?= ($lg->balance ?? 0) > 0 ? 'text-success' : 'text-muted' ?>">
                                                        <?= number_format($lg->balance ?? 0, 2) ?>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        <?php else: ?>
                                            <tr>
                                                <td colspan="4" class="text-center text-muted py-4">ไม่มีรายการบันทึกในระบบขณะนี้</td>
                                            </tr>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Recent Orders Summary -->
                <div class="col-lg-5 mb-4">
                    <div class="card border-0 shadow-sm h-100 rounded-4">
                        <div class="card-header bg-white border-0 pt-4 px-4 d-flex align-items-center justify-content-between">
                            <div>
                                <h5 class="mb-1 fw-bold text-dark"><i class="bx bx-cart text-warning me-2"></i>ทะเบียนสั่งซื้อ / สั่งจ้าง</h5>
                                <p class="text-muted fs-8 mb-0">ยอดรวม ฿<?= number_format($total_orders_amount ?? 0, 2) ?> (<?= $total_orders ?? 0 ?> รายการ)</p>
                            </div>
                            <a href="<?= base_url('Admin/Registry/Purchase'); ?>" class="btn btn-sm btn-outline-warning rounded-pill px-3">ดูคำสั่งซื้อ</a>
                        </div>
                        <div class="card-body px-4 pb-4">
                            <div class="table-responsive">
                                <table class="table table-hover align-middle mb-0">
                                    <thead class="table-light">
                                        <tr class="text-secondary fs-8">
                                            <th class="text-center">เลขที่</th>
                                            <th>รายการ</th>
                                            <th class="text-end">จำนวนเงิน</th>
                                            <th class="text-center">สถานะ</th>
                                        </tr>
                                    </thead>
                                    <tbody class="fs-8">
                                        <?php if (!empty($recent_orders)): ?>
                                            <?php foreach ($recent_orders as $order): ?>
                                                <tr>
                                                    <td class="text-center fw-bold text-dark font-monospace"><?= esc($order->order_number); ?></td>
                                                    <td class="text-truncate" style="max-width: 140px;" title="<?= esc($order->description); ?>"><?= esc($order->description); ?></td>
                                                    <td class="text-end fw-bold text-orange font-monospace"><?= number_format($order->amount, 2); ?></td>
                                                    <td class="text-center">
                                                        <?php if ($order->system_sent_status == 'ส่งลงระบบ/ทำฎีกาแล้ว'): ?>
                                                            <span class="badge bg-success-soft text-success px-2 py-1 rounded-pill">ทำฎีกาแล้ว</span>
                                                        <?php else: ?>
                                                            <span class="badge bg-secondary-soft text-secondary px-2 py-1 rounded-pill">รอส่ง</span>
                                                        <?php endif; ?>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        <?php else: ?>
                                            <tr>
                                                <td colspan="4" class="text-center text-muted py-4">ไม่มีข้อมูลทะเบียนสั่งซื้อ/สั่งจ้าง</td>
                                            </tr>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

<style>
    /* Luxury Admin Dashboard Styling */
    .luxury-admin-hero-card {
        background: linear-gradient(135deg, #1e1e24 0%, #2b2b36 100%);
        border-radius: 24px !important;
        box-shadow: 0 15px 35px rgba(0,0,0,0.15);
    }
    .hero-glow-1 {
        position: absolute;
        top: -60%;
        left: -20%;
        width: 400px;
        height: 400px;
        background: radial-gradient(circle, rgba(251, 140, 0, 0.25) 0%, rgba(251, 140, 0, 0) 70%);
        pointer-events: none;
    }
    .hero-glow-2 {
        position: absolute;
        bottom: -60%;
        right: -10%;
        width: 400px;
        height: 400px;
        background: radial-gradient(circle, rgba(251, 140, 0, 0.15) 0%, rgba(251, 140, 0, 0) 70%);
        pointer-events: none;
    }
    .hero-title {
        font-family: 'Plus Jakarta Sans', 'K2D', sans-serif;
        font-weight: 700;
        color: #ffffff;
        font-size: 2rem;
    }
    .hero-subtitle {
        font-family: 'K2D', sans-serif;
        color: rgba(255,255,255,0.85);
        font-size: 0.95rem;
        line-height: 1.6;
    }
    .system-badge {
        background: rgba(251, 140, 0, 0.2);
        color: #FB8C00;
        border: 1px solid rgba(251, 140, 0, 0.4);
        padding: 5px 12px;
        font-size: 0.75rem;
        font-weight: 700;
        border-radius: 30px;
        letter-spacing: 0.5px;
    }
    .backdrop-blur {
        backdrop-filter: blur(10px);
    }

    /* Stat Cards */
    .luxury-stat-card {
        background: #ffffff;
        border-radius: 18px !important;
        transition: transform 0.25s ease, box-shadow 0.25s ease;
    }
    .luxury-stat-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 12px 24px rgba(0, 0, 0, 0.08) !important;
    }
    .stat-icon-container {
        width: 46px;
        height: 46px;
        border-radius: 12px;
        background: rgba(105, 108, 255, 0.1);
        color: #696cff;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.4rem;
    }
    .stat-icon-container.warning {
        background: rgba(255, 171, 0, 0.1);
        color: #ffab00;
    }
    .stat-card-title {
        font-size: 0.85rem;
        font-weight: 600;
    }
    .stat-card-number {
        font-weight: 700;
        letter-spacing: -0.5px;
    }
    .bg-primary-soft { background-color: #e8f0fe !important; }
    .bg-danger-soft { background-color: #fce8e6 !important; }
    .bg-success-soft { background-color: #e6f4ea !important; }
    .bg-warning-soft { background-color: #fef7e0 !important; }
    .bg-secondary-soft { background-color: #f1f3f4 !important; }

    /* Budget status table - balanced column widths and readable density */
    #adminDashboardProjectsTable {
        table-layout: fixed;
        min-width: 980px;
        margin-bottom: 0;
    }
    #adminDashboardProjectsTable th,
    #adminDashboardProjectsTable td {
        vertical-align: middle;
        padding: .75rem .85rem;
    }
    #adminDashboardProjectsTable th {
        white-space: nowrap;
        font-size: .78rem;
        font-weight: 700;
    }
    #adminDashboardProjectsTable td {
        font-size: .84rem;
        white-space: normal;
        overflow-wrap: anywhere;
        word-break: break-word;
        line-height: 1.45;
    }
    #adminDashboardProjectsTable th {
        overflow-wrap: anywhere;
    }
    #adminDashboardProjectsTable td .text-truncate {
        max-width: 100% !important;
        white-space: normal !important;
        overflow: visible !important;
        text-overflow: clip !important;
        overflow-wrap: anywhere;
        word-break: break-word;
    }
    /* 8 columns: keep the proportions aligned with the actual table structure */
    #adminDashboardProjectsTable th:nth-child(1),
    #adminDashboardProjectsTable td:nth-child(1) { width: 12%; }
    #adminDashboardProjectsTable th:nth-child(2),
    #adminDashboardProjectsTable td:nth-child(2) { width: 24%; }
    #adminDashboardProjectsTable th:nth-child(3),
    #adminDashboardProjectsTable td:nth-child(3) { width: 20%; }
    #adminDashboardProjectsTable th:nth-child(4),
    #adminDashboardProjectsTable td:nth-child(4),
    #adminDashboardProjectsTable th:nth-child(5),
    #adminDashboardProjectsTable td:nth-child(5),
    #adminDashboardProjectsTable th:nth-child(6),
    #adminDashboardProjectsTable td:nth-child(6) { width: 11%; }
    #adminDashboardProjectsTable th:nth-child(7),
    #adminDashboardProjectsTable td:nth-child(7) { width: 7%; }
    #adminDashboardProjectsTable th:nth-child(8),
    #adminDashboardProjectsTable td:nth-child(8) { width: 6%; }
    #adminDashboardProjectsTable td:first-child {
        white-space: normal;
        line-height: 1.45;
    }
    /* แหล่งเงิน / ยุทธศาสตร์: ให้ข้อความตัดบรรทัดตามพื้นที่จริง */
    #adminDashboardProjectsTable td:nth-child(3),
    #adminDashboardProjectsTable td:nth-child(3) * {
        white-space: normal !important;
        overflow-wrap: anywhere;
        word-break: break-word;
        line-height: 1.45;
    }
    #adminDashboardProjectsTable td:nth-child(3) .badge {
        display: inline-block;
        max-width: 100%;
        min-width: 0;
        white-space: normal !important;
        overflow-wrap: anywhere;
        word-break: break-word;
        border-radius: .65rem !important;
    }
    #adminDashboardProjectsTable .badge {
        min-width: 82px;
        padding: .45rem .65rem;
        font-weight: 600;
    }
    #adminDashboardProjectsTable_wrapper .dataTables_length,
    #adminDashboardProjectsTable_wrapper .dataTables_filter {
        margin-bottom: .75rem;
    }
    #adminDashboardProjectsTable_wrapper .dataTables_info,
    #adminDashboardProjectsTable_wrapper .dataTables_paginate {
        margin-top: .75rem;
    }
    @media (max-width: 991.98px) {
        #adminDashboardProjectsTable {
            min-width: 900px;
        }
    }
</style>

<script>
(function () {
    function initializeAdminHome() {
        // 1. Initialize DataTable for Admin Dashboard Projects Table
        if (window.jQuery && $.fn.DataTable && $('#adminDashboardProjectsTable').length) {
        $('#adminDashboardProjectsTable').DataTable({
            language: {
                url: 'https://cdn.datatables.net/plug-ins/1.13.4/i18n/th.json'
            },
            pageLength: 10,
            order: [[0, 'asc']],
            responsive: true
        });
    }

    // 2. ApexCharts for Monthly Ledger Expenditure Trend
    if (typeof ApexCharts !== 'undefined' && $('#ledgerMonthlyChart').length) {
        var monthlyData = <?= json_encode($monthly_ledger_totals ?? array_fill(0, 12, 0)); ?>;
        
        var options = {
            series: [{
                name: 'ยอดเบิกจ่าย (บาท)',
                data: monthlyData
            }],
            chart: {
                type: 'area',
                height: 290,
                toolbar: { show: false },
                fontFamily: 'K2D, sans-serif'
            },
            colors: ['#FB8C00'],
            fill: {
                type: 'gradient',
                gradient: {
                    shadeIntensity: 1,
                    opacityFrom: 0.45,
                    opacityTo: 0.05,
                    stops: [0, 90, 100]
                }
            },
            dataLabels: { enabled: false },
            stroke: {
                curve: 'smooth',
                width: 3
            },
            xaxis: {
                categories: ['ต.ค.', 'พ.ย.', 'ธ.ค.', 'ม.ค.', 'ก.พ.', 'มี.ค.', 'เม.ย.', 'พ.ค.', 'มิ.ย.', 'ก.ค.', 'ส.ค.', 'ก.ย.']
            },
            yaxis: {
                labels: {
                    formatter: function (val) {
                        return '฿' + val.toLocaleString('th-TH');
                    }
                }
            },
            tooltip: {
                y: {
                    formatter: function (val) {
                        return '฿' + val.toLocaleString('th-TH', { minimumFractionDigits: 2 }) + ' บาท';
                    }
                }
            },
            grid: {
                borderColor: '#f1f1f1'
            }
        };

        var chart = new ApexCharts(document.querySelector("#ledgerMonthlyChart"), options);
        chart.render();
    }

    }

    // รองรับกรณี jQuery ถูกโหลดหลัง View นี้
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', function () {
            if (window.jQuery) {
                initializeAdminHome();
            } else {
                window.addEventListener('load', initializeAdminHome, { once: true });
            }
        });
    } else if (window.jQuery) {
        initializeAdminHome();
    } else {
        window.addEventListener('load', initializeAdminHome, { once: true });
    }
})();
</script>