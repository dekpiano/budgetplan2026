<!-- Layout container -->
<div class="layout-page align-self-center">
    <div class="content-wrapper d-flex align-items-center justify-content-center" style="min-height: 80vh;">
        <div class="container-xxl">
            <div class="row justify-content-center">
                <div class="col-md-6 col-lg-5 col-12">
                    
                    <!-- Login Card -->
                    <div class="card border-0 shadow-lg position-relative overflow-hidden" style="border-radius: 20px; background: rgba(255, 255, 255, 0.95); backdrop-filter: blur(10px);">
                        <!-- แถบสีสีชมพู-ฟ้า สัญลักษณ์ของโรงเรียนสวนกุหลาบ -->
                        <div class="position-absolute top-0 start-0 w-100" style="height: 6px; background: linear-gradient(90deg, #e05780 0%, #e05780 50%, #2f80ed 50%, #2f80ed 100%);"></div>
                        
                        <div class="card-body p-5 text-center">
                            <!-- Logo School -->
                            <div class="mb-4">
                                <img src="https://skj.ac.th/uploads/logoSchool/LogoSKJ_4.png" alt="SKJ Logo" class="img-fluid" style="max-height: 90px; filter: drop-shadow(0 4px 8px rgba(0,0,0,0.1));">
                            </div>
                            
                            <!-- Titles -->
                            <h4 class="fw-bold mb-1" style="font-family: 'Sarabun', sans-serif; color: #0d3b66;">ระบบงานงบประมาณและแผน</h4>
                            <p class="text-muted mb-4 small" style="letter-spacing: 0.5px;">โรงเรียนสวนกุหลาบวิทยาลัย (จิรประวัติ) นครสวรรค์</p>
                            
                            <!-- Decorative Box -->
                            <div class="p-3 mb-4 rounded-3 text-start" style="background-color: #f8f9fa; border-left: 4px solid #0077b6;">
                                <small class="text-secondary d-block fw-bold"><i class="bx bx-info-circle align-middle me-1"></i> เข้าสู่ระบบเฉพาะเจ้าหน้าที่</small>
                                <span class="text-muted" style="font-size: 0.8rem; line-height: 1.3;">กรุณาใช้อีเมลของโรงเรียน (@skj.ac.th) เพื่อยืนยันตัวตนผ่าน Google</span>
                            </div>
                            
                            <!-- Google Login Button -->
                            <div class="d-grid gap-2 mb-4">
                                <?php 
                                    // แปลงลิงก์ Google button ให้มีความสวยงามขึ้น
                                    if (preg_match('/href="([^"]+)"/', $GoogleButton, $matches)) {
                                        $url = $matches[1];
                                        echo '<a href="'.$url.'" class="btn btn-google-custom py-3 fw-bold d-flex align-items-center justify-content-center shadow-sm" style="transition: all 0.3s; border-radius: 12px;">';
                                        echo '<svg class="me-2" width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">';
                                        echo '<path d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z" fill="#4285F4"/>';
                                        echo '<path d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z" fill="#34A853"/>';
                                        echo '<path d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.06H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.94l2.85-2.22.81-.63z" fill="#FBBC05"/>';
                                        echo '<path d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.06l3.66 2.84c.87-2.6 3.3-4.52 6.16-4.52z" fill="#EA4335"/>';
                                        echo '</svg>';
                                        echo 'ลงชื่อเข้าใช้ด้วย Google';
                                        echo '</a>';
                                    } else {
                                        echo $GoogleButton; 
                                    }
                                ?>
                            </div>
                            
                            <hr class="my-4 text-muted">
                            
                            <!-- Bottom Info -->
                            <div class="d-flex justify-content-between align-items-center text-muted" style="font-size: 0.75rem;">
                                <span><i class="bx bx-lock-alt"></i> ข้อมูลปลอดภัย</span>
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
    .btn-google-custom {
        background-color: #ffffff;
        color: #4f5e71;
        border: 1px solid #dadce0;
    }
    .btn-google-custom:hover {
        background-color: #f7f9fa;
        color: #2b3d51;
        border-color: #d0d4dc;
        box-shadow: 0 4px 12px rgba(0,0,0,0.08) !important;
        transform: translateY(-1px);
    }
    .btn-google-custom:active {
        transform: translateY(0);
        box-shadow: none !important;
    }
</style>

<?php if (session()->getFlashdata('Error')): ?>
<script>
    window.addEventListener('DOMContentLoaded', (event) => {
        Swal.fire({
            icon: 'error',
            title: 'เข้าสู่ระบบล้มเหลว',
            text: '<?= esc(session()->getFlashdata('Error')) ?>',
            confirmButtonColor: '#3085d6',
            confirmButtonText: 'ตกลง'
        });
    });
</script>
<?php endif; ?>