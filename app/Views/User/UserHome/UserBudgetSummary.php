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

            <!-- Projects Table Card -->
            <div class="row animate-fade-in-up" style="animation-delay: 0.05s;">
                <div class="col-12">
                    <div class="card border-0 shadow-sm rounded-4">
                        <div class="card-header bg-white border-0 pt-4 px-4 pb-2 d-flex align-items-center justify-content-between flex-wrap gap-2">
                            <div>
                                <h5 class="fw-bold mb-1 text-dark">
                                    <i class="bx bx-list-ul text-primary me-2"></i>รายชื่อโครงการและรายละเอียดงบประมาณ
                                </h5>
                                <p class="text-muted fs-7 mb-0">ตรวจสอบข้อมูลโครงการ ยอดงบประมาณจัดสรร การเบิกจ่าย และยอดคงเหลือ</p>
                            </div>
                            <span id="recordCountBadge" class="badge bg-label-primary rounded-pill px-3 py-2 fs-8 fw-bold">
                                ทั้งหมด <?= count($projects ?? []) ?> โครงการ
                            </span>
                        </div>
                        <div class="card-body px-4 pb-4">
                            <div class="table-responsive">
                                <table id="userProjectsTable" class="table table-hover align-middle w-100">
                                    <thead class="table-light">
                                        <tr class="text-muted fs-7">
                                            <th style="width: 100px;">รหัสโครงการ</th>
                                            <th>ชื่อโครงการ / กิจกรรม</th>
                                            <th>ยุทธศาสตร์ & กลยุทธ์</th>
                                            <th class="text-end" style="width: 140px;">งบจัดสรร (บาท)</th>
                                            <th class="text-end" style="width: 140px;">เบิกจ่ายแล้ว (บาท)</th>
                                            <th class="text-end" style="width: 140px;">คงเหลือสุทธิ (บาท)</th>
                                            <th class="text-center" style="width: 110px;">ความก้าวหน้า</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if (!empty($projects)): ?>
                                            <?php foreach ($projects as $p): ?>
                                                <tr data-strategy="<?= esc($p->strategy_name ?? '') ?>" data-has-balance="<?= ($p->current_balance ?? 0) > 0 ? '1' : '0' ?>">
                                                    <td data-order="<?= natural_code_sort_key($p->project_code) ?>">
                                                        <span class="badge bg-label-primary font-monospace fs-8">
                                                            <?= esc($p->project_code ?: '-') ?>
                                                        </span>
                                                    </td>
                                                    <td>
                                                        <div class="fw-bold text-dark fs-7 mb-1"><?= esc($p->project_name ?? '-') ?></div>
                                                        <?php if (!empty($p->budget_source ?? null)): ?>
                                                            <div class="text-muted fs-8">
                                                                <i class="bx bx-wallet me-1 text-warning"></i><b>แหล่งเงิน:</b> <?= esc($p->budget_source) ?>
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
                                                <td colspan="7" class="text-center py-5 text-muted">
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

<style>
    .filter-box-card {
        background: #ffffff;
        border: 1px solid rgba(0, 0, 0, 0.05) !important;
    }
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    var $ = window.jQuery;
    if (!$) return;
    var dtTable = null;

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
            var info = dtTable.page.info();
            $('#recordCountBadge').text('ทั้งหมด ' + info.recordsDisplay + ' โครงการ');
        });

        // Custom Search Input Filter
        $('#filterKeyword').on('keyup', function() {
            dtTable.search(this.value).draw();
        });

        // Custom Strategy Filter
        $('#filterStrategy').on('change', function() {
            var val = $(this).val();
            dtTable.column(2).search(val ? val : '', true, false).draw();
        });

        // Custom Balance Status Filter
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

        $('#filterBalanceStatus').on('change', function() {
            dtTable.draw();
        });

        // Reset Filter button
        $('#btnResetFilters').on('click', function() {
            $('#filterKeyword').val('');
            $('#filterStrategy').val('');
            $('#filterBalanceStatus').val('');
            dtTable.search('').columns().search('').draw();
        });
    }
});
</script>
