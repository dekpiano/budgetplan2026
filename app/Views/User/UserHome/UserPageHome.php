<!-- Layout container -->
<div class="layout-page">
    <?php echo view('User/UserLeyout/UserNavbar'); ?>

    <div class="content-wrapper">
        <div class="container-xxl flex-grow-1 container-p-y">

            
            <!-- Hero / Welcome Banner (Luxury Minimalist) -->
            <div class="row mb-5">
                <div class="col-12">
                    <div class="card border-0 position-relative overflow-hidden luxury-hero-card">
                        <!-- Background glow effect -->
                        <div class="hero-glow-1"></div>
                        <div class="hero-glow-2"></div>
                        
                        <div class="card-body p-4 p-md-5 position-relative z-index-1">
                            <div class="row align-items-center">
                                <div class="col-lg-8">
                                    <div class="d-flex align-items-center mb-3">
                                        <span class="system-badge">BUDGET PLAN SYSTEM</span>
                                        <div class="status-dot-pulse ms-2"></div>
                                    </div>
                                    <h1 class="hero-title mb-2">ระบบงานงบประมาณและแผน</h1>
                                    <p class="hero-subtitle mb-0">
                                        โรงเรียนสวนกุหลาบวิทยาลัย (จิรประวัติ) นครสวรรค์
                                    </p>
                                    <div class="d-flex align-items-center mt-4 text-white gap-4 fs-7">
                                        <span class="d-flex align-items-center opacity-90"><i class="bx bx-check-shield me-1 text-white"></i> โปร่งใส</span>
                                        <span class="d-flex align-items-center opacity-90"><i class="bx bx-compass me-1 text-white"></i> วางแผนแม่นยำ</span>
                                        <span class="d-flex align-items-center opacity-90"><i class="bx bx-data me-1 text-white"></i> ตรวจสอบได้</span>
                                    </div>
                                </div>
                                <div class="col-lg-4 text-center d-none d-lg-block">
                                    <div class="hero-graphic-container">
                                        <div class="graphic-circle"></div>
                                        <img src="<?=base_url();?>uploads/Procurement/undraw_finance_m6vw.svg" alt="Finance" class="hero-illustration">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Quick Access / Feature Cards -->
            <div class="row mb-5">
                <div class="col-12 mb-4">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <h4 class="section-title mb-1">เมนูเข้าถึงด่วน</h4>
                            <p class="text-muted fs-7 mb-0">เริ่มต้นจัดการงานโครงการและเอกสารทางการเงินของคุณ</p>
                        </div>
                        <div class="section-line"></div>
                    </div>
                </div>
                
                <!-- จัดซื้อจัดจ้าง -->
                <div class="col-md-6 mb-4">
                    <a href="<?=base_url('User/Procurement/Process');?>" class="text-decoration-none">
                        <div class="card luxury-action-card h-100">
                            <div class="card-body p-4 d-flex flex-column justify-content-between">
                                <div class="row align-items-center">
                                    <div class="col-8">
                                        <div class="d-flex justify-content-between align-items-start mb-4">
                                            <div class="luxury-icon-wrapper">
                                                <i class="bx bx-shopping-bag"></i>
                                            </div>
                                        </div>
                                        <h4 class="card-heading">ขั้นตอนจัดซื้อ / จัดจ้าง</h4>
                                        <p class="card-desc mb-0">คู่มือขั้นตอนงานพัสดุ การจัดเตรียมเอกสาร และกระบวนการขออนุมัติงบประมาณภายในโปรเจกต์</p>
                                    </div>
                                    <div class="col-4 text-center">
                                        <!-- Procurement unDraw Illustration -->
                                        <svg viewBox="0 0 200 150" class="img-fluid d-block mx-auto" style="max-height: 100px; width: 100%; filter: drop-shadow(0 8px 16px rgba(251, 140, 0, 0.1));">
                                            <circle cx="100" cy="75" r="50" fill="#fff4e6" />
                                            <rect x="50" y="60" width="100" height="60" rx="10" fill="#FB8C00" />
                                            <rect x="65" y="45" width="70" height="15" rx="5" fill="#FFA534" />
                                            <rect x="85" y="25" width="40" height="50" rx="5" fill="#ffffff" stroke="#e2e8f0" stroke-width="2" />
                                            <line x1="95" y1="38" x2="115" y2="38" stroke="#FB8C00" stroke-width="3" stroke-linecap="round" />
                                            <line x1="95" y1="48" x2="110" y2="48" stroke="#a0aec0" stroke-width="3" stroke-linecap="round" />
                                            <line x1="95" y1="58" x2="105" y2="58" stroke="#a0aec0" stroke-width="3" stroke-linecap="round" />
                                            <circle cx="45" cy="45" r="8" fill="#e2e8f0" />
                                            <circle cx="160" cy="85" r="10" fill="#ffd8a8" />
                                            <circle cx="150" cy="40" r="6" fill="#38d9a9" />
                                        </svg>
                                    </div>
                                </div>
                                <div class="mt-4 pt-3 border-top border-light d-flex align-items-center justify-content-between">
                                    <span class="action-text">เข้าสู่ขั้นตอนจัดซื้อ / จัดจ้าง <i class="bx bx-right-arrow-alt align-middle ms-1"></i></span>
                                    <span class="badge bg-orange-soft text-orange px-3 py-2 rounded-pill fs-8">Procurement</span>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>

                <!-- ใบสำคัญรับเงิน -->
                <div class="col-md-6 mb-4">
                    <a href="<?=base_url('User/Procurement/MoneyReceipt');?>" class="text-decoration-none">
                        <div class="card luxury-action-card h-100">
                            <div class="card-body p-4 d-flex flex-column justify-content-between">
                                <div class="row align-items-center">
                                    <div class="col-8">
                                        <div class="d-flex justify-content-between align-items-start mb-4">
                                            <div class="luxury-icon-wrapper theme-alt">
                                                <i class="bx bx-receipt"></i>
                                            </div>
                                        </div>
                                        <h4 class="card-heading">ใบสำคัญรับเงินตอบแทน</h4>
                                        <p class="card-desc mb-0">ระบบออกเอกสารและใบสำคัญรับเงินสำหรับวิทยากรภายนอกแบบอัตโนมัติ สะดวก รวดเร็ว และถูกต้องตามระเบียบ</p>
                                    </div>
                                    <div class="col-4 text-center">
                                        <!-- Money Receipt unDraw Illustration -->
                                        <svg viewBox="0 0 200 150" class="img-fluid d-block mx-auto" style="max-height: 100px; width: 100%; filter: drop-shadow(0 8px 16px rgba(251, 140, 0, 0.1));">
                                            <circle cx="100" cy="75" r="50" fill="#fff4e6" />
                                            <path d="M60 30 L120 30 L140 50 L140 120 L60 120 Z" fill="#ffffff" stroke="#e2e8f0" stroke-width="2" />
                                            <path d="M120 30 L120 50 L140 50 Z" fill="#e2e8f0" />
                                            <line x1="75" y1="50" x2="110" y2="50" stroke="#FB8C00" stroke-width="4" stroke-linecap="round" />
                                            <line x1="75" y1="65" x2="125" y2="65" stroke="#718096" stroke-width="2.5" stroke-linecap="round" />
                                            <line x1="75" y1="78" x2="125" y2="78" stroke="#718096" stroke-width="2.5" stroke-linecap="round" />
                                            <line x1="75" y1="91" x2="115" y2="91" stroke="#718096" stroke-width="2.5" stroke-linecap="round" />
                                            <circle cx="140" cy="100" r="16" fill="#FB8C00" />
                                            <circle cx="140" cy="100" r="11" fill="#FFA534" />
                                            <text x="134" y="105" fill="#ffffff" font-family="Plus Jakarta Sans" font-weight="bold" font-size="14">฿</text>
                                            <circle cx="150" cy="50" r="12" fill="#38d9a9" />
                                            <path d="M145 50 L148 53 L155 46" stroke="#ffffff" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" fill="none" />
                                        </svg>
                                    </div>
                                </div>
                                <div class="mt-4 pt-3 border-top border-light d-flex align-items-center justify-content-between">
                                    <span class="action-text">เขียนใบสำคัญรับเงิน <i class="bx bx-right-arrow-alt align-middle ms-1"></i></span>
                                    <span class="badge bg-orange-soft text-orange px-3 py-2 rounded-pill fs-8">Lecturer Receipt</span>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>
            </div>

            <!-- School Budget Steps Overview -->
            <div class="row">
                <div class="col-12">
                    <div class="card border-0 shadow-sm budget-steps-card" style="border-radius: 20px;">
                        <div class="card-body p-4 p-md-5">
                            <div class="text-center mb-5">
                                <span class="badge bg-orange-soft text-orange px-3 py-2 rounded-pill mb-2 fw-semibold">WORKFLOW</span>
                                <h3 class="fw-bold text-dark" style="font-family: 'Sarabun', sans-serif;">ขั้นตอนการดำเนินงานงบประมาณ</h3>
                                <p class="text-muted col-lg-6 mx-auto fs-7">ภาพรวมกระบวนการบริหารงบประมาณและแผน ตั้งแต่เริ่มต้นเสนอโครงการจนสิ้นสุดการเบิกจ่าย</p>
                            </div>
                            
                            <div class="row justify-content-between g-4 position-relative step-flow-container">
                                <!-- Step 1 -->
                                <div class="col-lg-3 col-md-6 text-center px-4 step-item">
                                    <div class="step-num-container mb-3">
                                        <span class="step-num">01</span>
                                    </div>
                                    <h6 class="fw-bold mb-2 text-dark">เสนอโครงการ</h6>
                                    <p class="text-muted small px-lg-2">ยื่นขออนุมัติโครงการและแผนงบประมาณประจำปีของแต่ละกลุ่มสาระ/งาน</p>
                                </div>
                                <!-- Step 2 -->
                                <div class="col-lg-3 col-md-6 text-center px-4 step-item">
                                    <div class="step-num-container mb-3">
                                        <span class="step-num">02</span>
                                    </div>
                                    <h6 class="fw-bold mb-2 text-dark">จัดซื้อ / จัดจ้าง</h6>
                                    <p class="text-muted small px-lg-2">ดำเนินงานตามขั้นตอนพัสดุและจัดเก็บเอกสารใบเสนอราคา</p>
                                </div>
                                <!-- Step 3 -->
                                <div class="col-lg-3 col-md-6 text-center px-4 step-item">
                                    <div class="step-num-container mb-3">
                                        <span class="step-num">03</span>
                                    </div>
                                    <h6 class="fw-bold mb-2 text-dark">ตรวจสอบ & อนุมัติ</h6>
                                    <p class="text-muted small px-lg-2">งานงบประมาณและผู้อำนวยการตรวจสอบความถูกต้องและอนุมัติจ่าย</p>
                                </div>
                                <!-- Step 4 -->
                                <div class="col-lg-3 col-md-6 text-center px-4 step-item">
                                    <div class="step-num-container completed mb-3">
                                        <i class="bx bx-check fs-4"></i>
                                    </div>
                                    <h6 class="fw-bold mb-2 text-success">เสร็จสมบูรณ์</h6>
                                    <p class="text-muted small px-lg-2">เบิกจ่ายเงินสำเร็จและบันทึกข้อมูลเพื่อส่งประเมินผลโครงการ</p>
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
    /* Premium CSS variables & styling */
    :root {
        --primary-orange: #ff6b35;
        --dark-orange: #d9480f;
        --soft-orange: #fff4e6;
        --luxury-dark: #1e1e24;
        --luxury-gray: #fcfbfa;
    }

    body {
        background-color: #f7f6f3 !important; /* Soft warm minimalist background */
    }

    .text-orange {
        color: var(--primary-orange) !important;
    }
    .bg-orange-soft {
        background-color: var(--soft-orange) !important;
    }

    /* Luxury Hero Card */
    .luxury-hero-card {
        background: linear-gradient(135deg, #FF9F43 0%, #FB8C00 100%);
        border-radius: 24px !important;
        box-shadow: 0 20px 40px rgba(251, 140, 0, 0.2);
    }
    .hero-glow-1 {
        position: absolute;
        top: -50%;
        left: -20%;
        width: 350px;
        height: 350px;
        background: radial-gradient(circle, rgba(255, 255, 255, 0.35) 0%, rgba(255, 255, 255, 0) 70%);
        pointer-events: none;
    }
    .hero-glow-2 {
        position: absolute;
        bottom: -50%;
        right: -10%;
        width: 400px;
        height: 400px;
        background: radial-gradient(circle, rgba(255, 255, 255, 0.25) 0%, rgba(255, 255, 255, 0) 70%);
        pointer-events: none;
    }
    .hero-title {
        font-family: 'Plus Jakarta Sans', 'Sarabun', sans-serif;
        font-weight: 700;
        color: #ffffff;
        font-size: 2.25rem;
        letter-spacing: -0.5px;
        text-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }
    .hero-subtitle {
        font-family: 'Sarabun', sans-serif;
        font-weight: 300;
        color: rgba(255,255,255,0.9);
        font-size: 1.15rem;
        text-shadow: 0 1px 2px rgba(0,0,0,0.08);
    }
    .system-badge {
        background: rgba(255, 255, 255, 0.25);
        color: #ffffff;
        border: 1px solid rgba(255, 255, 255, 0.4);
        padding: 6px 14px;
        font-size: 0.75rem;
        font-weight: 600;
        border-radius: 30px;
        letter-spacing: 1px;
        backdrop-filter: blur(4px);
    }
    .status-dot-pulse {
        width: 8px;
        height: 8px;
        background-color: #2ed573;
        border-radius: 50%;
        box-shadow: 0 0 0 0 rgba(46, 213, 115, 0.7);
        animation: pulse-green 2s infinite;
    }
    @keyframes pulse-green {
        0% {
            transform: scale(0.95);
            box-shadow: 0 0 0 0 rgba(46, 213, 115, 0.7);
        }
        70% {
            transform: scale(1);
            box-shadow: 0 0 0 6px rgba(46, 213, 115, 0);
        }
        100% {
            transform: scale(0.95);
            box-shadow: 0 0 0 0 rgba(46, 213, 115, 0);
        }
    }

    .hero-graphic-container {
        position: relative;
        display: inline-block;
    }
    .graphic-circle {
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        width: 180px;
        height: 180px;
        border-radius: 50%;
        background: rgba(255, 255, 255, 0.1);
        border: 1px dashed rgba(255, 255, 255, 0.35);
        animation: rotate-circle 20s linear infinite;
    }
    @keyframes rotate-circle {
        from { transform: translate(-50%, -50%) rotate(0deg); }
        to { transform: translate(-50%, -50%) rotate(360deg); }
    }
    .hero-illustration {
        position: relative;
        height: 150px;
        z-index: 2;
    }

    /* Section styling */
    .section-title {
        font-family: 'Plus Jakarta Sans', 'Sarabun', sans-serif;
        font-weight: 700;
        color: var(--luxury-dark);
        font-size: 1.35rem;
    }
    .section-line {
        flex-grow: 1;
        height: 1px;
        background: linear-gradient(90deg, rgba(0,0,0,0.06) 0%, rgba(0,0,0,0) 100%);
        margin-left: 20px;
    }

    /* Luxury Action Cards */
    .luxury-action-card {
        background: #ffffff;
        border: 1px solid rgba(0,0,0,0.03) !important;
        border-radius: 20px !important;
        box-shadow: 0 4px 25px rgba(0,0,0,0.02) !important;
        transition: all 0.4s cubic-bezier(0.16, 1, 0.3, 1) !important;
    }
    .luxury-action-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 20px 45px rgba(255, 107, 53, 0.07) !important;
        border-color: rgba(255, 107, 53, 0.15) !important;
    }
    .luxury-icon-wrapper {
        width: 52px;
        height: 52px;
        border-radius: 14px;
        background: #fff4e6;
        color: var(--primary-orange);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
        transition: all 0.3s ease;
    }
    .luxury-action-card:hover .luxury-icon-wrapper {
        background: var(--primary-orange);
        color: #ffffff;
        transform: scale(1.05);
    }
    .arrow-indicator {
        font-size: 1.25rem;
        color: #d1d5db;
        transition: all 0.3s ease;
    }
    .luxury-action-card:hover .arrow-indicator {
        color: var(--primary-orange);
        transform: translateX(3px);
    }
    .rotate-180 {
        transform: rotate(180deg);
        display: inline-block;
    }
    .card-heading {
        font-family: 'Sarabun', sans-serif;
        font-weight: 600;
        color: var(--luxury-dark);
        font-size: 1.15rem;
        margin-bottom: 8px;
    }
    .card-desc {
        font-family: 'Sarabun', sans-serif;
        font-size: 0.88rem;
        color: #718096;
        line-height: 1.5;
    }
    .action-text {
        font-size: 0.9rem;
        font-weight: 600;
        color: var(--luxury-dark);
        transition: all 0.3s ease;
    }
    .luxury-action-card:hover .action-text {
        color: var(--primary-orange);
    }

    /* Workflow Steps */
    .budget-steps-card {
        border: 1px solid rgba(0,0,0,0.02) !important;
    }
    .step-num-container {
        width: 55px;
        height: 55px;
        border-radius: 50%;
        background: #f8f9fa;
        border: 1px solid rgba(0,0,0,0.05);
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto;
        transition: all 0.3s ease;
    }
    .step-num {
        font-family: 'Plus Jakarta Sans', sans-serif;
        font-weight: 700;
        color: #a0aec0;
        font-size: 0.95rem;
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
</style>