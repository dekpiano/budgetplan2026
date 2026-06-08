<!-- Layout container -->
<div class="layout-page align-self-center">
    <div class="content-wrapper d-flex align-items-center justify-content-center" style="min-height: 85vh;">
        <div class="container-xxl">
            <div class="row justify-content-center align-items-center g-5">
                
                <!-- Left Column: unDraw Illustration (visible on lg screens) -->
                <div class="col-lg-6 d-none d-lg-block text-center position-relative">
                    <div class="login-illustration-glow"></div>
                    <img src="<?=base_url();?>uploads/Procurement/undraw_finance_m6vw.svg" alt="Financial Planning" class="img-fluid position-relative z-index-2 animate-float" style="max-height: 360px; filter: drop-shadow(0 20px 40px rgba(251, 140, 0, 0.12));">
                    <div class="mt-4">
                        <h4 class="fw-bold text-dark mb-2" style="font-family: 'Sarabun', sans-serif;">บริหารจัดการงบประมาณอย่างชาญฉลาด</h4>
                        <p class="text-muted small px-5" style="font-family: 'Sarabun', sans-serif; line-height: 1.6;">ระบบวิเคราะห์ ติดตาม และดำเนินงานพัสดุ/ใบสำคัญรับเงิน สะดวก รวดเร็ว โปร่งใส ตรวจสอบได้ทุกขั้นตอน</p>
                    </div>
                </div>

                <!-- Right Column: Login Card -->
                <div class="col-md-8 col-lg-5 col-12">
                    
                    <!-- Login Card (Luxury Minimalist) -->
                    <div class="card border-0 position-relative overflow-hidden luxury-login-card">
                        <!-- Top Accent Line -->
                        <div class="position-absolute top-0 start-0 w-100" style="height: 4px; background: linear-gradient(90deg, #ff6b35 0%, #ff9f43 100%);"></div>
                        
                        <div class="card-body p-4 p-sm-5 text-center">
                            <!-- Logo School -->
                            <div class="mb-4 position-relative d-inline-block">
                                <div class="logo-backdrop"></div>
                                <img src="https://skj.ac.th/uploads/logoSchool/LogoSKJ_4.png" alt="SKJ Logo" class="img-fluid position-relative z-index-2" style="max-height: 90px; filter: drop-shadow(0 8px 16px rgba(255, 107, 53, 0.1));">
                            </div>
                            
                            <!-- Titles -->
                            <h3 class="fw-bold mb-1 login-title">ระบบงานงบประมาณและแผน</h3>
                            <p class="text-muted mb-4 small subtitle-text">โรงเรียนสวนกุหลาบวิทยาลัย (จิรประวัติ) นครสวรรค์</p>
                            
                            <!-- Informational Box (Minimalist Luxury) -->
                            <div class="p-3 mb-4 text-start luxury-info-box">
                                <small class="d-block fw-semibold info-box-title"><i class="bx bx-info-circle align-middle me-1"></i> เข้าสู่ระบบเฉพาะเจ้าหน้าที่</small>
                                <span class="text-muted info-box-desc">กรุณาใช้อีเมลของโรงเรียน (@skj.ac.th) เพื่อยืนยันตัวตนผ่านระบบ Google Workspace ของโรงเรียน</span>
                            </div>
                            
                            <!-- Google Login Button -->
                            <div class="d-grid gap-2 mb-4">
                                <?php 
                                    if (preg_match('/href="([^"]+)"/', $GoogleButton, $matches)) {
                                        $url = $matches[1];
                                        echo '<a href="'.$url.'" class="btn btn-luxury-google py-3 fw-bold d-flex align-items-center justify-content-center" style="border-radius: 12px; transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1);">';
                                        echo '<svg class="me-2" width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">';
                                        echo '<path d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z" fill="#4285F4"/>';
                                        echo '<path d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z" fill="#34A853"/>';
                                        echo '<path d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.06H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.94l2.85-2.22.81-.63z" fill="#FBBC05"/>';
                                        echo '<path d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.06l3.66 2.84c.87-2.6 3.3-4.52 6.16-4.52z" fill="#EA4335"/>';
                                        echo '</svg>';
                                        echo 'ลงชื่อเข้าใช้ด้วย Google Account';
                                        echo '</a>';
                                    } else {
                                        echo $GoogleButton; 
                                    }
                                ?>
                            </div>
                            
                            <hr class="my-4 border-light opacity-50">
                            
                            <!-- Bottom Info -->
                            <div class="d-flex justify-content-between align-items-center text-muted" style="font-size: 0.75rem;">
                                <span><i class="bx bx-lock-alt text-orange"></i> ข้อมูลปลอดภัยด้วยระบบ OAuth 2.0</span>
                                <span>SKJ Budget Plan 2026</span>
                            </div>
                        </div>
                    </div>
                    
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .luxury-login-wrapper {
        background-color: #f7f6f3;
    }
    
    /* Card design */
    .luxury-login-card {
        background: #ffffff;
        border: 1px solid rgba(0, 0, 0, 0.02) !important;
        border-radius: 24px !important;
        box-shadow: 0 20px 50px rgba(0, 0, 0, 0.03) !important;
    }
    
    /* Titles */
    .login-title {
        font-family: 'Plus Jakarta Sans', 'Sarabun', sans-serif;
        font-weight: 700;
        color: #1e1e24;
        letter-spacing: -0.5px;
    }
    .subtitle-text {
        font-family: 'Sarabun', sans-serif;
        font-weight: 400;
        letter-spacing: 0.2px;
    }

    /* Logo background glow */
    .logo-backdrop {
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        width: 120px;
        height: 120px;
        border-radius: 50%;
        background: radial-gradient(circle, rgba(255, 107, 53, 0.08) 0%, rgba(255, 107, 53, 0) 70%);
        z-index: 1;
    }

    /* Info Box */
    .luxury-info-box {
        background-color: #fffaf5;
        border-left: 3px solid #ff6b35;
        border-radius: 12px;
    }
    .info-box-title {
        color: #d9480f;
        font-family: 'Sarabun', sans-serif;
    }
    .info-box-desc {
        font-size: 0.78rem;
        line-height: 1.5;
        display: block;
        margin-top: 4px;
        color: #718096 !important;
    }

    /* Google Button */
    .btn-luxury-google {
        background-color: #ffffff;
        color: #1e1e24;
        border: 1px solid #e2e8f0;
        font-size: 0.95rem;
    }
    .btn-luxury-google:hover {
        background-color: #fffaf5;
        color: #ff6b35;
        border-color: #ffd8a8;
        box-shadow: 0 10px 25px rgba(255, 107, 53, 0.08);
        transform: translateY(-2px);
    }
    .btn-luxury-google:active {
        transform: translateY(0);
        box-shadow: none;
    }

    /* Illustration Glow & Floating */
    .login-illustration-glow {
        position: absolute;
        top: 40%;
        left: 50%;
        transform: translate(-50%, -50%);
        width: 380px;
        height: 380px;
        background: radial-gradient(circle, rgba(251, 140, 0, 0.12) 0%, rgba(251, 140, 0, 0) 70%);
        z-index: 1;
        pointer-events: none;
    }
    .animate-float {
        animation: float 6s ease-in-out infinite;
    }
    @keyframes float {
        0% { transform: translateY(0px); }
        50% { transform: translateY(-10px); }
        100% { transform: translateY(0px); }
    }
</style>

<?php if (session()->getFlashdata('Error')): ?>
<script>
    window.addEventListener('DOMContentLoaded', (event) => {
        Swal.fire({
            icon: 'error',
            title: 'เข้าสู่ระบบล้มเหลว',
            text: '<?= esc(session()->getFlashdata('Error')) ?>',
            confirmButtonColor: '#ff6b35',
            confirmButtonText: 'ตกลง',
            customClass: {
                popup: 'luxury-swal-popup',
                confirmButton: 'luxury-swal-button'
            }
        });
    });
</script>
<?php endif; ?>