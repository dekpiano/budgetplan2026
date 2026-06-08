<!-- Layout container -->
<div class="layout-page">
    <?php echo view('Admin/AdminLeyout/AdminNavbar'); ?>

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
                            <p class="text-muted fs-7 mb-0">บันทึกข้อมูลและคุมใบ<?= esc($order_type); ?>ของระบบงบประมาณและแผนงาน</p>
                        </div>
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb mb-0 bg-transparent p-0">
                                <li class="breadcrumb-item"><a href="<?= base_url('Admin/Home'); ?>" class="text-warning">หน้าแรก</a></li>
                                <li class="breadcrumb-item active text-secondary" aria-current="page">ทะเบียนคุม</li>
                            </ol>
                        </nav>
                    </div>
                </div>
            </div>

            <!-- Stats Mini Cards -->
            <div class="row mb-4">
                <div class="col-md-4 mb-3">
                    <div class="card border-0 shadow-sm p-3" style="border-radius: 12px; background: #fff;">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <span class="text-muted fs-8">ยอดเงินทะเบียนคุมรวม</span>
                                <h3 class="fw-bold mb-0 text-orange mt-1"><?= number_format($total_amount, 2); ?> <span class="fs-8 text-secondary">บาท</span></h3>
                            </div>
                            <div class="avatar bg-success-soft rounded p-2 text-success">
                                <i class="bx bx-wallet-alt fs-3"></i>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 mb-3">
                    <div class="card border-0 shadow-sm p-3" style="border-radius: 12px; background: #fff;">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <span class="text-muted fs-8">รายการทั้งหมด</span>
                                <h3 class="fw-bold mb-0 text-dark mt-1"><?= $total_orders; ?> <span class="fs-8 text-secondary">รายการ</span></h3>
                            </div>
                            <div class="avatar bg-info-soft rounded p-2 text-info">
                                <i class="bx bx-list-ol fs-3"></i>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 mb-3">
                    <div class="card border-0 shadow-sm p-3" style="border-radius: 12px; background: #fff;">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <span class="text-muted fs-8">ส่งลงระบบ/ทำฎีกาแล้ว</span>
                                <h3 class="fw-bold mb-0 text-warning mt-1"><?= $total_sent; ?> <span class="fs-8 text-secondary">รายการ</span></h3>
                            </div>
                            <div class="avatar bg-warning-soft rounded p-2 text-warning">
                                <i class="bx bx-check-square fs-3"></i>
                            </div>
                        </div>
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
                                <p class="text-muted fs-8 mb-0">ค้นหา แก้ไข และจัดการข้อมูลรายการใบสั่งจ้างและสั่งซื้อทั้งหมด</p>
                            </div>
                            <div class="d-flex gap-2 align-items-center flex-wrap">
                                <button type="button" class="btn btn-outline-success btn-md rounded-pill shadow-sm" id="btnExportExcel">
                                    <i class="bx bxs-file-export me-1"></i> ดาวน์โหลด Excel
                                </button>
                                <button type="button" class="btn btn-warning btn-md rounded-pill shadow-sm" onclick="openAddModal()">
                                    <i class="bx bx-plus me-1"></i> เพิ่มรายการทะเบียนคุม
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
                                            <th class="text-center" style="width: 100px;">จัดการ</th>
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
                                                    <td style="max-width: 200px; white-space: normal; word-wrap: break-word;">
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
                                                        <div class="d-flex justify-content-center gap-1">
                                                            <a href="<?= base_url('Admin/Registry/PrintOrder/' . $order->id); ?>" target="_blank" class="btn btn-sm btn-icon btn-outline-info rounded-circle" title="พิมพ์รายงาน">
                                                                <i class="bx bx-printer"></i>
                                                            </a>
                                                            <button type="button" class="btn btn-sm btn-icon btn-outline-warning rounded-circle" onclick="openEditModal(<?= htmlspecialchars(json_encode($order), ENT_QUOTES, 'UTF-8'); ?>)" title="แก้ไข">
                                                                <i class="bx bx-edit"></i>
                                                            </button>
                                                            <button type="button" class="btn btn-sm btn-icon btn-outline-danger rounded-circle" onclick="deleteOrder(<?= $order->id; ?>)" title="ลบ">
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

        </div>
        <!-- / Content -->

        <!-- Add/Edit Modal -->
        <div class="modal fade" id="orderModal" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
            <div class="modal-dialog modal-lg modal-dialog-centered">
                <div class="modal-content border-0 shadow-lg" style="border-radius: 16px;">
                    <div class="modal-header bg-warning py-3 px-4">
                        <h5 class="modal-title text-white fw-bold" id="orderModalTitle">เพิ่มรายการทะเบียนคุม</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form id="orderForm" method="post" enctype="multipart/form-data" class="needs-validation" novalidate>
                        <div class="modal-body p-4">
                            <input type="hidden" name="id" id="order_id">
                            <input type="hidden" name="order_type" id="order_type" value="<?= esc($order_type); ?>">
                            
                            <!-- Hidden input for retained images in edit mode -->
                            <input type="hidden" name="retained_images" id="retained_images">

                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold text-dark">เลขที่ใบ<?= esc($order_type); ?> <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" name="order_number" id="order_number_field" required placeholder="เช่น <?= $type_slug === 'hire' ? 'สจ.' : 'สซ.' ?>01/2569">
                                    <div class="invalid-feedback">กรุณาระบุเลขที่ใบ<?= esc($order_type); ?></div>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold text-dark">วัน เดือน ปี (ที่<?= esc($order_type); ?>) <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control flatpickr-custom" name="order_date" id="order_date_field" required placeholder="เลือกวันที่">
                                    <div class="invalid-feedback">กรุณาเลือกวันที่</div>
                                </div>

                                <div class="col-12">
                                    <label class="form-label fw-semibold text-dark">รายการ (รายละเอียดการ<?= esc($order_type); ?>) <span class="text-danger">*</span></label>
                                    <textarea class="form-control" name="description" id="description_field" rows="2" required placeholder="รายละเอียดรายการจัดซื้อจัดจ้าง..."></textarea>
                                    <div class="invalid-feedback">กรุณาระบุรายละเอียดรายการ</div>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label fw-semibold text-dark">จำนวนเงิน (บาท) <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control text-end fw-bold text-orange" name="amount" id="amount_field" required placeholder="0.00">
                                    <div class="invalid-feedback">กรุณาระบุจำนวนเงิน</div>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold text-dark">กำหนดส่งมอบ</label>
                                    <input type="text" class="form-control flatpickr-custom" name="delivery_date" id="delivery_date_field" placeholder="เลือกกำหนดส่งมอบ">
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label fw-semibold text-dark">ผู้ลงนาม<?= esc($order_type); ?> <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" name="signatory" id="signatory_field" required placeholder="ผู้แทนโรงเรียน/ผู้อำนวยการ">
                                    <div class="invalid-feedback">กรุณาระบุผู้ลงนาม<?= esc($order_type); ?></div>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold text-dark">ผู้รับจ้าง (บริษัท / ห้างร้าน / บุคคล) <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" name="contractor" id="contractor_field" required placeholder="ชื่อร้านค้าหรือคู่สัญญา">
                                    <div class="invalid-feedback">กรุณาระบุผู้รับจ้าง</div>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label fw-semibold text-dark">ผู้ตรวจรับ</label>
                                    <select class="form-select" name="inspection_status" id="inspection_status_field" style="width: 100%;">
                                        <option value="">-- ยังไม่ตรวจรับ (เลือกผู้ตรวจรับ) --</option>
                                        <?php if (!empty($teachers)): ?>
                                            <?php foreach ($teachers as $t): ?>
                                                <?php $fullName = esc($t->pers_prefix . $t->pers_firstname . ' ' . $t->pers_lastname); ?>
                                                <option value="<?= $fullName; ?>"><?= $fullName; ?></option>
                                            <?php endforeach; ?>
                                        <?php endif; ?>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold text-dark">การส่งลงระบบ / ทำฎีกา <span class="text-danger">*</span></label>
                                    <select class="form-select" name="system_sent_status" id="system_sent_status_field" required>
                                        <option value="ยังไม่ส่ง">ยังไม่ส่งลงระบบ / ยังไม่ทำฎีกา</option>
                                        <option value="ส่งลงระบบ/ทำฎีกาแล้ว">ส่งลงระบบ / ทำฎีกาเรียบร้อยแล้ว</option>
                                    </select>
                                </div>

                                <div class="col-12">
                                    <label class="form-label fw-semibold text-dark">หมายเหตุ</label>
                                    <textarea class="form-control" name="remarks" id="remarks_field" rows="2" placeholder="ระบุรายละเอียดเพิ่มเติม หรือหมายเหตุ (ถ้ามี)"></textarea>
                                </div>

                                <div class="col-12">
                                    <label class="form-label fw-semibold text-dark d-flex align-items-center justify-content-between">
                                        <span>อัปโหลดรูปภาพหลักฐาน (เลือกได้มากกว่า 1 รูป)</span>
                                        <span class="text-muted fs-8">ไฟล์ JPG, PNG, GIF</span>
                                    </label>
                                    <div class="image-upload-wrapper border border-dashed rounded-3 p-4 text-center bg-light position-relative" style="border-width: 2px !important; cursor: pointer;">
                                        <input type="file" name="images[]" id="images_field" class="position-absolute w-100 h-100 top-0 start-0 opacity-0" multiple accept="image/*" style="cursor: pointer;">
                                        <i class="bx bx-cloud-upload text-warning fs-1 mb-2"></i>
                                        <p class="mb-0 text-secondary fw-semibold">คลิกเพื่ออัปโหลด หรือลากไฟล์รูปภาพมาวางที่นี่</p>
                                    </div>
                                </div>

                                <!-- Current Images Preview (Edit mode) -->
                                <div class="col-12 d-none" id="existingImagesSection">
                                    <label class="form-label fw-semibold text-dark">รูปภาพปัจจุบัน (คลิก [x] ที่มุมรูปภาพเพื่อลบออก)</label>
                                    <div id="existingImagesContainer" class="d-flex flex-wrap gap-2"></div>
                                </div>

                                <!-- New Uploads Preview -->
                                <div class="col-12 d-none" id="newImagesPreviewSection">
                                    <label class="form-label fw-semibold text-dark">รูปภาพที่จะอัปโหลดใหม่</label>
                                    <div id="newImagesPreviewContainer" class="d-flex flex-wrap gap-2"></div>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer bg-light px-4 py-3 border-top" style="border-radius: 0 0 16px 16px;">
                            <button type="button" class="btn btn-outline-secondary rounded-pill" data-bs-dismiss="modal">ยกเลิก</button>
                            <button type="submit" class="btn btn-warning rounded-pill px-4 text-white shadow-sm" id="btnSubmitForm">บันทึกข้อมูล</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

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
    /* Admin Control Center Theme Overrides */
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

    /* Luxury Stat Card */
    .bg-success-soft { background-color: #e6fcf5 !important; }
    .bg-warning-soft { background-color: #fff9db !important; }
    .bg-info-soft { background-color: #e7f5ff !important; }

    /* Datatable styling overrides */
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

    .image-preview-thumbnail {
        position: relative;
        width: 75px;
        height: 75px;
        border-radius: 8px;
        overflow: hidden;
        border: 1px solid #ddd;
    }
    .image-preview-thumbnail img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }
    .image-preview-thumbnail .btn-delete-img {
        position: absolute;
        top: 2px;
        right: 2px;
        background: rgba(220, 53, 69, 0.85);
        color: #fff;
        border: none;
        border-radius: 50%;
        width: 18px;
        height: 18px;
        font-size: 9px;
        line-height: 18px;
        text-align: center;
        padding: 0;
        cursor: pointer;
    }
    .image-upload-wrapper {
        transition: all 0.2s ease-in-out;
    }
    .image-upload-wrapper.drag-over {
        background-color: rgba(251, 140, 0, 0.08) !important;
        border-color: #FB8C00 !important;
        transform: scale(1.01);
        box-shadow: 0 4px 15px rgba(251, 140, 0, 0.1);
    }
    .swal2-container {
        z-index: 100000 !important;
    }
    
    /* Select2 Sneat Styling Override */
    .select2-container--default .select2-selection--single {
        border: 1px solid #d9dee3 !important;
        border-radius: 0.375rem !important;
        height: 38px !important;
        background-color: #fff !important;
        transition: border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out;
    }
    .select2-container--default .select2-selection--single .select2-selection__rendered {
        line-height: 36px !important;
        color: #697a8d !important;
        padding-left: 10px !important;
    }
    .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: 36px !important;
    }
    .select2-container--default.select2-container--focus .select2-selection--single {
        border-color: #FB8C00 !important;
        box-shadow: 0 0 0.25rem rgba(251, 140, 0, 0.25) !important;
        outline: 0 !important;
    }
    .select2-dropdown {
        border: 1px solid #d9dee3 !important;
        border-radius: 0.375rem !important;
        box-shadow: 0 0.25rem 1rem rgba(161, 172, 184, 0.15) !important;
        z-index: 106000 !important;
    }
    .select2-container--default .select2-results__option--highlighted[aria-selected] {
        background-color: #FB8C00 !important;
        color: #fff !important;
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

        // Initialize Select2 for inspector
        $('#inspection_status_field').select2({
            dropdownParent: $('#orderModal'),
            width: '100%'
        });

        // Initialize flatpickr localized
        flatpickr.localize(flatpickr.l10ns.th);
        
        // Setup flatpickr alt-input format for Buddhist Year
        const fpConfig = {
            dateFormat: "Y-m-d",
            altInput: true,
            altFormat: "d/m/Y",
            parseDate: function(dateStr, format) {
                if (dateStr && dateStr.indexOf('-') > -1) {
                    const parts = dateStr.split('-');
                    return new Date(parts[0], parts[1] - 1, parts[2]);
                }
                const parts = dateStr.split('/');
                if (parts.length === 3) {
                    parts[2] = parseInt(parts[2]) - 543;
                    return new Date(parts[2], parts[1] - 1, parts[0]);
                }
                return new Date(dateStr);
            },
            formatDate: function(date, format) {
                const day = String(date.getDate()).padStart(2, '0');
                const month = String(date.getMonth() + 1).padStart(2, '0');
                const year = date.getFullYear();
                if (format === 'Y-m-d') {
                    return `${year}-${month}-${day}`;
                }
                return `${day}/${month}/${year + 543}`;
            },
            onReady: function(selectedDates, dateStr, instance) {
                setTimeout(() => setBuddhistYear(instance), 5);
            },
            onYearChange: function(selectedDates, dateStr, instance) {
                setTimeout(() => setBuddhistYear(instance), 5);
            },
            onMonthChange: function(selectedDates, dateStr, instance) {
                setTimeout(() => setBuddhistYear(instance), 5);
            },
            onOpen: function(selectedDates, dateStr, instance) {
                setTimeout(() => setBuddhistYear(instance), 5);
            }
        };

        $("#order_date_field").flatpickr(fpConfig);
        $("#delivery_date_field").flatpickr(fpConfig);

        // Format amount with comma when user types
        const amountInput = document.getElementById("amount_field");
        amountInput.addEventListener("input", function (e) {
            let value = e.target.value.replace(/,/g, '');
            if (!isNaN(value) && value !== '') {
                let formatted = parseFloat(value).toLocaleString('en-US', {
                    maximumFractionDigits: 2
                });
                if (e.target.value.endsWith('.')) {
                    formatted += '.';
                }
                e.target.value = formatted;
            }
        });

        // Handle image uploads preview
        const imagesInput = document.getElementById("images_field");
        const newImagesContainer = document.getElementById("newImagesPreviewContainer");
        const newImagesSection = document.getElementById("newImagesPreviewSection");
        let selectedFiles = [];

        imagesInput.addEventListener("change", function () {
            // Append newly selected files to our tracking array
            if (this.files && this.files.length > 0) {
                Array.from(this.files).forEach(file => {
                    selectedFiles.push(file);
                });
            }
            // Clear input so change can be fired again even for same files
            this.value = '';
            renderNewImagesPreview();
        });

        // Setup Drag & Drop behavior on wrapper
        const uploadWrapper = document.querySelector(".image-upload-wrapper");

        ['dragenter', 'dragover'].forEach(eventName => {
            uploadWrapper.addEventListener(eventName, (e) => {
                e.preventDefault();
                e.stopPropagation();
                uploadWrapper.classList.add('drag-over');
            }, false);
        });

        ['dragleave', 'dragend', 'drop'].forEach(eventName => {
            uploadWrapper.addEventListener(eventName, (e) => {
                e.preventDefault();
                e.stopPropagation();
                uploadWrapper.classList.remove('drag-over');
            }, false);
        });

        uploadWrapper.addEventListener('drop', (e) => {
            const dt = e.dataTransfer;
            const files = dt.files;
            if (files && files.length > 0) {
                Array.from(files).forEach(file => {
                    if (file.type.startsWith('image/')) {
                        selectedFiles.push(file);
                    }
                });
                renderNewImagesPreview();
            }
        }, false);

        function renderNewImagesPreview() {
            newImagesContainer.innerHTML = '';
            if (selectedFiles.length > 0) {
                newImagesSection.classList.remove("d-none");
                selectedFiles.forEach((file, index) => {
                    const reader = new FileReader();
                    reader.onload = function (e) {
                        const thumb = document.createElement("div");
                        thumb.className = "image-preview-thumbnail";
                        thumb.innerHTML = `
                            <img src="${e.target.result}" alt="preview">
                            <span class="badge bg-dark position-absolute bottom-0 start-50 translate-middle-x fs-9 w-100 text-center text-white" style="opacity:0.8">${(file.size/1024).toFixed(0)}KB</span>
                            <button type="button" class="btn-delete-img" onclick="removeNewFile(${index})">&times;</button>
                        `;
                        newImagesContainer.appendChild(thumb);
                    }
                    reader.readAsDataURL(file);
                });
            } else {
                newImagesSection.classList.add("d-none");
            }
        }

        window.removeNewFile = function (index) {
            selectedFiles.splice(index, 1);
            renderNewImagesPreview();
        };

        window.clearSelectedFiles = function () {
            selectedFiles = [];
            newImagesSection.classList.add("d-none");
            newImagesContainer.innerHTML = '';
        };

        // Image Compression Function
        async function compressImage(file) {
            return new Promise((resolve) => {
                const reader = new FileReader();
                reader.readAsDataURL(file);
                reader.onload = (event) => {
                    const img = new Image();
                    img.src = event.target.result;
                    img.onload = () => {
                        const canvas = document.createElement('canvas');
                        let width = img.width;
                        let height = img.height;
                        const max_size = 1280; // Max width/height

                        if (width > height) {
                            if (width > max_size) {
                                height *= max_size / width;
                                width = max_size;
                            }
                        } else {
                            if (height > max_size) {
                                width *= max_size / height;
                                height = max_size;
                            }
                        }

                        canvas.width = width;
                        canvas.height = height;
                        const ctx = canvas.getContext('2d');
                        ctx.drawImage(img, 0, 0, width, height);
                        
                        canvas.toBlob((blob) => {
                            // Strictly allow only English alphanumeric characters, dashes and underscores
                            let safeName = file.name.split('.').slice(0, -1).join('.') // Get name without extension
                                .replace(/[^\w-]/g, "_") // Replace non-alphanumeric/underscore/dash with _
                                .replace(/_+/g, "_") // Consolidate multiple underscores
                                .trim();
                            
                            safeName = (safeName || 'image') + '.jpg';

                            resolve(new File([blob], safeName, {
                                type: 'image/jpeg',
                                lastModified: Date.now()
                            }));
                        }, 'image/jpeg', 0.7); // 0.7 quality
                    };
                };
            });
        }

        // Form Submit Handler
        const form = document.getElementById("orderForm");
        form.addEventListener("submit", async function (event) {
            event.preventDefault();
            if (!form.checkValidity()) {
                event.stopPropagation();
                form.classList.add("was-validated");
                return;
            }

            // Show Loading
            Swal.fire({
                title: 'กำลังย่อขนาดรูปภาพ...',
                text: 'กรุณารอสักครู่',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            // Prepare form data
            const formData = new FormData(form);
            
            // Remove standard file input entry and append our compressed files
            formData.delete('images[]');
            
            if (selectedFiles.length > 0) {
                for (let i = 0; i < selectedFiles.length; i++) {
                    const file = selectedFiles[i];
                    Swal.update({
                        title: 'กำลังย่อขนาดรูปภาพ...',
                        text: `กำลังย่อรูปที่ ${i + 1}/${selectedFiles.length}...`
                    });
                    
                    if (file.type.startsWith('image/')) {
                        const compressedFile = await compressImage(file);
                        formData.append('images[]', compressedFile);
                    } else {
                        formData.append('images[]', file);
                    }
                }
            }

            Swal.update({
                title: 'กำลังบันทึกข้อมูล...',
                text: 'กรุณารอสักครู่'
            });

            // Send via AJAX
            fetch("<?= base_url('Admin/Registry/SaveOrder'); ?>", {
                method: "POST",
                body: formData
            })
            .then(res => res.json())
            .then(data => {
                if (data.status === 'success') {
                    Swal.fire({
                        icon: 'success',
                        title: 'สำเร็จ!',
                        text: data.message,
                        timer: 1500,
                        showConfirmButton: false
                    }).then(() => {
                        window.location.reload();
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'เกิดข้อผิดพลาด',
                        text: data.message
                    });
                }
            })
            .catch(err => {
                console.error(err);
                Swal.fire({
                    icon: 'error',
                    title: 'เกิดข้อผิดพลาด',
                    text: 'ไม่สามารถติดต่อเซิร์ฟเวอร์ได้'
                });
            });
        });
    });

    // Helper functions
    function setBuddhistYear(instance) {
        if (!instance.currentYearElement) return;
        const yearEl = instance.currentYearElement;
        const buddhistYear = parseInt(yearEl.value);
        if (buddhistYear < 2500) {
            yearEl.value = buddhistYear + 543;
        }
    }

    // Modal Operations
    function openAddModal() {
        const form = document.getElementById("orderForm");
        form.reset();
        form.classList.remove("was-validated");
        
        clearSelectedFiles();
        
        document.getElementById("order_id").value = "";
        document.getElementById("order_type").value = "<?= esc($order_type); ?>";
        document.getElementById("retained_images").value = "[]";
        $('#inspection_status_field').val('').trigger('change');
        
        document.getElementById("orderModalTitle").textContent = "เพิ่มรายการทะเบียนคุมใบ<?= esc($order_type); ?>";
        
        document.getElementById("existingImagesSection").classList.add("d-none");
        document.getElementById("existingImagesContainer").innerHTML = "";
        document.getElementById("newImagesPreviewSection").classList.add("d-none");
        document.getElementById("newImagesPreviewContainer").innerHTML = "";

        document.getElementById("order_date_field")._flatpickr.clear();
        if(document.getElementById("delivery_date_field")._flatpickr) {
            document.getElementById("delivery_date_field")._flatpickr.clear();
        }

        const modal = new bootstrap.Modal(document.getElementById("orderModal"));
        modal.show();
    }

    function openEditModal(order) {
        const form = document.getElementById("orderForm");
        form.reset();
        form.classList.remove("was-validated");
        
        clearSelectedFiles();

        document.getElementById("orderModalTitle").textContent = "แก้ไขรายการทะเบียนคุมใบ<?= esc($order_type); ?> (เลขที่ " + order.order_number + ")";
        
        document.getElementById("order_id").value = order.id;
        document.getElementById("order_type").value = order.order_type || "<?= esc($order_type); ?>";
        document.getElementById("order_number_field").value = order.order_number;
        document.getElementById("description_field").value = order.description;
        
        document.getElementById("amount_field").value = parseFloat(order.amount).toLocaleString('en-US', {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2
        });

        document.getElementById("signatory_field").value = order.signatory;
        document.getElementById("contractor_field").value = order.contractor;
        const inspectVal = order.inspection_status || '';
        if (inspectVal && $('#inspection_status_field option[value="' + inspectVal + '"]').length === 0) {
            const newOption = new Option(inspectVal, inspectVal, true, true);
            $('#inspection_status_field').append(newOption).trigger('change');
        } else {
            $('#inspection_status_field').val(inspectVal).trigger('change');
        }
        document.getElementById("system_sent_status_field").value = order.system_sent_status || "ยังไม่ส่ง";
        document.getElementById("remarks_field").value = order.remarks || "";

        if (order.order_date) {
            document.getElementById("order_date_field")._flatpickr.setDate(new Date(order.order_date));
        } else {
            document.getElementById("order_date_field")._flatpickr.clear();
        }

        if (order.delivery_date) {
            document.getElementById("delivery_date_field")._flatpickr.setDate(new Date(order.delivery_date));
        } else {
            document.getElementById("delivery_date_field")._flatpickr.clear();
        }

        const imagesList = order.images ? JSON.parse(order.images) : [];
        document.getElementById("retained_images").value = JSON.stringify(imagesList);

        const existingSection = document.getElementById("existingImagesSection");
        const existingContainer = document.getElementById("existingImagesContainer");
        existingContainer.innerHTML = "";

        if (imagesList.length > 0) {
            existingSection.classList.remove("d-none");
            imagesList.forEach((img, idx) => {
                const thumb = document.createElement("div");
                thumb.className = "image-preview-thumbnail";
                thumb.id = `retained-img-${idx}`;
                const isLocal = img.startsWith('uploads/');
                const imgSrc = isLocal ? '<?= base_url(); ?>/' + img : '<?= env('upload.server.baseurl'); ?>' + img;
                thumb.innerHTML = `
                    <img src="${imgSrc}" alt="current-image">
                    <button type="button" class="btn-delete-img" onclick="removeRetainedImage('${img}', ${idx})">&times;</button>
                `;
                existingContainer.appendChild(thumb);
            });
        } else {
            existingSection.classList.add("d-none");
        }

        document.getElementById("newImagesPreviewSection").classList.add("d-none");
        document.getElementById("newImagesPreviewContainer").innerHTML = "";

        const modal = new bootstrap.Modal(document.getElementById("orderModal"));
        modal.show();
    }

    function removeRetainedImage(imagePath, index) {
        const retainedInput = document.getElementById("retained_images");
        let list = JSON.parse(retainedInput.value);
        list = list.filter(item => item !== imagePath);
        retainedInput.value = JSON.stringify(list);

        const el = document.getElementById(`retained-img-${index}`);
        if (el) {
            el.remove();
        }

        if (list.length === 0) {
            document.getElementById("existingImagesSection").classList.add("d-none");
        }
    }

    function deleteOrder(id) {
        Swal.fire({
            title: 'ยืนยันการลบข้อมูล?',
            text: "ข้อมูลนี้จะถูกลบถาวรและไม่สามารถกู้คืนได้!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'ใช่, ต้องการลบ!',
            cancelButtonText: 'ยกเลิก'
        }).then((result) => {
            if (result.isConfirmed) {
                Swal.fire({
                    title: 'กำลังลบข้อมูล...',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });

                fetch(`<?= base_url('Admin/Registry/DeleteOrder'); ?>/${id}`, {
                    method: 'POST'
                })
                .then(res => res.json())
                .then(data => {
                    if (data.status === 'success') {
                        Swal.fire({
                            icon: 'success',
                            title: 'ลบสำเร็จ!',
                            text: data.message,
                            timer: 1500,
                            showConfirmButton: false
                        }).then(() => {
                            window.location.reload();
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'เกิดข้อผิดพลาด',
                            text: data.message
                        });
                    }
                })
                .catch(err => {
                    console.error(err);
                    Swal.fire({
                        icon: 'error',
                        title: 'เกิดข้อผิดพลาด',
                        text: 'ไม่สามารถติดต่อเซิร์ฟเวอร์ได้'
                    });
                });
            }
        });
    }

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
