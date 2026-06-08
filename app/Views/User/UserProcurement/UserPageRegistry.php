<!-- Layout container -->
<div class="layout-page">
    <?php echo view('User/UserLeyout/UserNavbar'); ?>

    <!-- Content wrapper -->
    <div class="content-wrapper">
        <!-- Content -->
        <div class="container-xxl flex-grow-1 container-p-y">

            <!-- Title Header -->
            <div class="row mb-4">
                <div class="col-12">
                    <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
                        <div>
                            <h4 class="fw-bold mb-1" style="font-family: 'Plus Jakarta Sans', 'Sarabun', sans-serif;"><i class="bx <?= $type_slug === 'hire' ? 'bx-spreadsheet' : 'bx-cart-alt' ?> text-warning me-2"></i> ทะเบียนคุมใบ<?= esc($order_type); ?></h4>
                            <p class="text-muted fs-7 mb-0">ข้อมูลทะเบียนคุมใบ<?= esc($order_type); ?>ของระบบงบประมาณและแผนงาน</p>
                        </div>
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb mb-0 bg-transparent p-0">
                                <li class="breadcrumb-item"><a href="<?= base_url(); ?>" class="text-warning">หน้าแรก</a></li>
                                <li class="breadcrumb-item active text-secondary" aria-current="page">ทะเบียนคุม (ดูข้อมูล)</li>
                            </ol>
                        </nav>
                    </div>
                </div>
            </div>


            <!-- Registry Datatable Card -->
            <div class="row">
                <div class="col-12">
                    <div class="card border-0 shadow-sm" style="border-radius: 20px;">
                        <div class="card-header bg-white border-0 pt-4 px-4 d-flex align-items-center justify-content-between flex-wrap gap-3">
                            <div>
                                <h5 class="mb-1" style="font-weight: 700; color: var(--luxury-dark);"><i class="bx bx-list-ul me-2 text-warning"></i>รายการทะเบียนคุม</h5>
                                <p class="text-muted fs-8 mb-0">ค้นหาและดูข้อมูลรายการใบสั่งจ้างและสั่งซื้อทั้งหมด</p>
                            </div>
                            <div class="d-flex gap-2 align-items-center flex-wrap">
                                <button type="button" class="btn btn-outline-success btn-md rounded-pill shadow-sm" id="btnExportExcel">
                                    <i class="bx bxs-file-export me-1"></i> ดาวน์โหลด Excel
                                </button>
                            </div>
                        </div>
                        <div class="card-body px-4 pb-4">
                            <div class="table-responsive">
                                <table id="orderTable" class="table table-hover align-middle" style="width:100%">
                                    <thead>
                                        <tr class="table-light text-secondary">
                                            <th class="text-center" style="width: 50px;">ลำดับ</th>
                                            <th class="text-center">เลขที่</th>
                                            <th class="text-center">วัน เดือน ปี</th>
                                            <th>รายการ</th>
                                            <th class="text-end">จำนวนเงิน (บาท)</th>
                                            <th>ผู้ลงนามสั่งจ้าง</th>
                                            <th>ผู้รับจ้าง</th>
                                            <th class="text-center">กำหนดส่งมอบ</th>
                                            <th>หมายเหตุ</th>
                                            <th class="text-center">ตรวจรับ</th>
                                            <th class="text-center">ลงระบบ/ฎีกา</th>
                                            <th class="text-center">รูปภาพ</th>
                                            <th class="text-center" style="width: 70px;">พิมพ์</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if (!empty($orders)): ?>
                                            <?php foreach ($orders as $index => $order): ?>
                                                <tr>
                                                    <td class="text-center"><?= $index + 1; ?></td>
                                                    <td class="text-center fw-bold text-dark"><?= esc($order->order_number); ?></td>
                                                    <td class="text-center" data-order="<?= $order->order_date; ?>">
                                                        <?php 
                                                            $d = date_create($order->order_date);
                                                            echo date_format($d, "d/m/") . (intval(date_format($d, "Y")) + 543);
                                                        ?>
                                                    </td>
                                                    <td style="max-width: 250px; white-space: normal; word-wrap: break-word;">
                                                        <?= esc($order->description); ?>
                                                    </td>
                                                    <td class="text-end fw-bold text-orange">
                                                        <?= number_format($order->amount, 2); ?>
                                                    </td>
                                                    <td><?= esc($order->signatory); ?></td>
                                                    <td><?= esc($order->contractor); ?></td>
                                                    <td class="text-center">
                                                        <?php if ($order->delivery_date): ?>
                                                            <?php 
                                                                $dd = date_create($order->delivery_date);
                                                                echo date_format($dd, "d/m/") . (intval(date_format($dd, "Y")) + 543);
                                                            ?>
                                                        <?php else: ?>
                                                            <span class="text-muted">-</span>
                                                        <?php endif; ?>
                                                    </td>
                                                    <td style="max-width: 150px; white-space: normal; word-wrap: break-word;">
                                                        <?= esc($order->remarks) ?: '<span class="text-muted">-</span>'; ?>
                                                    </td>
                                                    <td class="text-center">
                                                        <?php if (!empty($order->inspection_status) && $order->inspection_status !== 'ยังไม่ตรวจรับ' && $order->inspection_status !== 'ตรวจรับแล้ว'): ?>
                                                            <span class="badge bg-success-soft text-success px-2 py-1 rounded-pill" title="ผู้ตรวจรับ: <?= esc($order->inspection_status); ?>">
                                                                <i class="bx bx-user me-1"></i><?= esc($order->inspection_status); ?>
                                                            </span>
                                                        <?php elseif ($order->inspection_status === 'ตรวจรับแล้ว'): ?>
                                                            <span class="badge bg-success-soft text-success px-2 py-1 rounded-pill"><i class="bx bx-check me-1"></i>ตรวจรับแล้ว</span>
                                                        <?php else: ?>
                                                            <span class="badge bg-warning-soft text-warning px-2 py-1 rounded-pill">ยังไม่ตรวจรับ</span>
                                                        <?php endif; ?>
                                                    </td>
                                                    <td class="text-center">
                                                        <?php if ($order->system_sent_status == 'ส่งลงระบบ/ทำฎีกาแล้ว'): ?>
                                                            <span class="badge bg-success px-2 py-1 rounded-pill">ทำฎีกาแล้ว</span>
                                                        <?php else: ?>
                                                            <span class="badge bg-secondary px-2 py-1 rounded-pill">ยังไม่ส่ง</span>
                                                        <?php endif; ?>
                                                    </td>
                                                    <td class="text-center">
                                                        <?php 
                                                            $imgs = $order->images ? json_decode($order->images, true) : [];
                                                            if (count($imgs) > 0): 
                                                        ?>
                                                            <button type="button" class="btn btn-sm btn-outline-warning rounded-circle p-1 position-relative" onclick="openGallery(<?= htmlspecialchars(json_encode($imgs), ENT_QUOTES, 'UTF-8'); ?>)">
                                                                <i class="bx bx-image-alt" style="font-size: 1.15rem;"></i>
                                                                 <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger fs-9" style="padding: 0.25em 0.5em; min-width: 1.5em; height: 1.5em; display: flex; align-items: center; justify-content: center;"><?= count($imgs); ?></span>
                                                            </button>
                                                        <?php else: ?>
                                                            <span class="text-muted">-</span>
                                                        <?php endif; ?>
                                                    </td>
                                                    <td class="text-center">
                                                        <a href="<?= base_url('User/Registry/PrintOrder/' . $order->id); ?>" target="_blank" class="btn btn-sm btn-icon btn-outline-info rounded-circle" title="พิมพ์รายงาน">
                                                            <i class="bx bx-printer"></i>
                                                        </a>
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

        </div>
        <!-- / Content -->

        <!-- Lightbox Gallery Modal -->
        <div class="modal fade" id="galleryModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-centered">
                <div class="modal-content bg-transparent border-0 shadow-none">
                    <div class="modal-header border-0 p-0 position-absolute w-100 justify-content-end z-index-1" style="top: -40px; right: 0;">
                        <button type="button" class="btn-close btn-close-white fs-4" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body p-0 text-center">
                        <div id="galleryCarousel" class="carousel slide" data-bs-ride="carousel">
                            <div class="carousel-inner" id="galleryCarouselInner" style="border-radius: 12px; overflow: hidden; background: rgba(0,0,0,0.95); min-height: 300px; display: block;">
                                <!-- images will be injected here -->
                            </div>
                            <button class="carousel-control-prev" type="button" data-bs-target="#galleryCarousel" data-bs-slide="prev">
                                <span class="carousel-control-prev-icon bg-dark rounded-circle p-3" aria-hidden="true"></span>
                                <span class="visually-hidden">ก่อนหน้า</span>
                            </button>
                            <button class="carousel-control-next" type="button" data-bs-target="#galleryCarousel" data-bs-slide="next">
                                <span class="carousel-control-next-icon bg-dark rounded-circle p-3" aria-hidden="true"></span>
                                <span class="visually-hidden">ถัดไป</span>
                            </button>
                        </div>
                        <div id="galleryIndicator" class="text-white mt-2 fs-7 fw-semibold"></div>
                    </div>
                </div>
            </div>
        </div>

        <div class="content-backdrop fade"></div>
    </div>
    <!-- Content wrapper -->
</div>
<!-- / Layout page -->

<style>
    :root {
        --primary-orange: #ff6b35;
        --dark-orange: #d9480f;
        --soft-orange: #fff4e6;
        --luxury-dark: #1e1e24;
    }

    body {
        background-color: #f7f6f3 !important;
    }

    .text-orange {
        color: var(--primary-orange) !important;
    }
    .bg-orange-soft {
        background-color: var(--soft-orange) !important;
    }

    .bg-success-soft { background-color: #e6fcf5 !important; }
    .bg-warning-soft { background-color: #fff9db !important; }
    .bg-info-soft { background-color: #e7f5ff !important; }

    .table > :not(caption) > * > * {
        padding: 0.75rem 1rem;
    }
    .dataTables_wrapper .dataTables_paginate .paginate_button {
        padding: 0.3rem 0.6rem !important;
        border-radius: 8px !important;
    }
    .dataTables_wrapper .dataTables_paginate .paginate_button.current {
        background: var(--primary-orange) !important;
        color: #fff !important;
        border: none !important;
    }
</style>

<!-- DataTables Buttons CSS -->
<link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.3.6/css/buttons.bootstrap5.min.css">

<!-- Add necessary scripts for this page -->
<script>
    document.addEventListener("DOMContentLoaded", function () {
        // Initialize Datatable
        const table = $('#orderTable').DataTable({
            buttons: [
                {
                    extend: 'excelHtml5',
                    title: 'ทะเบียนคุมใบ' + '<?= esc($order_type); ?>',
                    exportOptions: {
                        columns: [0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10]
                    }
                }
            ],
            language: {
                url: 'https://cdn.datatables.net/plug-ins/1.13.4/i18n/th.json'
            },
            pageLength: 10,
            order: [[2, 'desc'], [0, 'desc']], // Order by Date and Index
            columnDefs: [
                { targets: [11, 12], orderable: false }
            ]
        });

        // Trigger Excel download
        $('#btnExportExcel').on('click', function () {
            table.button('.buttons-excel').trigger();
        });
    });

    function openGallery(images) {
        const inner = document.getElementById("galleryCarouselInner");
        const indicator = document.getElementById("galleryIndicator");
        inner.innerHTML = "";

        if (!images || images.length === 0) return;

        images.forEach((img, idx) => {
            const item = document.createElement("div");
            item.className = "carousel-item" + (idx === 0 ? " active" : "");
            const isLocal = img.startsWith('uploads/');
            const imgSrc = isLocal ? '<?= base_url(); ?>/' + img : '<?= env('upload.server.baseurl'); ?>' + img;
            item.innerHTML = `
                <img src="${imgSrc}" class="img-fluid mx-auto d-block" style="max-height: 85vh; width: auto; max-width: 100%; object-fit: contain;" alt="gallery-img">
            `;
            inner.appendChild(item);
        });

        indicator.textContent = `รูปภาพที่ 1 จาก ${images.length}`;

        const carousel = document.getElementById("galleryCarousel");
        carousel.addEventListener('slide.bs.carousel', function (e) {
            indicator.textContent = `รูปภาพที่ ${e.to + 1} จาก ${images.length}`;
        });

        const modal = new bootstrap.Modal(document.getElementById("galleryModal"));
        modal.show();
    }
</script>
