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
                            <h4 class="fw-bold mb-1" style="font-family: 'Plus Jakarta Sans', 'Sarabun', sans-serif;">
                                <i class="bx bx-sitemap text-primary me-2"></i>ตั้งค่าโครงสร้างยุทธศาสตร์ กลยุทธ์ และโครงการ
                            </h4>
                            <p class="text-muted fs-7 mb-0">กำหนดและเรียงลำดับ: <b>1. ยุทธศาสตร์</b> &rarr; <b>1.1 กลยุทธ์</b> &rarr; <b>1.1.1 โครงการ</b> &rarr; <b>10.1 กิจกรรมหลัก</b> &rarr; <b>10.1.2 กิจกรรมย่อย</b></p>
                        </div>
                        <div class="d-flex gap-2 align-items-center">
                            <!-- Year Filter -->
                            <form method="get" action="<?= base_url('Admin/BudgetLedger/Structure') ?>" class="d-inline-flex align-items-center">
                                <div class="input-group input-group-sm">
                                    <span class="input-group-text bg-white"><i class="bx bx-calendar"></i></span>
                                    <select class="form-select" id="yearFilter" name="year" onchange="this.form.submit()">
                                        <?php foreach ($years as $y): ?>
                                            <option value="<?= $y->budget_year ?>" <?= $y->budget_year == $selected_year ? 'selected' : '' ?>>
                                                ปีงบประมาณ พ.ศ. <?= $y->budget_year ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </form>

                            <a href="<?= base_url('Admin/BudgetLedger') ?>" class="btn btn-outline-secondary btn-sm rounded-pill px-3">
                                <i class="bx bx-arrow-back me-1"></i> ไปหน้าทะเบียนคุม
                            </a>
                            <button type="button" class="btn btn-primary btn-sm rounded-pill px-3 shadow-sm" data-bs-toggle="modal" data-bs-target="#modalAddStrategy">
                                <i class="bx bx-plus me-1"></i> เพิ่มยุทธศาสตร์ (1.)
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Strategic Tree Hierarchy Card -->
            <div class="card border-0 shadow-sm" style="border-radius: 18px;">
                <div class="card-header bg-transparent d-flex justify-content-between align-items-center py-3 border-bottom flex-wrap gap-2">
                    <div>
                        <h5 class="card-title mb-0 fw-bold d-flex align-items-center">
                            <i class="bx bx-layer text-primary me-2"></i>โครงสร้างแผนงาน ประจำปีงบประมาณ <?= $selected_year ?>
                        </h5>
                        <small class="text-muted">คลิกปุ่มเพื่อเพิ่มกลยุทธ์ย่อย (1.1) หรือโครงการ (1.1.1) ภายใต้ยุทธศาสตร์ที่ต้องการ</small>
                    </div>
                    <span class="badge bg-label-primary rounded-pill px-3 py-2">
                        <?= count($strategies) ?> ยุทธศาสตร์หลัก / <?= count($projects) ?> โครงการ
                    </span>
                </div>

                <div class="card-body p-3 p-md-4">
                    <?php if (empty($strategies)): ?>
                        <div class="text-center py-5 text-muted">
                            <i class="bx bx-folder-open display-4 opacity-50 mb-2 d-block"></i>
                            <h6 class="fw-bold">ยังไม่มีข้อมูลยุทธศาสตร์ในปีงบประมาณ <?= $selected_year ?></h6>
                            <p class="small">คลิกปุ่ม "+ เพิ่มยุทธศาสตร์ (1.)" เพื่อเริ่มต้นตั้งค่าโครงสร้าง</p>
                        </div>
                    <?php else: ?>
                        <?php foreach ($strategies as $stIndex => $st): ?>
                            <div class="strategy-block mb-4 p-3 rounded-4" style="background: #f8fafc; border: 1px solid #e2e8f0;">
                                <!-- Level 1: Strategy Header -->
                                <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 pb-2 border-bottom">
                                    <div>
                                        <span class="badge bg-primary rounded-pill px-3 py-1 fs-7 fw-bold me-2">
                                            ยุทธศาสตร์ที่ <?= esc($st->strategy_no) ?>
                                        </span>
                                        <span class="fw-bold fs-6 text-dark"><?= esc($st->strategy_name) ?></span>
                                    </div>
                                    <div class="d-inline-flex gap-1">
                                        <button type="button" class="btn btn-sm btn-outline-success rounded-pill px-2" 
                                            onclick="openAddTacticModal(<?= $st->id ?>, '<?= esc($st->strategy_no) ?>', '<?= esc($st->strategy_name) ?>')">
                                            <i class="bx bx-plus me-1"></i> เพิ่มกลยุทธ์ย่อย (1.1)
                                        </button>
                                        <button type="button" class="btn btn-sm btn-icon btn-outline-secondary rounded-circle" 
                                            onclick="editStrategy(<?= htmlspecialchars(json_encode($st), ENT_QUOTES, 'UTF-8') ?>)">
                                            <i class="bx bx-edit-alt"></i>
                                        </button>
                                        <button type="button" class="btn btn-sm btn-icon btn-outline-danger rounded-circle" 
                                            onclick="deleteStrategy(<?= $st->id ?>, 'ยุทธศาสตร์ที่ <?= esc($st->strategy_no) ?>')">
                                            <i class="bx bx-trash"></i>
                                        </button>
                                    </div>
                                </div>

                                <!-- Level 2: Tactics List -->
                                <div class="tactics-container mt-3 ps-md-3">
                                    <?php if (empty($st->tactics)): ?>
                                        <div class="text-muted small py-2 ps-2">
                                            <i class="bx bx-info-circle me-1"></i>ยังไม่มีกลยุทธ์ย่อย — คลิก <b>"เพิ่มกลยุทธ์ย่อย (1.1)"</b>
                                        </div>
                                    <?php else: ?>
                                        <?php foreach ($st->tactics as $tc): ?>
                                            <div class="tactic-card bg-white p-3 rounded-3 shadow-sm mb-3 border">
                                                <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 pb-2 border-bottom">
                                                    <div>
                                                        <span class="badge bg-label-info rounded-pill px-2 py-1 fw-bold me-2">
                                                            กลยุทธ์ <?= esc($tc->tactic_no) ?>
                                                        </span>
                                                        <span class="fw-semibold text-secondary"><?= esc($tc->tactic_name) ?></span>
                                                        <?php if (!empty($tc->responsible_agency)): ?>
                                                            <span class="badge bg-label-secondary rounded-pill ms-1 fs-8">
                                                                <i class="bx bx-buildings me-1"></i><?= esc($tc->responsible_agency) ?>
                                                            </span>
                                                        <?php endif; ?>
                                                    </div>
                                                    <div class="d-inline-flex gap-1">
                                                        <button type="button" class="btn btn-xs btn-outline-warning rounded-pill px-2"
                                                            onclick="openAddProjectModal(<?= $st->id ?>, <?= $tc->id ?>, '<?= esc($tc->tactic_no) ?>')">
                                                            <i class="bx bx-plus me-1"></i> เพิ่มโครงการ (1.1.1)
                                                        </button>
                                                        <button type="button" class="btn btn-xs btn-icon btn-outline-secondary rounded-circle"
                                                            onclick="editTactic(<?= htmlspecialchars(json_encode($tc), ENT_QUOTES, 'UTF-8') ?>)">
                                                            <i class="bx bx-edit-alt"></i>
                                                        </button>
                                                        <button type="button" class="btn btn-xs btn-icon btn-outline-danger rounded-circle"
                                                            onclick="deleteTactic(<?= $tc->id ?>, 'กลยุทธ์ <?= esc($tc->tactic_no) ?>')">
                                                            <i class="bx bx-trash"></i>
                                                        </button>
                                                    </div>
                                                </div>

                                                <?php if (!empty($tc->objective) || !empty($tc->actions)): ?>
                                                    <div class="row g-2 mt-2 mb-2 p-2 rounded bg-light text-muted fs-8">
                                                        <?php if (!empty($tc->objective)): ?>
                                                            <div class="<?= !empty($tc->actions) ? 'col-12 col-md-6' : 'col-12' ?>">
                                                                <b class="text-dark d-block mb-1"><i class="bx bx-target-lock text-primary me-1"></i>วัตถุประสงค์:</b>
                                                                <div class="ps-3 text-secondary" style="white-space: pre-line; line-height: 1.6;"><?= esc(format_item_lines($tc->objective)) ?></div>
                                                            </div>
                                                        <?php endif; ?>
                                                        <?php if (!empty($tc->actions)): ?>
                                                            <div class="<?= !empty($tc->objective) ? 'col-12 col-md-6' : 'col-12' ?>">
                                                                <b class="text-dark d-block mb-1"><i class="bx bx-task text-success me-1"></i>งานที่ทำ / แนวทางการดำเนินงาน:</b>
                                                                <div class="ps-3 text-secondary" style="white-space: pre-line; line-height: 1.6;"><?= esc(format_item_lines($tc->actions)) ?></div>
                                                            </div>
                                                        <?php endif; ?>
                                                    </div>
                                                <?php endif; ?>

                                                 <!-- Level 3: Projects Table -->
                                                 <div class="table-responsive mt-2">
                                                     <table class="table table-sm table-hover align-middle mb-0 table-fit-screen" style="font-size: 0.81rem;">
                                                         <thead class="table-light">
                                                              <tr>
                                                                  <th style="width: 80px;" class="text-center text-nowrap">รหัส</th>
                                                                  <th style="width: auto;">ชื่อโครงการ / กิจกรรม</th>
                                                                  <th style="width: 175px;" class="text-nowrap">ห้วงเวลาโครงการ</th>
                                                                  <th style="width: 200px;" class="text-nowrap">แหล่งเงินงบประมาณ</th>
                                                                  <th class="text-end text-nowrap" style="width: 130px;">งบประมาณตั้งต้น</th>
                                                                  <th class="text-center text-nowrap" style="width: 130px;">การจัดการ</th>
                                                              </tr>
                                                          </thead>
                                                          <tbody>
                                                              <?php if (empty($tc->projects)): ?>
                                                                  <tr>
                                                                      <td colspan="6" class="text-center py-3 text-muted">
                                                                         ยังไม่มีโครงการภายใต้กลยุทธ์นี้ — คลิก <b>"+ เพิ่มโครงการ (1.1.1)"</b>
                                                                     </td>
                                                                 </tr>
                                                             <?php else: ?>
                                                                 <?php foreach ($tc->projects as $p): ?>
                                                                     <tr>
                                                                         <td class="text-center text-nowrap align-top pt-2" data-order="<?= natural_code_sort_key($p->project_code) ?>">
                                                                             <span class="badge bg-label-dark fw-bold font-monospace px-2 py-1"><?= esc($p->project_code ?: '-') ?></span>
                                                                         </td>
                                                                         <td class="align-top pt-2">
                                                                             <span class="fw-bold text-dark d-block mb-1" style="font-size: 0.86rem; line-height: 1.4;"><?= esc($p->project_name) ?></span>
                                                                             <?php if (!empty($p->sub_items)): ?>
                                                                                 <div class="mt-1 ps-2 border-start border-2 border-warning">
                                                                                     <?php foreach ($p->sub_items as $sub): ?>
                                                                                         <div class="small text-muted d-flex justify-content-between align-items-center gap-2 py-0.5" style="font-size: 0.80rem;">
                                                                                             <span class="d-inline-flex align-items-center flex-wrap gap-1">
                                                                                                 <i class="bx bx-layer text-warning me-0.5"></i>
                                                                                                 <b class="text-dark"><?= esc($sub->item_no ?: '-') ?></b>
                                                                                                 <span><?= esc($sub->item_name) ?></span>
                                                                                                 <?php if (!empty($sub->children)): ?>
                                                                                                     <span class="badge bg-label-info rounded-pill py-0 px-1.5 ms-1 fs-9 text-nowrap"><?= count($sub->children) ?> กิจกรรมย่อย</span>
                                                                                                 <?php endif; ?>
                                                                                             </span>
                                                                                             <span class="fw-semibold text-secondary text-nowrap flex-shrink-0 ms-auto ps-2"><?= number_format($sub->allocated_budget, 2) ?> ฿</span>
                                                                                         </div>
                                                                                         <?php if (!empty($sub->children)): ?>
                                                                                             <div class="ps-3 border-start border-2 border-info ms-2 my-1">
                                                                                                 <?php foreach ($sub->children as $child): ?>
                                                                                                     <div class="small text-muted d-flex justify-content-between align-items-center gap-2 py-0.5" style="font-size: 0.77rem;">
                                                                                                         <span class="d-inline-flex align-items-center flex-wrap gap-1">
                                                                                                             <i class="bx bx-subdirectory-right text-info me-0.5"></i>
                                                                                                             <span class="badge bg-label-info py-0 px-1 text-nowrap"><?= esc($child->item_no ?: '-') ?></span>
                                                                                                             <span><?= esc($child->item_name) ?></span>
                                                                                                         </span>
                                                                                                         <span class="text-muted text-nowrap flex-shrink-0 ms-auto ps-2" style="font-size: 0.76rem;"><?= number_format($child->allocated_budget, 2) ?> ฿</span>
                                                                                                     </div>
                                                                                                 <?php endforeach; ?>
                                                                                             </div>
                                                                                         <?php endif; ?>
                                                                                     <?php endforeach; ?>
                                                                                 </div>
                                                                             <?php endif; ?>
                                                                         </td>
                                                                         <td class="align-top pt-2" style="font-size: 0.78rem;">
                                                                              <?php $timeStatus = get_project_time_status($p->start_date ?? null, $p->end_date ?? null); ?>
                                                                              <div class="mb-1">
                                                                                  <?= $timeStatus['badge_html'] ?>
                                                                              </div>
                                                                              <?php if ($timeStatus['status'] !== 'unspecified'): ?>
                                                                                  <div class="small text-muted" style="font-size: 0.74rem; line-height: 1.35;">
                                                                                      <i class="bx bx-calendar-event me-1 text-primary"></i><?= esc($timeStatus['date_range']) ?>
                                                                                  </div>
                                                                              <?php endif; ?>
                                                                          </td>
                                                                          <td class="small text-muted align-top pt-2" style="font-size: 0.78rem; line-height: 1.4;">
                                                                             <?= esc($p->budget_source ?: '-') ?>
                                                                         </td>
                                                                         <td class="text-end fw-bold text-success align-top text-nowrap pt-2" style="font-size: 0.85rem;">
                                                                             <?= number_format($p->allocated_budget, 2) ?> บาท
                                                                         </td>
                                                                         <td class="text-center text-nowrap align-top pt-2">
                                                                             <div class="d-inline-flex gap-1">
                                                                                 <a href="<?= base_url('Admin/BudgetLedger/view/' . $p->id) ?>" class="btn btn-xs btn-outline-primary rounded-pill px-2 text-nowrap" title="เปิดดูทะเบียนคุม">
                                                                                     <i class="bx bx-book-open me-1"></i>ทะเบียนคุม
                                                                                 </a>
                                                                                 <button type="button" class="btn btn-xs btn-icon btn-outline-secondary rounded-circle btn-edit-project" 
                                                                                     data-project='<?= htmlspecialchars(json_encode($p), ENT_QUOTES, 'UTF-8') ?>' 
                                                                                     title="แก้ไขข้อมูลโครงการ">
                                                                                     <i class="bx bx-edit-alt"></i>
                                                                                 </button>
                                                                                 <button type="button" class="btn btn-xs btn-icon btn-outline-danger rounded-circle" onclick="deleteProject(<?= $p->id ?>, '<?= esc($p->project_name) ?>')" title="ลบโครงการ">
                                                                                     <i class="bx bx-trash"></i>
                                                                                 </button>
                                                                             </div>
                                                                         </td>
                                                                     </tr>
                                                                 <?php endforeach; ?>
                                                             <?php endif; ?>
                                                         </tbody>
                                                     </table>
                                                 </div>
                                            </div>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>

        </div>
    </div>
</div>

<!-- ==================== MODALS ==================== -->

<!-- Modal 1: Add / Edit Strategy (ระดับ 1) -->
<div class="modal fade" id="modalAddStrategy" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border-radius: 16px; border: none; box-shadow: 0 10px 30px rgba(0,0,0,0.15);">
            <div class="modal-header border-bottom py-3">
                <h5 class="modal-title fw-bold" id="modalStrategyTitle">
                    <i class="bx bx-flag text-primary me-2"></i>1. เพิ่มยุทธศาสตร์ (ระดับที่ 1)
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="formStrategy">
                <input type="hidden" name="id" id="strategyId" value="">
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label required fw-semibold">ปีงบประมาณ (พ.ศ.)</label>
                        <input type="text" class="form-control" name="budget_year" id="stratYear" value="<?= $selected_year ?>" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label required fw-semibold">ลำดับยุทธศาสตร์</label>
                        <input type="text" class="form-control" name="strategy_no" id="stratNo" required placeholder="เช่น 1 หรือ 2 หรือ 3">
                    </div>
                    <div class="mb-3">
                        <label class="form-label required fw-semibold">ชื่อยุทธศาสตร์</label>
                        <textarea class="form-control" name="strategy_name" id="stratName" rows="3" required placeholder="เช่น ยุทธศาสตร์การพัฒนากระบวนการจัดการเรียนรู้ที่เน้นผู้เรียนเป็นสำคัญ"></textarea>
                    </div>
                </div>
                <div class="modal-footer border-top py-3">
                    <button type="button" class="btn btn-outline-secondary rounded-pill" data-bs-dismiss="modal">ยกเลิก</button>
                    <button type="submit" class="btn btn-primary rounded-pill px-4" id="btnSubmitStrategy">
                        <span class="spinner-border spinner-border-sm me-1 d-none" role="status" aria-hidden="true"></span>
                        <i class="bx bx-save me-1 btn-icon"></i> <span class="btn-text">บันทึกยุทธศาสตร์</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal 2: Add / Edit Tactic (ระดับ 2) -->
<div class="modal fade" id="modalAddTactic" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border-radius: 16px; border: none; box-shadow: 0 10px 30px rgba(0,0,0,0.15);">
            <div class="modal-header border-bottom py-3">
                <h5 class="modal-title fw-bold" id="modalTacticTitle">
                    <i class="bx bx-git-branch text-success me-2"></i>1.1 เพิ่มกลยุทธ์ (ระดับที่ 2)
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="formTactic">
                <input type="hidden" name="id" id="tacticId" value="">
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label required fw-semibold">เลือกยุทธศาสตร์หลัก</label>
                        <select class="form-select" name="strategy_id" id="tacticStrategyId" required>
                            <?php foreach ($strategies as $st): ?>
                                <option value="<?= $st->id ?>">ยุทธศาสตร์ที่ <?= esc($st->strategy_no) ?>: <?= esc($st->strategy_name) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label required fw-semibold">รหัสกลยุทธ์</label>
                        <input type="text" class="form-control" name="tactic_no" id="tacticNo" required placeholder="เช่น 1.1 หรือ 3.1">
                    </div>
                    <div class="mb-3">
                        <label class="form-label required fw-semibold">ชื่อกลยุทธ์</label>
                        <textarea class="form-control" name="tactic_name" id="tacticName" rows="2" required placeholder="เช่น กลยุทธ์การพัฒนา 1 คน 1 ความสามารถ ตามอัจฉริยภาพของผู้เรียน"></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">วัตถุประสงค์ (ระบุเป็นข้อ ๆ)</label>
                        <textarea class="form-control" name="objective" id="tacticObjective" rows="3" placeholder="1. เพื่อส่งเสริมและพัฒนาศักยภาพผู้เรียน&#10;2. เพื่อยกระดับผลสัมฤทธิ์ทางการเรียน"></textarea>
                        <small class="text-muted fs-8">สามารถกด Enter เพื่อขึ้นบรรทัดใหม่ในแต่ละข้อได้</small>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">งานที่ทำ / แนวทางการดำเนินงาน (ระบุเป็นข้อ ๆ)</label>
                        <textarea class="form-control" name="actions" id="tacticActions" rows="3" placeholder="1. จัดกิจกรรมค่ายวิชาการ&#10;2. อบรมพัฒนาทักษะวิชาชีพ"></textarea>
                        <small class="text-muted fs-8">สามารถกด Enter เพื่อขึ้นบรรทัดใหม่ในแต่ละข้อได้</small>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">หน่วยงานที่รับผิดชอบ</label>
                        <input type="text" class="form-control" name="responsible_agency" id="tacticResponsibleAgency" placeholder="เช่น กลุ่มบริหารวิชาการ / กลุ่มสาระการเรียนรู้ภาษาไทย">
                    </div>
                </div>
                <div class="modal-footer border-top py-3">
                    <button type="button" class="btn btn-outline-secondary rounded-pill" data-bs-dismiss="modal">ยกเลิก</button>
                    <button type="submit" class="btn btn-success rounded-pill px-4" id="btnSubmitTactic">
                        <span class="spinner-border spinner-border-sm me-1 d-none" role="status" aria-hidden="true"></span>
                        <i class="bx bx-save me-1 btn-icon"></i> <span class="btn-text">บันทึกกลยุทธ์</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal 3: Add / Edit Project (ระดับ 3) -->
<div class="modal fade" id="modalAddProject" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-xl">
        <div class="modal-content" style="border-radius: 16px; border: none; box-shadow: 0 10px 30px rgba(0,0,0,0.15);">
            <div class="modal-header border-bottom py-3">
                <h5 class="modal-title fw-bold" id="modalProjectTitle">
                    <i class="bx bx-folder-plus text-warning me-2"></i>1.1.1 เพิ่มโครงการและงบประมาณ (ระดับที่ 3)
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="formProject">
                <div class="modal-body p-4">
                    <input type="hidden" name="id" id="projectId" value="">
                    
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label required fw-semibold">1. เลือกยุทธศาสตร์</label>
                            <select class="form-select" name="strategy_id" id="projectStrategySelect" required onchange="loadTacticsForProject(this.value)">
                                <option value="">-- เลือกยุทธศาสตร์ --</option>
                                <?php foreach ($strategies as $st): ?>
                                    <option value="<?= $st->id ?>">ยุทธศาสตร์ที่ <?= esc($st->strategy_no) ?>: <?= esc($st->strategy_name) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label required fw-semibold">1.1 เลือกกลยุทธ์</label>
                            <select class="form-select" name="tactic_id" id="projectTacticSelect" required>
                                <option value="">-- กรุณาเลือกยุทธศาสตร์ก่อน --</option>
                            </select>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label required fw-semibold">ปีงบประมาณ (พ.ศ.)</label>
                            <input type="text" class="form-control" name="budget_year" id="projectYear" value="<?= $selected_year ?>" required placeholder="เช่น 2569">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">รหัสโครงการ (เช่น 1.1.1, 3.1.7)</label>
                            <input type="text" class="form-control" name="project_code" id="projectCode" placeholder="เช่น 3.1.7">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label required fw-semibold d-flex justify-content-between align-items-center">
                                <span>งบประมาณตั้งต้น (บาท)</span>
                                <span class="badge bg-label-info py-0 px-1.5 fs-9 fw-normal d-none" id="allocatedBudgetAutoBadge">
                                    <i class="bx bx-sync me-0.5"></i>รวมจากกิจกรรม
                                </span>
                            </label>
                            <input type="text" class="form-control text-end fw-bold input-money" name="allocated_budget" id="allocatedBudget" required placeholder="0.00" value="0.00">
                        </div>

                        <div class="col-12">
                            <label class="form-label required fw-semibold">ชื่อโครงการ / กิจกรรม</label>
                            <input type="text" class="form-control" name="project_name" id="projectName" required placeholder="เช่น โครงการกิจกรรมพัฒนากลุ่มสาระการเรียนรู้สุขศึกษาและพลศึกษา">
                        </div>

                        <div class="col-12">
                            <label class="form-label fw-semibold">แหล่งเงินงบประมาณ / หมวดเงิน</label>
                            <textarea class="form-control" name="budget_source" id="budgetSource" rows="2" placeholder="เช่น เงินงบประมาณรายจ่ายประจำปีงบประมาณ พ.ศ. 2569 (เงินพัฒนาการจัดการศึกษา - กิจกรรมพัฒนาผู้เรียน)"></textarea>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">
                                <i class="bx bx-calendar text-primary me-1"></i>วันที่เริ่มต้นห้วงเวลาโครงการ (พ.ศ.)
                            </label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0"><i class="bx bx-calendar text-primary"></i></span>
                                <input type="text" class="form-control border-start-0" name="start_date" id="projectStartDate" placeholder="เลือกวันเริ่มต้น (พ.ศ.)">
                            </div>
                            <small class="text-muted" style="font-size: 0.76rem;">ระบุวันที่เริ่มต้นดำเนินโครงการ (เว้นว่างได้)</small>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">
                                <i class="bx bx-calendar-check text-warning me-1"></i>วันที่สิ้นสุดห้วงเวลา (ใช้เงินวันสุดท้าย พ.ศ.)
                            </label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0"><i class="bx bx-calendar-check text-warning"></i></span>
                                <input type="text" class="form-control border-start-0" name="end_date" id="projectEndDate" placeholder="เลือกวันสิ้นสุด (พ.ศ.)">
                            </div>
                            <small class="text-muted" style="font-size: 0.76rem;">หากเลยวันนี้ ระบบจะแสดงสถานะ "เลยกำหนดเวลา" (เว้นว่างได้)</small>
                        </div>

                        <!-- Sub-Items Section (รายการกิจกรรมหลักและกิจกรรมย่อย ระดับ 4 & 5) -->
                        <div class="col-12 mt-4">
                            <div class="card border bg-light shadow-none" style="border-radius: 12px;">
                                <div class="card-header bg-transparent d-flex justify-content-between align-items-center py-2.5 px-3 border-bottom flex-wrap gap-2">
                                    <div>
                                        <b class="text-dark"><i class="bx bx-list-check text-warning me-1"></i>รายการกิจกรรมและกิจกรรมย่อย (ระดับ 4 และระดับ 5)</b>
                                        <small class="text-muted d-block fs-8">กำหนดกิจกรรมหลัก (เช่น 10.1) และสามารถแตกกิจกรรมย่อย (เช่น 10.1.2) ได้ตามต้องการ</small>
                                    </div>
                                    <button type="button" class="btn btn-sm btn-outline-primary rounded-pill px-3 shadow-xs fw-semibold" id="btnAddMainActivity">
                                        <i class="bx bx-plus me-1"></i> เพิ่มกิจกรรมหลัก (เช่น 10.1)
                                    </button>
                                </div>
                                <div class="card-body p-3">
                                    <div id="mainActivitiesContainer" class="d-flex flex-column gap-3">
                                        <!-- Dynamic main activities with nested sub-activities will be added here -->
                                    </div>
                                    <div id="emptyActivityAlert" class="text-center py-4 text-muted">
                                        <i class="bx bx-folder-open fs-3 mb-1 d-block opacity-50"></i>
                                        <small>ยังไม่มีรายการกิจกรรม — คลิก <b>"+ เพิ่มกิจกรรมหลัก"</b> เพื่อเริ่มต้น</small>
                                    </div>
                                    <div class="border-top mt-3 pt-2 d-flex justify-content-between align-items-center d-none" id="subItemsTotalRow">
                                        <span class="fw-bold text-dark fs-7">รวมงบประมาณจากกิจกรรมทั้งหมด:</span>
                                        <span class="fw-bolder text-success fs-6" id="subItemsTotalDisplay">0.00 บาท</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-top py-3">
                    <button type="button" class="btn btn-outline-secondary rounded-pill" data-bs-dismiss="modal">ยกเลิก</button>
                    <button type="submit" class="btn btn-warning rounded-pill px-4 text-white fw-semibold" id="btnSubmitProject">
                        <span class="spinner-border spinner-border-sm me-1 d-none" role="status" aria-hidden="true"></span>
                        <i class="bx bx-save me-1 btn-icon"></i> <span class="btn-text">บันทึกโครงการ</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>


<style>
.flatpickr-calendar {
    z-index: 999999 !important;
}
</style>
