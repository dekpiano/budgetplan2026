<!-- Layout container -->
<div class="layout-page">
    <?php echo view('Admin/AdminLeyout/AdminNavbar'); ?>

    <!-- Content wrapper -->
    <div class="content-wrapper">
        <!-- Content -->
        <div class="container-xxl flex-grow-1 container-p-y">

            <!-- Hero Welcome Card -->
            <div class="row mb-4">
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
                                        <svg viewBox="0 0 200 150" class="img-fluid d-block mx-auto" style="max-height: 120px; filter: drop-shadow(0 10px 20px rgba(255,255,255,0.15));">
                                            <circle cx="100" cy="75" r="45" fill="rgba(255,255,255,0.2)" />
                                            <rect x="65" y="45" width="70" height="60" rx="8" fill="#ffffff" />
                                            <rect x="75" y="55" width="50" height="8" rx="2" fill="#FB8C00" />
                                            <rect x="75" y="70" width="35" height="6" rx="2" fill="#E2E8F0" />
                                            <rect x="75" y="82" width="45" height="6" rx="2" fill="#E2E8F0" />
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

            <!-- Stats Overview Cards -->
            <div class="row mb-4">
                <!-- Card 1: Total Registered Budget -->
                <div class="col-lg-4 col-md-6 mb-3">
                    <div class="card luxury-stat-card border-0 shadow-sm">
                        <div class="card-body p-4">
                            <div class="d-flex align-items-center justify-content-between mb-3">
                                <div class="stat-icon-container">
                                    <i class="bx bx-wallet-alt"></i>
                                </div>
                                <span class="badge bg-success-soft text-success px-3 py-1 rounded-pill fs-8">ยอดจดทะเบียน</span>
                            </div>
                            <span class="stat-card-title text-muted">ยอดรวมทะเบียนคุมสั่งจ้าง/สั่งซื้อ</span>
                            <h2 class="stat-card-number mt-1 mb-2"><?= number_format($total_amount, 2); ?> <span class="currency">บาท</span></h2>
                            <p class="stat-card-footer mb-0 text-success"><i class="bx bx-receipt me-1"></i> รวมจากทั้งหมด <?= $total_orders; ?> รายการ</p>
                        </div>
                    </div>
                </div>

                <!-- Card 2: Processed rate -->
                <div class="col-lg-4 col-md-6 mb-3">
                    <div class="card luxury-stat-card border-0 shadow-sm">
                        <div class="card-body p-4">
                            <div class="d-flex align-items-center justify-content-between mb-3">
                                <div class="stat-icon-container warning">
                                    <i class="bx bx-check-square"></i>
                                </div>
                                <span class="badge bg-warning-soft text-warning px-3 py-1 rounded-pill fs-8">
                                    <?= $total_orders > 0 ? round(($total_sent / $total_orders) * 100, 1) : 0; ?>% สำเร็จ
                                </span>
                            </div>
                            <span class="stat-card-title text-muted">จำนวนรายการทำฎีกาแล้ว</span>
                            <h2 class="stat-card-number mt-1 mb-2"><?= $total_sent; ?> <span class="currency">รายการ</span></h2>
                            <div class="progress mb-1" style="height: 6px;">
                                <div class="progress-bar bg-warning" role="progressbar" style="width: <?= $total_orders > 0 ? ($total_sent / $total_orders) * 100 : 0; ?>%;"></div>
                            </div>
                            <p class="stat-card-footer mb-0 text-muted">ส่งลงระบบและทำฎีกาเรียบร้อยแล้ว</p>
                        </div>
                    </div>
                </div>

                <!-- Card 3: Pending Action -->
                <div class="col-lg-4 col-md-12 mb-3">
                    <div class="card luxury-stat-card border-0 shadow-sm">
                        <div class="card-body p-4">
                            <div class="d-flex align-items-center justify-content-between mb-3">
                                <div class="stat-icon-container info">
                                    <i class="bx bx-time-five"></i>
                                </div>
                                <span class="badge bg-info-soft text-info px-3 py-1 rounded-pill fs-8">รอดำเนินการ</span>
                            </div>
                            <span class="stat-card-title text-muted">รายการอยู่ระหว่างรอดำเนินการ</span>
                            <h2 class="stat-card-number mt-1 mb-2"><?= ($total_orders - $total_sent); ?> <span class="currency">รายการ</span></h2>
                            <p class="stat-card-footer mb-0 text-info"><i class="bx bx-refresh me-1"></i> รอประสานงานทำฎีกาต่อ</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Charts Section -->
            <div class="row mb-4">
                <!-- Utilisation Chart -->
                <div class="col-lg-8 mb-4">
                    <div class="card border-0 shadow-sm h-100" style="border-radius: 20px;">
                        <div class="card-header bg-white border-0 pt-4 px-4 pb-0">
                            <h5 class="mb-1" style="font-weight: 700; color: var(--luxury-dark);"><i class="bx bx-chart text-warning me-2"></i>วิเคราะห์งบประมาณรายเดือน</h5>
                            <p class="text-muted fs-8 mb-0">แสดงมูลค่าการจดทะเบียนใบสั่งจ้าง/สั่งซื้อแยกตามเดือน ประจำปีงบประมาณนี้</p>
                        </div>
                        <div class="card-body px-4">
                            <div id="utilizationChart" style="min-height: 280px;"></div>
                        </div>
                    </div>
                </div>

                <!-- Circular Gauge -->
                <div class="col-lg-4 mb-4">
                    <div class="card border-0 shadow-sm h-100" style="border-radius: 20px;">
                        <div class="card-header bg-white border-0 pt-4 px-4 pb-0">
                            <h5 class="mb-1" style="font-weight: 700; color: var(--luxury-dark);"><i class="bx bx-pie-chart-alt-2 text-warning me-2"></i>สัดส่วนฎีกาสำเร็จ</h5>
                            <p class="text-muted fs-8 mb-0">อัตราการทำฎีกาเมื่อเทียบกับใบสั่งจ้างทั้งหมด</p>
                        </div>
                        <div class="card-body px-4 d-flex flex-column align-items-center justify-content-center">
                            <div id="sentStatusChart"></div>
                            <div class="text-center mt-3">
                                <span class="badge bg-success-soft text-success px-3 py-1 rounded-pill fw-semibold">
                                    ตรวจรับแล้ว: <?= $total_sent; ?> จาก <?= $total_orders; ?>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Recent Activity & Quick Navigation -->
            <div class="row mb-5">
                <!-- Recent orders summary -->
                <div class="col-lg-8 mb-4">
                    <div class="card border-0 shadow-sm h-100" style="border-radius: 20px;">
                        <div class="card-header bg-white border-0 pt-4 px-4 d-flex align-items-center justify-content-between">
                            <div>
                                <h5 class="mb-1" style="font-weight: 700; color: var(--luxury-dark);"><i class="bx bx-time me-2 text-warning"></i>รายการทะเบียนคุมล่าสุด</h5>
                                <p class="text-muted fs-8 mb-0">แสดง 5 รายการที่ลงทะเบียนเข้าสู่ระบบล่าสุด</p>
                            </div>
                            <a href="<?= base_url('Admin/Registry'); ?>" class="btn btn-sm btn-outline-warning rounded-pill px-3">ดูทั้งหมด</a>
                        </div>
                        <div class="card-body px-4 pb-4">
                            <div class="table-responsive">
                                <table class="table table-hover align-middle">
                                    <thead>
                                        <tr class="table-light text-secondary fs-8">
                                            <th class="text-center">เลขที่</th>
                                            <th class="text-center">วันที่</th>
                                            <th>รายการ</th>
                                            <th class="text-end">จำนวนเงิน</th>
                                            <th>ผู้รับจ้าง</th>
                                            <th class="text-center">สถานะ</th>
                                        </tr>
                                    </thead>
                                    <tbody class="fs-8">
                                        <?php if (!empty($recent_orders)): ?>
                                            <?php foreach ($recent_orders as $order): ?>
                                                <tr>
                                                    <td class="text-center fw-bold text-dark"><?= esc($order->order_number); ?></td>
                                                    <td class="text-center">
                                                        <?php 
                                                            $d = date_create($order->order_date);
                                                            echo date_format($d, "d/m/") . (intval(date_format($d, "Y")) + 543);
                                                        ?>
                                                    </td>
                                                    <td class="text-truncate" style="max-width: 180px;"><?= esc($order->description); ?></td>
                                                    <td class="text-end fw-bold text-orange"><?= number_format($order->amount, 2); ?></td>
                                                    <td><?= esc($order->contractor); ?></td>
                                                    <td class="text-center">
                                                        <?php if ($order->system_sent_status == 'ส่งลงระบบ/ทำฎีกาแล้ว'): ?>
                                                            <span class="badge bg-success-soft text-success px-2 py-1 rounded-pill">ทำฎีกาแล้ว</span>
                                                        <?php else: ?>
                                                            <span class="badge bg-secondary-soft text-secondary px-2 py-1 rounded-pill">รอส่ง</span>
                                                        <?php endif; ?>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        <?php else: ?>
                                            <tr>
                                                <td colspan="6" class="text-center text-muted py-4">ไม่มีข้อมูลทะเบียนคุมบันทึกในระบบขณะนี้</td>
                                            </tr>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Quick Actions / Quick Menu -->
                <div class="col-lg-4 mb-4">
                    <div class="card border-0 shadow-sm h-100" style="border-radius: 20px;">
                        <div class="card-header bg-white border-0 pt-4 px-4 pb-2">
                            <h5 class="mb-1" style="font-weight: 700; color: var(--luxury-dark);"><i class="bx bx-rocket text-warning me-2"></i>เมนูจัดการระบบ</h5>
                            <p class="text-muted fs-8 mb-0">เข้าทำงานและจัดการข้อมูลส่วนต่าง ๆ อย่างรวดเร็ว</p>
                        </div>
                        <div class="card-body px-4 pb-4">
                            <div class="d-flex flex-column gap-3">
                                <!-- Action 1 -->
                                <a href="<?= base_url('Admin/Registry'); ?>" class="text-decoration-none d-flex align-items-center justify-content-between p-3 border rounded-3 bg-light-hover transition-all">
                                    <div class="d-flex align-items-center gap-3">
                                        <div class="avatar bg-warning-soft text-warning rounded p-2">
                                            <i class="bx bx-spreadsheet fs-4"></i>
                                        </div>
                                        <div>
                                            <h6 class="mb-0 fw-bold text-dark fs-8">ทะเบียนคุมใบสั่งจ้าง/สั่งซื้อ</h6>
                                            <span class="text-muted fs-9">บันทึก อัปโหลดรูป ลบข้อมูลคุมสั่งจ้าง</span>
                                        </div>
                                    </div>
                                    <i class="bx bx-chevron-right text-muted"></i>
                                </a>
                                <!-- Action 2 -->
                                <a href="<?= base_url('User/Procurement/Process'); ?>" class="text-decoration-none d-flex align-items-center justify-content-between p-3 border rounded-3 bg-light-hover transition-all">
                                    <div class="d-flex align-items-center gap-3">
                                        <div class="avatar bg-orange-soft text-orange rounded p-2">
                                            <i class="bx bx-cart fs-4"></i>
                                        </div>
                                        <div>
                                            <h6 class="mb-0 fw-bold text-dark fs-8">ขั้นตอนการจัดซื้อ/จัดจ้าง</h6>
                                            <span class="text-muted fs-9">ดูแผนผังงานพัสดุและกระบวนการอนุมัติ</span>
                                        </div>
                                    </div>
                                    <i class="bx bx-chevron-right text-muted"></i>
                                </a>
                                <!-- Action 3 -->
                                <a href="<?= base_url('User/Procurement/MoneyReceipt'); ?>" class="text-decoration-none d-flex align-items-center justify-content-between p-3 border rounded-3 bg-light-hover transition-all">
                                    <div class="d-flex align-items-center gap-3">
                                        <div class="avatar bg-info-soft text-info rounded p-2">
                                            <i class="bx bx-file fs-4"></i>
                                        </div>
                                        <div>
                                            <h6 class="mb-0 fw-bold text-dark fs-8">ใบสำคัญรับเงินตอบแทน</h6>
                                            <span class="text-muted fs-9">ออกใบสำคัญรับเงินสำหรับวิทยากร</span>
                                        </div>
                                    </div>
                                    <i class="bx bx-chevron-right text-muted"></i>
                                </a>
                            </div>
                        </div>
                    </div>
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

    .bg-light-hover:hover {
        background-color: rgba(251, 140, 0, 0.05) !important;
        border-color: rgba(251, 140, 0, 0.2) !important;
    }
    .transition-all {
        transition: all 0.2s ease-in-out;
    }
</style>

<!-- ApexCharts scripts -->
<script>
    document.addEventListener("DOMContentLoaded", function () {
        // Line/Area utilization chart
        const utilizationOptions = {
            series: [{
                name: 'ยอดทะเบียนงบรวม (บาท)',
                data: <?= json_encode($monthly_totals); ?>
            }],
            chart: {
                type: 'area',
                height: 280,
                toolbar: {
                    show: false
                },
                zoom: {
                    enabled: false
                }
            },
            colors: ['#FB8C00'],
            dataLabels: {
                enabled: false
            },
            stroke: {
                curve: 'smooth',
                width: 3
            },
            fill: {
                type: 'gradient',
                gradient: {
                    shadeIntensity: 1,
                    opacityFrom: 0.45,
                    opacityTo: 0.05,
                    stops: [0, 90, 100]
                }
            },
            xaxis: {
                categories: ['ม.ค.', 'ก.พ.', 'มี.ค.', 'เม.ย.', 'พ.ค.', 'มิ.ย.', 'ก.ค.', 'ส.ค.', 'ก.ย.', 'ต.ค.', 'พ.ย.', 'ธ.ค.'],
                labels: {
                    style: {
                        colors: '#718096',
                        fontSize: '11px',
                        fontFamily: 'Sarabun'
                    }
                }
            },
            yaxis: {
                labels: {
                    formatter: function (val) {
                        return (val / 1000).toFixed(0) + 'k';
                    },
                    style: {
                        colors: '#718096',
                        fontSize: '11px',
                        fontFamily: 'Sarabun'
                    }
                }
            },
            tooltip: {
                y: {
                    formatter: function (val) {
                        return val.toLocaleString('th-TH') + ' บาท';
                    }
                }
            }
        };

        const utilizationChart = new ApexCharts(document.querySelector("#utilizationChart"), utilizationOptions);
        utilizationChart.render();

        // Circular Gauge
        const percentageSent = <?= $total_orders > 0 ? round(($total_sent / $total_orders) * 100, 0) : 0; ?>;
        const sentOptions = {
            series: [percentageSent],
            chart: {
                type: 'radialBar',
                height: 250,
                sparkline: {
                    enabled: true
                }
            },
            plotOptions: {
                radialBar: {
                    startAngle: -90,
                    endAngle: 90,
                    track: {
                        background: "#f3f3f3",
                        strokeWidth: '97%',
                        margin: 5, // margin is in pixels
                        dropShadow: {
                            enabled: false
                        }
                    },
                    dataLabels: {
                        name: {
                            show: false
                        },
                        value: {
                            offsetY: -2,
                            fontSize: '22px',
                            fontWeight: 'bold',
                            fontFamily: 'Plus Jakarta Sans',
                            formatter: function (val) {
                                return val + "%";
                            }
                        }
                    }
                }
            },
            grid: {
                padding: {
                    top: -10
                }
            },
            fill: {
                type: 'gradient',
                gradient: {
                    shade: 'light',
                    shadeIntensity: 0.4,
                    inverseColors: false,
                    opacityFrom: 1,
                    opacityTo: 1,
                    stops: [0, 50, 53, 91]
                },
            },
            labels: ['ทำฎีกาสำเร็จ'],
            colors: ['#FB8C00']
        };

        const sentStatusChart = new ApexCharts(document.querySelector("#sentStatusChart"), sentOptions);
        sentStatusChart.render();
    });
</script>