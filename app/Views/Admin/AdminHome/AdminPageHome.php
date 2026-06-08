<!-- Layout container -->
<div class="layout-page">
    <?php echo view('Admin/AdminLeyout/AdminNavbar'); ?>

    <!-- Content wrapper -->
    <div class="content-wrapper">
        <!-- Content -->
        <div class="container-xxl flex-grow-1 container-p-y">

            <!-- Hero Welcome Card for Admin Budget -->
            <div class="row mb-5">
                <div class="col-12">
                    <div class="card border-0 position-relative overflow-hidden luxury-admin-hero-card">
                        <div class="hero-glow-1"></div>
                        <div class="hero-glow-2"></div>
                        
                        <div class="card-body p-4 p-md-5 position-relative z-index-1">
                            <div class="row align-items-center">
                                <div class="col-lg-8">
                                    <div class="d-flex align-items-center mb-3">
                                        <span class="system-badge">BUDGET & PLAN CONTROL</span>
                                        <div class="status-dot-pulse ms-2"></div>
                                    </div>
                                    <h1 class="hero-title mb-2">ระบบควบคุมงบประมาณและแผนงาน</h1>
                                    <p class="hero-subtitle mb-0">
                                        ยินดีต้อนรับผู้ดูแลระบบงานงบประมาณและแผน โรงเรียนสวนกุหลาบวิทยาลัย (จิรประวัติ) นครสวรรค์
                                    </p>
                                </div>
                                <div class="col-lg-4 text-center d-none d-lg-block">
                                    <div class="hero-graphic-container">
                                        <div class="graphic-circle"></div>
                                        <!-- Budget Plan illustration -->
                                        <svg viewBox="0 0 200 150" class="img-fluid d-block mx-auto" style="max-height: 120px; filter: drop-shadow(0 10px 20px rgba(255,255,255,0.15));">
                                            <circle cx="100" cy="75" r="45" fill="rgba(255,255,255,0.2)" />
                                            <rect x="65" y="45" width="70" height="60" rx="8" fill="#ffffff" />
                                            <rect x="75" y="55" width="50" height="8" rx="2" fill="#FB8C00" />
                                            <rect x="75" y="70" width="35" height="6" rx="2" fill="#E2E8F0" />
                                            <rect x="75" y="82" width="45" height="6" rx="2" fill="#E2E8F0" />
                                            <!-- Chart bars -->
                                            <rect x="145" y="75" width="10" height="30" rx="2" fill="#ffffff" opacity="0.8" />
                                            <rect x="160" y="60" width="10" height="45" rx="2" fill="#ffffff" />
                                            <rect x="130" y="90" width="10" height="15" rx="2" fill="#ffffff" opacity="0.5" />
                                        </svg>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Budget Overview Stats -->
            <div class="row mb-5">
                <div class="col-12 mb-4">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <h4 class="section-title mb-1">ภาพรวมงบประมาณประจำปี 2026</h4>
                            <p class="text-muted fs-7 mb-0">สรุปข้อมูลสถานะงบประมาณ การใช้งาน และแผนงานโครงการในภาพรวม</p>
                        </div>
                        <div class="section-line"></div>
                    </div>
                </div>

                <!-- Total Approved Budget -->
                <div class="col-lg-4 col-md-6 mb-4">
                    <div class="card luxury-stat-card border-0 shadow-sm">
                        <div class="card-body p-4">
                            <div class="d-flex align-items-center justify-content-between mb-3">
                                <div class="stat-icon-container">
                                    <i class="bx bx-wallet-alt"></i>
                                </div>
                                <span class="badge bg-success-soft text-success px-3 py-1 rounded-pill fs-8">งบประมาณอนุมัติ</span>
                            </div>
                            <span class="stat-card-title text-muted">งบประมาณจัดสรรทั้งหมด</span>
                            <h2 class="stat-card-number mt-1 mb-2">5,450,000.00 <span class="currency">บาท</span></h2>
                            <p class="stat-card-footer mb-0 text-success"><i class="bx bx-up-arrow-alt me-1"></i> โครงการประจำปีงบประมาณ 2569</p>
                        </div>
                    </div>
                </div>

                <!-- Spent Budget -->
                <div class="col-lg-4 col-md-6 mb-4">
                    <div class="card luxury-stat-card border-0 shadow-sm">
                        <div class="card-body p-4">
                            <div class="d-flex align-items-center justify-content-between mb-3">
                                <div class="stat-icon-container warning">
                                    <i class="bx bx-credit-card-front"></i>
                                </div>
                                <span class="badge bg-warning-soft text-warning px-3 py-1 rounded-pill fs-8">ใช้ไปแล้ว 38.9%</span>
                            </div>
                            <span class="stat-card-title text-muted">งบประมาณที่เบิกจ่ายแล้ว</span>
                            <h2 class="stat-card-number mt-1 mb-2">2,120,500.00 <span class="currency">บาท</span></h2>
                            <div class="progress mb-1" style="height: 6px;">
                                <div class="progress-bar bg-warning" role="progressbar" style="width: 38.9%;" aria-valuenow="38.9" aria-valuemin="0" aria-valuemax="100"></div>
                            </div>
                            <p class="stat-card-footer mb-0 text-muted">ผูกพันงบประมาณแล้วตามใบเสนอขออนุมัติ</p>
                        </div>
                    </div>
                </div>

                <!-- Remaining Budget -->
                <div class="col-lg-4 col-md-12 mb-4">
                    <div class="card luxury-stat-card border-0 shadow-sm">
                        <div class="card-body p-4">
                            <div class="d-flex align-items-center justify-content-between mb-3">
                                <div class="stat-icon-container info">
                                    <i class="bx bx-badge-check"></i>
                                </div>
                                <span class="badge bg-info-soft text-info px-3 py-1 rounded-pill fs-8">คงเหลือใช้งาน</span>
                            </div>
                            <span class="stat-card-title text-muted">งบประมาณคงเหลือจริง</span>
                            <h2 class="stat-card-number mt-1 mb-2">3,329,500.00 <span class="currency">บาท</span></h2>
                            <p class="stat-card-footer mb-0 text-info"><i class="bx bx-check-circle me-1"></i> พร้อมจัดสรรสำหรับโครงการส่วนที่เหลือ</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Quick Access / Action Menu for Budget -->
            <div class="row">
                <div class="col-12 mb-4">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <h4 class="section-title mb-1">เมนูการจัดการงบประมาณ</h4>
                            <p class="text-muted fs-7 mb-0">เริ่มต้นเข้าทำงานเกี่ยวกับงานจัดซื้อ/จัดจ้าง และเอกสารตอบแทนวิทยากร</p>
                        </div>
                        <div class="section-line"></div>
                    </div>
                </div>

                <!-- จัดซื้อจัดจ้าง -->
                <div class="col-md-6 mb-4">
                    <a href="<?=base_url('User/Procurement/Process');?>" class="text-decoration-none">
                        <div class="card luxury-admin-card h-100">
                            <div class="card-body p-4 d-flex flex-column justify-content-between">
                                <div class="row align-items-center">
                                    <div class="col-8">
                                        <div class="d-flex align-items-center mb-3">
                                            <div class="luxury-icon-wrapper">
                                                <i class="bx bx-cart"></i>
                                            </div>
                                            <span class="badge bg-orange-soft text-orange ms-3 px-3 py-2 rounded-pill fs-8">Procurement</span>
                                        </div>
                                        <h4 class="card-heading">ขั้นตอนจัดซื้อ / จัดจ้าง</h4>
                                        <p class="card-desc mb-0">คู่มือขั้นตอนงานพัสดุ การตรวจสอบเอกสาร และกระบวนการอนุมัติใบเสนอราคา</p>
                                    </div>
                                    <div class="col-4 text-center">
                                        <svg viewBox="0 0 200 150" class="img-fluid d-block mx-auto" style="max-height: 90px; width: 100%;">
                                            <circle cx="100" cy="75" r="45" fill="#fff4e6" />
                                            <rect x="60" y="60" width="80" height="50" rx="8" fill="#FB8C00" />
                                            <rect x="75" y="45" width="50" height="15" rx="3" fill="#FFA534" />
                                            <circle cx="150" cy="50" r="10" fill="#38d9a9" />
                                        </svg>
                                    </div>
                                </div>
                                <div class="mt-4 pt-3 border-top border-light d-flex align-items-center justify-content-between">
                                    <span class="action-text">เข้าสู่ขั้นตอนจัดซื้อ / จัดจ้าง <i class="bx bx-right-arrow-alt align-middle ms-1"></i></span>
                                    <span class="text-muted fs-8">24 รายการล่าสุด</span>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>

                <!-- ใบสำคัญรับเงินตอบแทน -->
                <div class="col-md-6 mb-4">
                    <a href="<?=base_url('User/Procurement/MoneyReceipt');?>" class="text-decoration-none">
                        <div class="card luxury-admin-card h-100">
                            <div class="card-body p-4 d-flex flex-column justify-content-between">
                                <div class="row align-items-center">
                                    <div class="col-8">
                                        <div class="d-flex align-items-center mb-3">
                                            <div class="luxury-icon-wrapper theme-alt">
                                                <i class="bx bx-file"></i>
                                            </div>
                                            <span class="badge bg-orange-soft text-orange ms-3 px-3 py-2 rounded-pill fs-8">Money Receipt</span>
                                        </div>
                                        <h4 class="card-heading">ใบสำคัญรับเงินตอบแทนวิทยากร</h4>
                                        <p class="card-desc mb-0">ระบบออกเอกสารสำคัญรับเงินสำหรับวิทยากรแบบอัตโนมัติ ถูกต้อง โปร่งใส ตามหลักงบประมาณ</p>
                                    </div>
                                    <div class="col-4 text-center">
                                        <svg viewBox="0 0 200 150" class="img-fluid d-block mx-auto" style="max-height: 90px; width: 100%;">
                                            <circle cx="100" cy="75" r="45" fill="#fff4e6" />
                                            <path d="M70 40 L115 40 L130 55 L130 110 L70 110 Z" fill="#ffffff" stroke="#e2e8f0" stroke-width="2" />
                                            <circle cx="130" cy="95" r="14" fill="#FB8C00" />
                                            <text x="125" y="100" fill="#ffffff" font-family="Plus Jakarta Sans" font-weight="bold" font-size="12">฿</text>
                                        </svg>
                                    </div>
                                </div>
                                <div class="mt-4 pt-3 border-top border-light d-flex align-items-center justify-content-between">
                                    <span class="action-text">จัดการใบสำคัญรับเงิน <i class="bx bx-right-arrow-alt align-middle ms-1"></i></span>
                                    <span class="text-muted fs-8">ตรวจสอบความถูกต้อง</span>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>
            </div>

        </div>
        <!-- / Content -->

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

    /* Luxury Admin Hero Card */
    .luxury-admin-hero-card {
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
        0% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(46, 213, 115, 0.7); }
        70% { transform: scale(1); box-shadow: 0 0 0 6px rgba(46, 213, 115, 0); }
        100% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(46, 213, 115, 0); }
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
        width: 160px;
        height: 160px;
        border-radius: 50%;
        background: rgba(255, 255, 255, 0.1);
        border: 1px dashed rgba(255, 255, 255, 0.35);
        animation: rotate-circle 20s linear infinite;
    }
    @keyframes rotate-circle {
        from { transform: translate(-50%, -50%) rotate(0deg); }
        to { transform: translate(-50%, -50%) rotate(360deg); }
    }

    /* Section Styling */
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

    /* Stat Card Styling */
    .luxury-stat-card {
        background: #ffffff;
        border: 1px solid rgba(0,0,0,0.02) !important;
        border-radius: 20px !important;
        box-shadow: 0 4px 25px rgba(0,0,0,0.02) !important;
        transition: all 0.3s ease;
    }
    .luxury-stat-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 10px 30px rgba(0,0,0,0.04) !important;
    }
    .stat-icon-container {
        width: 44px;
        height: 44px;
        border-radius: 12px;
        background: #e6fcf5;
        color: #0ca678;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.35rem;
    }
    .stat-icon-container.warning {
        background: #fff9db;
        color: #f59f00;
    }
    .stat-icon-container.info {
        background: #e7f5ff;
        color: #1c7ed6;
    }
    .stat-card-title {
        font-family: 'Sarabun', sans-serif;
        font-size: 0.82rem;
        font-weight: 500;
        letter-spacing: 0.2px;
    }
    .stat-card-number {
        font-family: 'Plus Jakarta Sans', 'Sarabun', sans-serif;
        font-weight: 800;
        color: var(--luxury-dark);
        font-size: 1.85rem;
        letter-spacing: -0.5px;
    }
    .stat-card-number .currency {
        font-size: 0.95rem;
        font-weight: 500;
        color: #718096;
        margin-left: 3px;
    }
    .stat-card-footer {
        font-family: 'Sarabun', sans-serif;
        font-size: 0.76rem;
    }
    .bg-success-soft { background-color: #e6fcf5 !important; }
    .bg-warning-soft { background-color: #fff9db !important; }
    .bg-info-soft { background-color: #e7f5ff !important; }

    /* Luxury Admin Cards */
    .luxury-action-card, .luxury-admin-card {
        background: #ffffff;
        border: 1px solid rgba(0,0,0,0.03) !important;
        border-radius: 20px !important;
        box-shadow: 0 4px 25px rgba(0,0,0,0.02) !important;
        transition: all 0.4s cubic-bezier(0.16, 1, 0.3, 1) !important;
    }
    .luxury-admin-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 20px 45px rgba(251, 140, 0, 0.07) !important;
        border-color: rgba(251, 140, 0, 0.15) !important;
    }

    .luxury-icon-wrapper {
        width: 48px;
        height: 48px;
        border-radius: 12px;
        background: #fff4e6;
        color: var(--primary-orange);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.4rem;
        transition: all 0.3s ease;
    }
    .luxury-admin-card:hover .luxury-icon-wrapper {
        background: var(--primary-orange);
        color: #ffffff;
        transform: scale(1.05);
    }
    .luxury-icon-wrapper.theme-alt {
        background: #fff4e6;
        color: var(--primary-orange);
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
    .luxury-admin-card:hover .action-text {
        color: var(--primary-orange);
    }
</style>