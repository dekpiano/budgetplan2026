<!-- Layout container -->
<div class="layout-page">
    <?php echo view('User/UserLeyout/UserNavbar'); ?>

    <div class="content-wrapper">
        <div class="container-xxl flex-grow-1 container-p-y">

            <!-- Hero / Welcome Banner (Orange Luxury Minimalist) -->
            <div class="row mb-5 animate-fade-in-up">
                <div class="col-12">
                    <div class="card border-0 position-relative overflow-hidden luxury-hero-card">
                        <!-- Background glow effect -->
                        <div class="hero-glow-1"></div>
                        <div class="hero-glow-2"></div>
                        
                        <div class="card-body p-4 p-md-5 position-relative z-index-1">
                            <div class="row align-items-center">
                                <div class="col-lg-8">
                                    <div class="d-flex align-items-center mb-3 flex-wrap gap-2">
                                        <span class="system-badge">SKJ BUDGET & PLANNING</span>
                                        <div class="status-dot-pulse ms-1"></div>
                                    </div>
                                    <h1 class="hero-title mb-2">ระบบบริหารงานงบประมาณและแผน</h1>
                                    <p class="hero-subtitle mb-3">
                                        โรงเรียนสวนกุหลาบวิทยาลัย (จิรประวัติ) นครสวรรค์
                                    </p>
                                    <div class="d-flex align-items-center mt-4 text-white gap-3 fs-7 flex-wrap">
                                        <span class="d-flex align-items-center opacity-90"><i class="bx bx-check-shield me-1 text-warning"></i> โปร่งใส</span>
                                        <span class="d-flex align-items-center opacity-90"><i class="bx bx-compass me-1 text-warning"></i> วางแผนแม่นยำ</span>
                                        <span class="d-flex align-items-center opacity-90"><i class="bx bx-data me-1 text-warning"></i> ตรวจสอบได้</span>
                                        <?php if (auth_is_admin()): ?>
                                            <a href="<?= base_url('Admin/Home'); ?>" class="btn btn-warning btn-sm rounded-pill px-3 text-dark fw-bold shadow-sm ms-md-2">
                                                <i class="bx bxs-dashboard me-1"></i> เข้าสู่หน้าจัดการระบบ (เจ้าหน้าที่)
                                            </a>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <div class="col-lg-4 text-center d-none d-lg-block">
                                    <div class="hero-graphic-container">
                                        <div class="graphic-circle"></div>
                                        <img src="<?= base_url(); ?>uploads/Procurement/undraw_finance_m6vw.svg" alt="Finance" class="hero-illustration">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Portal Feature Cards (4 Main Cards) -->
            <div class="row mb-5">
                <div class="col-12 mb-4">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <h4 class="section-title mb-1 fw-bold text-dark">เมนูบริการและสารสนเทศ</h4>
                            <p class="text-muted fs-7 mb-0">เลือกเมนูที่ต้องการเพื่อตรวจสอบข้อมูลโครงการและจัดทำเอกสารทางการเงิน</p>
                        </div>
                        <div class="section-line"></div>
                    </div>
                </div>
                
                <!-- 1. สรุปโครงการและงบประมาณ -->
                <div class="col-xl-3 col-md-6 mb-4 animate-fade-in-up" style="animation-delay: 0.05s;">
                    <a href="<?= base_url('User/BudgetSummary'); ?>" class="text-decoration-none">
                        <div class="card luxury-action-card h-100">
                            <div class="card-body p-4 d-flex flex-column justify-content-between">
                                <div>
                                    <div class="d-flex justify-content-between align-items-start mb-3">
                                        <div class="luxury-icon-wrapper">
                                            <i class="bx bx-book-content"></i>
                                        </div>
                                        <span class="badge bg-orange-soft text-orange rounded-pill fs-8">Budget Summary</span>
                                    </div>
                                    <h5 class="card-heading fw-bold mb-2">สรุปโครงการและงบประมาณ</h5>
                                    <p class="card-desc mb-0">ตรวจสอบวงเงินงบประมาณจัดสรร ยอดเบิกจ่าย และยอดเงินคงเหลือของแต่ละโครงการ</p>
                                </div>
                                <div class="mt-4 pt-3 border-top border-light d-flex align-items-center justify-content-between">
                                    <span class="action-text">เปิดดูข้อมูลโครงการ <i class="bx bx-right-arrow-alt align-middle ms-1"></i></span>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>

                <!-- 2. จัดซื้อจัดจ้าง -->
                <div class="col-xl-3 col-md-6 mb-4 animate-fade-in-up" style="animation-delay: 0.1s;">
                    <a href="<?= base_url('User/Procurement/Process'); ?>" class="text-decoration-none">
                        <div class="card luxury-action-card h-100">
                            <div class="card-body p-4 d-flex flex-column justify-content-between">
                                <div>
                                    <div class="d-flex justify-content-between align-items-start mb-3">
                                        <div class="luxury-icon-wrapper">
                                            <i class="bx bx-shopping-bag"></i>
                                        </div>
                                        <span class="badge bg-orange-soft text-orange rounded-pill fs-8">Procurement</span>
                                    </div>
                                    <h5 class="card-heading fw-bold mb-2">ขั้นตอนจัดซื้อ / จัดจ้าง</h5>
                                    <p class="card-desc mb-0">คู่มือขั้นตอนงานพัสดุ การจัดเตรียมเอกสาร และกระบวนการขออนุมัติงบประมาณโครงการ</p>
                                </div>
                                <div class="mt-4 pt-3 border-top border-light d-flex align-items-center justify-content-between">
                                    <span class="action-text">เข้าสู่ขั้นตอนจัดซื้อ <i class="bx bx-right-arrow-alt align-middle ms-1"></i></span>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>

                <!-- 3. ใบสำคัญรับเงิน -->
                <div class="col-xl-3 col-md-6 mb-4 animate-fade-in-up" style="animation-delay: 0.15s;">
                    <a href="<?= base_url('User/Procurement/MoneyReceipt'); ?>" class="text-decoration-none">
                        <div class="card luxury-action-card h-100">
                            <div class="card-body p-4 d-flex flex-column justify-content-between">
                                <div>
                                    <div class="d-flex justify-content-between align-items-start mb-3">
                                        <div class="luxury-icon-wrapper">
                                            <i class="bx bx-receipt"></i>
                                        </div>
                                        <span class="badge bg-orange-soft text-orange rounded-pill fs-8">Receipt Form</span>
                                    </div>
                                    <h5 class="card-heading fw-bold mb-2">ใบสำคัญรับเงินตอบแทน</h5>
                                    <p class="card-desc mb-0">ระบบจัดทำและพิมพ์ใบสำคัญรับเงินสำหรับวิทยากรภายนอก สะดวก ถูกต้องตามระเบียบ</p>
                                </div>
                                <div class="mt-4 pt-3 border-top border-light d-flex align-items-center justify-content-between">
                                    <span class="action-text">เขียนใบสำคัญรับเงิน <i class="bx bx-right-arrow-alt align-middle ms-1"></i></span>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>

                <!-- 4. แผนดำเนินงานและแผนปฏิบัติการ -->
                <div class="col-xl-3 col-md-6 mb-4 animate-fade-in-up" style="animation-delay: 0.2s;">
                    <a href="<?= base_url('User/Plan'); ?>" class="text-decoration-none">
                        <div class="card luxury-action-card h-100">
                            <div class="card-body p-4 d-flex flex-column justify-content-between">
                                <div>
                                    <div class="d-flex justify-content-between align-items-start mb-3">
                                        <div class="luxury-icon-wrapper">
                                            <i class="bx bx-bar-chart-alt-2"></i>
                                        </div>
                                        <span class="badge bg-orange-soft text-orange rounded-pill fs-8">Action Plan</span>
                                    </div>
                                    <h5 class="card-heading fw-bold mb-2">โครงสร้างยุทธศาสตร์และแผน</h5>
                                    <p class="card-desc mb-0">ศึกษาโครงสร้างยุทธศาสตร์ กลยุทธ์ วัตถุประสงค์ โครงการ และงบประมาณจัดสรรประจำปี</p>
                                </div>
                                <div class="mt-4 pt-3 border-top border-light d-flex align-items-center justify-content-between">
                                    <span class="action-text">ศึกษาแผนยุทธศาสตร์ <i class="bx bx-right-arrow-alt align-middle ms-1"></i></span>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>
            </div>

            <!-- Workflow Steps Overview -->
            <div class="row mb-5 animate-fade-in-up" style="animation-delay: 0.25s;">
                <div class="col-12">
                    <div class="card border-0 shadow-sm budget-steps-card rounded-4">
                        <div class="card-body p-4 p-md-5">
                            <div class="text-center mb-5">
                                <span class="badge bg-orange-soft text-orange px-3 py-2 rounded-pill mb-2 fw-bold fs-8">WORKFLOW</span>
                                <h3 class="fw-bold text-dark mb-2" style="font-family: 'K2D', sans-serif;">ขั้นตอนการดำเนินงานงบประมาณ</h3>
                                <p class="text-muted col-lg-6 mx-auto fs-7">ภาพรวมกระบวนการบริหารงบประมาณและแผน ตั้งแต่เริ่มต้นเสนอโครงการจนสิ้นสุดการเบิกจ่าย</p>
                            </div>
                            
                            <div class="row justify-content-between g-4 position-relative step-flow-container">
                                <!-- Step 1 -->
                                <div class="col-lg-3 col-md-6 text-center px-4 step-item">
                                    <div class="step-num-container mb-3 shadow-sm">
                                        <span class="step-num">01</span>
                                    </div>
                                    <div class="step-icon-bubble mb-3">
                                        <i class="bx bx-edit-alt fs-3 text-orange"></i>
                                    </div>
                                    <h5 class="fw-bold fs-6 mb-2">1. เสนอขออนุมัติโครงการ</h5>
                                    <p class="text-muted fs-8">จัดทำบันทึกข้อความขออนุมัติจัดซื้อจัดจ้าง พร้อมแนบรายละเอียดโครงการและประมาณการค่าใช้จ่าย</p>
                                </div>

                                <!-- Step 2 -->
                                <div class="col-lg-3 col-md-6 text-center px-4 step-item">
                                    <div class="step-num-container mb-3 shadow-sm">
                                        <span class="step-num">02</span>
                                    </div>
                                    <div class="step-icon-bubble mb-3">
                                        <i class="bx bx-cart fs-3 text-orange"></i>
                                    </div>
                                    <h5 class="fw-bold fs-6 mb-2">2. จัดซื้อ / จัดจ้างพัสดุ</h5>
                                    <p class="text-muted fs-8">งานพัสดุดำเนินการจัดทำใบสั่งซื้อ/สั่งจ้าง และส่งมอบให้ผู้รับจ้าง/ร้านค้าดำเนินการ</p>
                                </div>

                                <!-- Step 3 -->
                                <div class="col-lg-3 col-md-6 text-center px-4 step-item">
                                    <div class="step-num-container mb-3 shadow-sm">
                                        <span class="step-num">03</span>
                                    </div>
                                    <div class="step-icon-bubble mb-3">
                                        <i class="bx bx-check-double fs-3 text-orange"></i>
                                    </div>
                                    <h5 class="fw-bold fs-6 mb-2">3. ตรวจรับพัสดุ / งานจ้าง</h5>
                                    <p class="text-muted fs-8">คณะกรรมการตรวจรับพัสดุตรวจสอบความถูกต้อง ครบถ้วนของสินค้าและบริการ</p>
                                </div>

                                <!-- Step 4 -->
                                <div class="col-lg-3 col-md-6 text-center px-4 step-item">
                                    <div class="step-num-container mb-3 shadow-sm completed">
                                        <i class="bx bx-check fs-4"></i>
                                    </div>
                                    <div class="step-icon-bubble mb-3">
                                        <i class="bx bx-money fs-3 text-success"></i>
                                    </div>
                                    <h5 class="fw-bold fs-6 mb-2">4. เบิกจ่ายและตัดทะเบียนคุม</h5>
                                    <p class="text-muted fs-8">งานการเงินและแผนตัดงบประมาณในทะเบียนคุม และดำเนินการจ่ายเงินให้แก่ผู้รับจ้าง</p>
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
    :root {
        --primary-orange: #FB8C00;
        --secondary-orange: #FFA534;
        --dark-orange: #E65100;
        --soft-orange: #fff4e6;
        --luxury-dark: #1e1e24;
    }

    /* Hero Banner */
    .luxury-hero-card {
        background: linear-gradient(135deg, #1e1e24 0%, #2b2b36 100%);
        border-radius: 24px !important;
        box-shadow: 0 20px 40px rgba(0,0,0,0.1);
    }
    .hero-glow-1 {
        position: absolute;
        top: -50%;
        left: -10%;
        width: 350px;
        height: 350px;
        background: radial-gradient(circle, rgba(251, 140, 0, 0.25) 0%, rgba(251, 140, 0, 0) 70%);
        pointer-events: none;
    }
    .hero-glow-2 {
        position: absolute;
        bottom: -50%;
        right: -10%;
        width: 400px;
        height: 400px;
        background: radial-gradient(circle, rgba(251, 140, 0, 0.15) 0%, rgba(251, 140, 0, 0) 70%);
        pointer-events: none;
    }
    .hero-title {
        font-family: 'Plus Jakarta Sans', 'K2D', sans-serif;
        font-weight: 700;
        color: #ffffff;
        font-size: 2.2rem;
        letter-spacing: -0.5px;
    }
    .hero-subtitle {
        font-family: 'K2D', sans-serif;
        color: rgba(255,255,255,0.85);
        font-size: 1.05rem;
        line-height: 1.6;
    }
    .system-badge {
        background: rgba(251, 140, 0, 0.2);
        color: var(--primary-orange);
        border: 1px solid rgba(251, 140, 0, 0.4);
        padding: 6px 14px;
        font-size: 0.8rem;
        font-weight: 700;
        border-radius: 30px;
        letter-spacing: 0.5px;
    }
    .status-dot-pulse {
        width: 8px;
        height: 8px;
        background: #00e676;
        border-radius: 50%;
        box-shadow: 0 0 0 rgba(0, 230, 118, 0.4);
        animation: pulseDot 2s infinite;
    }
    @keyframes pulseDot {
        0% { box-shadow: 0 0 0 0 rgba(0, 230, 118, 0.7); }
        70% { box-shadow: 0 0 0 8px rgba(0, 230, 118, 0); }
        100% { box-shadow: 0 0 0 0 rgba(0, 230, 118, 0); }
    }
    .hero-illustration {
        max-height: 160px;
        filter: drop-shadow(0 15px 30px rgba(0,0,0,0.3));
    }

    /* Action Cards */
    .luxury-action-card {
        background: #ffffff;
        border-radius: 20px !important;
        border: 1px solid rgba(0,0,0,0.04) !important;
        transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1);
        box-shadow: 0 4px 15px rgba(0,0,0,0.02);
    }
    .luxury-action-card:hover {
        transform: translateY(-6px);
        box-shadow: 0 16px 30px rgba(251, 140, 0, 0.12) !important;
        border-color: rgba(251, 140, 0, 0.3) !important;
    }
    .luxury-icon-wrapper {
        width: 52px;
        height: 52px;
        border-radius: 14px;
        background: var(--soft-orange);
        color: var(--primary-orange);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.6rem;
        transition: all 0.3s ease;
    }
    .luxury-action-card:hover .luxury-icon-wrapper {
        background: var(--primary-orange);
        color: #ffffff;
        transform: scale(1.06);
    }
    .card-heading {
        font-family: 'K2D', sans-serif;
        font-weight: 700;
        color: var(--luxury-dark);
        font-size: 1.15rem;
    }
    .card-desc {
        font-family: 'K2D', sans-serif;
        font-size: 0.85rem;
        color: #718096;
        line-height: 1.5;
    }
    .action-text {
        font-size: 0.88rem;
        font-weight: 700;
        color: var(--primary-orange);
        transition: all 0.2s ease;
    }
    .luxury-action-card:hover .action-text {
        color: var(--dark-orange);
    }
    .bg-orange-soft {
        background-color: var(--soft-orange) !important;
    }
    .text-orange {
        color: var(--primary-orange) !important;
    }

    /* Workflow Steps */
    .budget-steps-card {
        background: #ffffff;
    }
    .step-num-container {
        width: 48px;
        height: 48px;
        border-radius: 50%;
        background: #ffffff;
        border: 2px solid #e2e8f0;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto;
        transition: all 0.3s ease;
    }
    .step-num {
        font-family: 'Plus Jakarta Sans', sans-serif;
        font-weight: 800;
        color: #a0aec0;
        font-size: 0.9rem;
    }
    .step-item:hover .step-num-container:not(.completed) {
        background: var(--soft-orange);
        border-color: var(--primary-orange);
    }
    .step-item:hover .step-num {
        color: var(--primary-orange);
    }
    .step-num-container.completed {
        background: #e6fcf5;
        border-color: #38d9a9;
        color: #0ca678;
    }
    .step-icon-bubble {
        width: 56px;
        height: 56px;
        border-radius: 16px;
        background: #f8f9fa;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto;
        transition: all 0.3s ease;
    }
    .step-item:hover .step-icon-bubble {
        background: var(--soft-orange);
        transform: translateY(-2px);
    }
</style>