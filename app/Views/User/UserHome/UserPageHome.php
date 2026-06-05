<!-- Layout container -->
<div class="layout-page">
    <?php echo view('User/UserLeyout/UserNavbar'); ?>

    <div class="content-wrapper">
        <div class="container-xxl flex-grow-1 container-p-y">
            
            <!-- Hero Section -->
            <div class="row">
                <div class="col-12 mb-4">
                    <div class="card border-0 shadow-sm overflow-hidden position-relative" style="background: linear-gradient(135deg, #0d3b66 0%, #0077b6 100%); min-height: 220px; border-radius: 16px;">
                        <!-- ตกแต่งพื้นหลังสไตล์คลื่น/กราฟิกงบประมาณ -->
                        <div class="position-absolute end-0 bottom-0 opacity-10" style="pointer-events: none;">
                            <svg width="400" height="200" viewBox="0 0 400 200" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M0,150 C150,200 250,50 400,100 L400,200 L0,200 Z" fill="white"/>
                            </svg>
                        </div>
                        
                        <div class="card-body d-flex align-items-center h-100 py-4 px-4 px-md-5">
                            <div class="row w-100 align-items-center">
                                <div class="col-sm-8 text-white">
                                    <span class="badge bg-warning text-dark mb-2 px-3 py-2 fs-7 fw-bold" style="border-radius: 30px; letter-spacing: 0.5px;">BUDGET PLAN SYSTEM</span>
                                    <h2 class="fw-bold text-white mb-2" style="font-family: 'Sarabun', sans-serif;">ระบบงานงบประมาณและแผน</h2>
                                    <p class="mb-4 text-white-50" style="font-size: 1.05rem;">
                                        โรงเรียนสวนกุหลาบวิทยาลัย (จิรประวัติ) นครสวรรค์ <br>
                                        <small class="text-warning">ประสานงาน วางแผน โปร่งใส ตรวจสอบได้</small>
                                    </p>
                                </div>
                                <div class="col-sm-4 text-center d-none d-sm-block">
                                    <img src="<?=base_url();?>uploads/Procurement/undraw_finance_m6vw.svg"
                                        height="160" alt="Finance illustration" class="img-fluid" style="filter: drop-shadow(0px 8px 16px rgba(0,0,0,0.25));">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Quick Access / Feature Cards -->
            <div class="row mb-4">
                <div class="col-12">
                    <h5 class="fw-bold mb-3 text-secondary" style="font-family: 'Sarabun', sans-serif;"><i class="bx bx-grid-alt me-1"></i> เมนูเข้าถึงด่วน (Quick Access)</h5>
                </div>
                
                <!-- จัดซื้อจัดจ้าง -->
                <div class="col-md-6 mb-4">
                    <div class="card border-0 shadow-sm h-100 card-hover-effect" style="border-radius: 12px; transition: transform 0.2s, box-shadow 0.2s;">
                        <div class="card-body p-4 d-flex flex-column justify-content-between">
                            <div class="d-flex align-items-center mb-3">
                                <div class="avatar avatar-md me-3" style="background-color: rgba(0, 119, 182, 0.1); border-radius: 10px; padding: 10px; display: flex; align-items: center; justify-content: center;">
                                    <i class="bx bx-shopping-bag text-primary fs-3"></i>
                                </div>
                                <div>
                                    <h5 class="card-title mb-0 fw-bold">ขั้นตอนจัดซื้อ / จัดจ้าง</h5>
                                    <small class="text-muted">Procurement Process</small>
                                </div>
                            </div>
                            <p class="card-text text-secondary">ขั้นตอนการดำเนินงานจัดซื้อจัดจ้าง เอกสารพัสดุ ลำดับการอนุมัติโครงการ และงบประมาณในโรงเรียน</p>
                            <a href="<?=base_url('User/Procurement/Process');?>" class="btn btn-outline-primary w-100 mt-2 py-2" style="border-radius: 8px;">
                                เข้าสู่ขั้นตอนจัดซื้อ / จัดจ้าง <i class="bx bx-chevron-right ms-1"></i>
                            </a>
                        </div>
                    </div>
                </div>

                <!-- ใบสำคัญรับเงิน -->
                <div class="col-md-6 mb-4">
                    <div class="card border-0 shadow-sm h-100 card-hover-effect" style="border-radius: 12px; transition: transform 0.2s, box-shadow 0.2s;">
                        <div class="card-body p-4 d-flex flex-column justify-content-between">
                            <div class="d-flex align-items-center mb-3">
                                <div class="avatar avatar-md me-3" style="background-color: rgba(76, 201, 240, 0.1); border-radius: 10px; padding: 10px; display: flex; align-items: center; justify-content: center;">
                                    <i class="bx bx-receipt text-info fs-3"></i>
                                </div>
                                <div>
                                    <h5 class="card-title mb-0 fw-bold">ใบสำคัญรับเงินตอบแทนค่าวิทยากร</h5>
                                    <small class="text-muted">Lecturer Remuneration Receipt</small>
                                </div>
                            </div>
                            <p class="card-text text-secondary">กรอกแบบฟอร์มเพื่อเสนอขอเบิกจ่ายเงินค่าตอบแทนวิทยากรภายนอก บันทึกข้อมูลและจัดทำใบสำคัญรับเงินอัตโนมัติ</p>
                            <a href="<?=base_url('User/Procurement/MoneyReceipt');?>" class="btn btn-outline-info w-100 mt-2 py-2" style="border-radius: 8px;">
                                เขียนใบสำคัญรับเงิน <i class="bx bx-chevron-right ms-1"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- School Budget Steps Overview -->
            <div class="row">
                <div class="col-12">
                    <div class="card border-0 shadow-sm" style="border-radius: 12px;">
                        <div class="card-body p-4">
                            <h5 class="fw-bold mb-4" style="font-family: Sarabun, sans-serif;"><i class="bx bx-git-commit me-1 text-warning"></i> กระบวนการเบิกจ่ายและวางแผนงบประมาณ</h5>
                            
                            <div class="row justify-content-between g-3">
                                <div class="col-lg-3 col-md-6 text-center px-3 position-relative">
                                    <div class="mx-auto mb-3 d-flex align-items-center justify-content-center" style="width: 60px; height: 60px; border-radius: 50%; background-color: rgba(13, 59, 102, 0.08);">
                                        <span class="fs-4 fw-bold text-primary">1</span>
                                    </div>
                                    <h6 class="fw-bold">เสนอโครงการ</h6>
                                    <p class="text-muted small">ยื่นขออนุมัติโครงการและแผนงบประมาณประจำปีของแต่ละกลุ่มสาระ/งาน</p>
                                </div>
                                <div class="col-lg-3 col-md-6 text-center px-3 position-relative">
                                    <div class="mx-auto mb-3 d-flex align-items-center justify-content-center" style="width: 60px; height: 60px; border-radius: 50%; background-color: rgba(13, 59, 102, 0.08);">
                                        <span class="fs-4 fw-bold text-primary">2</span>
                                    </div>
                                    <h6 class="fw-bold">จัดซื้อ/จัดจ้าง</h6>
                                    <p class="text-muted small">ดำเนินงานตามขั้นตอนพัสดุและจัดเก็บเอกสารใบเสนอราคา</p>
                                </div>
                                <div class="col-lg-3 col-md-6 text-center px-3 position-relative">
                                    <div class="mx-auto mb-3 d-flex align-items-center justify-content-center" style="width: 60px; height: 60px; border-radius: 50%; background-color: rgba(13, 59, 102, 0.08);">
                                        <span class="fs-4 fw-bold text-primary">3</span>
                                    </div>
                                    <h6 class="fw-bold">ตรวจสอบ & อนุมัติ</h6>
                                    <p class="text-muted small">งานงบประมาณและผู้อำนวยการตรวจสอบความถูกต้องและอนุมัติจ่าย</p>
                                </div>
                                <div class="col-lg-3 col-md-6 text-center px-3">
                                    <div class="mx-auto mb-3 d-flex align-items-center justify-content-center" style="width: 60px; height: 60px; border-radius: 50%; background-color: rgba(40, 167, 69, 0.1);">
                                        <i class="bx bx-check text-success fs-3"></i>
                                    </div>
                                    <h6 class="fw-bold text-success">เสร็จสมบูรณ์</h6>
                                    <p class="text-muted small">เบิกจ่ายเงินสำเร็จและบันทึกข้อมูลเพื่อส่งประเมินผลโครงการ</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

<style>
    .card-hover-effect:hover {
        transform: translateY(-4px);
        box-shadow: 0 10px 20px rgba(0, 0, 0, 0.08) !important;
    }
</style>