<?php
/**
 * @var array<object> $news
 * @var array<int, int> $fileCounts
 * @var array<int, array> $filesByNews
 * @var array<int, array> $fileTypesByNews
 */
?>
<!-- Layout container -->
<div class="layout-page">
    <?= view('Admin/AdminLeyout/AdminNavbar') ?>

    <!-- Content wrapper -->
    <div class="content-wrapper">
        <div class="container-xxl flex-grow-1 container-p-y">

            <!-- Page Header Card -->
            <div class="row mb-4">
                <div class="col-12">
                    <div class="card border-0 shadow-sm overflow-hidden" style="background: linear-gradient(135deg, #f8f9fa 0%, #ffffff 100%);">
                        <div class="card-body p-4">
                            <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">
                                <div class="d-flex align-items-center gap-3">
                                    <div class="avatar avatar-lg rounded-3 bg-label-primary d-flex align-items-center justify-content-center p-2">
                                        <i class="bx bx-file-blank fs-2 text-primary"></i>
                                    </div>
                                    <div>
                                        <div class="d-flex align-items-center gap-2 mb-1">
                                            <span class="badge bg-label-primary rounded-pill">ข้อมูลเผยแพร่</span>
                                            <span class="badge bg-label-secondary rounded-pill">
                                                <i class="bx bx-cloud-upload me-1"></i>Remote Server Storage
                                            </span>
                                        </div>
                                        <h4 class="mb-1 fw-bold text-dark">จัดการประชาสัมพันธ์และเอกสารเผยแพร่</h4>
                                        <p class="text-muted mb-0 small">
                                            ข่าวสาร ประกาศ คำสั่ง และไฟล์เอกสารเผยแพร่ของระบบงานงบประมาณและแผน
                                        </p>
                                    </div>
                                </div>
                                <div class="d-flex gap-2">
                                    <a href="<?= base_url('News') ?>" target="_blank" class="btn btn-outline-primary rounded-pill px-3">
                                        <i class="bx bx-globe me-1"></i>หน้าประชาสัมพันธ์ (ผู้ใช้ทั่วไป)
                                    </a>
                                    <button type="button" class="btn btn-primary rounded-pill px-4 shadow-sm"
                                            data-bs-toggle="modal" data-bs-target="#newsFormModal"
                                            onclick="prepareNewsForm()">
                                        <i class="bx bx-plus me-1"></i>สร้างประชาสัมพันธ์
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Alerts with SweetAlert2 -->
            <?php if (session()->getFlashdata('success')): ?>
                <div class="alert alert-success alert-dismissible fade show mb-4 border-0 shadow-sm" role="alert">
                    <div class="d-flex align-items-center">
                        <i class="bx bx-check-circle fs-4 me-2"></i>
                        <div><?= esc(session()->getFlashdata('success')) ?></div>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
                <script>
                    window.addEventListener('load', function() {
                        if (typeof Swal !== 'undefined') {
                            Swal.fire({
                                icon: 'success',
                                title: 'สำเร็จ!',
                                text: <?= json_encode((string) session()->getFlashdata('success'), JSON_UNESCAPED_UNICODE) ?>,
                                timer: 2500,
                                showConfirmButton: false,
                                customClass: { popup: 'rounded-4 shadow' }
                            });
                        }
                    });
                </script>
            <?php endif; ?>

            <?php if (session()->getFlashdata('error')): ?>
                <div class="alert alert-danger alert-dismissible fade show mb-4 border-0 shadow-sm" role="alert">
                    <div class="d-flex align-items-center">
                        <i class="bx bx-error-circle fs-4 me-2"></i>
                        <div><?= esc(session()->getFlashdata('error')) ?></div>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
                <script>
                    window.addEventListener('load', function() {
                        if (typeof Swal !== 'undefined') {
                            Swal.fire({
                                icon: 'error',
                                title: 'เกิดข้อผิดพลาด!',
                                text: <?= json_encode((string) session()->getFlashdata('error'), JSON_UNESCAPED_UNICODE) ?>,
                                confirmButtonText: 'ตกลง',
                                confirmButtonColor: '#d33',
                                customClass: { popup: 'rounded-4 shadow' }
                            });
                        }
                    });
                </script>
            <?php endif; ?>

            <?php
                $allNews = $news ?? [];
                $totalNews = count($allNews);
                $publishedNews = count(array_filter($allNews, fn($n) => ($n->status ?? '') === 'published'));
                $draftNews = count(array_filter($allNews, fn($n) => ($n->status ?? '') === 'draft'));
                $closedNews = count(array_filter($allNews, fn($n) => ($n->status ?? '') === 'closed'));
                $totalAttachedFiles = array_sum($fileCounts ?? []);
            ?>

            <!-- Stats Widgets -->
            <div class="row g-3 mb-4">
                <div class="col-xl-3 col-sm-6">
                    <div class="card h-100 border-0 shadow-sm">
                        <div class="card-body d-flex align-items-center p-3 p-md-4">
                            <div class="avatar flex-shrink-0 me-3">
                                <span class="avatar-initial rounded-3 bg-label-primary">
                                    <i class="bx bx-file fs-4"></i>
                                </span>
                            </div>
                            <div>
                                <span class="text-muted small">รายการทั้งหมด</span>
                                <h4 class="mb-0 fw-bold"><?= number_format($totalNews) ?></h4>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-xl-3 col-sm-6">
                    <div class="card h-100 border-0 shadow-sm">
                        <div class="card-body d-flex align-items-center p-3 p-md-4">
                            <div class="avatar flex-shrink-0 me-3">
                                <span class="avatar-initial rounded-3 bg-label-success">
                                    <i class="bx bx-check-double fs-4"></i>
                                </span>
                            </div>
                            <div>
                                <span class="text-muted small">เผยแพร่แล้ว</span>
                                <h4 class="mb-0 fw-bold text-success"><?= number_format($publishedNews) ?></h4>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-xl-3 col-sm-6">
                    <div class="card h-100 border-0 shadow-sm">
                        <div class="card-body d-flex align-items-center p-3 p-md-4">
                            <div class="avatar flex-shrink-0 me-3">
                                <span class="avatar-initial rounded-3 bg-label-warning">
                                    <i class="bx bx-edit fs-4"></i>
                                </span>
                            </div>
                            <div>
                                <span class="text-muted small">แบบร่าง</span>
                                <h4 class="mb-0 fw-bold text-warning"><?= number_format($draftNews) ?></h4>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-xl-3 col-sm-6">
                    <div class="card h-100 border-0 shadow-sm">
                        <div class="card-body d-flex align-items-center p-3 p-md-4">
                            <div class="avatar flex-shrink-0 me-3">
                                <span class="avatar-initial rounded-3 bg-label-info">
                                    <i class="bx bx-paperclip fs-4"></i>
                                </span>
                            </div>
                            <div>
                                <span class="text-muted small">เอกสารแนบบน Server</span>
                                <h4 class="mb-0 fw-bold text-info"><?= number_format($totalAttachedFiles) ?> <small class="fs-8 text-muted fw-normal">ไฟล์</small></h4>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- News List Card -->
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-0 pt-4 px-4 pb-3">
                    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">
                        <div>
                            <h5 class="mb-1 fw-bold text-dark">
                                <i class="bx bx-list-ul text-warning me-2"></i>รายการเอกสารประชาสัมพันธ์
                            </h5>
                            <p class="text-muted small mb-0">ตรวจสอบ แก้ไข ดาวน์โหลดไฟล์ และจัดการเอกสารเผยแพร่</p>
                        </div>

                        <div class="d-flex gap-2">
                            <div class="input-group" style="max-width:320px;">
                                <span class="input-group-text bg-white border-end-0">
                                    <i class="bx bx-search text-muted"></i>
                                </span>
                                <input type="search" id="newsSearch" class="form-control border-start-0 ps-0"
                                       placeholder="ค้นหาหัวข้อ / ประเภท...">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0" id="adminNewsTable">
                        <thead class="table-light">
                            <tr>
                                <th class="ps-4" style="width: 130px;">ประเภทไฟล์</th>
                                <th>หัวข้อประชาสัมพันธ์</th>
                                <th>ประเภท</th>
                                <th class="text-center">สถานะ</th>
                                <th>วันเวลาเผยแพร่</th>
                                <th class="text-center">ไฟล์แนบ</th>
                                <th class="text-center">เข้าชม</th>
                                <th class="text-end pe-4">จัดการ</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php if (empty($allNews)): ?>
                            <tr>
                                <td colspan="8" class="text-center py-5 text-muted">
                                    <i class="bx bx-file-blank fs-1 d-block mb-2 opacity-50"></i>
                                    ยังไม่มีรายการประชาสัมพันธ์
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($allNews as $item): ?>
                                <?php
                                    $filesCount = $fileCounts[(int) $item->id] ?? 0;
                                    $types = $fileTypesByNews[(int) $item->id] ?? [];
                                ?>
                                <tr data-search="<?= esc(mb_strtolower(($item->title ?? '') . ' ' . ($item->category ?? ''))) ?>">
                                    <td class="ps-4">
                                        <!-- แสดงประเภทไฟล์ที่แนบไว้ -->
                                        <?php if (!empty($types)): ?>
                                            <div class="d-flex flex-wrap gap-1 align-items-center">
                                                <?php foreach ($types as $t): ?>
                                                    <span class="badge <?= esc($t['badge_class']) ?> rounded-pill" title="<?= esc($t['label']) ?>">
                                                        <i class="<?= esc($t['icon']) ?> me-1"></i><?= esc($t['ext']) ?>
                                                    </span>
                                                <?php endforeach; ?>
                                            </div>
                                        <?php else: ?>
                                            <span class="badge bg-label-secondary rounded-pill text-muted">
                                                <i class="bx bx-file me-1"></i>ไม่มีไฟล์
                                            </span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <div class="fw-semibold text-dark news-title"><?= esc($item->title ?? '-') ?></div>
                                        <div class="d-flex align-items-center gap-2 mt-1">
                                            <small class="text-muted">ID #<?= (int) $item->id ?></small>
                                            <?php if (!empty($item->created_by)): ?>
                                                <small class="text-muted">· โดย <?= esc($item->created_by) ?></small>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge bg-label-primary rounded-pill">
                                            <?= esc($item->category ?? 'ประชาสัมพันธ์') ?>
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        <?php if (($item->status ?? '') === 'published'): ?>
                                            <span class="badge bg-label-success rounded-pill">เผยแพร่</span>
                                        <?php elseif (($item->status ?? '') === 'closed'): ?>
                                            <span class="badge bg-label-secondary rounded-pill">ปิด</span>
                                        <?php else: ?>
                                            <span class="badge bg-label-warning rounded-pill">แบบร่าง</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?= !empty($item->published_at)
                                            ? esc(date('d/m/Y H:i', strtotime($item->published_at)))
                                            : '-' ?>
                                    </td>
                                    <td class="text-center">
                                        <?php if ($filesCount > 0): ?>
                                            <button type="button" class="btn btn-sm btn-outline-info rounded-pill px-2 py-1"
                                                    onclick="viewNewsFiles(<?= (int) $item->id ?>, '<?= esc($item->title, 'js') ?>')"
                                                    title="ดูรายการไฟล์แนบ">
                                                <i class="bx bx-paperclip me-1"></i><?= number_format($filesCount) ?> ไฟล์
                                            </button>
                                        <?php else: ?>
                                            <span class="badge bg-label-secondary rounded-pill">-</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge bg-label-light text-muted">
                                            <i class="bx bx-show me-1"></i><?= number_format((int) ($item->view_count ?? 0)) ?>
                                        </span>
                                    </td>
                                    <td class="text-end pe-4">
                                        <div class="d-inline-flex gap-1">
                                            <a href="<?= base_url('News/View/' . (int) $item->id) ?>"
                                               target="_blank"
                                               class="btn btn-sm btn-icon btn-outline-primary rounded-pill"
                                               title="ดูหน้าเว็บ">
                                                <i class="bx bx-show"></i>
                                            </a>

                                            <button type="button"
                                                    class="btn btn-sm btn-icon btn-outline-secondary rounded-pill"
                                                    title="แก้ไข"
                                                    onclick='editNews(<?= json_encode([
                                                        'id' => (int) $item->id,
                                                        'title' => $item->title ?? '',
                                                        'category' => $item->category ?? 'ประชาสัมพันธ์',
                                                        'content' => $item->content ?? '',
                                                        'published_at' => !empty($item->published_at) ? date('Y-m-d H:i', strtotime($item->published_at)) : '',
                                                        'expires_at' => !empty($item->expires_at) ? date('Y-m-d H:i', strtotime($item->expires_at)) : '',
                                                        'status' => $item->status ?? 'draft'
                                                    ], JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP) ?>)'>
                                                <i class="bx bx-edit"></i>
                                            </button>

                                            <button type="button"
                                                    class="btn btn-sm btn-icon btn-outline-danger rounded-pill"
                                                    title="ลบ"
                                                    onclick="confirmDeleteNews(<?= (int) $item->id ?>, '<?= esc($item->title, 'js') ?>')">
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

        </div>
    </div>
</div>

<!-- ========================================== -->
<!-- Modal: Create / Edit News Form             -->
<!-- ========================================== -->
<div class="modal fade" id="newsFormModal" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
    <div class="modal-dialog modal-xl">
        <div class="modal-content border-0 shadow">
            <form action="<?= base_url('Admin/News/Save') ?>" method="post" enctype="multipart/form-data" id="newsMainForm">
                <?= csrf_field() ?>
                <input type="hidden" name="id" id="news_id">

                <div class="modal-header bg-light">
                    <div>
                        <h5 class="modal-title fw-bold text-dark mb-0" id="newsModalTitle">สร้างประชาสัมพันธ์</h5>
                        <small class="text-muted">กรอกข้อมูลประชาสัมพันธ์และแนบไฟล์เอกสารสำหรับจัดเก็บบน Remote Server</small>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body p-4">
                    <div class="row g-3">

                        <!-- Title -->
                        <div class="col-md-8">
                            <label for="news_title" class="form-label fw-semibold">
                                หัวข้อประชาสัมพันธ์ / ชื่อเรื่อง <span class="text-danger">*</span>
                            </label>
                            <input type="text" class="form-control" name="title" id="news_title"
                                   placeholder="เช่น ประกาศแผนการจัดซื้อจัดจ้าง ประจำปีงบประมาณ 2569" required>
                            <div class="form-text">ระบุชื่อเรื่องที่จะแสดงเป็นหัวข้อหลักของเอกสารประชาสัมพันธ์</div>
                        </div>

                        <!-- Category -->
                        <div class="col-md-4">
                            <label for="news_category" class="form-label fw-semibold">ประเภท / หมวดหมู่</label>
                            <select class="form-select" name="category" id="news_category">
                                <option value="ประชาสัมพันธ์">ประชาสัมพันธ์ทั่วไป</option>
                                <option value="ประกาศและแจ้งเวียน">ประกาศและแจ้งเวียน</option>
                                <option value="งบประมาณและการเงิน">งบประมาณและการเงิน</option>
                                <option value="จัดซื้อจัดจ้าง">จัดซื้อจัดจ้าง</option>
                                <option value="แผนงานและโครงการ">แผนงานและโครงการ</option>
                                <option value="ติดตามและประเมินผล">ติดตามและประเมินผล</option>
                                <option value="เอกสารเผยแพร่">เอกสารเผยแพร่</option>
                            </select>
                        </div>

                        <!-- Content -->
                        <div class="col-12">
                            <label for="news_content" class="form-label fw-semibold">รายละเอียด / คำอธิบายเพิ่มเติม</label>
                            <textarea class="form-control" name="content" id="news_content" rows="6"
                                      placeholder="เขียนรายละเอียดข้อความ สาระสำคัญ หรือหมายเหตุเกี่ยวกับเอกสารแนบ..."></textarea>
                        </div>

                        <!-- Published At -->
                        <div class="col-md-4">
                            <label for="news_published_at" class="form-label fw-semibold">วันเวลาเริ่มเผยแพร่</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bx bx-calendar"></i></span>
                                <input type="text" class="form-control news-datetime" name="published_at" id="news_published_at"
                                       autocomplete="off" placeholder="เลือกวันและเวลาเริ่มเผยแพร่">
                            </div>
                            <div class="form-text">หากไม่ระบุ ระบบจะใช้วันเวลาปัจจุบันเมื่อเผยแพร่</div>
                        </div>

                        <!-- Expires At -->
                        <div class="col-md-4">
                            <label for="news_expires_at" class="form-label fw-semibold">วันเวลาสิ้นสุด (ถ้ามี)</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bx bx-calendar-x"></i></span>
                                <input type="text" class="form-control news-datetime" name="expires_at" id="news_expires_at"
                                       autocomplete="off" placeholder="เลือกวันและเวลาสิ้นสุด">
                            </div>
                            <div class="form-text">หากไม่ระบุ จะแสดงผลตลอดไปจนกว่าจะปิด</div>
                        </div>

                        <!-- Status -->
                        <div class="col-md-4">
                            <label for="news_status" class="form-label fw-semibold">สถานะการแสดงผล</label>
                            <select class="form-select" name="status" id="news_status">
                                <option value="published">เผยแพร่ (แสดงหน้าเว็บทันที)</option>
                                <option value="draft">แบบร่าง (ยังไม่แสดงหน้าเว็บ)</option>
                                <option value="closed">ปิดการเผยแพร่</option>
                            </select>
                        </div>

                        <!-- Existing Files Section (Shown when editing) -->
                        <div class="col-12" id="existingFilesSection" style="display:none;">
                            <div class="card border p-3">
                                <div class="d-flex align-items-center justify-content-between mb-2">
                                    <h6 class="mb-0 fw-semibold text-dark">
                                        <i class="bx bx-paperclip text-info me-1"></i>ไฟล์แนบปัจจุบันบน Remote Server
                                    </h6>
                                    <span class="badge bg-label-info" id="existingFilesCountBadge">0 ไฟล์</span>
                                </div>
                                <div id="existingFilesLoading" class="text-center py-3 text-muted small" style="display:none;">
                                    <span class="spinner-border spinner-border-sm me-1"></span> กำลังโหลดรายการไฟล์...
                                </div>
                                <div class="table-responsive">
                                    <table class="table table-sm table-hover align-middle mb-0" id="existingFilesTable">
                                        <thead class="table-light">
                                            <tr>
                                                <th style="width:90px;">ประเภท</th>
                                                <th>ชื่อไฟล์</th>
                                                <th>ขนาด</th>
                                                <th>วันที่</th>
                                                <th class="text-end">จัดการ</th>
                                            </tr>
                                        </thead>
                                        <tbody id="existingFilesTableBody">
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <!-- Attachments Input Section -->
                        <div class="col-12">
                            <label for="news_files" class="form-label fw-semibold d-flex align-items-center justify-content-between">
                                <span>
                                    <i class="bx bx-upload text-primary me-1"></i>
                                    แนบไฟล์เอกสาร (Attachments)
                                    <small class="text-muted fw-normal">(เลือกได้หลายไฟล์, สูงสุด 50 MB/ไฟล์)</small>
                                </span>
                            </label>
                            <input type="file" class="form-control" name="files[]" id="news_files" multiple
                                   accept=".pdf,.doc,.docx,.xls,.xlsx,.ppt,.pptx,.jpg,.jpeg,.png,.gif,.webp,.zip,.rar,.txt,.csv"
                                   onchange="previewSelectedFiles(this)">
                            <div class="form-text">
                                รองรับไฟล์ PDF, Word, Excel, PowerPoint, ZIP, RAR และรูปภาพ โดยไฟล์จะถูกส่งไปจัดเก็บบน Remote Server ทันที
                            </div>

                            <!-- Selected Files Preview List -->
                            <div id="newFilesPreviewList" class="mt-2 d-none">
                                <div class="card bg-light border-0 p-3">
                                    <div class="small fw-semibold text-dark mb-2">
                                        <i class="bx bx-check-circle text-success me-1"></i>ไฟล์ที่เลือกเตรียมอัปโหลด:
                                    </div>
                                    <div class="d-grid gap-1" id="newFilesListContainer"></div>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>

                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-label-secondary rounded-pill px-4" data-bs-dismiss="modal">ยกเลิก</button>
                    <button type="submit" class="btn btn-primary rounded-pill px-4 shadow-sm" id="btnSaveNews">
                        <i class="bx bx-save me-1"></i>บันทึกข้อมูล
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- ========================================== -->
<!-- Modal: Quick View Attachments              -->
<!-- ========================================== -->
<div class="modal fade" id="newsFilesModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-light">
                <div>
                    <h5 class="modal-title fw-bold text-dark mb-0">
                        <i class="bx bx-paperclip text-info me-1"></i>ไฟล์เอกสารแนบ
                    </h5>
                    <small class="text-muted" id="quickFilesNewsTitle">-</small>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <div id="quickFilesLoading" class="text-center py-4 text-muted">
                    <div class="spinner-border text-primary mb-2" role="status"></div>
                    <div>กำลังโหลดไฟล์เอกสารจาก Remote Server...</div>
                </div>
                <div id="quickFilesEmpty" class="text-center py-4 text-muted d-none">
                    <i class="bx bx-folder-open fs-1 opacity-50 d-block mb-2"></i>
                    ไม่มีไฟล์แนบสำหรับข่าวนี้
                </div>
                <div class="d-grid gap-2" id="quickFilesList"></div>
            </div>
            <div class="modal-footer bg-light">
                <button type="button" class="btn btn-secondary rounded-pill px-4" data-bs-dismiss="modal">ปิด</button>
            </div>
        </div>
    </div>
</div>

<style>
    #adminNewsTable {
        min-width: 1000px;
    }
    #adminNewsTable th,
    #adminNewsTable td {
        vertical-align: middle;
    }
    #adminNewsTable th {
        white-space: nowrap;
        font-size: .8rem;
    }
    #adminNewsTable td {
        white-space: normal;
    }
    #adminNewsTable .news-title {
        max-width: 400px;
        overflow-wrap: anywhere;
        line-height: 1.5;
    }
</style>

<script>
(function () {
    let currentNewsId = null;

    // Reset Form for Create
    window.prepareNewsForm = function () {
        currentNewsId = null;
        document.getElementById('newsModalTitle').textContent = 'สร้างประชาสัมพันธ์';
        document.getElementById('news_id').value = '';
        document.getElementById('news_title').value = '';
        document.getElementById('news_category').value = 'ประชาสัมพันธ์';
        document.getElementById('news_content').value = '';
        document.getElementById('news_status').value = 'published';

        // Reset Flatpickr
        const publishedPicker = document.getElementById('news_published_at')._flatpickr;
        const expiresPicker = document.getElementById('news_expires_at')._flatpickr;
        if (publishedPicker) {
            publishedPicker.setDate(new Date(), true);
        }
        if (expiresPicker) {
            expiresPicker.clear();
        }

        // Reset attachments input
        document.getElementById('news_files').value = '';
        document.getElementById('newFilesPreviewList').classList.add('d-none');
        document.getElementById('newFilesListContainer').innerHTML = '';

        // Hide existing files section
        document.getElementById('existingFilesSection').style.display = 'none';
        document.getElementById('existingFilesTableBody').innerHTML = '';
    };

    // Open Form for Edit
    window.editNews = function (item) {
        currentNewsId = item.id;
        document.getElementById('newsModalTitle').textContent = 'แก้ไขประชาสัมพันธ์ (ID #' + item.id + ')';
        document.getElementById('news_id').value = item.id || '';
        document.getElementById('news_title').value = item.title || '';
        document.getElementById('news_category').value = item.category || 'ประชาสัมพันธ์';
        document.getElementById('news_content').value = item.content || '';
        document.getElementById('news_status').value = item.status || 'draft';

        // Flatpickr Dates
        const publishedPicker = document.getElementById('news_published_at')._flatpickr;
        const expiresPicker = document.getElementById('news_expires_at')._flatpickr;
        if (publishedPicker) publishedPicker.setDate(item.published_at || null, true, 'Y-m-d H:i');
        if (expiresPicker) expiresPicker.setDate(item.expires_at || null, true, 'Y-m-d H:i');

        // Reset new attachments input
        document.getElementById('news_files').value = '';
        document.getElementById('newFilesPreviewList').classList.add('d-none');
        document.getElementById('newFilesListContainer').innerHTML = '';

        // Load existing files from remote server via AJAX
        loadExistingFiles(item.id);

        const modalElement = document.getElementById('newsFormModal');
        if (window.bootstrap && modalElement) {
            bootstrap.Modal.getOrCreateInstance(modalElement).show();
        }
    };

    // Preview Multiple Selected Files with Type Badge
    window.previewSelectedFiles = function (input) {
        const container = document.getElementById('newFilesPreviewList');
        const list = document.getElementById('newFilesListContainer');
        list.innerHTML = '';

        if (input.files && input.files.length > 0) {
            container.classList.remove('d-none');
            Array.from(input.files).forEach(file => {
                const ext = (file.name.split('.').pop() || 'file').toLowerCase();
                let iconClass = 'bx bx-file';
                let badgeClass = 'bg-label-secondary text-secondary';
                if (ext === 'pdf') {
                    iconClass = 'bx bxs-file-pdf';
                    badgeClass = 'bg-label-danger text-danger';
                } else if (['doc', 'docx'].includes(ext)) {
                    iconClass = 'bx bxs-file-doc';
                    badgeClass = 'bg-label-primary text-primary';
                } else if (['xls', 'xlsx', 'csv'].includes(ext)) {
                    iconClass = 'bx bx-spreadsheet';
                    badgeClass = 'bg-label-success text-success';
                } else if (['ppt', 'pptx'].includes(ext)) {
                    iconClass = 'bx bxs-file-presentation';
                    badgeClass = 'bg-label-warning text-warning';
                } else if (['zip', 'rar', '7z'].includes(ext)) {
                    iconClass = 'bx bxs-archive';
                    badgeClass = 'bg-label-secondary text-secondary';
                } else if (['jpg', 'jpeg', 'png', 'gif', 'webp'].includes(ext)) {
                    iconClass = 'bx bx-image';
                    badgeClass = 'bg-label-info text-info';
                }

                const item = document.createElement('div');
                item.className = 'd-flex align-items-center justify-content-between p-2 rounded bg-white border';
                const sizeStr = file.size > 1048576
                    ? (file.size / 1048576).toFixed(2) + ' MB'
                    : (file.size / 1024).toFixed(1) + ' KB';
                item.innerHTML = `
                    <div class="d-flex align-items-center gap-2 min-w-0">
                        <span class="badge ${badgeClass} rounded-pill px-2 py-1">
                            <i class="${iconClass} me-1"></i>${ext.toUpperCase()}
                        </span>
                        <span class="text-truncate small fw-semibold text-dark">${file.name}</span>
                    </div>
                    <span class="text-muted small text-nowrap ms-2">${sizeStr}</span>
                `;
                list.appendChild(item);
            });
        } else {
            container.classList.add('d-none');
        }
    };

    // Load Existing Files for Edit Modal
    function loadExistingFiles(newsId) {
        const section = document.getElementById('existingFilesSection');
        const loading = document.getElementById('existingFilesLoading');
        const tbody = document.getElementById('existingFilesTableBody');
        const badge = document.getElementById('existingFilesCountBadge');

        section.style.display = 'block';
        loading.style.display = 'block';
        tbody.innerHTML = '';
        badge.textContent = 'กำลังโหลด...';

        fetch('<?= base_url('Admin/News/GetFiles') ?>/' + newsId)
            .then(res => res.json())
            .then(data => {
                loading.style.display = 'none';
                if (data.status === 'success' && data.files && data.files.length > 0) {
                    badge.textContent = data.files.length + ' ไฟล์';
                    data.files.forEach(f => {
                        const tr = document.createElement('tr');
                        tr.id = 'file-row-' + f.id;
                        const ti = f.type_info || { ext: 'FILE', icon: 'bx bx-file', badge_class: 'bg-label-secondary' };
                        tr.innerHTML = `
                            <td>
                                <span class="badge ${ti.badge_class} rounded-pill">
                                    <i class="${ti.icon} me-1"></i>${ti.ext}
                                </span>
                            </td>
                            <td>
                                <a href="${f.url}" target="_blank" class="fw-semibold text-primary text-decoration-none">
                                    ${f.original_name}
                                </a>
                            </td>
                            <td class="text-muted small">${f.file_size_formatted}</td>
                            <td class="text-muted small">${f.created_at}</td>
                            <td class="text-end">
                                <a href="${f.url}" target="_blank" class="btn btn-xs btn-outline-primary rounded-pill me-1" title="เปิดดู/ดาวน์โหลด">
                                    <i class="bx bx-download"></i>
                                </a>
                                <button type="button" class="btn btn-xs btn-outline-danger rounded-pill" title="ลบไฟล์นี้"
                                        onclick="deleteExistingFile(${f.id}, '${f.original_name.replace(/'/g, "\\'")}')">
                                    <i class="bx bx-trash"></i>
                                </button>
                            </td>
                        `;
                        tbody.appendChild(tr);
                    });
                } else {
                    badge.textContent = '0 ไฟล์';
                    tbody.innerHTML = `<tr><td colspan="5" class="text-center py-3 text-muted small">ยังไม่มีไฟล์แนบในรายการนี้</td></tr>`;
                }
            })
            .catch(err => {
                loading.style.display = 'none';
                badge.textContent = 'เกิดข้อผิดพลาด';
                tbody.innerHTML = `<tr><td colspan="5" class="text-center text-danger py-2 small">ไม่สามารถโหลดไฟล์ได้</td></tr>`;
            });
    }

    // Delete Single File from Edit Modal with Swal2
    window.deleteExistingFile = function (fileId, fileName) {
        Swal.fire({
            title: 'ยืนยันการลบไฟล์แนบ?',
            html: `ต้องการลบไฟล์ <b>"${fileName}"</b><br>ออกจาก Remote Server ใช่หรือไม่?`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#6c757d',
            confirmButtonText: '<i class="bx bx-trash me-1"></i> ใช่, ลบไฟล์นี้',
            cancelButtonText: 'ยกเลิก',
            customClass: { popup: 'rounded-4 shadow' }
        }).then((result) => {
            if (result.isConfirmed) {
                Swal.fire({
                    title: 'กำลังลบไฟล์จากเซิร์ฟเวอร์...',
                    allowOutsideClick: false,
                    didOpen: () => { Swal.showLoading(); }
                });

                fetch('<?= base_url('Admin/News/DeleteFile') ?>/' + fileId, {
                    method: 'POST',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                .then(res => res.json())
                .then(data => {
                    if (data.status === 'success') {
                        const row = document.getElementById('file-row-' + fileId);
                        if (row) row.remove();
                        Swal.fire({
                            icon: 'success',
                            title: 'ลบไฟล์สำเร็จ!',
                            text: data.message || 'ลบไฟล์เรียบร้อยแล้ว',
                            timer: 1500,
                            showConfirmButton: false,
                            customClass: { popup: 'rounded-4 shadow' }
                        });
                        if (currentNewsId) loadExistingFiles(currentNewsId);
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'เกิดข้อผิดพลาด',
                            text: data.message || 'ไม่สามารถลบไฟล์ได้',
                            customClass: { popup: 'rounded-4 shadow' }
                        });
                    }
                })
                .catch(err => {
                    Swal.fire({
                        icon: 'error',
                        title: 'เกิดข้อผิดพลาดในการเชื่อมต่อ',
                        text: String(err),
                        customClass: { popup: 'rounded-4 shadow' }
                    });
                });
            }
        });
    };

    // Quick View Files Modal from Main Table
    window.viewNewsFiles = function (newsId, newsTitle) {
        document.getElementById('quickFilesNewsTitle').textContent = newsTitle;
        const modal = new bootstrap.Modal(document.getElementById('newsFilesModal'));
        modal.show();

        const loading = document.getElementById('quickFilesLoading');
        const empty = document.getElementById('quickFilesEmpty');
        const list = document.getElementById('quickFilesList');

        loading.classList.remove('d-none');
        empty.classList.add('d-none');
        list.innerHTML = '';

        fetch('<?= base_url('Admin/News/GetFiles') ?>/' + newsId)
            .then(res => res.json())
            .then(data => {
                loading.classList.add('d-none');
                if (data.status === 'success' && data.files && data.files.length > 0) {
                    data.files.forEach(f => {
                        const item = document.createElement('div');
                        item.className = 'card border shadow-xs p-3 d-flex flex-row align-items-center justify-content-between gap-3';
                        const ti = f.type_info || { ext: 'FILE', icon: 'bx bx-file', badge_class: 'bg-label-secondary' };
                        item.innerHTML = `
                            <div class="d-flex align-items-center gap-3 min-w-0">
                                <div class="avatar flex-shrink-0">
                                    <span class="avatar-initial rounded ${ti.badge_class}">
                                        <i class="${ti.icon} fs-4"></i>
                                    </span>
                                </div>
                                <div class="min-w-0">
                                    <div class="fw-semibold text-dark text-truncate">${f.original_name}</div>
                                    <small class="text-muted">${ti.ext} · ${f.file_size_formatted} · ดาวน์โหลด ${f.download_count} ครั้ง</small>
                                </div>
                            </div>
                            <div class="d-flex gap-2">
                                <a href="${f.url}" target="_blank" class="btn btn-sm btn-primary rounded-pill px-3">
                                    <i class="bx bx-download me-1"></i>เปิด / ดาวน์โหลด
                                </a>
                            </div>
                        `;
                        list.appendChild(item);
                    });
                } else {
                    empty.classList.remove('d-none');
                }
            })
            .catch(err => {
                loading.classList.add('d-none');
                empty.classList.remove('d-none');
                empty.textContent = 'ไม่สามารถโหลดรายการไฟล์ได้';
            });
    };

    // Confirm Delete Entire News Item with Swal2
    window.confirmDeleteNews = function (id, title) {
        Swal.fire({
            title: 'ยืนยันการลบประชาสัมพันธ์?',
            html: `ต้องการลบรายการ <b>"${title}"</b><br>และไฟล์แนบทั้งหมดออกจากเซิร์ฟเวอร์ใช่หรือไม่?<br><small class="text-danger">การกระทำนี้ไม่สามารถย้อนกลับได้</small>`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#6c757d',
            confirmButtonText: '<i class="bx bx-trash me-1"></i> ใช่, ต้องการลบ',
            cancelButtonText: 'ยกเลิก',
            customClass: { popup: 'rounded-4 shadow' }
        }).then((result) => {
            if (result.isConfirmed) {
                Swal.fire({
                    title: 'กำลังลบข้อมูลและไฟล์...',
                    allowOutsideClick: false,
                    didOpen: () => { Swal.showLoading(); }
                });
                window.location.href = '<?= base_url('Admin/News/Delete') ?>/' + id;
            }
        });
    };

    // Form Submit Loading Indicator with Swal2
    const newsForm = document.getElementById('newsMainForm');
    if (newsForm) {
        newsForm.addEventListener('submit', async function (e) {
            e.preventDefault();

            const titleInput = document.getElementById('news_title');
            if (!titleInput.value.trim()) {
                Swal.fire({
                    icon: 'warning',
                    title: 'กรุณากรอกข้อมูล',
                    text: 'กรุณาระบุหัวข้อประชาสัมพันธ์',
                    customClass: { popup: 'rounded-4 shadow' }
                });
                titleInput.focus();
                return;
            }

            const filesInput = document.getElementById('news_files');
            const files = filesInput.files;
            const hasUploads = (files && files.length > 0);

            const formData = new FormData(newsForm);
            formData.delete('files[]');

            Swal.fire({
                title: hasUploads ? 'กำลังบันทึกข้อมูลและเตรียมอัปโหลด...' : 'กำลังบันทึกข้อมูล...',
                html: hasUploads ? `
                    <div id="uploadStatusText" class="mb-3 text-muted">กำลังเตรียมส่งข้อมูล...</div>
                    <div class="progress" style="height: 20px; border-radius: 10px;">
                        <div id="uploadProgressBar" class="progress-bar progress-bar-striped progress-bar-animated bg-primary" role="progressbar" style="width: 0%;" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100">0%</div>
                    </div>
                ` : 'กรุณารอสักครู่ ระบบกำลังประมวลผล...',
                allowOutsideClick: false,
                showConfirmButton: false,
                didOpen: () => {
                    if (!hasUploads) {
                        Swal.showLoading();
                    }
                }
            });

            let newlyCreatedNewsId = null;
            const isNewNews = !document.getElementById('news_id').value;

            try {
                // Save metadata
                const saveRes = await fetch(newsForm.action, {
                    method: 'POST',
                    body: formData,
                    headers: { 'X-Requested-With': 'XMLHttpRequest' }
                });
                const saveData = await saveRes.json();

                if (saveData.status !== 'success') {
                    throw new Error(saveData.message || 'บันทึกข้อมูลไม่สำเร็จ');
                }

                if (isNewNews && saveData.news_id) {
                    newlyCreatedNewsId = saveData.news_id;
                }

                const csrfInput = document.querySelector('input[name="csrf_token_name"]');
                if (saveData.csrfToken && csrfInput) {
                    csrfInput.value = saveData.csrfToken;
                }

                if (hasUploads) {
                    const newsId = saveData.news_id;
                    const chunkSize = 512 * 1024; // 512KB (เพื่อป้องกันไม่ให้เกิน Limit Default ของ Nginx ที่ตั้งไว้ 1MB)
                    let totalBytes = 0;
                    for (let i = 0; i < files.length; i++) totalBytes += files[i].size;
                    let uploadedBytes = 0;

                    const statusText = document.getElementById('uploadStatusText');
                    const progressBar = document.getElementById('uploadProgressBar');

                    for (let i = 0; i < files.length; i++) {
                        const file = files[i];
                        const totalChunks = Math.ceil(file.size / chunkSize);
                        
                        for (let chunkIndex = 0; chunkIndex < totalChunks; chunkIndex++) {
                            const start = chunkIndex * chunkSize;
                            const end = Math.min(start + chunkSize, file.size);
                            const chunk = file.slice(start, end);

                            const chunkData = new FormData();
                            chunkData.append('news_id', newsId);
                            chunkData.append('file_name', file.name);
                            chunkData.append('chunk_index', chunkIndex);
                            chunkData.append('total_chunks', totalChunks);
                            chunkData.append('chunk', chunk, file.name);
                            const csrfInput = document.querySelector('input[name="csrf_token_name"]');
                            if (csrfInput) {
                                chunkData.append(csrfInput.name, csrfInput.value);
                            }

                            if (statusText) statusText.textContent = `กำลังอัปโหลดไฟล์ที่ ${i + 1} จาก ${files.length} (${file.name})`;

                            const chunkRes = await fetch('<?= base_url('Admin/News/UploadChunk') ?>', {
                                method: 'POST',
                                body: chunkData,
                                headers: { 'X-Requested-With': 'XMLHttpRequest' }
                            });
                            
                            const chunkResult = await chunkRes.json();
                            
                            // Update CSRF token for next chunk if rotated
                            if (chunkResult.csrfToken) {
                                if (csrfInput) csrfInput.value = chunkResult.csrfToken;
                            }

                            if (chunkResult.status !== 'success') {
                                throw new Error(chunkResult.message || `ไม่สามารถอัปโหลดชิ้นส่วนของไฟล์ ${file.name} ได้`);
                            }

                            uploadedBytes += (end - start);
                            const percent = Math.round((uploadedBytes / totalBytes) * 100);
                            if (progressBar) {
                                progressBar.style.width = percent + '%';
                                progressBar.setAttribute('aria-valuenow', percent);
                                progressBar.textContent = percent + '%';
                            }
                            
                            // หน่วงเวลา 500ms ระหว่างแต่ละ Chunk เพื่อป้องกัน Cloudflare ตัด Connection
                            if (chunkIndex < totalChunks - 1) {
                                await new Promise(resolve => setTimeout(resolve, 500));
                            }
                        }
                    }
                }
                
                // Done
                window.location.href = '<?= base_url('Admin/News') ?>';
            } catch (err) {
                // Rollback if new news was created but upload failed
                if (newlyCreatedNewsId) {
                    try {
                        await fetch('<?= base_url('Admin/News/Delete') ?>/' + newlyCreatedNewsId, {
                            method: 'GET',
                            headers: { 'X-Requested-With': 'XMLHttpRequest' }
                        });
                    } catch(e) {
                        console.error('Rollback failed:', e);
                    }
                }

                Swal.fire({
                    icon: 'error',
                    title: 'เกิดข้อผิดพลาด',
                    text: err.message || 'ไม่สามารถดำเนินการได้',
                    customClass: { popup: 'rounded-4 shadow' }
                });
            }
        });
    }

    // Flatpickr Localization & Buddhist Year
    window.addEventListener('load', function () {
        if (typeof flatpickr !== 'undefined') {
            flatpickr.localize(flatpickr.l10ns.th);

            const fpConfig = {
                locale: 'th',
                enableTime: true,
                time_24hr: true,
                dateFormat: 'Y-m-d H:i',
                altInput: true,
                altFormat: 'd/m/Y H:i',
                allowInput: true,
                onReady: function (selectedDates, dateStr, instance) {
                    setTimeout(function () {
                        if (instance.currentYearElement) {
                            const y = parseInt(instance.currentYearElement.value, 10);
                            if (y < 2500) instance.currentYearElement.value = y + 543;
                        }
                        updateNewsBuddhistDisplay(instance);
                    }, 5);
                },
                onOpen: function (selectedDates, dateStr, instance) {
                    setTimeout(function () {
                        if (instance.currentYearElement) {
                            const y = parseInt(instance.currentYearElement.value, 10);
                            if (y < 2500) instance.currentYearElement.value = y + 543;
                        }
                    }, 5);
                },
                onMonthChange: function (selectedDates, dateStr, instance) {
                    setTimeout(function () {
                        if (instance.currentYearElement) {
                            const y = parseInt(instance.currentYearElement.value, 10);
                            if (y < 2500) instance.currentYearElement.value = y + 543;
                        }
                    }, 5);
                },
                onYearChange: function (selectedDates, dateStr, instance) {
                    setTimeout(function () {
                        if (instance.currentYearElement) {
                            const y = parseInt(instance.currentYearElement.value, 10);
                            if (y < 2500) instance.currentYearElement.value = y + 543;
                        }
                    }, 5);
                },
                onChange: function (selectedDates, dateStr, instance) {
                    updateNewsBuddhistDisplay(instance);
                }
            };

            function updateNewsBuddhistDisplay(instance) {
                if (!instance.altInput || !instance.selectedDates.length) return;
                const d = instance.selectedDates[0];
                const day = String(d.getDate()).padStart(2, '0');
                const month = String(d.getMonth() + 1).padStart(2, '0');
                const year = d.getFullYear() + 543;
                const hour = String(d.getHours()).padStart(2, '0');
                const minute = String(d.getMinutes()).padStart(2, '0');
                instance.altInput.value = day + '/' + month + '/' + year + ' ' + hour + ':' + minute;
            }

            document.querySelectorAll('.news-datetime').forEach(function (el) {
                flatpickr(el, fpConfig);
            });
        }
    });

    // Client-side search
    const search = document.getElementById('newsSearch');
    if (search) {
        search.addEventListener('input', function () {
            const query = this.value.trim().toLowerCase();
            document.querySelectorAll('#adminNewsTable tbody tr[data-search]').forEach(function (row) {
                row.style.display = (row.dataset.search || '').includes(query) ? '' : 'none';
            });
        });
    }
})();
</script>
