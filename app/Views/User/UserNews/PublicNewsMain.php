<div class="layout-page">
    <?= view('User/UserLeyout/UserNavbar') ?>
    <div class="content-wrapper">
        <div class="container-xxl flex-grow-1 container-p-y public-news-page">
            <div class="d-flex justify-content-between align-items-end gap-3 mb-4">
                <div>
                    <div class="text-uppercase small fw-semibold text-primary mb-1">SKJ BUDGET & PLANNING</div>
                    <h4 class="fw-bold mb-1"><i class="bx bx-file text-primary me-2"></i>ประชาสัมพันธ์และเอกสารเผยแพร่</h4>
                    <p class="text-muted mb-0">ข่าวสาร ประกาศ คำสั่ง และไฟล์เอกสารดาวน์โหลดจากงานงบประมาณและแผน</p>
                </div>
                <span class="badge bg-label-primary d-none d-md-inline-flex align-items-center py-2 px-3">
                    <i class="bx bx-folder-open me-1"></i>เอกสารเผยแพร่
                </span>
            </div>

            <form class="card public-news-toolbar mb-4 border-0 shadow-sm" method="get" action="<?= base_url('News') ?>">
                <div class="card-body p-3 p-md-4">
                    <div class="row g-3 align-items-end">
                        <div class="col-lg-6">
                            <label class="form-label small fw-semibold">ค้นหาข้อมูล</label>
                            <div class="input-group">
                                <span class="input-group-text bg-transparent"><i class="bx bx-search"></i></span>
                                <input class="form-control" name="q" value="<?= esc($keyword) ?>" placeholder="ค้นหาหัวข้อหรือเนื้อหาเอกสาร...">
                            </div>
                        </div>
                        <div class="col-lg-4">
                            <label class="form-label small fw-semibold">หมวดหมู่</label>
                            <select class="form-select" name="category">
                                <option value="">ทุกประเภท</option>
                                <?php foreach ($categories as $cat): ?>
                                    <option value="<?= esc($cat->category) ?>" <?= $category === $cat->category ? 'selected' : '' ?>>
                                        <?= esc($cat->category) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-lg-2 d-grid">
                            <button class="btn btn-primary rounded-pill"><i class="bx bx-search me-1"></i>ค้นหา</button>
                        </div>
                    </div>
                </div>
            </form>

            <div class="news-list-container">
                <?php 
                    // Group news by category
                    $groupedNews = [];
                    if (!empty($news)) {
                        foreach ($news as $item) {
                            $cat = !empty($item->category) ? trim($item->category) : 'ไม่มีหมวดหมู่';
                            $groupedNews[$cat][] = $item;
                        }
                    }
                ?>
                
                <?php if (empty($groupedNews)): ?>
                    <div class="card border-0 shadow-sm">
                        <div class="card-body text-center py-5">
                            <i class="bx bx-file-blank display-4 text-muted opacity-50"></i>
                            <h5 class="mt-3 mb-1">ยังไม่มีเอกสารที่เผยแพร่</h5>
                            <p class="text-muted mb-0">เมื่อมีเอกสารหรือประกาศเผยแพร่ จะแสดงรายการในหน้านี้</p>
                        </div>
                    </div>
                <?php else: foreach ($groupedNews as $categoryName => $itemsInCategory): ?>
                    <div class="news-category-group mb-5">
                        <h4 class="fw-bold mb-4 border-bottom pb-2 text-primary">
                            <i class="bx bx-category me-2"></i><?= esc($categoryName) ?>
                        </h4>
                        
                        <div class="row g-3 ms-0 ms-md-2">
                            <?php foreach ($itemsInCategory as $item): ?>
                                <?php $itemFiles = $filesByNews[(int) $item->id] ?? []; ?>
                                <?php if (!empty($itemFiles)): ?>
                                    <?php foreach ($itemFiles as $file): ?>
                                        <?php 
                                            $ti = $file->type_info; 
                                            $bgClass = (strtolower($ti['ext']) === 'pdf') ? 'bg-danger' : 'bg-primary';
                                        ?>
                                        <div class="col-md-6 col-lg-4">
                                            <a href="<?= base_url('News/Download/' . $file->id) ?>" target="_blank" class="card border border-light shadow-sm h-100 text-decoration-none btn-download-file transition-all" style="border-radius: 12px; border-color: #e2e8f0 !important; background-color: #fff;">
                                                <div class="card-body p-3 d-flex align-items-center">
                                                    <div class="d-flex align-items-center justify-content-center text-white rounded me-3 <?= $bgClass ?>" style="width: 48px; height: 48px; flex-shrink: 0; border-radius: 10px !important;">
                                                        <i class="<?= esc($ti['icon']) ?> fs-3"></i>
                                                    </div>
                                                    <div class="flex-grow-1 min-w-0">
                                                        <div class="text-dark fw-bold text-truncate mb-1" style="font-size: 0.95rem;">
                                                            <?= esc($item->title) ?>
                                                        </div>
                                                        <div class="text-muted small text-truncate">
                                                            <?= !empty($item->content) ? esc(strip_tags((string) $item->content)) : 'คลิกเพื่อเปิดอ่านเอกสาร' ?>
                                                        </div>
                                                    </div>
                                                    <div class="ms-2 text-muted download-icon-container">
                                                        <i class="bx bx-chevron-right fs-4"></i>
                                                    </div>
                                                </div>
                                            </a>
                                        </div>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endforeach; endif; ?>
            </div>

        </div>
    </div>
</div>

<style>
.transition-all {
    transition: all 0.2s ease-in-out;
}
.btn-download-file:hover {
    background-color: #f8fafc;
    border-color: #cbd5e1 !important;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const downloadBtns = document.querySelectorAll('.btn-download-file');
    downloadBtns.forEach(btn => {
        btn.addEventListener('click', function(e) {
            if (this.classList.contains('disabled')) {
                e.preventDefault();
                return;
            }
            
            this.classList.add('disabled', 'pe-none');
            
            const iconContainer = this.querySelector('.download-icon-container');
            const originalIconHtml = iconContainer.innerHTML;
            
            iconContainer.innerHTML = '<span class="spinner-border spinner-border-sm text-primary" role="status" aria-hidden="true"></span>';
            
            setTimeout(() => {
                this.classList.remove('disabled', 'pe-none');
                iconContainer.innerHTML = originalIconHtml;
            }, 3000);
        });
    });
});
</script>