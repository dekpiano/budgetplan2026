    <!-- Navbar -->
    <nav class="layout-navbar container-xxl navbar navbar-expand-xl navbar-detached align-items-center bg-navbar-theme modern-glass-navbar"
        id="layout-navbar">
        <!-- Hamburger Toggler (Mobile/Tablet) -->
        <div class="layout-menu-toggle navbar-nav align-items-xl-center me-2 me-xl-0 d-xl-none">
            <a class="nav-item nav-link px-0 me-xl-4 modern-menu-toggler" href="javascript:void(0)" title="เปิด/ปิด เมนู">
                <i class="bx bx-menu fs-4"></i>
            </a>
        </div>

        <div class="navbar-nav-right d-flex align-items-center w-100" id="navbar-collapse">
            <!-- Left: Logo / Title (Responsive) -->
            <div class="navbar-nav align-items-center">
                <div class="nav-item d-flex align-items-center gap-2">
                    <span class="badge bg-label-warning text-warning rounded-pill px-2.5 py-1.5 d-none d-sm-inline-flex align-items-center fs-8">
                        <i class="bx bx-wallet me-1"></i>งบประมาณ
                    </span>
                    <span class="fw-bold text-dark text-truncate d-inline-block navbar-page-title" style="max-width: 280px; font-size: 0.92rem;">
                        <?= esc($title ?? 'ระบบบริหารงานงบประมาณและแผน') ?>
                    </span>
                </div>
            </div>

            <!-- Right: Actions & User Dropdown -->
            <ul class="navbar-nav flex-row align-items-center ms-auto gap-2">
                <?php if (auth_is_admin()): ?>
                    <li class="nav-item d-none d-md-block">
                        <a href="<?= base_url('Admin/BudgetLedger') ?>" class="btn btn-xs btn-primary rounded-pill px-3 shadow-sm" title="สลับไปยังระบบจัดการงบประมาณของผู้ดูแล">
                            <i class="bx bx-cog me-1"></i>จัดการระบบ
                        </a>
                    </li>
                <?php endif; ?>

                <?php if (isset($_SESSION['username'])): ?>
                    <!-- User Info -->
                    <li class="nav-item lh-1 text-end d-none d-sm-block me-1">
                        <div class="fw-bold text-dark text-truncate" style="max-width: 150px; font-size: 0.86rem;">
                            <?= esc($_SESSION['username']) ?>
                        </div>
                        <small class="text-muted" style="font-size: 0.74rem;">
                            <span class="badge bg-label-info rounded-pill px-1.5 py-0 fs-9"><?= esc($_SESSION['status'] ?? 'ผู้ใช้งาน') ?></span>
                        </small>
                    </li>
                    <!-- User Avatar Dropdown -->
                    <li class="nav-item navbar-dropdown dropdown-user dropdown">
                        <a class="nav-link dropdown-toggle hide-arrow p-0" href="javascript:void(0);" data-bs-toggle="dropdown" aria-expanded="false">
                            <div class="avatar avatar-online avatar-ring shadow-sm">
                                <img src="https://cdn-icons-png.flaticon.com/512/1077/1077114.png" alt="User Avatar"
                                    class="w-px-40 h-auto rounded-circle" style="width: 40px; height: 40px; object-fit: cover;" />
                            </div>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end shadow-lg border-0 rounded-4 mt-2 p-2" style="min-width: 220px;">
                            <li class="p-2 border-bottom mb-1">
                                <div class="d-flex align-items-center gap-2">
                                    <img src="https://cdn-icons-png.flaticon.com/512/1077/1077114.png" class="rounded-circle" style="width: 36px; height: 36px; object-fit: cover;" />
                                    <div class="text-truncate">
                                        <span class="fw-bold text-dark d-block text-truncate fs-7"><?= esc($_SESSION['username']) ?></span>
                                        <small class="text-muted fs-8"><?= esc($_SESSION['status'] ?? 'ผู้ใช้งาน') ?></small>
                                    </div>
                                </div>
                            </li>
                            <li>
                                <a class="dropdown-item rounded-3 py-2 text-primary" href="<?= base_url('Admin/Home') ?>">
                                    <i class="bx bx-cog me-2"></i>
                                    <span class="align-middle fw-semibold">ไปยังระบบ Admin</span>
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item rounded-3 py-2 text-danger" href="<?= base_url('/LogoutOfficerGeneral') ?>">
                                    <i class="bx bx-power-off me-2"></i>
                                    <span class="align-middle fw-semibold">ออกจากระบบ</span>
                                </a>
                            </li>
                        </ul>
                    </li>
                <?php else: ?>
                    <!-- Guest User -->
                    <li class="nav-item me-1 d-none d-sm-block">
                        <span class="badge bg-label-secondary rounded-pill px-2.5 py-1 fs-8">ผู้ใช้งานทั่วไป</span>
                    </li>
                    <li class="nav-item">
                        <a href="<?= base_url('login') ?>" class="btn btn-sm btn-primary rounded-pill px-3 shadow-sm">
                            <i class="bx bx-log-in me-1"></i>เข้าสู่ระบบ
                        </a>
                    </li>
                <?php endif; ?>
            </ul>
        </div>
    </nav>
    <!-- / Navbar -->
