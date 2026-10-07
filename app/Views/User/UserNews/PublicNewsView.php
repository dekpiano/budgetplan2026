<div class="layout-page">
    <?= view('User/UserLeyout/UserNavbar') ?>
    <div class="content-wrapper">
        <div class="container-xxl flex-grow-1 container-p-y public-news-detail">
            <div class="mb-3">
                <a href="<?= base_url('News') ?>" class="btn btn-sm btn-outline-secondary rounded-pill">
                    <i class="bx bx-arrow-back me-1"></i>กลับหน้ารวมเอกสารประชาสัมพันธ์
                </a>
            </div>
            <article class="card border-0 shadow-sm">
                <div class="card-body p-4 p-lg-5">
                    <div class="d-flex flex-wrap gap-2 align-items-center mb-3">
                        <span class="badge bg-label-primary rounded-pill"><?= esc($news->category) ?></span>
                        <span class="text-muted small">
                            <i class="bx bx-calendar me-1"></i>
                            <?= !empty($news->published_at) ? date('d/m/Y H:i', strtotime($news->published_at)) : '-' ?>
                        </span>
                        <span class="text-muted small">
                            <i class="bx bx-show me-1"></i><?= number_format((int) $news->view_count) ?> ครั้ง
                        </span>
                    </div>

                    <h3 class="fw-bold mb-4 text-dark"><?= esc($news->title) ?></h3>

                    <?php if (!empty($news->content)): ?>
                        <div class="news-content text-dark mb-4" style="white-space: pre-line; line-height: 1.8;">
                            <?= esc((string) $news->content) ?>
                        </div>
                    <?php endif; ?>

                    <?php if (!empty($news->files)): ?>
                        <hr class="my-4">
                        <div class="d-flex align-items-center justify-content-between mb-3">
                            <h5 class="mb-0 fw-bold text-dark">
                                <i class="bx bx-paperclip text-primary me-2"></i>ไฟล์เอกสารแนบสำหรับดาวน์โหลด
                            </h5>
                            <span class="badge bg-label-info rounded-pill"><?= count($news->files) ?> ไฟล์</span>
                        </div>
                        <div class="d-grid gap-2">
                            <?php foreach ($news->files as $file): ?>
                                <?php
                                    $ti = $file->type_info ?? get_file_type_info($file->original_name);
                                ?>
                                <a href="<?= base_url('News/Download/' . $file->id) ?>"
                                   class="public-news-file d-flex align-items-center justify-content-between gap-3 p-3 rounded border text-decoration-none bg-white shadow-xs">
                                    <div class="d-flex align-items-center gap-3 min-w-0">
                                        <div class="avatar flex-shrink-0">
                                            <span class="avatar-initial rounded <?= esc($ti['badge_class']) ?>">
                                                <i class="<?= esc($ti['icon']) ?> fs-3"></i>
                                            </span>
                                        </div>
                                        <div class="min-w-0">
                                            <div class="text-truncate fw-semibold text-dark"><?= esc($file->original_name) ?></div>
                                            <div class="d-flex align-items-center gap-2 small text-muted mt-1">
                                                <span class="badge <?= esc($ti['badge_class']) ?> rounded-pill px-2 py-0">
                                                    <?= esc($ti['ext']) ?>
                                                </span>
                                                <span><?= number_format($file->file_size / 1048576, 2) ?> MB</span>
                                                <?php if (!empty($file->download_count)): ?>
                                                    <span>· ดาวน์โหลดแล้ว <?= number_format((int) $file->download_count) ?> ครั้ง</span>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </div>
                                    <span class="btn btn-sm btn-primary rounded-pill px-3 flex-shrink-0">
                                        <i class="bx bx-download me-1"></i>ดาวน์โหลด
                                    </span>
                                </a>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </article>
        </div>
    </div>
</div>