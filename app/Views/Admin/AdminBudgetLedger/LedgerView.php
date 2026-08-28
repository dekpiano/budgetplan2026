<!-- Layout container -->
<div class="layout-page">
    <?php echo view('Admin/AdminLeyout/AdminNavbar'); ?>

    <!-- Content wrapper -->
    <div class="content-wrapper">
        <!-- Content -->
        <div class="container-xxl flex-grow-1 container-p-y">

            <!-- Title & Breadcrumb -->
            <div class="row mb-3">
                <div class="col-12">
                    <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
                        <div>
                            <h4 class="fw-bold mb-1" style="font-family: 'Plus Jakarta Sans', 'K2D', sans-serif;">
                                <i class="bx bx-spreadsheet text-primary me-2"></i>ทะเบียนคุมเงินงบประมาณรายจ่าย
                            </h4>
                            <p class="text-muted fs-7 mb-0">ตรวจสอบความเคลื่อนไหวงบประมาณ ยืมเงินทดรองจ่าย และส่งใช้เงินยืม</p>
                        </div>
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb mb-0 bg-transparent p-0">
                                <li class="breadcrumb-item"><a href="<?= base_url('Admin/Home'); ?>" class="text-primary">หน้าแรก</a></li>
                                <li class="breadcrumb-item"><a href="<?= base_url('Admin/BudgetLedger'); ?>" class="text-primary">ทะเบียนคุม</a></li>
                                <li class="breadcrumb-item active text-secondary" aria-current="page"><?= esc($project->project_code ?: 'โครงการ') ?></li>
                            </ol>
                        </nav>
                    </div>
                </div>
            </div>

            <!-- Project Details Header Card (Luxury Style) -->
            <div class="card border-0 shadow-sm mb-4 luxury-project-card" style="border-radius: 18px; background: #fff;">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-start flex-wrap gap-3 mb-3 border-bottom pb-3">
                        <div>
                            <div class="d-flex align-items-center gap-2 mb-1">
                                <span class="badge bg-label-primary fs-7 rounded-pill px-3 py-1 fw-bold">
                                    หมวด <?= esc($project->project_code ?: '-') ?>
                                </span>
                                <span class="badge bg-label-warning fs-7 rounded-pill px-3 py-1">
                                    ปีงบประมาณ พ.ศ. <?= esc($project->budget_year) ?>
                                </span>
                            </div>
                            <h4 class="fw-bold text-dark mb-1 mt-2"><?= esc($project->project_name) ?></h4>
                            <p class="text-muted mb-0 small">
                                <i class="bx bx-coin-stack text-warning me-1"></i><b>แหล่งงบประมาณ:</b> <?= esc($project->budget_source ?: '-') ?>
                            </p>
                        </div>

                        <!-- Action Buttons -->
                        <div class="d-flex gap-2 align-items-center">
                            <a href="<?= base_url('Admin/BudgetLedger') ?>" class="btn btn-outline-secondary btn-sm rounded-pill px-3">
                                <i class="bx bx-arrow-back me-1"></i> กลับ
                            </a>
                            <a href="<?= base_url('Admin/BudgetLedger/print/' . $project->id) ?>" target="_blank" class="btn btn-outline-primary btn-sm rounded-pill px-3">
                                <i class="bx bx-printer me-1"></i> พิมพ์ทะเบียนคุม
                            </a>
                            <?php if (auth_can_manage_finance()) : ?>
                            <button type="button" class="btn btn-primary btn-sm rounded-pill px-3 shadow-sm" data-bs-toggle="modal" data-bs-target="#modalLedgerItem">
                                <i class="bx bx-plus-circle me-1"></i> บันทึกรายการ
                            </button>
                            <?php else : ?>
                            <span class="badge bg-label-info rounded-pill px-3 py-2"><i class="bx bx-show me-1"></i>โหมดอ่านข้อมูล</span>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- Strategic info tags -->
                    <div class="row g-2 text-muted small">
                        <div class="col-md-6">
                            <i class="bx bx-target-lock text-primary me-1"></i><b>ยุทธศาสตร์:</b> <?= esc($project->strategy_name ?: '-') ?>
                        </div>
                        <div class="col-md-6">
                            <i class="bx bx-git-branch text-info me-1"></i><b>กลยุทธ์:</b> <?= esc($project->strategy_sub ?: '-') ?>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Stats 4 Mini Cards -->
            <div class="row g-3 mb-4">
                <div class="col-6 col-lg-3">
                    <div class="card border-0 shadow-sm p-3 h-100" style="border-radius: 14px; background: #fff;">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <span class="text-muted fs-8 fw-semibold">งบประมาณที่ได้รับจัดสรร</span>
                                <h4 class="fw-bold mb-0 text-primary mt-1" id="statAllocatedBudget"><?= number_format($project->allocated_budget, 2) ?></h4>
                            </div>
                            <div class="avatar bg-primary-soft rounded p-2 text-primary">
                                <i class="bx bx-wallet fs-3"></i>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-6 col-lg-3">
                    <div class="card border-0 shadow-sm p-3 h-100" style="border-radius: 14px; background: #fff;">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <span class="text-muted fs-8 fw-semibold">ยอดเงินยืมทดรองจ่าย</span>
                                <h4 class="fw-bold mb-0 text-warning mt-1" id="statTotalBorrow"><?= number_format($totals['borrow'], 2) ?></h4>
                            </div>
                            <div class="avatar bg-warning-soft rounded p-2 text-warning">
                                <i class="bx bx-time-five fs-3"></i>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-6 col-lg-3">
                    <div class="card border-0 shadow-sm p-3 h-100" style="border-radius: 14px; background: #fff;">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <span class="text-muted fs-8 fw-semibold">ยอดจ่ายจริง (ใบสำคัญ+เบิกตรง)</span>
                                <h4 class="fw-bold mb-0 text-orange mt-1" id="statTotalSpent"><?= number_format($totals['return_receipt'] + $totals['direct_withdraw'], 2) ?></h4>
                            </div>
                            <div class="avatar bg-danger-soft rounded p-2 text-danger">
                                <i class="bx bx-receipt fs-3"></i>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-6 col-lg-3">
                    <div class="card border-0 shadow-sm p-3 h-100" style="border-radius: 14px; background: linear-gradient(135deg, #198754 0%, #157347 100%); color: #fff;">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <span class="text-white-50 fs-8 fw-semibold">ยอดคงเหลือสุทธิ</span>
                                <h4 class="fw-bold mb-0 text-white mt-1" id="statNetBalance"><?= number_format($totals['last_balance'], 2) ?></h4>
                            </div>
                            <div class="avatar bg-white bg-opacity-25 rounded p-2 text-white">
                                <i class="bx bx-check-double fs-3"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Sub-Items Budget Summary Card (if project has sub-items) -->
            <?php if (!empty($sub_items)): ?>
            <div class="card border-0 shadow-sm mb-4" style="border-radius: 16px; border: 1px solid #e2e8f0;">
                <div class="card-header bg-white py-3 border-bottom d-flex align-items-center justify-content-between flex-wrap gap-2">
                    <div class="d-flex align-items-center gap-2">
                        <span class="badge bg-label-warning rounded-circle p-2"><i class="bx bx-list-check fs-5"></i></span>
                        <div>
                            <h6 class="fw-bold mb-0 text-dark">สรุปงบประมาณแยกตามรายการข้อย่อย (Sub-Items Breakdown)</h6>
                            <small class="text-muted">ตรวจสอบยอดจัดสรร การใช้จ่ายจริง และคงเหลือของแต่ละกิจกรรมย่อย</small>
                        </div>
                    </div>
                    <span class="badge bg-label-secondary rounded-pill px-3 py-1 fs-8"><?= count($sub_items) ?> รายการย่อย</span>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0" style="font-size: 0.86rem;">
                            <thead class="table-light">
                                <tr>
                                    <th style="width: 120px;" class="text-center">ข้อย่อย</th>
                                    <th>ชื่อรายการ / กิจกรรม</th>
                                    <th class="text-end" style="width: 140px;">งบจัดสรร</th>
                                    <th class="text-end" style="width: 140px;">จ่ายจริง</th>
                                    <th class="text-end" style="width: 150px;">คงเหลือ</th>
                                    <th style="width: 160px;" class="text-center">สัดส่วนการใช้</th>
                                </tr>
                            </thead>
                            <tbody id="subItemsSummaryBody">
                                <?php foreach ($sub_items as $sub): ?>
                                <tr>
                                    <td class="text-center">
                                        <span class="badge bg-label-dark fw-bold"><?= esc($sub->item_no ?: '-') ?></span>
                                    </td>
                                    <td>
                                        <span class="fw-bold text-dark"><?= esc($sub->item_name) ?></span>
                                    </td>
                                    <td class="text-end fw-semibold text-primary"><?= number_format($sub->allocated_budget, 2) ?> ฿</td>
                                    <td class="text-end fw-semibold text-danger"><?= number_format($sub->total_spent, 2) ?> ฿</td>
                                    <td class="text-end fw-bold <?= $sub->remaining < 0 ? 'text-danger' : 'text-success' ?>">
                                        <?= number_format($sub->remaining, 2) ?> ฿
                                    </td>
                                    <td class="text-center">
                                        <div class="d-flex align-items-center gap-2">
                                            <div class="progress flex-grow-1" style="height: 6px;">
                                                <div class="progress-bar <?= $sub->percent >= 90 ? 'bg-danger' : ($sub->percent >= 50 ? 'bg-warning' : 'bg-success') ?>" 
                                                     role="progressbar" style="width: <?= $sub->percent ?>%"></div>
                                            </div>
                                            <span class="fs-8 fw-semibold text-muted"><?= $sub->percent ?>%</span>
                                        </div>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <?php endif; ?>

            <!-- Active Borrow Contracts & Settlement Status Card -->
            <div id="borrowsSectionCard" class="card border-0 shadow-sm mb-4 <?= empty($borrows_list) ? 'd-none' : '' ?>" style="border-radius: 16px; border: 1px solid rgba(251, 140, 0, 0.2) !important;">
                <div class="card-header bg-white py-3 border-bottom d-flex align-items-center justify-content-between flex-wrap gap-2">
                    <div class="d-flex align-items-center gap-2">
                        <span class="badge bg-warning-soft text-warning rounded-circle p-2"><i class="bx bx-time fs-5"></i></span>
                        <div>
                            <h6 class="fw-bold mb-0 text-dark">สัญญายืมเงินที่อยู่ระหว่างรอเคลียร์ (Active Borrow Settlements)</h6>
                            <small class="text-muted">รายการสัญญายืมเงินที่ยังมียอดค้างชำระ (เมื่อเคลียร์ครบแล้วจะซ่อนอัตโนมัติ)</small>
                        </div>
                    </div>
                    <span class="badge bg-label-warning rounded-pill px-3 py-1 fs-8" id="borrowsCountBadge">
                        มีสัญญายืมรอเคลียร์ <?= count($borrows_list) ?> รายการ
                    </span>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0" style="font-size: 0.85rem;">
                            <thead class="table-light">
                                <tr>
                                    <th style="width: 130px;">เลขที่เอกสารยืม</th>
                                    <th>รายการ / วัตถุประสงค์การยืม</th>
                                    <th class="text-end" style="width: 120px;">ยอดเงินยืม</th>
                                    <th class="text-end" style="width: 120px;">ส่งใช้ใบสำคัญ</th>
                                    <th class="text-end" style="width: 120px;">ส่งใช้เงินสด</th>
                                    <th class="text-end" style="width: 130px;">คงเหลือค้างเคลียร์</th>
                                    <th class="text-center" style="width: 140px;">สถานะ</th>
                                    <th class="text-center" style="width: 100px;">จัดการ</th>
                                </tr>
                            </thead>
                            <tbody id="borrowsTableBody">
                                <?php if (!empty($borrows_list)): ?>
                                    <?php foreach ($borrows_list as $b): ?>
                                    <tr>
                                        <td>
                                            <span class="badge bg-label-warning font-monospace fs-8">
                                                <?= esc($b->doc_no) ?>
                                            </span>
                                        </td>
                                        <td>
                                            <span class="text-dark fw-medium"><?= esc($b->description) ?></span>
                                        </td>
                                        <td class="text-end fw-bold text-dark"><?= number_format($b->amount_borrow, 2) ?></td>
                                        <td class="text-end text-success fw-semibold"><?= number_format($b->cleared_receipt, 2) ?></td>
                                        <td class="text-end text-info fw-semibold"><?= number_format($b->cleared_cash, 2) ?></td>
                                        <td class="text-end fw-bold <?= $b->remaining_borrow > 0 ? 'text-danger' : 'text-success' ?>">
                                            <?= number_format($b->remaining_borrow, 2) ?>
                                        </td>
                                        <td class="text-center">
                                            <?php if ($b->is_settled): ?>
                                                <span class="badge bg-label-success rounded-pill px-2 py-1 fs-8">
                                                    <i class="bx bx-check-circle me-1"></i>เคลียร์ครบแล้ว
                                                </span>
                                            <?php else: ?>
                                                <div class="d-flex flex-column align-items-center">
                                                    <span class="badge bg-label-danger rounded-pill px-2 py-1 fs-8 mb-1">
                                                        <i class="bx bx-error-circle me-1"></i>ค้าง <?= number_format($b->remaining_borrow, 2) ?> ฿
                                                    </span>
                                                    <div class="progress w-100" style="height: 4px;">
                                                        <div class="progress-bar bg-warning" role="progressbar" style="width: <?= $b->percent ?>%"></div>
                                                    </div>
                                                </div>
                                            <?php endif; ?>
                                        </td>
                                        <td class="text-center">
                                            <?php if (!$b->is_settled && auth_can_manage_finance()): ?>
                                                <button type="button" class="btn btn-xs btn-primary rounded-pill px-2 py-1" onclick="quickClearBorrow(<?= $b->id ?>, '<?= esc($b->doc_no) ?>', <?= $b->remaining_borrow ?>)">
                                                    <i class="bx bx-bolt-circle me-1"></i>ส่งใช้
                                                </button>
                                            <?php else: ?>
                                                <span class="text-muted fs-8">-</span>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Ledger Table (Official School Format) -->
            <div class="card border-0 shadow-sm" style="border-radius: 16px;">
                <div class="card-header bg-transparent d-flex justify-content-between align-items-center py-3 border-bottom flex-wrap gap-2">
                    <div>
                        <h5 class="card-title mb-0 fw-bold">
                            <i class="bx bx-table text-primary me-2"></i>ตารางบันทึกทะเบียนคุมงบประมาณรายจ่าย
                        </h5>
                        <small class="text-muted">คำนวณยอดเงินคงเหลืออัตโนมัติแบบต่อเนื่อง</small>
                    </div>
                    <span class="badge bg-label-primary rounded-pill px-3 py-2" id="ledgerRecordCountBadge">
                        <?= count($ledger) ?> รายการบันทึก
                    </span>
                </div>
                <div class="table-responsive">
                    <table class="table table-bordered table-hover align-middle mb-0 text-nowrap" style="font-size: 0.88rem;">
                        <thead class="table-light text-center">
                            <tr>
                                <th rowspan="2" style="vertical-align: middle; width: 110px;">วัน/เดือน/ปี</th>
                                <th rowspan="2" style="vertical-align: middle; width: 110px;">ที่เอกสาร</th>
                                <th rowspan="2" style="vertical-align: middle; min-width: 260px;">รายการ</th>
                                <th rowspan="2" style="vertical-align: middle; width: 110px;">เพิ่ม/ลด</th>
                                <th rowspan="2" style="vertical-align: middle; width: 110px;">ยืมเงิน</th>
                                <th colspan="2">เงินงบประมาณรายจ่าย</th>
                                <th rowspan="2" style="vertical-align: middle; width: 110px;">เบิก</th>
                                <th rowspan="2" style="vertical-align: middle; width: 120px;">คงเหลือ</th>
                                <th rowspan="2" style="vertical-align: middle; width: 80px;">จัดการ</th>
                            </tr>
                            <tr>
                                <th style="width: 105px;">ส่งใช้เงินสด</th>
                                <th style="width: 105px;">ส่งใช้ใบสำคัญ</th>
                            </tr>
                        </thead>
                        <tbody id="ledgerTableBody">
                            <?php if (empty($ledger)): ?>
                                <tr id="emptyLedgerRow">
                                    <td colspan="10" class="text-center py-5 text-muted">
                                        <i class="bx bx-file display-4 text-muted opacity-50 mb-2 d-block"></i>
                                        ยังไม่มีรายการบันทึกในทะเบียนคุมนี้
                                    </td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($ledger as $idx => $row): ?>
                                    <?php
                                        // Format Thai date
                                        $displayDate = '-';
                                        if (!empty($row->doc_date) && $row->doc_date !== '0000-00-00') {
                                            $time = strtotime($row->doc_date);
                                            if ($time !== false && $time > 0) {
                                                $d = date('j', $time);
                                                $m = date('n', $time);
                                                $y = (int)date('Y', $time);
                                                if ($y < 2400) {
                                                    $y += 543;
                                                }
                                                $thaiMonths = ['', 'ม.ค.', 'ก.พ.', 'มี.ค.', 'เม.ย.', 'พ.ค.', 'มิ.ย.', 'ก.ค.', 'ส.ค.', 'ก.ย.', 'ต.ค.', 'พ.ย.', 'ธ.ค.'];
                                                $displayDate = $d . '-' . $thaiMonths[$m] . '-' . substr((string)$y, 2);
                                            }
                                        }
                                    ?>
                                    <tr>
                                        <td class="text-center">
                                            <span class="fw-semibold text-dark"><?= $displayDate ?></span>
                                        </td>
                                        <td class="text-center fw-bold text-primary"><?= esc($row->doc_no ?: '-') ?></td>
                                        <td style="white-space: normal; min-width: 260px; max-width: 380px;">
                                            <?php if (!empty($row->sub_item_no)): ?>
                                                <span class="badge bg-label-warning me-1 fs-8" title="<?= esc($row->sub_item_name) ?>">
                                                    <i class="bx bx-subdirectory-right me-1"></i><?= esc($row->sub_item_no) ?>
                                                </span>
                                            <?php endif; ?>
                                            <span class="text-dark fw-medium"><?= esc($row->description) ?></span>
                                            <?php if (($row->amount_return_cash > 0 || $row->amount_return_receipt > 0) && !empty($row->ref_doc_no)): ?>
                                                <span class="badge bg-label-info font-monospace fs-8 ms-1" title="อ้างอิงสัญญายืมเงิน">
                                                    <i class="bx bx-link me-1"></i>ล้างสัญญา: <?= esc($row->ref_doc_no) ?>
                                                </span>
                                            <?php endif; ?>
                                            <?php if (!empty($row->notes)): ?>
                                                <small class="text-muted d-block mt-1"><i class="bx bx-info-circle me-1"></i><?= esc($row->notes) ?></small>
                                            <?php endif; ?>
                                        </td>
                                        <td class="text-end fw-bold text-primary">
                                            <?= $row->amount_budget_adjust > 0 ? number_format($row->amount_budget_adjust, 2) : '' ?>
                                        </td>
                                        <td class="text-end text-warning fw-semibold">
                                            <?= $row->amount_borrow > 0 ? number_format($row->amount_borrow, 2) : '' ?>
                                        </td>
                                        <td class="text-end text-info fw-semibold">
                                            <?= $row->amount_return_cash > 0 ? number_format($row->amount_return_cash, 2) : '' ?>
                                        </td>
                                        <td class="text-end text-success fw-semibold">
                                            <?= $row->amount_return_receipt > 0 ? number_format($row->amount_return_receipt, 2) : '' ?>
                                        </td>
                                        <td class="text-end text-danger fw-semibold">
                                            <?= $row->amount_direct_withdraw > 0 ? number_format($row->amount_direct_withdraw, 2) : '' ?>
                                        </td>
                                        <td class="text-end fw-bold fs-7 <?= $row->balance < 0 ? 'text-danger' : 'text-dark' ?>" style="background-color: #fcfcfd;">
                                            <?= number_format($row->balance, 2) ?>
                                        </td>
                                        <td class="text-center">
                                            <?php if (auth_can_manage_finance()) : ?>
                                            <div class="d-inline-flex gap-1">
                                                <button type="button" class="btn btn-xs btn-icon btn-outline-secondary rounded-circle btn-edit-ledger"
                                                    data-ledger='<?= htmlspecialchars(json_encode($row), ENT_QUOTES, 'UTF-8') ?>'
                                                    title="แก้ไขรายการ">
                                                    <i class="bx bx-edit-alt"></i>
                                                </button>
                                                <button type="button" class="btn btn-xs btn-icon btn-outline-danger rounded-circle" 
                                                    onclick="deleteLedgerItem(<?= $row->id ?>)"
                                                    title="ลบรายการ">
                                                    <i class="bx bx-trash"></i>
                                                </button>
                                            </div>
                                            <?php else: ?>
                                                <span class="text-muted fs-8">-</span>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                        <tfoot id="ledgerTableFoot" class="table-light text-end fw-bold">
                            <tr style="background-color: #f8f9fa;">
                                <td colspan="3" class="text-center text-dark">รวมทั้งสิ้น</td>
                                <td class="text-primary"><?= number_format($totals['adjust'], 2) ?></td>
                                <td class="text-warning"><?= number_format($totals['borrow'], 2) ?></td>
                                <td class="text-info"><?= number_format($totals['return_cash'], 2) ?></td>
                                <td class="text-success"><?= number_format($totals['return_receipt'], 2) ?></td>
                                <td class="text-danger"><?= number_format($totals['direct_withdraw'], 2) ?></td>
                                <td class="text-dark bg-white fs-7"><?= number_format($totals['last_balance'], 2) ?></td>
                                <td></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>

        </div>
    </div>
</div>

<!-- Modal Add/Edit Ledger Item -->
<div class="modal fade" id="modalLedgerItem" tabindex="-1" aria-hidden="true" data-project-id="<?= $project->id ?>" data-last-balance="<?= $totals['last_balance'] ?>">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header border-bottom py-3">
                <h5 class="modal-title fw-bold" id="modalLedgerTitle">
                    <i class="bx bx-plus-circle text-primary me-2"></i>บันทึกรายการในทะเบียนคุม
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="formLedger">
                <div class="modal-body p-4">
                    <input type="hidden" name="id" id="ledgerId" value="">
                    <input type="hidden" name="project_id" value="<?= $project->id ?>">

                    <!-- Fast Entry Type Selector -->
                    <div class="mb-4 p-3 bg-light rounded-3">
                        <label class="form-label fw-bold text-dark mb-2">
                            <i class="bx bx-tag-alt me-1 text-primary"></i>ประเภทรายการด่วน:
                        </label>
                        <div class="row g-2">
                            <div class="col-6 col-md-3">
                                <input type="radio" class="btn-check" name="entry_type" id="typeBorrow" value="borrow" onchange="switchEntryType('borrow')">
                                <label class="btn btn-outline-warning w-100 py-2 rounded-3 text-start" for="typeBorrow">
                                    <i class="bx bx-wallet d-block fs-4 mb-1"></i>
                                    <span class="fw-bold fs-7">ยืมเงินทดรอง</span>
                                </label>
                            </div>
                            <div class="col-6 col-md-3">
                                <input type="radio" class="btn-check" name="entry_type" id="typeReturn" value="return" onchange="switchEntryType('return')">
                                <label class="btn btn-outline-success w-100 py-2 rounded-3 text-start" for="typeReturn">
                                    <i class="bx bx-check-shield d-block fs-4 mb-1"></i>
                                    <span class="fw-bold fs-7">ส่งใช้เงินยืม</span>
                                </label>
                            </div>
                            <div class="col-6 col-md-3">
                                <input type="radio" class="btn-check" name="entry_type" id="typeDirect" value="direct" onchange="switchEntryType('direct')">
                                <label class="btn btn-outline-danger w-100 py-2 rounded-3 text-start" for="typeDirect">
                                    <i class="bx bx-money d-block fs-4 mb-1"></i>
                                    <span class="fw-bold fs-7">เบิกตรง/จัดซื้อ</span>
                                </label>
                            </div>
                            <div class="col-6 col-md-3">
                                <input type="radio" class="btn-check" name="entry_type" id="typeAdjust" value="adjust" onchange="switchEntryType('adjust')">
                                <label class="btn btn-outline-primary w-100 py-2 rounded-3 text-start" for="typeAdjust">
                                    <i class="bx bx-plus-circle d-block fs-4 mb-1"></i>
                                    <span class="fw-bold fs-7">ปรับยอดงบ</span>
                                </label>
                            </div>
                        </div>
                    </div>

                    <!-- Referenced Borrow Document Link Container (Shown on Return) -->
                    <div id="borrowRefContainer" class="p-3 mb-4 rounded-3 bg-light border d-none">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <label class="form-label fw-bold text-dark mb-0">
                                <i class="bx bx-link text-primary me-1"></i>อ้างอิงสัญญายืมเงินที่ต้องการเคลียร์:
                            </label>
                            <span class="badge bg-label-warning fs-8" id="borrowRemainingBadge">รอเลือกสัญญา</span>
                        </div>
                        <select class="form-select mb-2" id="refBorrowSelect" name="ref_borrow_id">
                            <option value="">-- ไม่ระบุ / ส่งใช้นอกสัญญา --</option>
                        </select>
                        <input type="hidden" name="ref_doc_no" id="refDocNo" value="">

                        <!-- Live Borrow Settlement Info Box -->
                        <div id="borrowDetailBox" class="p-3 rounded-2 bg-white border d-none">
                            <div class="row text-center g-2 fs-8">
                                <div class="col-4 border-end">
                                    <span class="text-muted d-block">ยอดเงินยืมเดิม</span>
                                    <b class="text-dark fs-7" id="boxBorrowAmount">0.00 ฿</b>
                                </div>
                                <div class="col-4 border-end">
                                    <span class="text-muted d-block">ส่งใช้แล้วสะสม</span>
                                    <b class="text-success fs-7" id="boxClearedAmount">0.00 ฿</b>
                                </div>
                                <div class="col-4">
                                    <span class="text-muted d-block">คงเหลือต้องเคลียร์</span>
                                    <b class="text-danger fs-7" id="boxRemainingAmount">0.00 ฿</b>
                                </div>
                            </div>
                            <div class="d-flex justify-content-between align-items-center mt-2 pt-2 border-top flex-wrap gap-2">
                                <small class="text-muted fs-8"><i class="bx bx-bulb text-warning me-1"></i>กรอกยอดใบเสร็จ ระบบจะคำนวณเงินสดคืนให้อัตโนมัติ</small>
                                <div class="btn-group btn-group-sm">
                                    <button type="button" class="btn btn-xs btn-outline-success" id="btnFillAllReceipt" title="ใช้ใบเสร็จเคลียร์เต็มจำนวน">
                                        <i class="bx bx-receipt me-1"></i> ใบเสร็จเต็ม
                                    </button>
                                    <button type="button" class="btn btn-xs btn-outline-info" id="btnFillAllCash" title="คืนเงินสดเต็มจำนวน">
                                        <i class="bx bx-money me-1"></i> คืนสดเต็ม
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row g-3">
                        <?php if (!empty($sub_items)): ?>
                        <div class="col-12">
                            <label class="form-label fw-semibold text-warning">
                                <i class="bx bx-list-check me-1"></i>เลือกกิจกรรม / รายการข้อย่อย (ถ้ามี)
                            </label>
                            <select class="form-select border-warning bg-light" name="sub_item_id" id="ledgerSubItemId">
                                <option value="">-- ภาพรวมโครงการ (ไม่ระบุข้อย่อย) --</option>
                                <?php foreach ($sub_items as $sub): ?>
                                    <option value="<?= $sub->id ?>" data-no="<?= esc($sub->item_no) ?>" data-name="<?= esc($sub->item_name) ?>" data-budget="<?= $sub->allocated_budget ?>">
                                        [ <?= esc($sub->item_no ?: '-') ?> ] <?= esc($sub->item_name) ?> (งบ <?= number_format($sub->allocated_budget, 2) ?> ฿)
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <small class="text-muted fs-8">หากเลือกรายการย่อย ระบบจะช่วยใส่ชื่อข้อย่อยในช่องวัตถุประสงค์ให้อัตโนมัติ</small>
                        </div>
                        <?php endif; ?>

                        <div class="col-md-6">
                            <label class="form-label required fw-semibold">วัน/เดือน/ปี <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text bg-white"><i class="bx bx-calendar text-primary"></i></span>
                                <input type="text" class="form-control selector-date" name="doc_date" id="ledgerDocDate" placeholder="เลือก วัน/เดือน/ปี (พ.ศ.)" readonly style="background-color: #fff; cursor: pointer;" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label required fw-semibold">ที่เอกสาร <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text bg-white"><i class="bx bx-file text-secondary"></i></span>
                                <input type="text" class="form-control" name="doc_no" id="ledgerDocNo" placeholder="เช่น 15/2569 หรือ เลขที่ฎีกา" required>
                            </div>
                        </div>

                        <div class="col-12">
                            <label class="form-label required fw-semibold">รายการ / วัตถุประสงค์การใช้จ่าย <span class="text-danger">*</span></label>
                            <textarea class="form-control" name="description" id="ledgerDescription" rows="2" required placeholder="ระบุรายละเอียดรายการ..."></textarea>
                        </div>

                        <!-- Amount Inputs -->
                        <div class="col-md-6">
                            <label class="form-label fw-semibold text-primary">เพิ่ม/ลด งบประมาณ (บาท)</label>
                            <input type="text" class="form-control text-end fw-semibold input-money" name="amount_budget_adjust" id="amountBudgetAdjust" value="0.00">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold text-warning">ยืมเงินทดรองจ่าย (บาท)</label>
                            <input type="text" class="form-control text-end fw-semibold input-money" name="amount_borrow" id="amountBorrow" value="0.00">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold text-info">ส่งใช้เงินยืม: ส่งใช้เงินสดคืน (บาท)</label>
                            <input type="text" class="form-control text-end fw-semibold input-money" name="amount_return_cash" id="amountReturnCash" value="0.00">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold text-success">ส่งใช้เงินยืม: ส่งใช้ใบสำคัญจ่าย (บาท)</label>
                            <input type="text" class="form-control text-end fw-semibold input-money" name="amount_return_receipt" id="amountReturnReceipt" value="0.00">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold text-danger">เบิกตรง / จัดซื้อจัดจ้าง (บาท)</label>
                            <input type="text" class="form-control text-end fw-semibold input-money" name="amount_direct_withdraw" id="amountDirectWithdraw" value="0.00">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">หมายเหตุ</label>
                            <input type="text" class="form-control" name="notes" id="ledgerNotes" placeholder="หมายเหตุเพิ่มเติม">
                        </div>

                        <!-- Live Calculation Preview Card -->
                        <div class="col-12 mt-3">
                            <div class="p-3 rounded-3" style="background: #f8fafc; border: 1.5px dashed #cbd5e1;">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <span class="text-dark fw-bold fs-7">
                                        <i class="bx bx-calculator text-primary me-1"></i>แสดงผลการคำนวณยอดเงินคงเหลือในรายการนี้
                                    </span>
                                    <span class="badge bg-label-info fs-8" id="calcModeBadge">คำนวณแบบ Real-time</span>
                                </div>
                                <div class="row text-center g-2">
                                    <div class="col-4 border-end">
                                        <small class="text-muted d-block fs-8 mb-1">ยอดคงเหลือก่อนหน้า</small>
                                        <span class="fw-bold fs-7 text-secondary" id="previewPrevBalance">0.00 ฿</span>
                                    </div>
                                    <div class="col-4 border-end">
                                        <small class="text-muted d-block fs-8 mb-1">เปลี่ยนแปลงในรายการนี้</small>
                                        <span class="fw-bold fs-7 text-secondary" id="previewChangeAmount">0.00 ฿</span>
                                    </div>
                                    <div class="col-4">
                                        <small class="text-muted d-block fs-8 mb-1">ยอดคงเหลือสุทธิหลังบันทึก</small>
                                        <span class="fw-bold fs-6 text-success" id="previewNewBalance">0.00 ฿</span>
                                    </div>
                                </div>
                                <div id="calcWarningAlert" class="alert alert-danger py-2 px-3 mt-3 mb-0 d-none fs-8 text-center rounded-3">
                                    <i class="bx bx-error-circle me-1 fw-bold"></i> <b>คำเตือน:</b> รายการนี้ทำให้ยอดเงินงบประมาณคงเหลือ <b>ติดลบเกินวงเงิน!</b>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
                <div class="modal-footer border-top py-3">
                    <button type="button" class="btn btn-outline-secondary rounded-pill" data-bs-dismiss="modal">ยกเลิก</button>
                    <button type="submit" class="btn btn-primary rounded-pill px-4" id="btnSubmitLedger">
                        <span class="spinner-border spinner-border-sm me-1 d-none" role="status" aria-hidden="true"></span>
                        <i class="bx bx-save me-1 btn-icon"></i> <span class="btn-text">บันทึกรายการ</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
.bg-primary-soft { background-color: rgba(251, 140, 0, 0.1); }
.bg-success-soft { background-color: rgba(25, 135, 84, 0.1); }
.bg-warning-soft { background-color: rgba(255, 165, 52, 0.15); }
.bg-danger-soft { background-color: rgba(220, 53, 69, 0.1); }
.text-orange { color: #FB8C00 !important; }
.fs-7 { font-size: 0.85rem !important; }
.fs-8 { font-size: 0.78rem !important; }
</style>
