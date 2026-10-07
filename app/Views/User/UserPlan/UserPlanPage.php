<!-- Layout container -->
<div class="layout-page">
    <?php echo view('User/UserLeyout/UserNavbar'); ?>

    <div class="content-wrapper">
        <div class="container-xxl flex-grow-1 container-p-y">
            
            <!-- Hero / Header Section -->
            <div class="row mb-4 mt-2 animate-fade-in-up">
                <div class="col-12">
                    <div class="card border-0 rounded-4 overflow-hidden shadow-sm" style="background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%); color: #ffffff; position: relative;">
                        <!-- Background Glow Effect -->
                        <div style="position: absolute; right: -50px; top: -50px; width: 300px; height: 300px; background: radial-gradient(circle, rgba(2, 132, 199, 0.25) 0%, rgba(0,0,0,0) 70%); filter: blur(40px); pointer-events: none;"></div>

                        <div class="card-body p-4 p-md-5 position-relative z-index-1">
                            <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
                                <div>
                                    <div class="d-flex align-items-center gap-2 mb-2">
                                        <span class="badge rounded-pill px-3 py-1.5 fw-bold" style="background: #0284c7; color: #ffffff; font-size: 0.82rem; letter-spacing: 0.5px;">
                                            <i class="bx bx-file-blank me-1"></i>PLAN & DOCUMENTS
                                        </span>
                                    </div>
                                    <h2 class="fw-bolder mb-2 text-white" style="font-family: 'Plus Jakarta Sans', 'K2D', sans-serif;">
                                        แผนและโครงการ
                                    </h2>
                                    <p class="text-light mb-0 fs-6 fw-normal" style="max-width: 650px; line-height: 1.6; opacity: 0.9;">
                                        รวบรวมเอกสารแผนปฏิบัติการประจำปี ประกาศ และไฟล์เผยแพร่ที่เกี่ยวข้องกับงานแผนและโครงการ
                                    </p>
                                </div>

                                <div class="d-flex align-items-center gap-2 flex-wrap">
                                    <a href="<?= base_url('News?category=' . urlencode('แผนงานและโครงการ')) ?>" class="btn btn-outline-light btn-sm rounded-pill px-3 fw-bold shadow-sm">
                                        <i class="bx bx-news me-1"></i> หน้าประชาสัมพันธ์ทั้งหมด
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Published Plan Documents & News from Admin Section -->
            <div class="row mb-5 animate-fade-in-up">
                <div class="col-12 mb-3 d-flex align-items-center justify-content-between flex-wrap gap-2">
                    <div>
                        <h4 class="fw-bolder text-dark mb-1" style="font-family: 'Plus Jakarta Sans', 'K2D', sans-serif;">
                            <i class="bx bx-book-open text-primary me-2"></i>เอกสารและประกาศแผนงานและโครงการ
                        </h4>
                        <p class="text-secondary fs-7 mb-0 fw-medium">เอกสารเผยแพร่ แผนปฏิบัติการประจำปี และไฟล์แนบที่เกี่ยวข้อง</p>
                    </div>
                    <?php if (!empty($plan_news)): ?>
                    <a href="<?= base_url('News?category=' . urlencode('แผนงานและโครงการ')) ?>" class="btn btn-sm btn-outline-primary rounded-pill px-3 fw-bold">
                        <i class="bx bx-news me-1"></i> ดูข่าวสารและเอกสารทั้งหมด
                    </a>
                    <?php endif; ?>
                </div>

                <div class="col-12">
                    <?php if (empty($plan_news) && empty($plan_files)): ?>
                        <div class="card border-0 shadow-sm rounded-4 p-5 text-center bg-white" style="border: 2px dashed #cbd5e1 !important;">
                            <i class="bx bx-file-blank text-secondary opacity-50 display-3 mb-3"></i>
                            <h5 class="fw-bolder text-dark mb-1">ยังไม่มีเอกสารแผนงานและโครงการที่เผยแพร่</h5>
                            <p class="text-secondary small mb-0 fw-medium">เมื่อมีเอกสารหรือประกาศใหม่จากผู้ดูแลระบบ จะปรากฏให้ดาวน์โหลดในหน้านี้</p>
                        </div>
                    <?php else: ?>
                        <div class="row g-3">
                            <!-- Admin News / Uploaded Files -->
                            <?php if (!empty($plan_news)): ?>
                                <?php foreach ($plan_news as $pNews): ?>
                                    <?php $attachedFiles = $plan_news_files[(int)$pNews->id] ?? []; ?>
                                    <?php if (!empty($attachedFiles)): ?>
                                        <?php foreach ($attachedFiles as $f): ?>
                                            <?php 
                                                $ti = $f->type_info ?? get_file_type_info($f->original_name);
                                                $bgClass = (strtolower($ti['ext'] ?? '') === 'pdf') ? 'bg-danger' : 'bg-primary';
                                            ?>
                                            <div class="col-md-6 col-lg-4">
                                                <a href="<?= base_url('News/Download/' . $f->id); ?>" target="_blank" class="card border-0 shadow-sm p-3 rounded-3 h-100 bg-white doc-hover-card text-decoration-none transition-all" style="border: 2px solid #cbd5e1 !important;">
                                                    <div class="d-flex align-items-center gap-3">
                                                        <div class="avatar <?= $bgClass ?> text-white rounded-3 p-2 d-flex align-items-center justify-content-center" style="width: 48px; height: 48px; flex-shrink: 0; border-radius: 12px !important;">
                                                            <i class="<?= esc($ti['icon'] ?? 'bx-file') ?> fs-2"></i>
                                                        </div>
                                                        <div class="flex-grow-1 overflow-hidden">
                                                            <div class="d-flex align-items-center gap-1 mb-1">
                                                                <span class="badge bg-label-primary rounded-pill fs-8 py-0.5 px-2">
                                                                    <?= esc($pNews->category) ?>
                                                                </span>
                                                                <span class="badge bg-label-secondary rounded-pill fs-8 py-0.5 px-2">
                                                                    <?= esc($ti['ext'] ?? 'FILE') ?>
                                                                </span>
                                                            </div>
                                                            <h6 class="fw-bolder text-dark mb-0 text-truncate" style="font-size: 0.95rem; font-family: 'K2D', sans-serif;" title="<?= esc($pNews->title); ?>">
                                                                <?= esc($pNews->title); ?>
                                                            </h6>
                                                            <span class="text-secondary fs-8 fw-medium text-truncate d-block mt-0.5">
                                                                <?= esc($f->original_name); ?>
                                                            </span>
                                                        </div>
                                                        <i class="bx bx-download text-primary fs-3 ms-1"></i>
                                                    </div>
                                                </a>
                                            </div>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <div class="col-md-6 col-lg-4">
                                            <a href="<?= base_url('News/View/' . $pNews->id); ?>" class="card border-0 shadow-sm p-3 rounded-3 h-100 bg-white doc-hover-card text-decoration-none transition-all" style="border: 2px solid #cbd5e1 !important;">
                                                <div class="d-flex align-items-center gap-3">
                                                    <div class="avatar bg-info text-white rounded-3 p-2 d-flex align-items-center justify-content-center" style="width: 48px; height: 48px; flex-shrink: 0; border-radius: 12px !important;">
                                                        <i class="bx bx-news fs-2"></i>
                                                    </div>
                                                    <div class="flex-grow-1 overflow-hidden">
                                                        <div class="d-flex align-items-center gap-1 mb-1">
                                                            <span class="badge bg-label-primary rounded-pill fs-8 py-0.5 px-2">
                                                                <?= esc($pNews->category) ?>
                                                            </span>
                                                        </div>
                                                        <h6 class="fw-bolder text-dark mb-0 text-truncate" style="font-size: 0.95rem; font-family: 'K2D', sans-serif;" title="<?= esc($pNews->title); ?>">
                                                            <?= esc($pNews->title); ?>
                                                        </h6>
                                                        <span class="text-secondary fs-8 fw-semibold">คลิกเพื่ออ่านรายละเอียด</span>
                                                    </div>
                                                    <i class="bx bx-chevron-right text-dark fs-4 ms-1"></i>
                                                </div>
                                            </a>
                                        </div>
                                    <?php endif; ?>
                                <?php endforeach; ?>
                            <?php endif; ?>

                            <!-- Local Plan Files Fallback -->
                            <?php if (!empty($plan_files)): ?>
                                <?php foreach ($plan_files as $file): ?>
                                    <div class="col-md-6 col-lg-4">
                                        <a href="<?= base_url('uploads/plan/' . $file); ?>" target="_blank" class="card border-0 shadow-sm p-3 rounded-3 h-100 bg-white doc-hover-card text-decoration-none transition-all" style="border: 2px solid #cbd5e1 !important;">
                                            <div class="d-flex align-items-center gap-3">
                                                <div class="avatar bg-danger text-white rounded-3 p-2 d-flex align-items-center justify-content-center" style="width: 48px; height: 48px; flex-shrink: 0; border-radius: 12px !important;">
                                                    <i class="bx bxs-file-pdf fs-2"></i>
                                                </div>
                                                <div class="flex-grow-1 overflow-hidden">
                                                    <div class="d-flex align-items-center gap-1 mb-1">
                                                        <span class="badge bg-label-danger rounded-pill fs-8 py-0.5 px-2">
                                                            เอกสารเล่มแผน
                                                        </span>
                                                        <span class="badge bg-label-secondary rounded-pill fs-8 py-0.5 px-2">PDF</span>
                                                    </div>
                                                    <h6 class="fw-bolder text-dark mb-0 text-truncate" style="font-size: 0.95rem; font-family: 'K2D', sans-serif;" title="<?= esc($file); ?>">
                                                        <?= esc($file); ?>
                                                    </h6>
                                                    <span class="text-secondary fs-8 fw-semibold">คลิกเพื่อเปิดอ่านเอกสาร</span>
                                                </div>
                                                <i class="bx bx-download text-danger fs-3 ms-1"></i>
                                            </div>
                                        </a>
                                    </div>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

        </div>
    </div>
</div>

<style>
    .doc-hover-card {
        transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1);
    }
    .doc-hover-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 12px 24px rgba(0,0,0,0.08) !important;
        border-color: #0284c7 !important;
    }
    .bg-primary-soft { background-color: rgba(2, 132, 199, 0.12); }
    .bg-warning-soft { background-color: rgba(217, 119, 6, 0.12); }
    .bg-success-soft { background-color: rgba(22, 163, 74, 0.12); }
    .bg-danger-soft  { background-color: rgba(220, 38, 38, 0.12); }
    .fs-8 { font-size: 0.8rem; }
    .fs-7 { font-size: 0.9rem; }
</style>
