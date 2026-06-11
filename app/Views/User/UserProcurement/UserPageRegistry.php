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
                                <button type="button" class="btn btn-outline-primary btn-md rounded-pill shadow-sm" id="btnUserGuide" data-bs-toggle="modal" data-bs-target="#userGuideModal">
                                    <i class="bx bx-book-reader me-1"></i> คู่มือการใช้งาน
                                </button>
                                <button type="button" class="btn btn-outline-warning btn-md rounded-pill shadow-sm" id="btnStartTour">
                                    <i class="bx bx-compass me-1"></i> แนะนำการใช้งาน (Tour)
                                </button>
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
                                            <th class="text-center" style="width: 70px;">คำสั่ง</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if (!empty($orders)): ?>
                                            <?php foreach ($orders as $index => $order): ?>
                                                <tr>
                                                    <td class="text-center"><?= $index + 1; ?></td>
                                                    <td class="text-center fw-bold text-dark text-truncate" style="max-width: 120px;" title="<?= esc($order->order_number); ?>"><?= esc($order->order_number); ?></td>
                                                    <td class="text-center" data-order="<?= $order->order_date; ?>">
                                                        <?php
                                                            $d = date_create($order->order_date);
                                                            echo date_format($d, "d/m/") . (intval(date_format($d, "Y")) + 543);
                                                        ?>
                                                    </td>
                                                    <td class="text-truncate" style="max-width: 250px;" title="<?= esc($order->description); ?>">
                                                        <?= esc($order->description); ?>
                                                    </td>
                                                    <td class="text-end fw-bold text-orange">
                                                        <?= number_format($order->amount, 2); ?>
                                                    </td>
                                                    <td class="text-truncate" style="max-width: 150px;" title="<?= esc($order->signatory); ?>"><?= esc($order->signatory); ?></td>
                                                    <td class="text-truncate" style="max-width: 150px;" title="<?= esc($order->contractor); ?>"><?= esc($order->contractor); ?></td>
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
                                                    <td class="text-truncate" style="max-width: 150px;" title="<?= esc($order->remarks); ?>">
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
                                                            // ตรวจสอบว่าเป็นผู้ตรวจรับ: login แล้ว + login_type เป็น inspector + ชื่อตรงกับ inspection_status
                                                            $isInspector = ($session_logged_in === true &&
                                                                            $session_login_type === 'inspector' &&
                                                                            !empty($session_username) &&
                                                                            !empty($order->inspection_status) &&
                                                                            $session_username === $order->inspection_status);
                                                            // ตรวจสอบว่าต้องแสดงปุ่ม Login: ยังไม่ login หรือ login แบบ admin (ไม่ใช่ inspector)
                                                            $showLoginBtn = (!$session_logged_in) || ($session_logged_in && $session_login_type !== 'inspector');

                                                            if ($isInspector):
                                                        ?>
                                                            <div class="d-flex align-items-center justify-content-center gap-1">
                                                                <?php if (count($imgs) > 0): ?>
                                                                    <button type="button" class="btn btn-sm btn-outline-warning rounded-circle p-1 position-relative" onclick="openGallery(<?= htmlspecialchars(json_encode($imgs), ENT_QUOTES, 'UTF-8'); ?>)">
                                                                        <i class="bx bx-image-alt" style="font-size: 1.15rem;"></i>
                                                                        <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger fs-9" style="padding: 0.25em 0.5em; min-width: 1.5em; height: 1.5em; display: flex; align-items: center; justify-content: center;"><?= count($imgs); ?></span>
                                                                    </button>
                                                                <?php endif; ?>
                                                                <button type="button" class="btn btn-sm btn-outline-success rounded-circle p-1" onclick="openUploadModal(<?= $order->id; ?>, '<?= esc($order->order_number); ?>')" title="อัปโหลดรูปภาพ">
                                                                    <i class="bx bx-camera" style="font-size: 1.15rem;"></i>
                                                                </button>
                                                            </div>
                                                        <?php elseif (count($imgs) > 0): ?>
                                                            <button type="button" class="btn btn-sm btn-outline-warning rounded-circle p-1 position-relative" onclick="openGallery(<?= htmlspecialchars(json_encode($imgs), ENT_QUOTES, 'UTF-8'); ?>)">
                                                                <i class="bx bx-image-alt" style="font-size: 1.15rem;"></i>
                                                                 <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger fs-9" style="padding: 0.25em 0.5em; min-width: 1.5em; height: 1.5em; display: flex; align-items: center; justify-content: center;"><?= count($imgs); ?></span>
                                                            </button>
                                                        <?php else: ?>
                                                            <span class="text-muted">-</span>
                                                        <?php endif; ?>
                                                    </td>
                                                    <td class="text-center">
                                                        <?php if ($showLoginBtn): ?>
                                                            <button type="button" class="btn btn-sm btn-outline-primary rounded-pill px-2 py-1" onclick="openInspectorLoginModal()" title="เข้าสู่ระบบเพื่ออัปโหลดรูปภาพ">
                                                                <i class="bx bx-log-in me-1"></i><small>Login</small>
                                                            </button>
                                                        <?php else: ?>
                                                            <a href="<?= base_url('User/Registry/PrintOrder/' . $order->id); ?>" target="_blank" class="btn btn-sm btn-icon btn-outline-info rounded-circle" title="พิมพ์รายงาน">
                                                                <i class="bx bx-printer"></i>
                                                            </a>
                                                        <?php endif; ?>
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

        <!-- Inspector Login Modal -->
        <div class="modal fade" id="inspectorLoginModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" style="max-width: 420px;">
                <div class="modal-content border-0 shadow" style="border-radius: 20px; overflow: hidden;">
                    <div class="modal-header border-0 pb-0 pt-4 px-4 text-center d-block">
                        <div class="mb-2">
                            <div class="bg-warning bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center" style="width: 64px; height: 64px;">
                                <i class="bx bx-camera text-warning" style="font-size: 1.8rem;"></i>
                            </div>
                        </div>
                        <h5 class="modal-title fw-bold" style="font-family: 'Plus Jakarta Sans', 'Sarabun', sans-serif;">เข้าสู่ระบบผู้ตรวจรับ</h5>
                        <p class="text-muted fs-7 mb-0">เพื่ออัปโหลดรูปภาพงานตรวจรับ</p>
                    </div>
                    <div class="modal-body px-4 py-3 text-center">
                        <p class="text-muted fs-7 mb-3">กรุณาเข้าสู่ระบบด้วยบัญชี Google ที่ลงทะเบียนไว้ในระบบ</p>
                        <a href="<?= base_url('Inspector/Login?return_to=' . urlencode(current_url())); ?>" class="btn btn-primary w-100 py-2 fw-semibold d-flex align-items-center justify-content-center gap-2" style="border-radius: 12px; background: #4285f4; border-color: #4285f4;">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 48 48">
                                <path fill="#fff" d="M24 9.5c3.54 0 6.71 1.22 9.21 3.6l6.85-6.85C35.9 2.38 30.47 0 24 0 14.62 0 6.51 5.38 2.56 13.22l7.98 6.19C12.43 13.72 17.74 9.5 24 9.5z"/>
                                <path fill="#fff" d="M46.98 24.55c0-1.57-.15-3.09-.38-4.55H24v9.02h12.94c-.58 2.96-2.26 5.48-4.78 7.18l7.73 6c4.51-4.18 7.09-10.36 7.09-17.65z"/>
                                <path fill="#fff" d="M10.53 28.59a14.5 14.5 0 0 1 0-9.18l-7.98-6.19a24.01 24.01 0 0 0 0 21.56l7.98-6.19z"/>
                                <path fill="#fff" d="M24 48c6.48 0 11.93-2.13 15.89-5.81l-7.73-6c-2.15 1.45-4.92 2.3-8.16 2.3-6.26 0-11.57-4.22-13.47-9.91l-7.98 6.19C6.51 42.62 14.62 48 24 48z"/>
                            </svg>
                            เข้าสู่ระบบด้วย Google
                        </a>
                    </div>
                    <div class="modal-footer border-0 pt-0 pb-4 px-4 justify-content-center">
                        <button type="button" class="btn btn-outline-secondary rounded-pill px-4" data-bs-dismiss="modal">ยกเลิก</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- User Guide Modal -->
        <div class="modal fade" id="userGuideModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-lg">
                <div class="modal-content border-0 shadow" style="border-radius: 20px; overflow: hidden;">
                    <div class="modal-header border-0 pb-0 pt-4 px-4 d-flex align-items-center justify-content-between">
                        <div class="d-flex align-items-center gap-2">
                            <div class="bg-warning bg-opacity-10 rounded-circle d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                                <i class="bx bx-book-reader text-warning" style="font-size: 1.5rem;"></i>
                            </div>
                            <div>
                                <h5 class="modal-title fw-bold mb-0" style="font-family: 'Plus Jakarta Sans', 'Sarabun', sans-serif;">คู่มือการใช้งานระบบทะเบียนคุม</h5>
                                <small class="text-muted">คำแนะนำการใช้งานและขั้นตอนการทำงาน</small>
                            </div>
                        </div>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body px-4 py-3">
                        <!-- Navigation Tabs -->
                        <ul class="nav nav-tabs nav-fill border-bottom mb-3" id="guideTabs" role="tablist">
                            <li class="nav-item" role="presentation">
                                <button class="nav-link active fw-bold text-secondary" id="general-tab" data-bs-toggle="tab" data-bs-target="#general-content" type="button" role="tab" aria-controls="general-content" aria-selected="true">
                                    <i class="bx bx-group me-1"></i> บุคคลทั่วไป / ผู้ดูข้อมูล
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link fw-bold text-secondary" id="inspector-tab" data-bs-toggle="tab" data-bs-target="#inspector-content" type="button" role="tab" aria-controls="inspector-content" aria-selected="false">
                                    <i class="bx bx-badge-check me-1"></i> ผู้ตรวจรับพัสดุ (Inspector)
                                </button>
                            </li>
                        </ul>
                        
                        <!-- Tab Contents -->
                        <div class="tab-content border-0 p-0" id="guideTabContent">
                            <!-- General User Content -->
                            <div class="tab-pane fade show active" id="general-content" role="tabpanel" aria-labelledby="general-tab">
                                <div class="row g-3">
                                    <div class="col-md-4">
                                        <div class="card h-100 border border-light shadow-none p-3" style="border-radius: 12px; background: #faf9f6;">
                                            <div class="text-warning mb-2"><i class="bx bx-search-alt" style="font-size: 2rem;"></i></div>
                                            <h6 class="fw-bold mb-1">1. ค้นหาข้อมูลรวดเร็ว</h6>
                                            <p class="text-muted fs-8 mb-0">ใช้ช่องค้นหาขวาบนของตาราง เพื่อค้นหาตามเลขที่สั่งซื้อ, รายการ, ผู้ลงนาม, ผู้ตรวจรับ หรือวันที่ ได้ทันที</p>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="card h-100 border border-light shadow-none p-3" style="border-radius: 12px; background: #faf9f6;">
                                            <div class="text-success mb-2"><i class="bx bxs-file-export" style="font-size: 2rem;"></i></div>
                                            <h6 class="fw-bold mb-1">2. ดาวน์โหลด Excel</h6>
                                            <p class="text-muted fs-8 mb-0">คลิกปุ่ม "ดาวน์โหลด Excel" เพื่อนำข้อมูลทะเบียนคุมทั้งหมดส่งออกเป็นไฟล์สเปรดชีตอย่างง่ายดาย</p>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="card h-100 border border-light shadow-none p-3" style="border-radius: 12px; background: #faf9f6;">
                                            <div class="text-info mb-2"><i class="bx bx-images" style="font-size: 2rem;"></i></div>
                                            <h6 class="fw-bold mb-1">3. ดูภาพตรวจรับ</h6>
                                            <p class="text-muted fs-8 mb-0">หากมีรูปภาพในรายการ คลิกปุ่มภาพสีเหลือง <i class="bx bx-image-alt text-warning"></i> เพื่อเปิดดูสไลด์แกลเลอรีภาพถ่ายตรวจรับ</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Inspector Content -->
                            <div class="tab-pane fade" id="inspector-content" role="tabpanel" aria-labelledby="inspector-tab">
                                <div class="timeline-steps">
                                    <div class="d-flex align-items-start gap-3 mb-3">
                                        <div class="badge bg-warning text-white rounded-circle p-2 d-flex align-items-center justify-content-center" style="width: 32px; height: 32px; font-weight: bold;">1</div>
                                        <div>
                                            <h6 class="fw-bold mb-0">เข้าสู่ระบบด้วย Google</h6>
                                            <p class="text-muted fs-8 mb-0">หากยังไม่ได้เข้าระบบ ให้คลิกปุ่ม <span class="badge bg-outline-primary text-primary border border-primary px-2"><i class="bx bx-log-in me-1"></i>Login</span> ในช่องคำสั่งแถวที่ตนเองเป็นผู้ตรวจรับ</p>
                                        </div>
                                    </div>
                                    <div class="d-flex align-items-start gap-3 mb-3">
                                        <div class="badge bg-warning text-white rounded-circle p-2 d-flex align-items-center justify-content-center" style="width: 32px; height: 32px; font-weight: bold;">2</div>
                                        <div>
                                            <h6 class="fw-bold mb-0">คลิกอัปโหลดรูปภาพ (กล้องสีเขียว)</h6>
                                            <p class="text-muted fs-8 mb-0">คลิกปุ่มไอคอนกล้อง <i class="bx bx-camera text-success"></i> ในช่องรูปภาพของแถวรายการที่เป็นของคุณ</p>
                                        </div>
                                    </div>
                                    <div class="d-flex align-items-start gap-3 mb-3">
                                        <div class="badge bg-warning text-white rounded-circle p-2 d-flex align-items-center justify-content-center" style="width: 32px; height: 32px; font-weight: bold;">3</div>
                                        <div>
                                            <h6 class="fw-bold mb-0">เลือกและอัปโหลดรูปภาพ</h6>
                                            <p class="text-muted fs-8 mb-1">ลากไฟล์รูปภาพมาวางหรือคลิกเลือกรูปภาพ (รองรับหลายรูป) ระบบจะย่อขนาดให้อัตโนมัติเพื่อความรวดเร็วในการโหลด</p>
                                            <div class="alert alert-warning py-2 px-3 fs-9 mb-0" role="alert" style="border-radius: 8px;">
                                                <i class="bx bx-error-circle me-1"></i> <strong>ข้อควรระวัง:</strong> การอัปโหลดใหม่จะเป็นการเขียนทับรูปภาพเดิมทั้งหมด ดังนั้นต้องเลือกรูปภาพที่จะอัปโหลดใหม่ให้ครบถ้วนในครั้งเดียว
                                            </div>
                                        </div>
                                    </div>
                                    <div class="d-flex align-items-start gap-3">
                                        <div class="badge bg-warning text-white rounded-circle p-2 d-flex align-items-center justify-content-center" style="width: 32px; height: 32px; font-weight: bold;">4</div>
                                        <div>
                                            <h6 class="fw-bold mb-0">พิมพ์รายงานใบสั่งซื้อ/สั่งจ้าง</h6>
                                            <p class="text-muted fs-8 mb-0">หลังจากเข้าระบบแล้ว คลิกปุ่มเครื่องพิมพ์ <i class="bx bx-printer text-info"></i> ในช่องคำสั่งเพื่อดูและพิมพ์รายงานทันที</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer border-0 pt-0 pb-4 px-4 justify-content-center">
                        <button type="button" class="btn btn-warning rounded-pill px-4 text-white" id="btnStartTourFromGuide" data-bs-dismiss="modal">
                            <i class="bx bx-compass me-1"></i> เริ่มทัวร์แนะนำหน้าจอจริง
                        </button>
                        <button type="button" class="btn btn-outline-secondary rounded-pill px-4" data-bs-dismiss="modal">ปิดคู่มือ</button>
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

    /* Upload area hover effect */
    #uploadArea:hover {
        border-color: #71dd37 !important;
        background-color: #f0ffe6;
    }

    /* ===== บีบตารางให้อยู่ในหน้าจอเดียว ===== */
    #orderTable {
        font-size: 0.8rem;
    }
    #orderTable > :not(caption) > * > * {
        padding: 0.35rem 0.5rem;
    }
    #orderTable thead th {
        font-size: 0.75rem;
        white-space: nowrap;
    }
    #orderTable td {
        vertical-align: middle;
    }
    @media (max-width: 1199.98px) {
        #orderTable th:nth-child(6), #orderTable td:nth-child(6),
        #orderTable th:nth-child(7), #orderTable td:nth-child(7),
        #orderTable th:nth-child(9), #orderTable td:nth-child(9),
        #orderTable th:nth-child(10), #orderTable td:nth-child(10),
        #orderTable th:nth-child(11), #orderTable td:nth-child(11) {
            display: none;
        }
    }
    @media (max-width: 767.98px) {
        #orderTable th:nth-child(5), #orderTable td:nth-child(5),
        #orderTable th:nth-child(8), #orderTable td:nth-child(8) {
            display: none;
        }
    }
    .dataTables_wrapper .dataTables_paginate .paginate_button {
        padding: 0.2rem 0.4rem !important;
        font-size: 0.75rem;
    }
    .dataTables_wrapper .dataTables_info,
    .dataTables_wrapper .dataTables_length label,
    .dataTables_wrapper .dataTables_filter label {
        font-size: 0.75rem;
    }
    .dataTables_wrapper .dataTables_length select {
        font-size: 0.75rem;
        padding: 0.15rem 0.3rem;
    }
    .card-header h5 { font-size: 0.9rem; }
    .card-header p { font-size: 0.7rem; }
    .container-xxl > .row.mb-4 h4 { font-size: 1.1rem; }
    .container-xxl > .row.mb-4 p { font-size: 0.75rem; }
    .card-body { padding: 0.75rem !important; }
    .card-header { padding: 0.75rem !important; }

    /* Custom CSS for guide modal tabs */
    #guideTabs .nav-link {
        border: none !important;
        border-bottom: 2px solid transparent !important;
        transition: all 0.25s ease;
        color: #6c757d;
    }
    #guideTabs .nav-link.active {
        color: var(--primary-orange) !important;
        border-bottom: 2px solid var(--primary-orange) !important;
        background: transparent !important;
    }
    #guideTabs .nav-link:hover {
        color: var(--primary-orange) !important;
    }
    
    /* Intro.js Orange customization */
    .introjs-donebutton {
        background: var(--primary-orange) !important;
        text-shadow: none !important;
        border: none !important;
    }
    .introjs-nextbutton {
        background: var(--primary-orange) !important;
        text-shadow: none !important;
        border: none !important;
    }
    .introjs-bullets ul li a.active {
        background: var(--primary-orange) !important;
    }
</style>

<!-- Intro.js CSS -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/intro.js@7.2.0/introjs.min.css">

<!-- DataTables Buttons CSS -->
<link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.3.6/css/buttons.bootstrap5.min.css">

<!-- Intro.js JavaScript -->
<script src="https://cdn.jsdelivr.net/npm/intro.js@7.2.0/intro.min.js"></script>

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

    // ===== Auto-open Upload Modal after Inspector Login =====
    <?php if ($auto_open_upload): ?>
    document.addEventListener("DOMContentLoaded", function () {
        // หา order แรกที่ user เป็นผู้ตรวจรับ
        const inspectorOrderIds = [];
        <?php foreach ($orders as $order): ?>
            <?php if ($session_logged_in && $session_login_type === 'inspector' && !empty($order->inspection_status) && $session_username === $order->inspection_status): ?>
                inspectorOrderIds.push({ id: <?= $order->id; ?>, number: '<?= esc($order->order_number); ?>' });
            <?php endif; ?>
        <?php endforeach; ?>

        if (inspectorOrderIds.length > 0) {
            // เปิด modal อัปโหลดสำหรับรายการแรก
            const firstOrder = inspectorOrderIds[0];
            setTimeout(function() {
                openUploadModal(firstOrder.id, firstOrder.number);
            }, 500); // delay ให้ DataTable render เสร็จก่อน
        }
    });
    <?php endif; ?>

    // ===== Inspector Login Modal =====
    function openInspectorLoginModal() {
        const modal = new bootstrap.Modal(document.getElementById('inspectorLoginModal'));
        modal.show();
    }

    // ===== Image Upload for Inspector (Compress + Replace) =====
    let selectedFiles = [];
    let currentUploadOrderId = null;

    // --- Compress รูปเหมือน admin (canvas resize max 1280px, quality 0.7) ---
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
                    const max_size = 1280;

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
                        let safeName = file.name.split('.').slice(0, -1).join('.')
                            .replace(/[^\w-]/g, '_')
                            .replace(/_+/g, '_')
                            .trim();
                        safeName = (safeName || 'image') + '.jpg';

                        resolve(new File([blob], safeName, {
                            type: 'image/jpeg',
                            lastModified: Date.now()
                        }));
                    }, 'image/jpeg', 0.7);
                };
            };
        });
    }

    function openUploadModal(orderId, orderNumber) {
        selectedFiles = [];
        currentUploadOrderId = orderId;

        Swal.fire({
            title: `อัปโหลดรูปภาพงาน<br><small class="text-muted fs-7">ใบสั่งซื้อเลขที่: ${orderNumber}</small>`,
            html: `
                <div class="upload-area border border-2 border-dashed rounded-3 p-4" id="uploadArea" style="cursor: pointer; border-color: #d9dee3; transition: all 0.2s;">
                    <input type="file" id="imageInput" accept="image/jpeg,image/jpg,image/png,image/webp" multiple style="display: none;">
                    <div id="uploadPlaceholder">
                        <i class="bx bx-cloud-upload" style="font-size: 3rem; color: #a1acb8;"></i>
                        <p class="text-muted mt-2 mb-1">คลิกหรือลากรูปภาพมาวางที่นี่</p>
                        <small class="text-muted">รองรับ JPG, JPEG, PNG, WEBP (ไม่จำกัดขนาด — จะบีบอัดอัตโนมัติ)</small>
                    </div>
                    <div id="uploadPreview" class="d-none text-start">
                        <p class="text-muted fs-7 mb-2">เลือกได้หลายรูป (จะคอมเพรสก่อนอัปโหลด):</p>
                        <div id="fileList" class="mb-2" style="max-height: 200px; overflow-y: auto;"></div>
                        <button type="button" class="btn btn-sm btn-outline-danger" onclick="clearFiles()">
                            <i class="bx bx-trash me-1"></i> ลบทั้งหมด
                        </button>
                    </div>
                </div>
            `,
            showCancelButton: true,
            confirmButtonText: '<i class="bx bx-upload me-1"></i> อัปโหลด (<span id="uploadCount">0</span> รูป)',
            cancelButtonText: 'ยกเลิก',
            confirmButtonColor: '#71dd37',
            cancelButtonColor: '#a1acb8',
            width: 500,
            didOpen: () => {
                const uploadArea = document.getElementById('uploadArea');
                const imageInput = document.getElementById('imageInput');

                uploadArea.addEventListener('click', () => imageInput.click());

                uploadArea.addEventListener('dragover', (e) => {
                    e.preventDefault();
                    uploadArea.style.borderColor = '#71dd37';
                    uploadArea.style.backgroundColor = '#f0ffe6';
                });

                uploadArea.addEventListener('dragleave', () => {
                    uploadArea.style.borderColor = '#d9dee3';
                    uploadArea.style.backgroundColor = '';
                });

                uploadArea.addEventListener('drop', (e) => {
                    e.preventDefault();
                    uploadArea.style.borderColor = '#d9dee3';
                    uploadArea.style.backgroundColor = '';
                    if (e.dataTransfer.files.length > 0) {
                        handleFilesSelect(e.dataTransfer.files);
                    }
                });

                imageInput.addEventListener('change', (e) => {
                    if (e.target.files.length > 0) {
                        handleFilesSelect(e.target.files);
                    }
                });
            },
            preConfirm: () => {
                if (selectedFiles.length === 0) {
                    Swal.showValidationMessage('กรุณาเลือกไฟล์รูปภาพ');
                    return false;
                }
                return true;
            }
        }).then((result) => {
            if (result.isConfirmed && selectedFiles.length > 0) {
                submitUpload(currentUploadOrderId);
            }
        });
    }

    function handleFilesSelect(files) {
        const allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/webp'];

        for (let i = 0; i < files.length; i++) {
            const file = files[i];
            if (!allowedTypes.includes(file.type)) {
                Swal.showValidationMessage(`ไฟล์ "${file.name}" ไม่ใช่รูปภาพที่รองรับ`);
                continue;
            }
            const exists = selectedFiles.some(f => f.name === file.name && f.size === file.size);
            if (!exists) {
                selectedFiles.push(file);
            }
        }

        updateFilePreview();
    }

    function updateFilePreview() {
        const fileList = document.getElementById('fileList');
        const uploadPlaceholder = document.getElementById('uploadPlaceholder');
        const uploadPreview = document.getElementById('uploadPreview');
        const uploadCount = document.getElementById('uploadCount');

        if (selectedFiles.length === 0) {
            uploadPlaceholder.classList.remove('d-none');
            uploadPreview.classList.add('d-none');
            return;
        }

        uploadPlaceholder.classList.add('d-none');
        uploadPreview.classList.remove('d-none');
        uploadCount.textContent = selectedFiles.length;

        fileList.innerHTML = '';
        selectedFiles.forEach((file, index) => {
            const item = document.createElement('div');
            item.className = 'd-flex align-items-center justify-content-between bg-light rounded-2 px-2 py-1 mb-1';
            item.innerHTML = `
                <div class="d-flex align-items-center gap-2 overflow-hidden">
                    <i class="bx bx-image text-primary" style="font-size: 1.2rem;"></i>
                    <span class="text-truncate" style="max-width: 250px;" title="${file.name}">${file.name}</span>
                    <small class="text-muted">${formatFileSize(file.size)}</small>
                </div>
                <button type="button" class="btn btn-sm btn-link text-danger p-0" onclick="removeFile(${index})" title="ลบ">
                    <i class="bx bx-x" style="font-size: 1.2rem;"></i>
                </button>
            `;
            fileList.appendChild(item);
        });
    }

    function removeFile(index) {
        selectedFiles.splice(index, 1);
        updateFilePreview();
    }

    function clearFiles() {
        selectedFiles = [];
        document.getElementById('imageInput').value = '';
        updateFilePreview();
    }

    function formatFileSize(bytes) {
        if (bytes < 1024) return bytes + ' B';
        if (bytes < 1024 * 1024) return (bytes / 1024).toFixed(1) + ' KB';
        return (bytes / (1024 * 1024)).toFixed(1) + ' MB';
    }

    // --- อัปโหลด: compress → ลบไฟล์เก่า → เพิ่มใหม่ ---
    async function submitUpload(orderId) {
        const totalFiles = selectedFiles.length;

        Swal.fire({
            title: 'กำลังย่อขนาดรูปภาพ...',
            text: `กำลังย่อรูปที่ 0/${totalFiles}...`,
            allowOutsideClick: false,
            showConfirmButton: false,
            didOpen: () => { Swal.showLoading(); }
        });

        // 1) Compress ทุกรูปก่อน
        const compressedFiles = [];
        for (let i = 0; i < selectedFiles.length; i++) {
            Swal.update({
                title: 'กำลังย่อขนาดรูปภาพ...',
                text: `กำลังย่อรูปที่ ${i + 1}/${totalFiles}...`
            });
            const compressed = await compressImage(selectedFiles[i]);
            compressedFiles.push(compressed);
        }

        // 2) ลบไฟล์เก่าทิ้งก่อน (ส่ง flag replace=true)
        Swal.update({
            title: 'กำลังเตรียมอัปโหลด...',
            text: 'กำลังลบไฟล์เก่า...'
        });

        const replaceResp = await fetch('<?= base_url('User/Registry/ReplaceImages/'); ?>/' + orderId, {
            method: 'POST',
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        });
        const replaceData = await replaceResp.json();
        if (!replaceData.success) {
            Swal.fire({ icon: 'error', title: 'เกิดข้อผิดพลาด', text: replaceData.message });
            return;
        }

        // 3) อัปโหลดรูปใหม่ทีละรูป
        let uploadedCount = 0;
        let errorCount = 0;

        Swal.update({
            title: 'กำลังอัปโหลดรูปภาพใหม่...',
            text: `อัปโหลดรูปที่ 0/${totalFiles}...`
        });

        for (let i = 0; i < compressedFiles.length; i++) {
            Swal.update({
                title: 'กำลังอัปโหลดรูปภาพใหม่...',
                text: `อัปโหลดรูปที่ ${i + 1}/${totalFiles}...`
            });

            const formData = new FormData();
            formData.append('image', compressedFiles[i]);

            try {
                const resp = await fetch('<?= base_url('User/Registry/UploadImage/'); ?>/' + orderId, {
                    method: 'POST',
                    body: formData,
                    headers: { 'X-Requested-With': 'XMLHttpRequest' }
                });
                const data = await resp.json();
                if (data.success) {
                    uploadedCount++;
                } else {
                    errorCount++;
                }
            } catch (err) {
                errorCount++;
            }
        }

        // 4) เสร็จ
        if (errorCount === 0) {
            Swal.fire({
                icon: 'success',
                title: 'อัปโหลดสำเร็จ!',
                text: `อัปโหลด ${uploadedCount} รูปเรียบร้อย`,
                timer: 1500,
                showConfirmButton: false
            }).then(() => { location.reload(); });
        } else {
            Swal.fire({
                icon: 'warning',
                title: `อัปโหลดสำเร็จ ${uploadedCount}/${totalFiles} รูป`,
                text: `${errorCount} รูปอัปโหลดไม่สำเร็จ`,
                timer: 2000,
                showConfirmButton: false
            }).then(() => { location.reload(); });
        }
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

    // ===== Interactive Tour (Intro.js) =====
    function startInteractiveTour() {
        const tour = introJs();
        
        tour.setOptions({
            nextLabel: 'ถัดไป &rarr;',
            prevLabel: '&larr; ย้อนกลับ',
            skipLabel: 'ข้าม',
            doneLabel: 'เสร็จสิ้น',
            hidePrev: true,
            steps: [
                {
                    title: 'ยินดีต้อนรับ 👋',
                    intro: 'ยินดีต้อนรับสู่หน้าระเบียบทะเบียนคุมใบสั่งซื้อ / สั่งจ้าง! มาแนะนำการใช้งานหน้าจอนี้กันครับ'
                },
                {
                    element: '#btnUserGuide',
                    title: 'คู่มือสรุปขั้นตอน 📖',
                    intro: 'หากต้องการอ่านขั้นตอนการทำอย่างละเอียด (เช่น การอัปโหลดรูปภาพสำหรับผู้ตรวจรับ) สามารถคลิกอ่านคู่มือสรุปได้จากปุ่มนี้ครับ',
                    position: 'bottom'
                },
                {
                    element: '#btnStartTour',
                    title: 'แนะนำการใช้งาน (Tour) 🧭',
                    intro: 'คุณสามารถคลิกปุ่มนี้เมื่อใดก็ได้ เพื่อเล่นทัวร์แนะนำหน้าจอนี้อีกครั้งครับ',
                    position: 'bottom'
                },
                {
                    element: '#btnExportExcel',
                    title: 'ดาวน์โหลดข้อมูล Excel 📊',
                    intro: 'คุณสามารถดาวน์โหลดข้อมูลทะเบียนคุมทั้งหมดในตารางออกเป็นไฟล์ Excel เพื่อนำไปใช้งานต่อได้จากปุ่มนี้ครับ',
                    position: 'bottom'
                },
                {
                    element: '#orderTable_filter input',
                    title: 'ค้นหาข้อมูลด่วน 🔍',
                    intro: 'พิมพ์ค้นหาข้อมูลทุกอย่างในตารางได้จากตรงนี้ทันที เช่น เลขที่ใบสั่งซื้อ ชื่อรายการ หรือชื่อผู้ตรวจรับ',
                    position: 'bottom'
                },
                {
                    element: '#orderTable th:nth-child(10)',
                    title: 'สถานะการตรวจรับ 👥',
                    intro: 'คอลัมน์นี้จะแสดงสถานะการตรวจรับพัสดุและชื่อผู้ตรวจรับพัสดุของแต่ละรายการ',
                    position: 'top'
                },
                {
                    element: '#orderTable th:nth-child(12)',
                    title: 'รูปภาพและปุ่มอัปโหลด 📸',
                    intro: 'หากมีรูปภาพแล้วคุณสามารถคลิกเพื่อดูแกลเลอรีภาพถ่ายได้ที่นี่ และหากคุณเข้าระบบในฐานะผู้ตรวจรับพัสดุของแถวนั้นแล้ว จะมีปุ่มรูปกล้องถ่ายรูปปรากฏขึ้นเพื่อให้คุณกดอัปโหลดภาพถ่ายครับ',
                    position: 'top'
                },
                {
                    element: '#orderTable th:nth-child(13)',
                    title: 'เมนูคำสั่ง ⚙️',
                    intro: 'ผู้ตรวจรับพัสดุสามารถใช้ปุ่มในช่องนี้เพื่อเข้าสู่ระบบ (Login) หรือพิมพ์รายงานใบสั่งซื้อได้หลังจากเข้าสู่ระบบแล้ว',
                    position: 'left'
                }
            ]
        });

        tour.start();
    }

    // Bind event listeners
    document.addEventListener("DOMContentLoaded", function() {
        const btnStartTour = document.getElementById('btnStartTour');
        if (btnStartTour) {
            btnStartTour.addEventListener('click', function() {
                startInteractiveTour();
            });
        }

        const btnStartTourFromGuide = document.getElementById('btnStartTourFromGuide');
        if (btnStartTourFromGuide) {
            btnStartTourFromGuide.addEventListener('click', function() {
                setTimeout(function() {
                    startInteractiveTour();
                }, 350); // wait for bootstrap modal to hide fully
            });
        }
    });
</script>
