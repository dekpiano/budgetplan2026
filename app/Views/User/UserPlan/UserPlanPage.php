<!-- Layout container -->
<div class="layout-page">
    <?php echo view('User/UserLeyout/UserNavbar'); ?>

    <div class="content-wrapper">
        <div class="container-xxl flex-grow-1 container-p-y">
            
            <!-- Hero / Header Section (High Contrast Dark Luxury) -->
            <div class="row mb-4 mt-2 animate-fade-in-up">
                <div class="col-12">
                    <div class="card border-0 rounded-4 overflow-hidden shadow-sm" style="background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%); color: #ffffff; position: relative;">
                        <!-- Background Glow Effect -->
                        <div style="position: absolute; right: -50px; top: -50px; width: 300px; height: 300px; background: radial-gradient(circle, rgba(245, 158, 11, 0.2) 0%, rgba(0,0,0,0) 70%); filter: blur(40px); pointer-events: none;"></div>

                        <div class="card-body p-4 p-md-5 position-relative z-index-1">
                            <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
                                <div>
                                    <div class="d-flex align-items-center gap-2 mb-2">
                                        <span class="badge rounded-pill px-3 py-1.5 fw-bold" style="background: #e65100; color: #ffffff; font-size: 0.82rem; letter-spacing: 0.5px;">
                                            <i class="bx bx-search-alt me-1"></i>STRATEGIC SEARCH & EXPLORATION
                                        </span>
                                        <span class="badge rounded-pill px-3 py-1.5 fw-bold bg-dark text-white border border-secondary fs-8">
                                            ปีงบประมาณ พ.ศ. <?= $selected_year ?>
                                        </span>
                                    </div>
                                    <h2 class="fw-bolder mb-2 text-white" style="font-family: 'Plus Jakarta Sans', 'K2D', sans-serif;">
                                        ค้นหาแผนยุทธศาสตร์และโครงการ
                                    </h2>
                                    <p class="text-light mb-0 fs-6 fw-normal" style="max-width: 650px; line-height: 1.6; opacity: 0.9;">
                                        ค้นหาข้อมูลยุทธศาสตร์ กลยุทธ์ วัตถุประสงค์ งานที่ทำ และโครงการย่อย เพื่อเปิดดูรายละเอียดในหน้าต่าง Modal ได้ทันที
                                    </p>
                                </div>

                                <!-- Year Selector & Quick Actions -->
                                <div class="d-flex align-items-center gap-2 flex-wrap">
                                    <form method="get" action="<?= base_url('User/Plan') ?>" class="d-inline-flex align-items-center">
                                        <div class="input-group input-group-sm">
                                            <span class="input-group-text bg-dark border-secondary text-warning fw-bold"><i class="bx bx-calendar"></i></span>
                                            <select class="form-select border-secondary bg-white text-dark fw-bold rounded-pill-end shadow-sm" name="year" onchange="this.form.submit()" style="font-size: 0.88rem;">
                                                <?php foreach ($years as $y): ?>
                                                    <option value="<?= $y->budget_year ?>" <?= $y->budget_year == $selected_year ? 'selected' : '' ?>>
                                                        ปีงบประมาณ พ.ศ. <?= $y->budget_year ?>
                                                    </option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>
                                    </form>

                                    <a href="<?= base_url('User/BudgetSummary?year=' . $selected_year) ?>" class="btn btn-warning btn-sm rounded-pill px-3 text-dark fw-bolder shadow-sm">
                                        <i class="bx bx-spreadsheet me-1"></i> ดูทะเบียนคุมงบประมาณ
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Big Search & Filter Box (Primary Action) -->
            <div class="row mb-4 animate-fade-in-up">
                <div class="col-12">
                    <div class="card border-0 shadow-sm rounded-4" style="background: #ffffff; border: 2px solid #e65100 !important; box-shadow: 0 10px 30px rgba(230, 81, 0, 0.08) !important;">
                        <div class="card-body p-4 p-md-5">
                            <div class="text-center mb-4">
                                <span class="badge rounded-pill px-3 py-1.5 fs-7 fw-bold mb-2" style="background-color: #fff3e0; color: #b71c1c; border: 1px solid #ffcc80;">
                                    <i class="bx bx-search-alt me-1"></i>ระบบสืบค้นข้อมูลแผนงาน
                                </span>
                                <h3 class="fw-bolder text-dark mb-1" style="font-family: 'Plus Jakarta Sans', 'K2D', sans-serif;">
                                    ค้นหายุทธศาสตร์ กลยุทธ์ และโครงการ
                                </h3>
                                <p class="text-secondary fs-7 mb-0 fw-medium">พิมพ์คำค้นหา หรือเลือกยุทธศาสตร์เพื่อแสดงผลลัพธ์</p>
                            </div>

                            <form id="searchForm" onsubmit="event.preventDefault(); triggerSearch();">
                                <div class="row g-3 justify-content-center align-items-center">
                                    <div class="col-lg-6 col-md-7">
                                        <div class="input-group input-group-lg shadow-sm rounded-pill overflow-hidden" style="border: 2px solid #cbd5e1;">
                                            <span class="input-group-text bg-white border-0 ps-3 text-primary"><i class="bx bx-search fs-4"></i></span>
                                            <input type="text" id="searchStrategicKeyword" class="form-control border-0 bg-white text-dark fw-medium" placeholder="พิมพ์ชื่อยุทธศาสตร์, กลยุทธ์, โครงการ, วัตถุประสงค์ หรือหน่วยงาน..." autocomplete="off">
                                        </div>
                                    </div>
                                    <div class="col-lg-3 col-md-5">
                                        <div class="input-group input-group-lg shadow-sm rounded-pill overflow-hidden" style="border: 2px solid #cbd5e1;">
                                            <span class="input-group-text bg-white border-0 ps-3 text-info"><i class="bx bx-filter-alt fs-5"></i></span>
                                            <select id="filterByStrategySelect" class="form-select border-0 bg-white text-dark fw-bold" style="font-size: 0.95rem;">
                                                <option value="">-- ทุกยุทธศาสตร์ --</option>
                                                <?php if (!empty($strategies)): ?>
                                                    <?php foreach ($strategies as $st): ?>
                                                        <option value="<?= $st->id ?>">ยุทธศาสตร์ที่ <?= esc($st->strategy_no) ?></option>
                                                    <?php endforeach; ?>
                                                <?php endif; ?>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-lg-2 col-md-12 d-flex gap-2">
                                        <button type="button" class="btn btn-warning btn-lg rounded-pill px-4 fw-bolder text-dark flex-grow-1 shadow-sm" style="background-color: #ff9800; border-color: #f57c00;" onclick="triggerSearch()">
                                            <i class="bx bx-search me-1"></i> ค้นหา
                                        </button>
                                        <button type="button" id="btnResetSearch" class="btn btn-outline-secondary btn-lg rounded-circle p-2 text-dark" title="ล้างค่าการค้นหา">
                                            <i class="bx bx-reset fs-4"></i>
                                        </button>
                                    </div>
                                </div>
                            </form>

                            <!-- Quick Suggestion Tags -->
                            <div class="d-flex justify-content-center align-items-center gap-2 flex-wrap mt-3 text-dark fs-8">
                                <span class="fw-bold text-muted"><i class="bx bx-bulb text-warning me-1"></i>คำค้นหายอดนิยม:</span>
                                <button type="button" class="btn btn-xs btn-outline-dark rounded-pill fw-semibold quick-tag" onclick="setSearchKeyword('พัฒนาผู้เรียน')">พัฒนาผู้เรียน</button>
                                <button type="button" class="btn btn-xs btn-outline-dark rounded-pill fw-semibold quick-tag" onclick="setSearchKeyword('วิชาการ')">วิชาการ</button>
                                <button type="button" class="btn btn-xs btn-outline-dark rounded-pill fw-semibold quick-tag" onclick="setSearchKeyword('กิจกรรม')">กิจกรรม</button>
                                <button type="button" class="btn btn-xs btn-primary rounded-pill fw-bold quick-tag" onclick="showAllResults()"><i class="bx bx-grid-alt me-1"></i>แสดงทั้งหมด</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Initial Empty State (Show when not searched yet) -->
            <div id="initialSearchPrompt" class="row mb-5 animate-fade-in-up">
                <div class="col-12">
                    <div class="card border-0 shadow-sm rounded-4 p-5 text-center bg-white" style="border: 2px dashed #94a3b8 !important;">
                        <div class="mb-3">
                            <div class="avatar bg-warning-soft rounded-circle p-4 text-warning mx-auto d-flex align-items-center justify-content-center" style="width: 80px; height: 80px;">
                                <i class="bx bx-search-alt display-4" style="color: #e65100;"></i>
                            </div>
                        </div>
                        <h4 class="fw-bolder text-dark mb-1" style="font-family: 'K2D', sans-serif;">กรุณาพิมพ์คำค้นหา หรือเลือกยุทธศาสตร์ด้านบน</h4>
                        <p class="text-secondary small mb-3 fw-medium" style="max-width: 500px; margin: 0 auto; font-size: 0.95rem;">
                            ระบบจะแสดงรายการกลยุทธ์และโครงการที่ตรงกับคำค้นหาเพื่อให้คุณคลิกเปิดดูรายละเอียดในหน้าต่าง Modal
                        </p>
                        <div>
                            <button type="button" class="btn btn-primary btn-sm rounded-pill px-4 fw-bold shadow-sm" onclick="showAllResults()">
                                <i class="bx bx-grid-alt me-1"></i> หรือคลิกเพื่อเปิดดูทั้งหมด (<?= $total_strategies ?> ยุทธศาสตร์)
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Search Results Container (Hidden initially until user searches) -->
            <div id="searchResultsSection" class="row mb-5 d-none">
                <div class="col-12 mb-3 d-flex justify-content-between align-items-center flex-wrap gap-2">
                    <div>
                        <h4 class="fw-bolder mb-0 text-dark" style="font-family: 'Plus Jakarta Sans', 'K2D', sans-serif;">
                            <i class="bx bx-list-check text-primary me-2"></i>ผลการค้นหาข้อมูลแผนงาน
                        </h4>
                        <small class="text-secondary fw-semibold">คลิกที่กลยุทธ์เพื่อเปิดดูรายละเอียดฉบับเต็มในหน้าต่าง Modal</small>
                    </div>
                    <span class="badge bg-primary text-white rounded-pill px-3 py-2 fs-7 fw-bolder shadow-sm" id="matchCountBadge">
                        พบ 0 รายการ
                    </span>
                </div>

                <!-- No Results Found Card -->
                <div id="noResultsCard" class="col-12 d-none">
                    <div class="card border-0 shadow-sm rounded-4 p-5 text-center bg-white" style="border: 1px solid #e2e8f0;">
                        <i class="bx bx-search text-secondary opacity-50 display-3 mb-3"></i>
                        <h4 class="fw-bolder text-dark mb-1">ไม่พบข้อมูลที่ตรงกับคำค้นหา</h4>
                        <p class="text-secondary small mb-3 fw-medium">ลองเปลี่ยนคำค้นหา หรือตรวจสอบตัวสะกดใหม่อีกครั้ง</p>
                        <div>
                            <button type="button" class="btn btn-outline-dark btn-sm rounded-pill px-4 fw-bold" onclick="showAllResults()">
                                แสดงข้อมูลทั้งหมด
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Results List by Strategy -->
                <div id="resultsList" class="col-12">
                    <?php if (!empty($strategies)): ?>
                        <?php foreach ($strategies as $st): ?>
                            <div class="col-12 mb-4 strategy-item-wrapper" data-strategy-id="<?= $st->id ?>">
                                <div class="card border-0 shadow-sm rounded-4 overflow-hidden" style="border: 2px solid #cbd5e1 !important;">
                                    <!-- Strategy Header Banner -->
                                    <div class="card-header p-3 p-md-4 bg-white border-bottom d-flex justify-content-between align-items-center flex-wrap gap-2" style="border-bottom: 2px solid #e2e8f0 !important;">
                                        <div class="d-flex align-items-center gap-3">
                                            <span class="badge rounded-pill px-3 py-2 fs-6 fw-bolder shadow-sm text-white" style="background: #0284c7;">
                                                ยุทธศาสตร์ที่ <?= esc($st->strategy_no) ?>
                                            </span>
                                            <div>
                                                <h5 class="fw-bolder mb-0 text-dark strategy-title" style="font-family: 'K2D', sans-serif;">
                                                    <?= esc($st->strategy_name) ?>
                                                </h5>
                                                <div class="text-secondary fs-8 mt-0.5 fw-semibold">
                                                    <span><i class="bx bx-git-branch text-primary me-1"></i><?= count($st->tactics) ?> กลยุทธ์</span> &bull; 
                                                    <span><i class="bx bx-folder text-warning me-1"></i><?= $st->project_count ?> โครงการ</span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="text-end">
                                            <span class="text-secondary fs-8 d-block fw-bold">งบประมาณรวมยุทธศาสตร์</span>
                                            <h5 class="fw-bolder mb-0 text-success" style="color: #15803d !important;"><?= number_format($st->total_budget, 2) ?> <span class="fs-8 text-secondary">บาท</span></h5>
                                        </div>
                                    </div>

                                    <!-- Tactics Grid -->
                                    <div class="card-body p-3 p-md-4" style="background: #f1f5f9;">
                                        <?php if (empty($st->tactics)): ?>
                                            <div class="text-center py-3 text-secondary small fw-bold">
                                                <i class="bx bx-info-circle me-1"></i>ยังไม่มีกลยุทธ์ย่อยภายใต้ยุทธศาสตร์นี้
                                            </div>
                                        <?php else: ?>
                                            <div class="row g-3">
                                                <?php foreach ($st->tactics as $tc): ?>
                                                    <?php
                                                        // Collect all search keywords inside this tactic and its projects
                                                        $searchKeywords = $st->strategy_name . ' ' . $tc->tactic_no . ' ' . $tc->tactic_name . ' ' . $tc->objective . ' ' . $tc->actions . ' ' . $tc->responsible_agency;
                                                        foreach ($tc->projects as $p) {
                                                            $searchKeywords .= ' ' . $p->project_code . ' ' . $p->project_name . ' ' . $p->budget_source;
                                                            if (!empty($p->sub_items)) {
                                                                foreach ($p->sub_items as $sub) {
                                                                    $searchKeywords .= ' ' . $sub->item_no . ' ' . $sub->item_name;
                                                                }
                                                            }
                                                        }
                                                    ?>
                                                    <div class="col-lg-6 col-12 tactic-card-col" 
                                                         data-search-text="<?= esc(strtolower($searchKeywords)) ?>">
                                                        <div class="tactic-interactive-card bg-white rounded-3 p-3 p-md-4 h-100 d-flex flex-column justify-content-between cursor-pointer"
                                                             style="border: 2px solid #e2e8f0; box-shadow: 0 2px 6px rgba(0,0,0,0.04);"
                                                             onclick='openStrategicDetailModal(<?= htmlspecialchars(json_encode($tc), ENT_QUOTES, "UTF-8") ?>, "ยุทธศาสตร์ที่ <?= esc($st->strategy_no) ?>: <?= esc($st->strategy_name) ?>")'>
                                                            
                                                            <div>
                                                                <div class="d-flex justify-content-between align-items-start mb-2 gap-2">
                                                                    <span class="badge rounded-pill px-2.5 py-1 fw-bold fs-7" style="background-color: #e0f2fe; color: #0369a1; border: 1px solid #bae6fd;">
                                                                        กลยุทธ์ <?= esc($tc->tactic_no) ?>
                                                                    </span>
                                                                    <span class="badge rounded-pill px-2.5 py-1 fw-bolder fs-8" style="background-color: #dcfce7; color: #15803d; border: 1px solid #bbf7d0;">
                                                                        รวม <?= number_format($tc->total_budget, 2) ?> ฿
                                                                    </span>
                                                                </div>

                                                                <h5 class="fw-bolder text-dark mb-2 tactic-heading" style="font-family: 'K2D', sans-serif; line-height: 1.4; font-size: 1.05rem;">
                                                                    <?= esc($tc->tactic_name) ?>
                                                                </h5>

                                                                <?php if (!empty($tc->responsible_agency)): ?>
                                                                    <div class="mb-2">
                                                                        <span class="badge rounded-pill px-2.5 py-1 fs-8 fw-semibold" style="background-color: #f1f5f9; color: #334155; border: 1px solid #cbd5e1;">
                                                                            <i class="bx bx-buildings me-1 text-primary"></i><?= esc($tc->responsible_agency) ?>
                                                                        </span>
                                                                    </div>
                                                                <?php endif; ?>

                                                                <?php if (!empty($tc->objective)): ?>
                                                                    <p class="text-secondary fs-8 mb-2 text-truncate-2 fw-medium" style="color: #475569 !important;">
                                                                        <b class="text-dark"><i class="bx bx-target-lock text-primary me-1"></i>วัตถุประสงค์:</b> <?= esc($tc->objective) ?>
                                                                    </p>
                                                                <?php endif; ?>
                                                            </div>

                                                            <div class="pt-3 border-top d-flex align-items-center justify-content-between mt-auto fs-8" style="border-top: 1px solid #e2e8f0 !important;">
                                                                <span class="text-dark fw-bold"><i class="bx bx-folder me-1 text-warning"></i>มี <b><?= count($tc->projects) ?></b> โครงการ</span>
                                                                <span class="text-primary fw-bolder view-detail-link" style="color: #d97706 !important;">
                                                                    เปิดดูรายละเอียด <i class="bx bx-right-arrow-alt align-middle ms-1"></i>
                                                                </span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                <?php endforeach; ?>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Documents Section (PDF Download) -->
            <?php if (!empty($plan_files)): ?>
            <div class="row mb-5">
                <div class="col-12 mb-3">
                    <h5 class="fw-bolder text-dark mb-1" style="font-family: 'Plus Jakarta Sans', 'K2D', sans-serif;">
                        <i class="bx bxs-file-pdf text-danger me-2"></i>เอกสารแผนปฏิบัติการประจำปี (ดาวน์โหลด PDF)
                    </h5>
                    <p class="text-secondary fs-7 mb-0 fw-medium">ดาวน์โหลดเอกสารเล่มแผนปฏิบัติการและคู่มือการดำเนินงานฉบับสมบูรณ์</p>
                </div>
                <div class="col-12">
                    <div class="row g-3">
                        <?php foreach ($plan_files as $file): ?>
                            <div class="col-md-6 col-lg-4">
                                <a href="<?= base_url('uploads/plan/' . $file); ?>" target="_blank" class="text-decoration-none">
                                    <div class="card border-0 shadow-sm p-3 rounded-3 h-100 bg-white doc-hover-card" style="border: 2px solid #cbd5e1 !important;">
                                        <div class="d-flex align-items-center gap-3">
                                            <div class="avatar bg-danger text-white rounded-3 p-2 d-flex align-items-center justify-content-center" style="width: 44px; height: 44px;">
                                                <i class="bx bxs-file-pdf fs-3"></i>
                                            </div>
                                            <div class="flex-grow-1 overflow-hidden">
                                                <h6 class="fw-bolder text-dark mb-0 text-truncate" style="font-size: 0.95rem; font-family: 'K2D', sans-serif;"><?= esc($file); ?></h6>
                                                <span class="text-secondary fs-8 fw-semibold">คลิกเพื่อเปิดอ่านเอกสาร</span>
                                            </div>
                                            <i class="bx bx-chevron-right text-dark fs-4"></i>
                                        </div>
                                    </div>
                                </a>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
            <?php endif; ?>

        </div>
    </div>
</div>

<!-- ==================== MODAL: Strategic Detail View (High Contrast) ==================== -->
<div class="modal fade" id="modalStrategicDetail" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 20px; overflow: hidden; border: 2px solid #cbd5e1;">
            <div class="modal-header border-bottom py-3 px-4" style="background: #0f172a; color: #ffffff;">
                <div>
                    <span class="badge rounded-pill px-3 py-1 fs-8 fw-bold mb-1" id="modalStratNameHeader" style="background: #0284c7; color: #ffffff;">-</span>
                    <h4 class="modal-title fw-bolder text-white mb-0" id="modalTacticTitle" style="font-family: 'K2D', sans-serif;">รายละเอียดกลยุทธ์</h4>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4" style="background: #f8fafc;">
                <!-- Info Badges Bar -->
                <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-4 p-3 rounded-3 bg-white" style="border: 2px solid #e2e8f0;">
                    <div>
                        <span class="text-muted fs-8 d-block fw-bold">หน่วยงานที่รับผิดชอบ:</span>
                        <h6 class="text-dark fw-bolder mb-0 fs-6" id="modalResponsibleAgency">-</h6>
                    </div>
                    <div class="text-end">
                        <span class="text-muted fs-8 d-block fw-bold">รวมงบประมาณในกลยุทธ์นี้:</span>
                        <h4 class="fw-bolder mb-0 text-success" id="modalTacticTotalBudget" style="color: #15803d !important;">0.00 บาท</h4>
                    </div>
                </div>

                <!-- Objective & Actions -->
                <div class="row g-3 mb-4">
                    <div class="col-12" id="modalObjectiveContainer">
                        <div class="p-3 rounded-3 bg-white" style="border-left: 5px solid #0284c7; border-top: 1px solid #e2e8f0; border-right: 1px solid #e2e8f0; border-bottom: 1px solid #e2e8f0;">
                            <h6 class="fw-bolder text-primary mb-2" style="color: #0369a1 !important;"><i class="bx bx-target-lock me-1"></i>วัตถุประสงค์</h6>
                            <div class="text-dark fs-7 fw-medium" id="modalObjectiveText" style="white-space: pre-line; line-height: 1.7; color: #1e293b !important;">-</div>
                        </div>
                    </div>
                    <div class="col-12" id="modalActionsContainer">
                        <div class="p-3 rounded-3 bg-white" style="border-left: 5px solid #16a34a; border-top: 1px solid #e2e8f0; border-right: 1px solid #e2e8f0; border-bottom: 1px solid #e2e8f0;">
                            <h6 class="fw-bolder text-success mb-2" style="color: #15803d !important;"><i class="bx bx-task me-1"></i>งานที่ทำ / แนวทางการดำเนินงาน</h6>
                            <div class="text-dark fs-7 fw-medium" id="modalActionsText" style="white-space: pre-line; line-height: 1.7; color: #1e293b !important;">-</div>
                        </div>
                    </div>
                </div>

                <!-- Projects List -->
                <div>
                    <h6 class="fw-bolder text-dark mb-3 d-flex align-items-center">
                        <i class="bx bx-folder text-warning me-2 fs-5"></i>โครงการและงบประมาณจัดสรรภายใต้กลยุทธ์นี้
                        <span class="badge rounded-pill ms-2 fs-8 fw-bold" id="modalProjectCountBadge" style="background-color: #ffedd5; color: #c2410c; border: 1px solid #fed7aa;">0 โครงการ</span>
                    </h6>
                    <div class="table-responsive bg-white rounded-3 overflow-hidden" style="border: 2px solid #e2e8f0;">
                        <table class="table table-hover align-middle mb-0" style="font-size: 0.9rem;">
                            <thead style="background-color: #f1f5f9; border-bottom: 2px solid #cbd5e1;">
                                <tr>
                                    <th style="width: 110px;" class="text-center text-dark fw-bolder">รหัส</th>
                                    <th class="text-dark fw-bolder">ชื่อโครงการ / รายการข้อย่อย</th>
                                    <th class="text-dark fw-bolder">แหล่งเงินงบประมาณ</th>
                                    <th class="text-end text-dark fw-bolder" style="width: 160px;">งบประมาณ (บาท)</th>
                                </tr>
                            </thead>
                            <tbody id="modalProjectsTableBody">
                                <!-- Dynamic rows -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="modal-footer border-top py-3 px-4 bg-white" style="border-top: 2px solid #e2e8f0 !important;">
                <button type="button" class="btn btn-dark rounded-pill px-4 fw-bold" data-bs-dismiss="modal">ปิดหน้าต่าง</button>
            </div>
        </div>
    </div>
</div>

<style>
    .tactic-interactive-card {
        transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1);
        cursor: pointer;
    }
    .tactic-interactive-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 12px 25px rgba(0,0,0,0.1) !important;
        border-color: #d97706 !important;
    }
    .tactic-interactive-card:hover .tactic-heading {
        color: #d97706 !important;
    }
    .tactic-interactive-card:hover .view-detail-link {
        color: #b45309 !important;
    }
    .text-truncate-2 {
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }
    .cursor-pointer { cursor: pointer; }
    .doc-hover-card {
        transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1);
    }
    .doc-hover-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 12px 24px rgba(0,0,0,0.08) !important;
        border-color: #d97706 !important;
    }
    .bg-primary-soft { background-color: rgba(2, 132, 199, 0.12); }
    .bg-warning-soft { background-color: rgba(217, 119, 6, 0.12); }
    .bg-success-soft { background-color: rgba(22, 163, 74, 0.12); }
    .bg-danger-soft  { background-color: rgba(220, 38, 38, 0.12); }
    .fs-8 { font-size: 0.8rem; }
    .fs-7 { font-size: 0.9rem; }
</style>

<script>
function formatMoney(num) {
    return parseFloat(num || 0).toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
}

window.openStrategicDetailModal = function(tacticData, strategyName) {
    if (typeof tacticData === 'string') {
        try { tacticData = JSON.parse(tacticData); } catch (e) {}
    }

    $('#modalStratNameHeader').text(strategyName || 'ยุทธศาสตร์');
    $('#modalTacticTitle').text('กลยุทธ์ ' + (tacticData.tactic_no || '') + ': ' + (tacticData.tactic_name || ''));
    $('#modalResponsibleAgency').text(tacticData.responsible_agency || '-');
    $('#modalTacticTotalBudget').text(formatMoney(tacticData.total_budget) + ' บาท');

    // Objective
    if (tacticData.objective && tacticData.objective.trim() !== '') {
        $('#modalObjectiveContainer').removeClass('d-none');
        $('#modalObjectiveText').text(tacticData.objective);
    } else {
        $('#modalObjectiveContainer').addClass('d-none');
    }

    // Actions
    if (tacticData.actions && tacticData.actions.trim() !== '') {
        $('#modalActionsContainer').removeClass('d-none');
        $('#modalActionsText').text(tacticData.actions);
    } else {
        $('#modalActionsContainer').addClass('d-none');
    }

    // Projects
    var projects = tacticData.projects || [];
    $('#modalProjectCountBadge').text(projects.length + ' โครงการ');

    if (projects.length === 0) {
        $('#modalProjectsTableBody').html('<tr><td colspan="4" class="text-center py-4 text-muted fw-bold">ยังไม่มีโครงการภายใต้กลยุทธ์นี้</td></tr>');
    } else {
        var html = '';
        projects.forEach(function(p) {
            var subItemsHtml = '';
            if (p.sub_items && p.sub_items.length > 0) {
                subItemsHtml = '<div class="mt-2 ps-2 border-start border-3 border-warning">';
                p.sub_items.forEach(function(sub) {
                    subItemsHtml += '<div class="small text-dark d-flex justify-content-between gap-3 py-1" style="font-weight: 500;">' +
                        '<span><i class="bx bx-subdirectory-right text-warning me-1"></i><b class="text-dark">' + (sub.item_no || '-') + '</b> ' + sub.item_name + '</span>' +
                        '<span class="fw-bold text-dark">' + formatMoney(sub.allocated_budget) + ' ฿</span>' +
                        '</div>';
                });
                subItemsHtml += '</div>';
            }

            html += '<tr>' +
                '<td class="text-center align-top"><span class="badge bg-dark text-white font-monospace fw-bold">' + (p.project_code || '-') + '</span></td>' +
                '<td><span class="fw-bolder text-dark d-block">' + p.project_name + '</span>' + subItemsHtml + '</td>' +
                '<td class="align-top text-secondary fw-semibold small">' + (p.budget_source || '-') + '</td>' +
                '<td class="text-end fw-bolder text-success align-top" style="color: #15803d !important;">' + formatMoney(p.allocated_budget) + ' ฿</td>' +
                '</tr>';
        });
        $('#modalProjectsTableBody').html(html);
    }

    $('#modalStrategicDetail').modal('show');
};

function triggerSearch() {
    var searchInput = document.getElementById('searchStrategicKeyword');
    var strategyFilter = document.getElementById('filterByStrategySelect');
    var keyword = (searchInput.value || '').trim().toLowerCase();
    var selectedStrategyId = strategyFilter.value;

    if (!keyword && !selectedStrategyId) {
        document.getElementById('initialSearchPrompt').classList.remove('d-none');
        document.getElementById('searchResultsSection').classList.add('d-none');
        return;
    }

    executeFilter(keyword, selectedStrategyId);
}

function showAllResults() {
    document.getElementById('searchStrategicKeyword').value = '';
    document.getElementById('filterByStrategySelect').value = '';
    executeFilter('', '');
}

function setSearchKeyword(kw) {
    document.getElementById('searchStrategicKeyword').value = kw;
    triggerSearch();
}

function executeFilter(keyword, selectedStrategyId) {
    document.getElementById('initialSearchPrompt').classList.add('d-none');
    document.getElementById('searchResultsSection').classList.remove('d-none');

    var wrappers = document.querySelectorAll('.strategy-item-wrapper');
    var totalTacticsMatched = 0;
    var visibleStrategyCount = 0;

    wrappers.forEach(function (w) {
        var stratId = w.getAttribute('data-strategy-id');
        var matchStrategy = (!selectedStrategyId || selectedStrategyId === stratId);

        var tacticCols = w.querySelectorAll('.tactic-card-col');
        var tacticVisibleCount = 0;

        tacticCols.forEach(function (tCol) {
            var searchData = tCol.getAttribute('data-search-text') || '';
            var matchKeyword = (!keyword || searchData.includes(keyword));

            if (matchStrategy && matchKeyword) {
                tCol.style.display = 'block';
                tacticVisibleCount++;
                totalTacticsMatched++;
            } else {
                tCol.style.display = 'none';
            }
        });

        if (matchStrategy && tacticVisibleCount > 0) {
            w.style.display = 'block';
            visibleStrategyCount++;
        } else {
            w.style.display = 'none';
        }
    });

    var noResultsCard = document.getElementById('noResultsCard');
    var resultsList = document.getElementById('resultsList');
    var matchCountBadge = document.getElementById('matchCountBadge');

    if (totalTacticsMatched === 0) {
        noResultsCard.classList.remove('d-none');
        resultsList.classList.add('d-none');
        matchCountBadge.innerText = 'ไม่พบผลลัพธ์';
        matchCountBadge.className = 'badge bg-label-danger rounded-pill px-3 py-2 fs-8 fw-bold';
    } else {
        noResultsCard.classList.add('d-none');
        resultsList.classList.remove('d-none');
        matchCountBadge.innerText = 'พบ ' + totalTacticsMatched + ' กลยุทธ์ (' + visibleStrategyCount + ' ยุทธศาสตร์)';
        matchCountBadge.className = 'badge bg-label-primary rounded-pill px-3 py-2 fs-8 fw-bold';
    }
}

document.addEventListener('DOMContentLoaded', function () {
    var searchInput = document.getElementById('searchStrategicKeyword');
    var strategyFilter = document.getElementById('filterByStrategySelect');
    var btnReset = document.getElementById('btnResetSearch');

    // Live search on typing
    if (searchInput) {
        searchInput.addEventListener('input', function () {
            triggerSearch();
        });
    }

    if (strategyFilter) {
        strategyFilter.addEventListener('change', function () {
            triggerSearch();
        });
    }

    if (btnReset) {
        btnReset.addEventListener('click', function () {
            document.getElementById('searchStrategicKeyword').value = '';
            document.getElementById('filterByStrategySelect').value = '';
            document.getElementById('initialSearchPrompt').classList.remove('d-none');
            document.getElementById('searchResultsSection').classList.add('d-none');
        });
    }
});
</script>
