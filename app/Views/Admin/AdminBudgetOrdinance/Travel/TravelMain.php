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
                                <i class="bx bx-briefcase me-2" style="color: #FB8C00 !important;"></i>ทะเบียนคุมงบประมาณเงินข้อบัญญัติ - ค่าใช้จ่ายเดินทางไปราชการ
                            </h4>
                            <p class="text-muted fs-7 mb-0">ระบบบันทึกและติดตามการเบิกจ่ายค่าใช้จ่ายในการเดินทางไปราชการของข้าราชการครูและบุคลากร</p>
                        </div>
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb mb-0 bg-transparent p-0">
                                <li class="breadcrumb-item"><a href="<?= base_url('Admin/Home'); ?>" style="color: #FB8C00 !important;">หน้าแรก</a></li>
                                <li class="breadcrumb-item"><a href="<?= base_url('Admin/BudgetOrdinance/Travel'); ?>" style="color: #FB8C00 !important;">ทะเบียนคุมเงินข้อบัญญัติ</a></li>
                                <li class="breadcrumb-item active text-secondary" aria-current="page">ค่าใช้จ่ายเดินทางไปราชการ</li>
                            </ol>
                        </nav>
                    </div>
                </div>
            </div>

            <!-- Top Filter & Action Bar Card -->
            <div class="card border-0 shadow-sm mb-4" style="border-radius: 16px; background: #fff;">
                <div class="card-body p-3 p-md-4">
                    <div class="d-flex flex-column flex-md-row justify-content-between align-items-stretch align-items-md-center gap-3">
                        <!-- Year Selector Form -->
                        <div class="d-flex align-items-center justify-content-between justify-content-md-start flex-wrap gap-2">
                            <div class="d-flex align-items-center gap-2">
                                <label for="selectYearFilter" class="form-label mb-0 fw-bold text-dark text-nowrap fs-7">
                                    <i class="bx bx-calendar-event me-1" style="color: #FB8C00 !important;"></i>ปีงบประมาณ:
                                </label>
                                <select id="selectYearFilter" class="form-select form-select-sm rounded-pill px-3 shadow-none" style="min-width: 140px; border-color: #FB8C00 !important;" onchange="location.href='<?= base_url('Admin/BudgetOrdinance/Travel'); ?>?year=' + this.value;">
                                    <?php 
                                    $hasSelected = false;
                                    foreach ($years as $y) : 
                                        $isSelected = ($y->budget_year == $selected_year);
                                        if ($isSelected) $hasSelected = true;
                                    ?>
                                        <option value="<?= esc($y->budget_year); ?>" <?= $isSelected ? 'selected' : ''; ?>>
                                            พ.ศ. <?= esc($y->budget_year); ?>
                                        </option>
                                    <?php endforeach; ?>
                                    <?php if (!$hasSelected) : ?>
                                        <option value="<?= esc($selected_year); ?>" selected>พ.ศ. <?= esc($selected_year); ?></option>
                                    <?php endif; ?>
                                </select>
                            </div>
                            <span class="badge rounded-pill px-3 py-2 fs-8" style="background-color: rgba(251, 140, 0, 0.12); color: #E65100; font-weight: 600;">
                                <i class="bx bx-check-circle me-1"></i>พ.ศ. <?= esc($selected_year); ?>
                            </span>
                        </div>

                        <!-- Action Buttons -->
                        <div class="d-flex flex-wrap flex-sm-nowrap gap-2 align-items-center">
                            <!-- Main Action: บันทึกเบิกค่าเดินทาง -->
                            <button type="button" class="btn btn-primary btn-sm rounded-pill px-3 shadow-sm btn-orange-main order-1 order-sm-3 flex-grow-1 flex-sm-grow-0" id="btnOpenAddTravel" data-has-budget="<?= ($stats->total_budget > 0) ? '1' : '0'; ?>" data-year="<?= esc($selected_year); ?>" style="min-height: 38px;">
                                <i class="bx bx-briefcase me-1"></i> บันทึกเบิกค่าเดินทาง
                            </button>
                            <!-- Secondary Action: ตั้งงบ / ปรับยอด -->
                            <button type="button" class="btn btn-outline-warning text-dark fw-bold btn-sm rounded-pill px-3 shadow-sm order-2 order-sm-2 flex-grow-1 flex-sm-grow-0" style="border-color: #FB8C00; min-height: 38px;" data-bs-toggle="modal" data-bs-target="#modalAddBudget">
                                <i class="bx bx-plus-circle me-1" style="color: #FB8C00;"></i> ตั้งงบประมาณ
                            </button>
                            <!-- Print Report -->
                            <a href="<?= base_url('Admin/BudgetOrdinance/Travel/print?year=' . $selected_year); ?>" target="_blank" class="btn btn-outline-warning btn-sm rounded-pill px-3 order-3 order-sm-1 flex-grow-1 flex-sm-grow-0 d-flex align-items-center justify-content-center" style="color: #FB8C00; border-color: #FB8C00; min-height: 38px;">
                                <i class="bx bx-printer me-1"></i> พิมพ์รายงาน
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <?php if ($stats->total_budget <= 0) : ?>
            <!-- Alert: ยังไม่มีการตั้งงบประมาณ -->
            <div class="alert alert-warning border-0 shadow-sm d-flex align-items-center justify-content-between flex-wrap gap-2 mb-4 p-3" style="border-radius: 14px; background-color: #fff8e1; border-left: 5px solid #FB8C00 !important;">
                <div class="d-flex align-items-center gap-3">
                    <div class="avatar avatar-sm rounded-circle d-flex align-items-center justify-content-center flex-shrink-0" style="background-color: #ffe0b2; color: #e65100;">
                        <i class="bx bx-error-circle fs-4"></i>
                    </div>
                    <div>
                        <strong class="text-dark d-block">ยังไม่มีการตั้งงบประมาณสำหรับปีงบประมาณ พ.ศ. <?= esc($selected_year); ?></strong>
                        <span class="fs-8 text-muted">ระบบกำหนดให้ต้องตั้งต้นงบประมาณก่อนเสมอ จึงจะสามารถบันทึกรายการเบิกจ่ายได้</span>
                    </div>
                </div>
                <button type="button" class="btn btn-sm rounded-pill px-3 fw-bold shadow-xs text-white" data-bs-toggle="modal" data-bs-target="#modalAddBudget" style="background-color: #FB8C00;">
                    <i class="bx bx-plus-circle me-1"></i> ตั้งงบประมาณตอนนี้
                </button>
            </div>
            <?php endif; ?>

            <!-- Stats 4 Mini Cards -->
            <div class="row g-3 mb-4">
                <!-- 1. Total Allocated Budget -->
                <div class="col-12 col-sm-6 col-xl-3">
                    <div class="card border-0 shadow-sm h-100" style="border-radius: 16px; background: linear-gradient(135deg, #fff9f0 0%, #ffffff 100%); border-left: 4px solid #FB8C00 !important;">
                        <div class="card-body p-3 p-md-4">
                            <div class="d-flex align-items-center justify-content-between">
                                <div>
                                    <span class="text-muted fs-8 fw-semibold text-uppercase d-block mb-1">งบจัดสรร</span>
                                    <h5 class="fw-bold mb-0 font-monospace" style="color: #FB8C00;">
                                        <?= number_format($stats->total_budget, 2); ?>
                                    </h5>
                                    <small class="fs-8 text-muted">บาท</small>
                                </div>
                                <div class="avatar avatar-sm rounded-circle p-2 d-flex align-items-center justify-content-center flex-shrink-0" style="background: rgba(251, 140, 0, 0.12); color: #FB8C00;">
                                    <i class="bx bx-wallet fs-4"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- 2. Total Withdrawn / Spent -->
                <div class="col-12 col-sm-6 col-xl-3">
                    <div class="card border-0 shadow-sm h-100" style="border-radius: 16px; background: linear-gradient(135deg, #fff5f5 0%, #ffffff 100%); border-left: 4px solid #dc3545 !important;">
                        <div class="card-body p-3 p-md-4">
                            <div class="d-flex align-items-center justify-content-between">
                                <div>
                                    <span class="text-muted fs-8 fw-semibold text-uppercase d-block mb-1">เบิกจ่ายแล้ว</span>
                                    <h5 class="fw-bold mb-0 text-danger font-monospace">
                                        <?= number_format($stats->total_spent, 2); ?>
                                    </h5>
                                    <small class="fs-8 text-muted">บาท</small>
                                </div>
                                <div class="avatar avatar-sm bg-label-danger rounded-circle p-2 d-flex align-items-center justify-content-center flex-shrink-0">
                                    <i class="bx bx-credit-card fs-4"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- 3. Remaining Balance -->
                <div class="col-12 col-sm-6 col-xl-3">
                    <div class="card border-0 shadow-sm h-100" style="border-radius: 16px; background: linear-gradient(135deg, #f0fdf4 0%, #ffffff 100%); border-left: 4px solid #198754 !important;">
                        <div class="card-body p-3 p-md-4">
                            <div class="d-flex align-items-center justify-content-between">
                                <div>
                                    <span class="text-muted fs-8 fw-semibold text-uppercase d-block mb-1">งบคงเหลือ</span>
                                    <h5 class="fw-bold mb-0 text-success font-monospace">
                                        <?= number_format($stats->remaining, 2); ?>
                                    </h5>
                                    <small class="fs-8 text-muted">บาท</small>
                                </div>
                                <div class="avatar avatar-sm bg-label-success rounded-circle p-2 d-flex align-items-center justify-content-center flex-shrink-0">
                                    <i class="bx bx-check-shield fs-4"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- 4. Total Travel Stats -->
                <div class="col-12 col-sm-6 col-xl-3">
                    <div class="card border-0 shadow-sm h-100" style="border-radius: 16px; background: linear-gradient(135deg, #f5f3ff 0%, #ffffff 100%); border-left: 4px solid #8b5cf6 !important;">
                        <div class="card-body p-3 p-md-4">
                            <div class="d-flex align-items-center justify-content-between">
                                <div>
                                    <span class="text-muted fs-8 fw-semibold text-uppercase d-block mb-1">สถิติการเดินทาง</span>
                                    <h5 class="fw-bold mb-0 font-monospace" style="color: #8b5cf6;">
                                        <?= number_format($stats->total_travelers); ?> <small class="fs-8 text-muted fw-normal">คน</small>
                                    </h5>
                                    <small class="fs-8 text-muted"><?= number_format($stats->total_records); ?> ครั้ง (<?= number_format($stats->total_days); ?> วัน)</small>
                                </div>
                                <div class="avatar avatar-sm rounded-circle p-2 d-flex align-items-center justify-content-center flex-shrink-0" style="background: rgba(139, 92, 246, 0.12); color: #8b5cf6;">
                                    <i class="bx bx-map-pin fs-4"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Ledger Data Table Card -->
            <div class="card border-0 shadow-sm" style="border-radius: 16px;">
                <div class="card-header bg-white border-bottom py-3 d-flex align-items-center justify-content-between flex-wrap gap-2">
                    <div class="d-flex align-items-center gap-2">
                        <span class="badge rounded-pill bg-warning p-2" style="background-color: #FB8C00 !important;">
                            <i class="bx bx-list-ul text-white fs-6"></i>
                        </span>
                        <h5 class="card-title fw-bold mb-0 text-dark">
                            บัญชีรายการค่าใช้จ่ายเดินทางไปราชการ ประจำปีงบประมาณ พ.ศ. <?= esc($selected_year); ?>
                        </h5>
                    </div>
                    <span class="badge bg-light text-secondary rounded-pill px-3 py-2 border font-monospace">
                        <?= count($items); ?> รายการ
                    </span>
                </div>

                <div class="card-body p-0">
                    <div class="table-responsive p-1 p-md-2">
                        <table class="table table-hover align-middle mb-0 table-fit-screen" id="tableTravelLedger">
                            <thead class="table-light">
                                <tr>
                                    <th class="text-center" style="width: 3%;">#</th>
                                    <th style="width: 9%;">วัน/เดือน/ปี</th>
                                    <th style="width: 11%;">เลขที่ฎีกา/เอกสาร</th>
                                    <th style="width: 23%;">รายการ / วัตถุประสงค์</th>
                                    <th style="width: 12%;">สถานที่/ระยะเวลา</th>
                                    <th style="width: 12%;">ผู้ขอ/ผู้เดินทาง</th>
                                    <th class="text-end" style="width: 9%;">แจกแจง</th>
                                    <th class="text-end" style="width: 10%;">เบิกจ่าย (บาท)</th>
                                    <th class="text-end" style="width: 10%;">คงเหลือ (บาท)</th>
                                    <th class="text-center" style="width: 5%;">จัดการ</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($items)) : ?>
                                    <?php $i = 1; foreach ($items as $item) : ?>
                                        <?php $isAdjust = (($item->entry_type ?? ($item->amount_budget_adjust > 0 ? 'adjust' : 'withdraw')) === 'adjust'); ?>
                                        <tr class="<?= $isAdjust ? 'table-warning bg-opacity-25' : ''; ?>">
                                            <td class="text-center fw-bold text-muted"><?= $i++; ?></td>
                                            
                                            <!-- Date -->
                                            <td>
                                                <div class="fw-semibold text-dark">
                                                    <?= thaidate_format($item->doc_date, 'short'); ?>
                                                </div>
                                                <small class="text-muted d-block font-monospace fs-8">
                                                    พ.ศ. <?= esc($item->budget_year); ?>
                                                </small>
                                            </td>

                                            <!-- Doc / Dika -->
                                            <td>
                                                <?php if (!empty($item->dika_no)) : ?>
                                                    <span class="badge bg-label-primary font-monospace mb-1 d-inline-block">
                                                        <i class="bx bx-receipt me-1"></i><?= esc($item->dika_no); ?>
                                                    </span>
                                                <?php endif; ?>
                                                <?php if (!empty($item->doc_no)) : ?>
                                                    <small class="text-muted d-block font-monospace">
                                                        <i class="bx bx-file me-1"></i><?= esc($item->doc_no); ?>
                                                    </small>
                                                <?php endif; ?>
                                                <?php if (!empty($item->order_no)) : ?>
                                                    <small class="text-muted d-block font-monospace">
                                                        <i class="bx bx-notepad me-1"></i>คำสั่ง: <?= esc($item->order_no); ?>
                                                    </small>
                                                <?php endif; ?>
                                            </td>

                                            <!-- Description -->
                                            <td>
                                                <?php if ($isAdjust) : ?>
                                                    <span class="badge bg-warning text-dark me-1">ตั้งงบ/ปรับยอด</span>
                                                <?php endif; ?>
                                                <span class="fw-semibold text-dark"><?= esc($item->description); ?></span>
                                                <?php if (!empty($item->notes)) : ?>
                                                    <small class="text-muted d-block fs-8"><i class="bx bx-info-circle me-1"></i><?= esc($item->notes); ?></small>
                                                <?php endif; ?>
                                            </td>

                                            <!-- Destination & Dates -->
                                            <td>
                                                <?php if (!empty($item->destination)) : ?>
                                                    <div class="fw-semibold text-dark">
                                                        <i class="bx bx-map-pin text-warning me-1" style="color: #FB8C00 !important;"></i><?= esc($item->destination); ?>
                                                    </div>
                                                <?php endif; ?>
                                                <?php if (!empty($item->travel_start_date)) : ?>
                                                    <small class="text-muted d-block fs-8">
                                                        <?= thaidate_range_format($item->travel_start_date, $item->travel_end_date, 'short'); ?>
                                                        (<?= $item->travel_days; ?> วัน)
                                                    </small>
                                                <?php endif; ?>
                                            </td>

                                            <!-- Travelers -->
                                            <td>
                                                <?php if (!empty($item->requester_name)) : ?>
                                                    <div class="fw-semibold text-dark">
                                                        <i class="bx bx-user text-primary me-1"></i><?= esc($item->requester_name); ?>
                                                    </div>
                                                <?php endif; ?>
                                                <?php if (!empty($item->traveler_names)) : ?>
                                                    <small class="text-muted d-block">
                                                        <i class="bx bx-group me-1"></i><?= esc($item->traveler_names); ?>
                                                    </small>
                                                <?php endif; ?>
                                                <?php if ($item->traveler_count > 0 && !$isAdjust) : ?>
                                                    <span class="badge bg-label-info fs-8 mt-1">
                                                        <?= $item->traveler_count; ?> คน
                                                    </span>
                                                <?php endif; ?>
                                            </td>

                                            <!-- Breakdown -->
                                            <td class="text-end font-monospace">
                                                <?php if ($isAdjust) : ?>
                                                    <span class="text-warning fw-bold">+<?= number_format($item->amount_budget_adjust, 2); ?></span>
                                                <?php else : ?>
                                                    <?php 
                                                    $details = [];
                                                    if ($item->allowance_amount > 0) $details[] = 'เบี้ยเลี้ยง ' . number_format($item->allowance_amount, 2);
                                                    if ($item->accommodation_amount > 0) $details[] = 'ที่พัก ' . number_format($item->accommodation_amount, 2);
                                                    if ($item->transport_amount > 0) $details[] = 'พาหนะ ' . number_format($item->transport_amount, 2);
                                                    if ($item->other_expenses_amount > 0) $details[] = 'อื่นๆ ' . number_format($item->other_expenses_amount, 2);
                                                    ?>
                                                    <small class="text-muted d-block fs-8"><?= implode('<br>', $details); ?></small>
                                                <?php endif; ?>
                                            </td>

                                            <!-- Amount Withdraw -->
                                            <td class="text-end font-monospace">
                                                <?php if ($item->amount_withdraw > 0) : ?>
                                                    <span class="fw-bold text-danger"><?= number_format($item->amount_withdraw, 2); ?></span>
                                                <?php else : ?>
                                                    <span class="text-muted">-</span>
                                                <?php endif; ?>
                                            </td>

                                            <!-- Balance -->
                                            <td class="text-end font-monospace">
                                                <span class="fw-bold text-success"><?= number_format($item->balance, 2); ?></span>
                                            </td>

                                            <!-- Actions -->
                                            <td class="text-center">
                                                <div class="d-inline-flex gap-1">
                                                    <button type="button" class="btn btn-sm btn-icon btn-outline-warning rounded-circle btn-edit-travel" data-id="<?= $item->id; ?>" title="แก้ไขข้อมูล" style="border-color: #FB8C00; color: #FB8C00;">
                                                        <i class="bx bx-edit-alt"></i>
                                                    </button>
                                                    <button type="button" class="btn btn-sm btn-icon btn-outline-danger rounded-circle btn-delete-travel" data-id="<?= $item->id; ?>" data-desc="<?= esc($item->description); ?>" title="ลบรายการ">
                                                        <i class="bx bx-trash"></i>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else : ?>
                                    <tr>
                                        <td colspan="10" class="text-center py-5 text-muted">
                                            <i class="bx bx-briefcase fs-1 mb-2 d-block opacity-50"></i>
                                            ยังไม่มีรายการค่าใช้จ่ายเดินทางในปีงบประมาณ พ.ศ. <?= esc($selected_year); ?>
                                        </td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Modal 1: บันทึกค่าเดินทาง (Withdraw - Orange Theme) -->
            <div class="modal fade" id="modalAddTravel" tabindex="-1" aria-labelledby="modalTravelTitle" aria-hidden="true" data-bs-backdrop="static">
                <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
                    <form class="modal-content border-0 shadow-lg modal-orange-content" id="formSaveTravel">
                        <input type="hidden" name="id" id="travel_id" value="">
                        <input type="hidden" name="budget_year" id="travel_budget_year" value="<?= esc($selected_year); ?>">
                        <input type="hidden" name="entry_type" id="travel_entry_type" value="withdraw">

                        <!-- Modal Header (Orange Gradient) -->
                        <div class="modal-header text-white px-4 py-3 modal-header-orange flex-shrink-0">
                            <div class="d-flex align-items-center gap-3">
                                <div class="avatar avatar-md bg-white rounded-circle d-flex align-items-center justify-content-center shadow-sm" style="color: #FB8C00 !important;">
                                    <i class="bx bx-briefcase fs-3"></i>
                                </div>
                                <div>
                                    <h5 class="modal-title text-white fw-bold mb-0" id="modalTravelTitle">
                                        บันทึกรายการเบิกจ่ายค่าใช้จ่ายเดินทางไปราชการ
                                    </h5>
                                    <small class="text-white-50 fs-8"><span id="travelModalYearDisplay">ปีงบประมาณ พ.ศ. <?= esc($selected_year); ?></span> | โรงเรียนสวนกุหลาบวิทยาลัย (จิรประวัติ) นครสวรรค์</small>
                                </div>
                            </div>
                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>

                        <!-- Modal Body (Clean Cards + Scrollable) -->
                        <div class="modal-body p-3 p-md-4 modal-body-custom-scroll" style="background-color: #faf9f6; max-height: calc(82vh - 120px); overflow-y: auto;">
                            
                            <!-- Section 1: ข้อมูลเอกสารและวันที่ -->
                            <div class="card border-0 shadow-none mb-3 section-box">
                                <div class="card-body p-3 p-md-4">
                                    <div class="d-flex align-items-center mb-3">
                                        <span class="badge-icon-orange me-2"><i class="bx bx-calendar-check fs-5"></i></span>
                                        <h6 class="fw-bold text-dark mb-0">1. ข้อมูลเอกสารและวันที่</h6>
                                    </div>
                                    <div class="row g-3">
                                        <div class="col-md-3">
                                            <label class="form-label fw-semibold required-star">วันที่เอกสาร / เบิกจ่าย (พ.ศ.)</label>
                                            <div class="input-group">
                                                <span class="input-group-text bg-light border-end-0"><i class="bx bx-calendar text-warning" style="color: #FB8C00 !important;"></i></span>
                                                <input type="text" class="form-control flatpickr-thai-buddhist border-start-0" name="doc_date" id="travel_doc_date" required placeholder="เลือกวันที่ (พ.ศ.)">
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <label class="form-label fw-semibold">เลขที่ฎีกา</label>
                                            <div class="input-group">
                                                <span class="input-group-text bg-light border-end-0"><i class="bx bx-receipt text-secondary"></i></span>
                                                <input type="text" class="form-control border-start-0" name="dika_no" id="travel_dika_no" placeholder="เช่น 123/2569">
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <label class="form-label fw-semibold">เลขที่หนังสือขออนุมัติ</label>
                                            <div class="input-group">
                                                <span class="input-group-text bg-light border-end-0"><i class="bx bx-file text-secondary"></i></span>
                                                <input type="text" class="form-control border-start-0" name="doc_no" id="travel_doc_no" placeholder="เช่น ศธ 04xxx/...">
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <label class="form-label fw-semibold">เลขที่คำสั่ง</label>
                                            <div class="input-group">
                                                <span class="input-group-text bg-light border-end-0"><i class="bx bx-notepad text-secondary"></i></span>
                                                <input type="text" class="form-control border-start-0" name="order_no" id="travel_order_no" placeholder="เช่น 45/2569">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Section 2: รายละเอียดการเดินทาง/ภารกิจ -->
                            <div class="card border-0 shadow-none mb-3 section-box">
                                <div class="card-body p-3 p-md-4">
                                    <div class="d-flex align-items-center mb-3">
                                        <span class="badge-icon-orange me-2"><i class="bx bx-map-pin fs-5"></i></span>
                                        <h6 class="fw-bold text-dark mb-0">2. รายละเอียดการเดินทางและภารกิจ</h6>
                                    </div>
                                    <div class="row g-3">
                                        <div class="col-md-7">
                                            <label class="form-label fw-semibold required-star">รายการ / วัตถุประสงค์ไปราชการ</label>
                                            <input type="text" class="form-control" name="description" id="travel_description" required placeholder="เช่น เดินทางไปราชการอบรมเชิงปฏิบัติการ หรือ นำนักเรียนเข้าร่วมแข่งขัน">
                                        </div>
                                        <div class="col-md-5">
                                            <label class="form-label fw-semibold">สถานที่ไปราชการ</label>
                                            <div class="input-group">
                                                <span class="input-group-text bg-light border-end-0"><i class="bx bx-navigation text-secondary"></i></span>
                                                <input type="text" class="form-control border-start-0" name="destination" id="travel_destination" placeholder="เช่น โรงแรม..., จังหวัดพิษณุโลก">
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <label class="form-label fw-semibold">วันที่เริ่มเดินทาง (พ.ศ.)</label>
                                            <div class="input-group">
                                                <span class="input-group-text bg-light border-end-0"><i class="bx bx-calendar-event text-warning" style="color: #FB8C00 !important;"></i></span>
                                                <input type="text" class="form-control flatpickr-thai-buddhist border-start-0" name="travel_start_date" id="travel_start_date" placeholder="เลือกวันเริ่มเดินทาง">
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <label class="form-label fw-semibold">วันที่สิ้นสุดการเดินทาง (พ.ศ.)</label>
                                            <div class="input-group">
                                                <span class="input-group-text bg-light border-end-0"><i class="bx bx-calendar-event text-warning" style="color: #FB8C00 !important;"></i></span>
                                                <input type="text" class="form-control flatpickr-thai-buddhist border-start-0" name="travel_end_date" id="travel_end_date" placeholder="เลือกวันสิ้นสุด">
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <label class="form-label fw-semibold">จำนวนวันเดินทาง (วัน)</label>
                                            <div class="input-group">
                                                <input type="number" class="form-control text-center font-monospace" name="travel_days" id="travel_days" value="1" min="1">
                                                <span class="input-group-text">วัน</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Section 3: ผู้ขอเบิกและคณะผู้เดินทาง -->
                            <div class="card border-0 shadow-none mb-3 section-box">
                                <div class="card-body p-3 p-md-4">
                                    <div class="d-flex align-items-center mb-3">
                                        <span class="badge-icon-orange me-2"><i class="bx bx-group fs-5"></i></span>
                                        <h6 class="fw-bold text-dark mb-0">3. ผู้ขอเบิกและคณะผู้เดินทาง</h6>
                                    </div>
                                    <div class="row g-3">
                                        <div class="col-md-6">
                                            <label class="form-label fw-semibold required-star">ชื่อผู้ขอเบิกเงิน</label>
                                            <select class="form-select select2-modal" name="requester_name" id="select_travel_requester" required style="width: 100%;">
                                                <option value="">-- ค้นหา / เลือกผู้ขอ หรือพิมพ์ชื่อใหม่ --</option>
                                                <?php foreach ($personnel as $p) : ?>
                                                    <?php $pName = trim(($p->pers_prefix ?? '') . ($p->pers_firstname ?? '') . ' ' . ($p->pers_lastname ?? '')); ?>
                                                    <option value="<?= esc($pName); ?>"><?= esc($pName); ?> (<?= esc($p->pers_position ?? '-'); ?>)</option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label fw-semibold">จำนวนผู้เดินทาง (คน)</label>
                                            <div class="input-group">
                                                <input type="number" class="form-control text-center font-monospace" name="traveler_count" id="traveler_count" value="1" min="1">
                                                <span class="input-group-text">คน</span>
                                            </div>
                                        </div>
                                        <div class="col-12">
                                            <label class="form-label fw-semibold">รายชื่อผู้ร่วมเดินทางทั้งหมด</label>
                                            <input type="text" class="form-control" name="traveler_names" id="traveler_names" placeholder="เช่น นายสมชาย, นางสมหญิง, นายประสิทธิ์ หรือ คณะครู 5 คน">
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Section 4: แจกแจงรายการค่าใช้จ่ายและการเงิน -->
                            <div class="card border-0 shadow-none section-box">
                                <div class="card-body p-3 p-md-4">
                                    <div class="d-flex align-items-center mb-3">
                                        <span class="badge-icon-orange me-2"><i class="bx bx-money fs-5"></i></span>
                                        <h6 class="fw-bold text-dark mb-0">4. รายละเอียดค่าใช้จ่ายและยอดเงินเบิกจ่าย</h6>
                                    </div>
                                    <div class="row g-3">
                                        <div class="col-md-3">
                                            <label class="form-label fw-semibold">ค่าเบี้ยเลี้ยง (บาท)</label>
                                            <div class="input-group">
                                                <input type="text" class="form-control text-end font-monospace input-money" name="allowance_amount" id="travel_allowance_amount" placeholder="0.00">
                                                <span class="input-group-text">บาท</span>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <label class="form-label fw-semibold">ค่าเช่าที่พัก (บาท)</label>
                                            <div class="input-group">
                                                <input type="text" class="form-control text-end font-monospace input-money" name="accommodation_amount" id="travel_accommodation_amount" placeholder="0.00">
                                                <span class="input-group-text">บาท</span>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <label class="form-label fw-semibold">ค่าพาหนะ/เดินทาง (บาท)</label>
                                            <div class="input-group">
                                                <input type="text" class="form-control text-end font-monospace input-money" name="transport_amount" id="travel_transport_amount" placeholder="0.00">
                                                <span class="input-group-text">บาท</span>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <label class="form-label fw-semibold">ค่าใช้จ่ายอื่นๆ (บาท)</label>
                                            <div class="input-group">
                                                <input type="text" class="form-control text-end font-monospace input-money" name="other_expenses_amount" id="travel_other_expenses_amount" placeholder="0.00">
                                                <span class="input-group-text">บาท</span>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label fw-bold text-orange required-star" style="color: #FB8C00 !important;">ยอดเงินเบิกจ่ายรวมทั้งหมด (บาท)</label>
                                            <div class="input-group">
                                                <input type="text" class="form-control text-end font-monospace fw-bold fs-5 input-money" style="color: #FB8C00 !important; border-color: #FB8C00 !important;" name="amount_withdraw" id="travel_amount_withdraw" required placeholder="0.00">
                                                <span class="input-group-text text-white fw-bold" style="background-color: #FB8C00 !important;">บาท</span>
                                            </div>
                                            <small class="text-muted fs-8">คำนวณอัตโนมัติจากผลรวมของเบี้ยเลี้ยง ที่พัก พาหนะ และอื่นๆ</small>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label fw-semibold">หมายเหตุเพิ่มเติม</label>
                                            <input type="text" class="form-control" name="notes" id="travel_notes" placeholder="เช่น เงินทดรองจ่าย, ใช้รถส่วนบุคคล">
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>

                        <!-- Modal Footer -->
                        <div class="modal-footer px-4 py-3 bg-white border-top flex-shrink-0">
                            <button type="button" class="btn btn-outline-secondary rounded-pill px-4" data-bs-dismiss="modal">
                                <i class="bx bx-x me-1"></i> ยกเลิก
                            </button>
                            <button type="submit" class="btn btn-primary rounded-pill px-4 shadow-sm btn-orange-main" id="btnSubmitTravel">
                                <span class="spinner-border spinner-border-sm me-1 d-none" role="status"></span>
                                <i class="bx bx-save me-1 btn-icon"></i> <span class="btn-text">บันทึกข้อมูล</span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Modal 2: ตั้งงบประมาณ / ปรับยอดงบประมาณ (Orange Theme) -->
            <div class="modal fade" id="modalAddBudget" tabindex="-1" aria-labelledby="modalBudgetTitle" aria-hidden="true" data-bs-backdrop="static">
                <div class="modal-dialog modal-dialog-centered">
                    <form class="modal-content border-0 shadow-lg modal-orange-content" id="formSaveBudgetAdjust">
                        <input type="hidden" name="budget_year" id="budget_adjust_travel_year" value="<?= esc($selected_year); ?>">
                        <input type="hidden" name="entry_type" value="adjust">

                        <div class="modal-header text-white px-4 py-3 modal-header-orange flex-shrink-0">
                            <div class="d-flex align-items-center gap-3">
                                <div class="avatar avatar-md bg-white rounded-circle d-flex align-items-center justify-content-center shadow-sm" style="color: #FB8C00 !important;">
                                    <i class="bx bx-wallet fs-3"></i>
                                </div>
                                <div>
                                    <h5 class="modal-title fw-bold text-white mb-0" id="modalBudgetTitle">
                                        ตั้งงบประมาณ / ปรับเพิ่ม-ลดงบประมาณ
                                    </h5>
                                    <small class="text-white-50 fs-8" id="budgetAdjustTravelYearDisplay">ปีงบประมาณ พ.ศ. <?= esc($selected_year); ?></small>
                                </div>
                            </div>
                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>

                        <div class="modal-body p-4 modal-body-custom-scroll" style="background-color: #faf9f6; max-height: calc(80vh - 120px); overflow-y: auto;">
                            <div class="card border-0 shadow-none section-box">
                                <div class="card-body p-4">
                                    <div class="mb-3">
                                        <label class="form-label fw-semibold required-star">วันที่จัดสรรงบประมาณ (พ.ศ.)</label>
                                        <div class="input-group">
                                            <span class="input-group-text bg-light border-end-0"><i class="bx bx-calendar text-warning" style="color: #FB8C00 !important;"></i></span>
                                            <input type="text" class="form-control flatpickr-thai-buddhist border-start-0" name="doc_date" id="budget_adjust_travel_doc_date" required placeholder="เลือกวันที่ (พ.ศ.)">
                                        </div>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label fw-semibold">เลขที่หนังสือ / คำสั่งอนุมัติงบ</label>
                                        <input type="text" class="form-control" name="doc_no" placeholder="เช่น คำสั่งที่ 123/2569 หรือ งบประมาณข้อบัญญัติ">
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label fw-semibold required-star">รายการจัดสรร</label>
                                        <input type="text" class="form-control" name="description" id="budget_adjust_travel_description" value="จัดสรรงบประมาณค่าใช้จ่ายเดินทางไปราชการ ประจำปีงบประมาณ พ.ศ. <?= esc($selected_year); ?>" required>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label fw-bold text-orange required-star" style="color: #FB8C00 !important;">จำนวนเงินงบประมาณที่จัดสรร (บาท)</label>
                                        <div class="input-group">
                                            <input type="text" class="form-control text-end font-monospace fw-bold fs-5 input-money" style="color: #FB8C00 !important; border-color: #FB8C00 !important;" name="amount_budget_adjust" required placeholder="0.00">
                                            <span class="input-group-text text-white fw-bold" style="background-color: #FB8C00 !important;">บาท</span>
                                        </div>
                                    </div>
                                    <div class="mb-0">
                                        <label class="form-label fw-semibold">หมายเหตุ</label>
                                        <textarea class="form-control" name="notes" rows="2" placeholder="เช่น เงินงบประมาณข้อบัญญัติ อบจ. หรือเงินรายได้สถานศึกษา"></textarea>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="modal-footer px-4 py-3 bg-white border-top flex-shrink-0">
                            <button type="button" class="btn btn-outline-secondary rounded-pill px-4" data-bs-dismiss="modal">
                                <i class="bx bx-x me-1"></i> ยกเลิก
                            </button>
                            <button type="submit" class="btn btn-primary rounded-pill px-4 shadow-sm btn-orange-main" id="btnSubmitBudget">
                                <span class="spinner-border spinner-border-sm me-1 d-none" role="status"></span>
                                <i class="bx bx-save me-1 btn-icon"></i> <span class="btn-text">บันทึกงบประมาณ</span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>

        </div>
        <!-- / Content -->
    </div>
    <!-- / Content wrapper -->
</div>
<!-- / Layout page -->

<style>
/* ─── Orange Theme & Layout Styling ─── */
.btn-orange-main {
    background: linear-gradient(135deg, #FB8C00 0%, #FFA726 100%) !important;
    border-color: #FB8C00 !important;
    color: #ffffff !important;
    box-shadow: 0 4px 12px rgba(251, 140, 0, 0.25) !important;
}
.btn-orange-main:hover, .btn-orange-main:focus, .btn-orange-main:active {
    background: linear-gradient(135deg, #F57C00 0%, #E65100 100%) !important;
    border-color: #E65100 !important;
    color: #ffffff !important;
    box-shadow: 0 6px 16px rgba(251, 140, 0, 0.35) !important;
}
.modal-header-orange {
    background: linear-gradient(135deg, #FB8C00 0%, #FFA726 100%) !important;
}
.badge-icon-orange {
    background-color: rgba(251, 140, 0, 0.12);
    color: #E65100;
    border-radius: 8px;
    width: 32px;
    height: 32px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
}
.section-box {
    border-radius: 12px !important;
    background: #ffffff !important;
    border: 1px solid #f0e6d6 !important;
}
.required-star::after {
    content: " *";
    color: #e53935;
}
.fs-7 { font-size: 0.85rem !important; }
.fs-8 { font-size: 0.78rem !important; }
.shadow-xs { box-shadow: 0 1px 3px rgba(0,0,0,0.08); }

.modal-body-custom-scroll {
    scrollbar-width: thin;
    scrollbar-color: rgba(251, 140, 0, 0.3) transparent;
}
.modal-body-custom-scroll::-webkit-scrollbar {
    width: 6px;
}
.modal-body-custom-scroll::-webkit-scrollbar-thumb {
    background: rgba(251, 140, 0, 0.3);
    border-radius: 10px;
}
/* ─── Single Screen Table Fit (No Horizontal Scroll) ─── */
.table-fit-screen {
    width: 100% !important;
    table-layout: auto !important;
    white-space: normal !important;
}
.table-fit-screen th, .table-fit-screen td {
    white-space: normal !important;
    word-wrap: break-word !important;
    word-break: break-word !important;
    padding: 0.55rem 0.45rem !important;
    vertical-align: middle !important;
}
.table-fit-screen th {
    font-size: 0.82rem !important;
    font-weight: 700 !important;
    letter-spacing: 0.2px;
}
.table-fit-screen td {
    font-size: 0.83rem !important;
}

/* Select2 in Modal */
.select2-container--default .select2-selection--single {
    height: 40px !important;
    border-radius: 8px !important;
    border: 1px solid #d9dee3 !important;
    display: flex !important;
    align-items: center !important;
}
.select2-container--default .select2-selection--single .select2-selection__arrow {
    height: 38px !important;
}
.select2-container--default .select2-selection--single .select2-selection__rendered {
    color: #4a5568 !important;
    line-height: 38px !important;
    padding-left: 8px !important;
}
.select2-dropdown {
    border-radius: 12px !important;
    border: 1px solid rgba(251, 140, 0, 0.2) !important;
    box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15) !important;
    z-index: 99999 !important;
}
.select2-results__option--highlighted[aria-selected] {
    background-color: #FB8C00 !important;
    color: #fff !important;
}
.flatpickr-calendar {
    z-index: 999999 !important;
    border-radius: 16px !important;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.15) !important;
    border: 1px solid rgba(251, 140, 0, 0.2) !important;
}
</style>

<script>
    window.appBaseUrl = "<?= base_url(); ?>/";
    window.selectedBudgetYear = "<?= esc($selected_year); ?>";
</script>
<script src="<?= base_url('assets/js/Admin/AdminBudgetOrdinance/Travel.js'); ?>"></script>
