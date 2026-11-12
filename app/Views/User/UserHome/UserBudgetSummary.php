<!-- Layout container -->
<div class="layout-page">
    <?php echo view('User/UserLeyout/UserNavbar'); ?>

    <div class="content-wrapper">
        <div class="container-xxl flex-grow-1 container-p-y">

            <!-- Title & Year Filter Header -->
            <div class="row mb-4 align-items-center">
                <div class="col-md-7 mb-3 mb-md-0">
                    <div class="d-flex align-items-center gap-2">
                        <a href="<?= base_url(); ?>" class="btn btn-outline-secondary btn-sm rounded-circle p-2" title="กลับหน้าแรก">
                            <i class="bx bx-arrow-back fs-5"></i>
                        </a>
                        <div>
                            <h4 class="fw-bold mb-1 text-dark" style="font-family: 'Plus Jakarta Sans', 'K2D', sans-serif;">
                                <i class="bx bx-book-content text-primary me-2"></i>สรุปโครงการและงบประมาณ
                            </h4>
                            <p class="text-muted fs-7 mb-0">ค้นหา ตรวจสอบรายละเอียดโครงการ และติดตามรายการเบิกจ่ายงบประมาณ</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-5 text-md-end">
                    <form method="get" action="<?= base_url('User/BudgetSummary') ?>" class="d-inline-flex align-items-center gap-2">
                        <div class="input-group input-group-sm">
                            <span class="input-group-text bg-white"><i class="bx bx-calendar"></i></span>
                            <select class="form-select rounded-pill-end fw-semibold" name="year" onchange="this.form.submit()">
                                <?php foreach ($years as $y): ?>
                                    <option value="<?= $y->budget_year ?>" <?= $y->budget_year == ($selected_year ?? '2569') ? 'selected' : '' ?>>
                                        ปีงบประมาณ พ.ศ. <?= $y->budget_year ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <?php if (auth_is_admin()): ?>
                            <a href="<?= base_url('Admin/BudgetLedger?year=' . ($selected_year ?? '2569')) ?>" class="btn btn-primary btn-sm rounded-pill text-nowrap px-3 shadow-sm">
                                <i class="bx bx-edit me-1"></i> จัดการทะเบียนคุม
                            </a>
                        <?php endif; ?>
                    </form>
                </div>
            </div>

            <!-- Search & Filter Controls Card -->
            <div class="row mb-4 animate-fade-in-up">
                <div class="col-12">
                    <div class="card border-0 shadow-sm rounded-4 filter-box-card">
                        <div class="card-body p-4">
                            <div class="row g-3 align-items-end">
                                <!-- Search Keyword -->
                                <div class="col-lg-5 col-md-6">
                                    <label class="form-label small fw-bold text-muted mb-1">
                                        <i class="bx bx-search me-1 text-primary"></i>ค้นหาชื่อโครงการ หรือ รหัสโครงการ
                                    </label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light border-end-0"><i class="bx bx-search"></i></span>
                                        <input type="text" id="filterKeyword" class="form-control bg-light border-start-0" placeholder="พิมพ์ชื่อโครงการ, รหัส เช่น 1.1.1, หรือแหล่งเงิน...">
                                    </div>
                                </div>

                                <!-- Filter by Strategy -->
                                <div class="col-lg-4 col-md-6">
                                    <label class="form-label small fw-bold text-muted mb-1">
                                        <i class="bx bx-sitemap me-1 text-info"></i>ยุทธศาสตร์
                                    </label>
                                    <select id="filterStrategy" class="form-select bg-light">
                                        <option value="">-- ทั้งหมดยุทธศาสตร์ --</option>
                                        <?php if (!empty($strategies)): ?>
                                            <?php foreach ($strategies as $st): ?>
                                                <option value="ยุทธศาสตร์ที่ <?= $st->strategy_no ?>">
                                                    ยุทธศาสตร์ที่ <?= esc($st->strategy_no) ?> <?= esc($st->strategy_name) ?>
                                                </option>
                                            <?php endforeach; ?>
                                        <?php endif; ?>
                                    </select>
                                </div>

                                <!-- Filter by Balance Status -->
                                <div class="col-lg-3 col-md-12 d-flex gap-2">
                                    <div class="flex-grow-1">
                                        <label class="form-label small fw-bold text-muted mb-1">
                                            <i class="bx bx-wallet me-1 text-success"></i>สถานะงบประมาณ
                                        </label>
                                        <select id="filterBalanceStatus" class="form-select bg-light">
                                            <option value="">-- สถานะทั้งหมด --</option>
                                            <option value="has_balance">มีงบคงเหลือ</option>
                                            <option value="depleted">เบิกจ่ายเต็มจำนวน</option>
                                        </select>
                                    </div>
                                    <div>
                                        <button type="button" id="btnResetFilters" class="btn btn-outline-secondary rounded-pill px-3 shadow-none mt-auto" style="height: 38px;" title="ล้างตัวกรอง">
                                            <i class="bx bx-reset me-1"></i> ล้างค่า
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <?php
            // Group projects by strategy
            $strategyGroups = [];
            $otherProjects = [];

            if (!empty($strategies)) {
                foreach ($strategies as $st) {
                    $strategyGroups[$st->id] = [
                        'strategy'        => $st,
                        'projects'        => [],
                        'total_allocated' => 0.0,
                        'total_paid'      => 0.0,
                        'total_balance'   => 0.0,
                    ];
                }
            }

            if (!empty($projects)) {
                foreach ($projects as $p) {
                    $sid = $p->strategy_id ?? null;
                    if ($sid && isset($strategyGroups[$sid])) {
                        $strategyGroups[$sid]['projects'][] = $p;
                        $strategyGroups[$sid]['total_allocated'] += (float)($p->allocated_budget ?? 0);
                        $strategyGroups[$sid]['total_paid']      += (float)($p->total_paid ?? 0);
                        $strategyGroups[$sid]['total_balance']   += (float)($p->current_balance ?? 0);
                    } else {
                        $matched = false;
                        if (!empty($p->strategy_name)) {
                            foreach ($strategyGroups as $sidKey => &$grp) {
                                if (strpos($p->strategy_name, (string)$grp['strategy']->strategy_no) !== false || $p->strategy_name === $grp['strategy']->strategy_name) {
                                    $grp['projects'][] = $p;
                                    $grp['total_allocated'] += (float)($p->allocated_budget ?? 0);
                                    $grp['total_paid']      += (float)($p->total_paid ?? 0);
                                    $grp['total_balance']   += (float)($p->current_balance ?? 0);
                                    $matched = true;
                                    break;
                                }
                            }
                            unset($grp);
                        }
                        if (!$matched && !empty($p->project_code)) {
                            $codeParts = explode('.', $p->project_code);
                            $stratNo = $codeParts[0];
                            foreach ($strategyGroups as $sidKey => &$grp) {
                                if ((string)$grp['strategy']->strategy_no === (string)$stratNo) {
                                    $grp['projects'][] = $p;
                                    $grp['total_allocated'] += (float)($p->allocated_budget ?? 0);
                                    $grp['total_paid']      += (float)($p->total_paid ?? 0);
                                    $grp['total_balance']   += (float)($p->current_balance ?? 0);
                                    $matched = true;
                                    break;
                                }
                            }
                            unset($grp);
                        }
                        if (!$matched) {
                            $otherProjects[] = $p;
                        }
                    }
                }
            }
            ?>

            <!-- Projects Cards & Table Section -->
            <div class="row animate-fade-in-up" style="animation-delay: 0.05s;">
                <div class="col-12">
                    <div class="card border-0 shadow-sm rounded-4">
                        <div class="card-header bg-white border-0 pt-4 px-4 pb-2 d-flex align-items-center justify-content-between flex-wrap gap-2">
                            <div>
                                <h5 class="fw-bold mb-1 text-dark">
                                    <i class="bx bx-grid-alt text-primary me-2"></i>รายชื่อโครงการและรายละเอียดงบประมาณ
                                </h5>
                                <p class="text-muted fs-7 mb-0">จำแนกตามยุทธศาสตร์ พร้อมรายละเอียดห้วงเวลา การเบิกจ่าย และยอดคงเหลือ</p>
                            </div>
                            <div class="d-flex align-items-center gap-2 flex-wrap">
                                <span id="recordCountBadge" class="badge bg-label-primary rounded-pill px-3 py-2 fs-8 fw-bold">
                                    ทั้งหมด <?= count($projects ?? []) ?> โครงการ
                                </span>
                                <!-- View Switcher: Card vs Table -->
                                <div class="btn-group btn-group-sm p-1 bg-light rounded-pill border" role="group">
                                    <button type="button" class="btn btn-sm rounded-pill btn-primary px-3 shadow-none view-toggle-btn active" data-view="card" id="btnViewCard">
                                        <i class="bx bx-grid-alt me-1"></i>การ์ด
                                    </button>
                                    <button type="button" class="btn btn-sm rounded-pill btn-light text-muted px-3 shadow-none view-toggle-btn" data-view="table" id="btnViewTable">
                                        <i class="bx bx-table me-1"></i>ตาราง
                                    </button>
                                </div>
                            </div>
                        </div>

                        <div class="card-body px-4 pb-4">
                            <!-- 1. Cards View (Grouped by Strategy) -->
                            <div id="projectCardsContainer">
                                <?php if (!empty($projects)): ?>
                                    <?php foreach ($strategyGroups as $sId => $group): ?>
                                        <?php if (empty($group['projects'])) continue; ?>
                                        <?php $st = $group['strategy']; ?>
                                        
                                        <div class="strategy-section mb-4" data-strategy-no="<?= esc($st->strategy_no) ?>" data-strategy-name="ยุทธศาสตร์ที่ <?= esc($st->strategy_no) ?>">
                                            <!-- Strategy Banner Header -->
                                            <div class="strategy-banner p-3 p-md-3.5 rounded-4 mb-3 d-flex align-items-center justify-content-between flex-wrap gap-2 shadow-sm">
                                                <div class="d-flex align-items-center gap-2.5">
                                                    <span class="badge bg-warning text-dark fw-bold rounded-pill px-3 py-1.5 fs-7 shadow-sm">
                                                        ยุทธศาสตร์ที่ <?= esc($st->strategy_no) ?>
                                                    </span>
                                                    <h6 class="fw-bold mb-0 text-white strategy-header-title" style="font-size: 0.98rem; letter-spacing: 0.1px;">
                                                        <?= esc($st->strategy_name) ?>
                                                    </h6>
                                                </div>
                                                <div class="d-flex align-items-center gap-2 flex-wrap ms-auto">
                                                    <span class="badge rounded-pill px-3 py-1.5 fs-8 shadow-sm" style="background: #ffffff !important; color: #0f172a !important; font-weight: 700;">
                                                        <i class="bx bx-folder text-primary me-1"></i><?= count($group['projects']) ?> โครงการ
                                                    </span>
                                                    <span class="badge rounded-pill px-3 py-1.5 fs-8 font-monospace shadow-sm" style="background: rgba(251, 140, 0, 0.2) !important; color: #ffedd5 !important; border: 1px solid rgba(251, 140, 0, 0.45) !important; font-weight: 700;">
                                                        <i class="bx bx-coin-stack text-warning me-1"></i>งบรวม <?= number_format($group['total_allocated'], 2) ?> ฿
                                                    </span>
                                                </div>
                                            </div>

                                            <!-- Projects Cards Grid for this Strategy -->
                                            <div class="row g-3">
                                                <?php foreach ($group['projects'] as $p): ?>
                                                    <?php $timeStatus = get_project_time_status($p->start_date ?? null, $p->end_date ?? null); ?>
                                                    <div class="col-xl-4 col-lg-6 col-md-6 project-card-item" 
                                                         data-code="<?= strtolower(esc($p->project_code ?: '')) ?>" 
                                                         data-name="<?= strtolower(esc($p->project_name ?? '')) ?>" 
                                                         data-source="<?= strtolower(esc($p->budget_source ?? '')) ?>"
                                                         data-strategy="ยุทธศาสตร์ที่ <?= esc($st->strategy_no) ?>" 
                                                         data-has-balance="<?= ($p->current_balance ?? 0) > 0 ? '1' : '0' ?>"
                                                         data-time-status="<?= $timeStatus['status'] ?>">
                                                        
                                                        <div class="card h-100 border rounded-4 project-card-box">
                                                            <div class="card-body p-3 d-flex flex-column">
                                                                <!-- Top Badges: Code + Time Status -->
                                                                <div class="d-flex justify-content-between align-items-center mb-2.5">
                                                                    <span class="badge bg-label-primary font-monospace fs-8 px-2.5 py-1 rounded-pill fw-bold">
                                                                        <i class="bx bx-tag me-1"></i><?= esc($p->project_code ?: '-') ?>
                                                                    </span>
                                                                    <div>
                                                                        <?= $timeStatus['badge_html'] ?>
                                                                    </div>
                                                                </div>

                                                                <!-- Project Title -->
                                                                <h6 class="fw-bold text-dark mb-2 project-title" style="font-size: 0.93rem; line-height: 1.45; min-height: 2.7rem;">
                                                                    <?= esc($p->project_name ?? '-') ?>
                                                                </h6>

                                                                <!-- Strategy Sub & Budget Source Tags -->
                                                                <div class="d-flex flex-wrap gap-1 mb-2.5">
                                                                    <?php if (!empty($p->strategy_sub)): ?>
                                                                        <span class="badge bg-label-info rounded-pill fs-8 text-truncate" style="max-width: 100%;" title="<?= esc($p->strategy_sub) ?>">
                                                                            <i class="bx bx-subdirectory-right me-0.5"></i><?= esc($p->strategy_sub) ?>
                                                                        </span>
                                                                    <?php endif; ?>
                                                                    <?php if (!empty($p->budget_source)): ?>
                                                                        <span class="badge bg-light text-secondary rounded-pill fs-8 border text-truncate" style="max-width: 100%;" title="<?= esc($p->budget_source) ?>">
                                                                            <i class="bx bx-coin-stack text-warning me-1"></i><?= esc($p->budget_source) ?>
                                                                        </span>
                                                                    <?php endif; ?>
                                                                </div>

                                                                <!-- Time Period Box -->
                                                                <?php if ($timeStatus['status'] !== 'unspecified'): ?>
                                                                    <div class="small text-muted mb-3 d-flex align-items-center gap-1.5 bg-light rounded-3 px-2.5 py-1.5" style="font-size: 0.77rem;">
                                                                        <i class="bx bx-calendar-event text-primary flex-shrink-0 fs-6"></i>
                                                                        <span>ห้วงเวลา: <b class="text-dark"><?= esc($timeStatus['date_range']) ?></b></span>
                                                                    </div>
                                                                <?php endif; ?>

                                                                <!-- Financial 3-Column Metrics -->
                                                                <div class="row g-1 text-center py-2 bg-light rounded-3 mb-3 mt-auto">
                                                                    <div class="col-4 border-end">
                                                                        <span class="text-muted d-block" style="font-size: 0.73rem;">งบจัดสรร</span>
                                                                        <span class="fw-bold text-dark font-monospace" style="font-size: 0.84rem;">
                                                                            <?= number_format($p->allocated_budget ?? 0, 2) ?>
                                                                        </span>
                                                                    </div>
                                                                    <div class="col-4 border-end">
                                                                        <span class="text-muted d-block" style="font-size: 0.73rem;">เบิกจ่ายแล้ว</span>
                                                                        <span class="fw-bold text-danger font-monospace" style="font-size: 0.84rem;">
                                                                            <?= number_format($p->total_paid ?? 0, 2) ?>
                                                                        </span>
                                                                    </div>
                                                                    <div class="col-4">
                                                                        <span class="text-muted d-block" style="font-size: 0.73rem;">คงเหลือสุทธิ</span>
                                                                        <span class="fw-bold <?= ($p->current_balance ?? 0) > 0 ? 'text-success' : 'text-muted' ?> font-monospace" style="font-size: 0.84rem;">
                                                                            <?= number_format($p->current_balance ?? 0, 2) ?>
                                                                        </span>
                                                                    </div>
                                                                </div>

                                                                <!-- Progress Bar & Admin Button -->
                                                                <div class="pt-1">
                                                                    <div class="d-flex justify-content-between align-items-center mb-1 fs-8">
                                                                        <span class="text-muted" style="font-size: 0.76rem;">ใช้เงินไปแล้ว</span>
                                                                        <span class="fw-bold <?= ($p->used_percent ?? 0) > 80 ? 'text-danger' : (($p->used_percent ?? 0) > 50 ? 'text-warning' : 'text-success') ?>" style="font-size: 0.78rem;">
                                                                            <?= $p->used_percent ?? 0 ?>%
                                                                        </span>
                                                                    </div>
                                                                    <div class="progress mb-2" style="height: 6px; border-radius: 10px; background: #e2e8f0;">
                                                                        <div class="progress-bar rounded-pill <?= ($p->used_percent ?? 0) > 80 ? 'bg-danger' : (($p->used_percent ?? 0) > 50 ? 'bg-warning' : 'bg-success') ?>" 
                                                                             style="width: <?= min(100, $p->used_percent ?? 0) ?>%"></div>
                                                                    </div>

                                                                    <?php if (auth_is_admin()): ?>
                                                                        <div class="text-end pt-1">
                                                                            <a href="<?= base_url('Admin/BudgetLedger/view/' . $p->id) ?>" class="btn btn-xs btn-outline-primary rounded-pill px-3" title="เปิดดูทะเบียนคุมโครงการ">
                                                                                <i class="bx bx-book-open me-1"></i>ดูทะเบียนคุม
                                                                            </a>
                                                                        </div>
                                                                    <?php endif; ?>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                <?php endforeach; ?>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>

                                    <!-- Other Projects (if any not matching standard strategy) -->
                                    <?php if (!empty($otherProjects)): ?>
                                        <div class="strategy-section mb-4" data-strategy-no="other" data-strategy-name="โครงการทั่วไป">
                                            <div class="strategy-banner p-3 rounded-4 mb-3 d-flex align-items-center justify-content-between flex-wrap gap-2 shadow-sm">
                                                <div class="d-flex align-items-center gap-2">
                                                    <span class="badge bg-secondary text-white fw-bold rounded-pill px-3 py-1.5 fs-7">
                                                        โครงการทั่วไป / อื่นๆ
                                                    </span>
                                                </div>
                                                <span class="badge rounded-pill px-3 py-1.5 fs-8 shadow-sm" style="background: #ffffff !important; color: #0f172a !important; font-weight: 700;">
                                                    <i class="bx bx-folder text-primary me-1"></i><?= count($otherProjects) ?> โครงการ
                                                </span>
                                            </div>
                                            <div class="row g-3">
                                                <?php foreach ($otherProjects as $p): ?>
                                                    <?php $timeStatus = get_project_time_status($p->start_date ?? null, $p->end_date ?? null); ?>
                                                    <div class="col-xl-4 col-lg-6 col-md-6 project-card-item" 
                                                         data-code="<?= strtolower(esc($p->project_code ?: '')) ?>" 
                                                         data-name="<?= strtolower(esc($p->project_name ?? '')) ?>" 
                                                         data-source="<?= strtolower(esc($p->budget_source ?? '')) ?>"
                                                         data-strategy="other" 
                                                         data-has-balance="<?= ($p->current_balance ?? 0) > 0 ? '1' : '0' ?>"
                                                         data-time-status="<?= $timeStatus['status'] ?>">
                                                        
                                                        <div class="card h-100 border rounded-4 project-card-box">
                                                            <div class="card-body p-3 d-flex flex-column">
                                                                <div class="d-flex justify-content-between align-items-center mb-2.5">
                                                                    <span class="badge bg-label-primary font-monospace fs-8 px-2.5 py-1 rounded-pill fw-bold">
                                                                        <?= esc($p->project_code ?: '-') ?>
                                                                    </span>
                                                                    <?= $timeStatus['badge_html'] ?>
                                                                </div>
                                                                <h6 class="fw-bold text-dark mb-2 project-title" style="font-size: 0.93rem; line-height: 1.45; min-height: 2.7rem;">
                                                                    <?= esc($p->project_name ?? '-') ?>
                                                                </h6>
                                                                <div class="row g-1 text-center py-2 bg-light rounded-3 mb-3 mt-auto">
                                                                    <div class="col-4 border-end">
                                                                        <span class="text-muted d-block" style="font-size: 0.73rem;">งบจัดสรร</span>
                                                                        <span class="fw-bold text-dark font-monospace" style="font-size: 0.84rem;"><?= number_format($p->allocated_budget ?? 0, 2) ?></span>
                                                                    </div>
                                                                    <div class="col-4 border-end">
                                                                        <span class="text-muted d-block" style="font-size: 0.73rem;">เบิกจ่ายแล้ว</span>
                                                                        <span class="fw-bold text-danger font-monospace" style="font-size: 0.84rem;"><?= number_format($p->total_paid ?? 0, 2) ?></span>
                                                                    </div>
                                                                    <div class="col-4">
                                                                        <span class="text-muted d-block" style="font-size: 0.73rem;">คงเหลือสุทธิ</span>
                                                                        <span class="fw-bold <?= ($p->current_balance ?? 0) > 0 ? 'text-success' : 'text-muted' ?> font-monospace" style="font-size: 0.84rem;"><?= number_format($p->current_balance ?? 0, 2) ?></span>
                                                                    </div>
                                                                </div>
                                                                <div class="pt-1">
                                                                    <div class="progress mb-2" style="height: 6px; border-radius: 10px; background: #e2e8f0;">
                                                                        <div class="progress-bar rounded-pill" style="width: <?= min(100, $p->used_percent ?? 0) ?>%"></div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                <?php endforeach; ?>
                                            </div>
                                        </div>
                                    <?php endif; ?>
                                <?php else: ?>
                                    <div class="text-center py-5 text-muted">
                                        <i class="bx bx-folder-open fs-1 mb-2 d-block opacity-50"></i>
                                        ยังไม่มีข้อมูลโครงการในปีงบประมาณนี้
                                    </div>
                                <?php endif; ?>

                                <!-- No Cards Match Alert -->
                                <div id="noCardsAlert" class="text-center py-5 text-muted d-none">
                                    <i class="bx bx-search-alt fs-1 mb-2 d-block opacity-50 text-warning"></i>
                                    <h6 class="fw-bold">ไม่พบโครงการที่ตรงกับเงื่อนไขการค้นหา</h6>
                                    <p class="small text-muted mb-0">ลองปรับคำค้นหา หรือกดปุ่ม "ล้างค่า" เพื่อเริ่มค้นหาใหม่</p>
                                </div>
                            </div>

                            <!-- 2. Table View (Alternative) -->
                            <div id="projectTableView" class="d-none">
                                <div class="table-responsive">
                                    <table id="userProjectsTable" class="table table-hover align-middle w-100">
                                        <thead class="table-light">
                                            <tr class="text-muted fs-7">
                                                <th style="width: 85px;" class="text-center text-nowrap">รหัส</th>
                                                <th style="min-width: 320px;">ชื่อโครงการ / กิจกรรม</th>
                                                <th style="width: 165px;" class="text-nowrap">ห้วงเวลาโครงการ</th>
                                                <th style="width: 185px;">ยุทธศาสตร์ & กลยุทธ์</th>
                                                <th class="text-end text-nowrap" style="width: 120px;">งบจัดสรร (บาท)</th>
                                                <th class="text-end text-nowrap" style="width: 120px;">เบิกจ่ายแล้ว (บาท)</th>
                                                <th class="text-end text-nowrap" style="width: 120px;">คงเหลือสุทธิ (บาท)</th>
                                                <th class="text-center text-nowrap" style="width: 100px;">ความก้าวหน้า</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php if (!empty($projects)): ?>
                                                <?php foreach ($projects as $p): ?>
                                                    <tr data-strategy="<?= esc($p->strategy_name ?? '') ?>" data-has-balance="<?= ($p->current_balance ?? 0) > 0 ? '1' : '0' ?>">
                                                        <td class="text-center" data-order="<?= natural_code_sort_key($p->project_code) ?>">
                                                            <span class="badge bg-label-primary font-monospace fs-8 px-2 py-1 rounded">
                                                                <?= esc($p->project_code ?: '-') ?>
                                                            </span>
                                                        </td>
                                                        <td class="py-3" style="min-width: 320px;">
                                                            <div class="fw-bold text-dark mb-1" style="font-size: 0.90rem; line-height: 1.45; letter-spacing: 0.1px;">
                                                                <i class="bx bx-folder-open text-primary me-1"></i><?= esc($p->project_name ?? '-') ?>
                                                            </div>
                                                            <?php if (!empty($p->budget_source ?? null)): ?>
                                                                <div class="d-inline-flex align-items-center bg-light text-secondary rounded-pill px-2.5 py-0.5 mt-1 border" style="font-size: 0.77rem; max-width: 100%;">
                                                                    <i class="bx bx-coin-stack text-warning me-1 flex-shrink-0"></i>
                                                                    <span class="text-truncate" title="<?= esc($p->budget_source) ?>"><?= esc($p->budget_source) ?></span>
                                                                </div>
                                                            <?php endif; ?>
                                                        </td>
                                                        <td>
                                                            <?php $timeStatus = get_project_time_status($p->start_date ?? null, $p->end_date ?? null); ?>
                                                            <div class="mb-1">
                                                                <?= $timeStatus['badge_html'] ?>
                                                            </div>
                                                            <?php if ($timeStatus['status'] !== 'unspecified'): ?>
                                                                <div class="text-muted fs-8" style="line-height: 1.35;">
                                                                    <i class="bx bx-calendar-event me-1 text-primary"></i><?= esc($timeStatus['date_range']) ?>
                                                                </div>
                                                            <?php endif; ?>
                                                        </td>
                                                        <td>
                                                            <?php if (!empty($p->strategy_name)): ?>
                                                                <span class="badge bg-label-info rounded-pill fs-8 mb-1 d-inline-block text-truncate" style="max-width: 220px;" title="<?= esc($p->strategy_name) ?>">
                                                                    <?= esc($p->strategy_name) ?>
                                                                </span>
                                                            <?php endif; ?>
                                                            <?php if (!empty($p->strategy_sub)): ?>
                                                                <div class="text-muted fs-8 text-truncate" style="max-width: 220px;" title="<?= esc($p->strategy_sub) ?>">
                                                                    <i class="bx bx-chevron-right me-1"></i><?= esc($p->strategy_sub) ?>
                                                                </div>
                                                            <?php endif; ?>
                                                        </td>
                                                        <td class="text-end fw-bold text-dark font-monospace">
                                                            <?= number_format($p->allocated_budget ?? 0, 2) ?>
                                                        </td>
                                                        <td class="text-end text-danger font-monospace">
                                                            <?= number_format($p->total_paid ?? 0, 2) ?>
                                                        </td>
                                                        <td class="text-end fw-bold <?= ($p->current_balance ?? 0) > 0 ? 'text-success' : 'text-muted' ?> font-monospace">
                                                            <?= number_format($p->current_balance ?? 0, 2) ?>
                                                        </td>
                                                        <td class="text-center">
                                                            <div class="d-flex align-items-center justify-content-center gap-1">
                                                                <div class="progress" style="width: 50px; height: 6px;">
                                                                    <div class="progress-bar <?= ($p->used_percent ?? 0) > 80 ? 'bg-danger' : (($p->used_percent ?? 0) > 50 ? 'bg-warning' : 'bg-success') ?>" style="width: <?= $p->used_percent ?? 0 ?>%"></div>
                                                                </div>
                                                                <small class="fs-8 fw-semibold"><?= $p->used_percent ?? 0 ?>%</small>
                                                            </div>
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
            </div>

        </div>
    </div>
</div>

<style>
    .filter-box-card {
        background: #ffffff;
        border: 1px solid rgba(0, 0, 0, 0.05) !important;
    }
    .strategy-banner {
        background: linear-gradient(135deg, #1e293b 0%, #334155 100%);
        border: 1px solid rgba(255, 255, 255, 0.08);
    }
    .project-card-box {
        background: #ffffff;
        border-color: rgba(0, 0, 0, 0.08) !important;
        border-radius: 18px !important;
        box-shadow: 0 4px 15px -3px rgba(0, 0, 0, 0.04), 0 2px 6px -2px rgba(0, 0, 0, 0.03);
        transition: all 0.25s cubic-bezier(0.16, 1, 0.3, 1);
    }
    .project-card-box:hover {
        transform: translateY(-4px);
        box-shadow: 0 14px 28px -4px rgba(0, 0, 0, 0.09), 0 5px 10px -2px rgba(0, 0, 0, 0.04);
        border-color: rgba(251, 140, 0, 0.35) !important;
    }
    .project-title {
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }
    .view-toggle-btn {
        transition: all 0.2s ease-in-out;
    }
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    var $ = window.jQuery;
    if (!$) return;
    var dtTable = null;

    // 1. Initialize DataTable for Table View
    if ($.fn.DataTable && $('#userProjectsTable').length) {
        dtTable = $('#userProjectsTable').DataTable({
            language: {
                url: 'https://cdn.datatables.net/plug-ins/1.13.4/i18n/th.json'
            },
            pageLength: 15,
            order: [[0, 'asc']],
            responsive: true,
            dom: '<"row mb-3"<"col-sm-12"tr>><"row"<"col-sm-12 col-md-5"i><"col-sm-12 col-md-7"p>>'
        });

        dtTable.on('draw', function() {
            if ($('#btnViewTable').hasClass('active')) {
                var info = dtTable.page.info();
                $('#recordCountBadge').text('แสดง ' + info.recordsDisplay + ' จาก ' + info.recordsTotal + ' โครงการ');
            }
        });
    }

    // 2. Filter Cards & Strategy Sections Logic
    function filterCards() {
        var kw = $('#filterKeyword').val().trim().toLowerCase();
        var st = $('#filterStrategy').val();
        var bs = $('#filterBalanceStatus').val();

        var visibleCount = 0;
        var totalCount = $('.project-card-item').length;

        $('.project-card-item').each(function() {
            var $card = $(this);
            var name = $card.data('name') || '';
            var code = $card.data('code') || '';
            var source = $card.data('source') || '';
            var cardSt = $card.data('strategy') || '';
            var hasBal = $card.data('has-balance').toString();

            var matchKw = !kw || name.indexOf(kw) > -1 || code.indexOf(kw) > -1 || source.indexOf(kw) > -1;
            var matchSt = !st || cardSt.indexOf(st) > -1;
            var matchBs = true;
            if (bs === 'has_balance') matchBs = (hasBal === '1');
            else if (bs === 'depleted') matchBs = (hasBal === '0');

            if (matchKw && matchSt && matchBs) {
                $card.removeClass('d-none');
                visibleCount++;
            } else {
                $card.addClass('d-none');
            }
        });

        // Toggle visibility of strategy sections based on whether they contain visible cards
        $('.strategy-section').each(function() {
            var $section = $(this);
            var visibleInSec = $section.find('.project-card-item:not(.d-none)').length;
            if (visibleInSec > 0) {
                $section.removeClass('d-none');
            } else {
                $section.addClass('d-none');
            }
        });

        if (visibleCount === 0 && totalCount > 0) {
            $('#noCardsAlert').removeClass('d-none');
        } else {
            $('#noCardsAlert').addClass('d-none');
        }

        if ($('#btnViewCard').hasClass('active')) {
            $('#recordCountBadge').text('แสดง ' + visibleCount + ' จาก ' + totalCount + ' โครงการ');
        }
    }

    // 3. Connect Filters to both Cards & DataTable
    $('#filterKeyword').on('keyup', function() {
        filterCards();
        if (dtTable) dtTable.search(this.value).draw();
    });

    $('#filterStrategy').on('change', function() {
        filterCards();
        if (dtTable) {
            var val = $(this).val();
            dtTable.column(3).search(val ? val : '', true, false).draw();
        }
    });

    $('#filterBalanceStatus').on('change', function() {
        filterCards();
        if (dtTable) dtTable.draw();
    });

    // Custom Balance Status Filter for DataTable
    if ($.fn.DataTable) {
        $.fn.dataTable.ext.search.push(function(settings, data, dataIndex) {
            if (settings.nTable.id !== 'userProjectsTable') return true;

            var statusFilter = $('#filterBalanceStatus').val();
            if (!statusFilter) return true;

            var rowNode = dtTable.row(dataIndex).node();
            var hasBalance = $(rowNode).data('has-balance');

            if (statusFilter === 'has_balance') {
                return hasBalance == 1;
            } else if (statusFilter === 'depleted') {
                return hasBalance == 0;
            }
            return true;
        });
    }

    // Reset Filters
    $('#btnResetFilters').on('click', function() {
        $('#filterKeyword').val('');
        $('#filterStrategy').val('');
        $('#filterBalanceStatus').val('');
        filterCards();
        if (dtTable) dtTable.search('').columns().search('').draw();
    });

    // 4. View Switcher (Card vs Table)
    $('.view-toggle-btn').on('click', function() {
        var view = $(this).data('view');
        $('.view-toggle-btn').removeClass('active btn-primary').addClass('btn-light text-muted');
        $(this).addClass('active btn-primary').removeClass('btn-light text-muted');

        if (view === 'card') {
            $('#projectCardsContainer').removeClass('d-none');
            $('#projectTableView').addClass('d-none');
            filterCards();
        } else {
            $('#projectCardsContainer').addClass('d-none');
            $('#projectTableView').removeClass('d-none');
            if (dtTable) {
                dtTable.columns.adjust().draw();
                var info = dtTable.page.info();
                $('#recordCountBadge').text('แสดง ' + info.recordsDisplay + ' จาก ' + info.recordsTotal + ' โครงการ');
            }
        }
    });
});
</script>
