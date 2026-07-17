<!-- Layout container -->
<div class="layout-page">
    <?php echo view('User/UserLeyout/UserNavbar'); ?>

    <div class="content-wrapper">
        <div class="container-xxl flex-grow-1 container-p-y">
            
            <!-- Minimalist Page Header -->
            <div class="row mb-5 mt-4 animate-fade-in-up">
                <div class="col-12 text-center">
                    <span class="badge rounded-pill px-3 py-2 mb-3 fw-medium" style="background-color: #fff4e6; color: #FB8C00; letter-spacing: 0.5px; font-size: 0.75rem;">เอกสารอ้างอิงและคู่มือ</span>
                    <h2 class="fw-bolder mb-3 text-dark" style="font-family: 'Plus Jakarta Sans', 'K2D', sans-serif; letter-spacing: -0.5px;">แผนดำเนินงานและแผนปฏิบัติการ</h2>
                    <p class="text-muted mx-auto" style="font-family: 'K2D', sans-serif; font-size: 1.05rem; max-width: 550px; line-height: 1.6;">
                        รวบรวมเอกสารคู่มือและแผนการดำเนินงานประจำปีงบประมาณของโรงเรียน เพื่อใช้ประกอบการอ้างอิงและปฏิบัติงาน
                    </p>
                </div>
            </div>

            <!-- Documents Minimalist List -->
            <div class="row justify-content-center">
                <div class="col-lg-10 col-xl-9">
                    <?php if (!empty($plan_files)): ?>
                        <div class="row g-4">
                            <?php $delay = 0.1; foreach ($plan_files as $file): ?>
                                <div class="col-md-6 animate-fade-in-up" style="animation-delay: <?= $delay ?>s;">
                                    <a href="<?= base_url('uploads/plan/' . $file); ?>" target="_blank" class="text-decoration-none">
                                        <div class="minimal-doc-card h-100 p-4 bg-white d-flex flex-column justify-content-between">
                                            <div class="d-flex align-items-start mb-4">
                                                <div class="doc-icon-wrapper me-3">
                                                    <i class="bx bxs-file-pdf"></i>
                                                </div>
                                                <div class="doc-content flex-grow-1 pt-1">
                                                    <h6 class="fw-bold text-dark mb-1 doc-title" style="font-family: 'K2D', sans-serif; line-height: 1.5; font-size: 1.05rem;"><?= esc($file); ?></h6>
                                                    <p class="text-muted mb-0" style="font-size: 0.8rem; font-family: 'K2D', sans-serif;">PDF Document</p>
                                                </div>
                                            </div>
                                            <div class="doc-action d-flex align-items-center justify-content-between pt-3 mt-auto">
                                                <span class="action-text fw-semibold" style="font-size: 0.85rem; font-family: 'K2D', sans-serif;">เปิดดูเอกสาร</span>
                                                <div class="action-arrow">
                                                    <i class="bx bx-right-arrow-alt fs-5"></i>
                                                </div>
                                            </div>
                                        </div>
                                    </a>
                                </div>
                            <?php $delay += 0.1; endforeach; ?>
                        </div>
                    <?php else: ?>
                        <div class="text-center py-5 bg-white empty-state-box animate-fade-in-up" style="animation-delay: 0.2s;">
                            <div class="mb-3">
                                <i class="bx bx-folder text-muted" style="font-size: 3.5rem; opacity: 0.2;"></i>
                            </div>
                            <h6 class="text-dark fw-bold mb-2" style="font-family: 'K2D', sans-serif; font-size: 1.1rem;">ไม่มีข้อมูลเอกสาร</h6>
                            <p class="text-muted small mb-0" style="font-family: 'K2D', sans-serif;">ยังไม่มีการอัปโหลดไฟล์แผนปฏิบัติการในระบบ</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

        </div>
    </div>
</div>

<style>
    /* Minimalist UI Variables */
    :root {
        --min-bg: #f8fafc;
        --min-surface: #ffffff;
        --min-border: #e2e8f0;
        --min-text-main: #0f172a;
        --min-text-muted: #64748b;
        --min-accent: #FB8C00;
        --min-accent-light: #fff4e6;
    }

    body {
        background-color: var(--min-bg) !important;
    }

    /* Minimalist Card */
    .minimal-doc-card {
        border-radius: 20px;
        border: 1px solid rgba(0,0,0,0.04);
        box-shadow: 0 4px 20px rgba(0,0,0,0.02);
        transition: all 0.4s cubic-bezier(0.16, 1, 0.3, 1);
    }
    
    .minimal-doc-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 20px 40px rgba(0,0,0,0.06);
        border-color: rgba(0,0,0,0.08);
    }

    /* Icon Wrapper */
    .doc-icon-wrapper {
        width: 48px;
        height: 48px;
        background: var(--min-accent-light);
        color: var(--min-accent);
        border-radius: 14px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.6rem;
        transition: all 0.3s ease;
    }

    .minimal-doc-card:hover .doc-icon-wrapper {
        background: var(--min-accent);
        color: #ffffff;
        transform: scale(1.05);
    }

    /* Typography & Colors */
    .doc-title {
        transition: color 0.3s ease;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }

    .minimal-doc-card:hover .doc-title {
        color: var(--min-accent) !important;
    }

    /* Bottom Action Area */
    .doc-action {
        border-top: 1px dashed rgba(0,0,0,0.06);
    }

    .action-text {
        color: var(--min-text-muted);
        transition: color 0.3s ease;
    }

    .minimal-doc-card:hover .action-text {
        color: var(--min-text-main);
    }

    .action-arrow {
        width: 32px;
        height: 32px;
        border-radius: 50%;
        background: var(--min-bg);
        display: flex;
        align-items: center;
        justify-content: center;
        color: var(--min-text-muted);
        transition: all 0.3s ease;
    }

    .minimal-doc-card:hover .action-arrow {
        background: var(--min-accent);
        color: #ffffff;
        transform: translateX(4px);
    }

    /* Empty State */
    .empty-state-box {
        border-radius: 24px;
        border: 1px dashed rgba(0,0,0,0.1);
    }
</style>
