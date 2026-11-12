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
            <!-- Left: Page Title / Breadcrumb (Responsive) -->
            <div class="navbar-nav align-items-center">
                <div class="nav-item d-flex align-items-center gap-2">
                    <span class="badge bg-label-primary rounded-pill px-2.5 py-1.5 d-none d-sm-inline-flex align-items-center fs-8">
                        <i class="bx bx-shield-quarter me-1 text-primary"></i>ระบบจัดการ
                    </span>
                    <span class="fw-bold text-dark text-truncate d-inline-block navbar-page-title" style="max-width: 280px; font-size: 0.92rem;">
                        <?= esc($title ?? 'แผงควบคุมระบบ') ?>
                    </span>
                </div>
            </div>

            <!-- Right: Actions & User Dropdown -->
            <ul class="navbar-nav flex-row align-items-center ms-auto gap-2">
                <!-- Shortcut: Switch to Public/User View -->
                <li class="nav-item d-none d-md-block">
                    <a href="<?= base_url('User/BudgetSummary') ?>" class="btn btn-xs btn-outline-secondary rounded-pill px-2.5 shadow-none" title="เปิดหน้ามุมมองสำหรับผู้ใช้ทั่วไป">
                        <i class="bx bx-globe me-1"></i>มุมมองผู้ใช้
                    </a>
                </li>

                <!-- User Info -->
                <li class="nav-item lh-1 text-end d-none d-sm-block me-1">
                    <div class="fw-bold text-dark text-truncate" style="max-width: 160px; font-size: 0.86rem;">
                        <?= esc($_SESSION['username'] ?? 'เจ้าหน้าที่') ?>
                    </div>
                    <small class="text-muted" style="font-size: 0.74rem;">
                        <span class="badge bg-label-success rounded-pill px-1.5 py-0 fs-9"><?= esc($_SESSION['status'] ?? 'Admin') ?></span>
                    </small>
                </li>

                <!-- User Avatar & Dropdown -->
                <li class="nav-item navbar-dropdown dropdown-user dropdown">
                    <a class="nav-link dropdown-toggle hide-arrow p-0" href="javascript:void(0);" data-bs-toggle="dropdown" aria-expanded="false">
                        <div class="avatar avatar-online avatar-ring shadow-sm">
                            <?php
                                $navbarSession = session();
                                $navbarPersId = $navbarSession->get('pers_id') ?: $navbarSession->get('id');
                                $navbarPersImg = '';
                                $navbarPersName = $navbarSession->get('username') ?: 'เจ้าหน้าที่';

                                if (!empty($navbarPersId)) {
                                    try {
                                        $navbarPersonnelDb = \Config\Database::connect('personnel');
                                        $navbarPerson = $navbarPersonnelDb->table('tb_personnel')
                                            ->select('pers_prefix, pers_firstname, pers_lastname, pers_img')
                                            ->where('pers_id', $navbarPersId)
                                            ->get()
                                            ->getRow();

                                        if ($navbarPerson) {
                                            $navbarPersImg = $navbarPerson->pers_img ?? '';
                                            $navbarPersName = trim(($navbarPerson->pers_prefix ?? '') . ($navbarPerson->pers_firstname ?? '') . ' ' . ($navbarPerson->pers_lastname ?? '')) ?: $navbarPersName;
                                        }
                                    } catch (\Throwable $e) {}
                                }

                                $navbarAvatarUrl = !empty($navbarPersImg)
                                    ? 'https://personnel.skj.ac.th/uploads/admin/Personnal/' . rawurlencode(basename($navbarPersImg))
                                    : base_url('assets/img/avatars/1.png');
                            ?>
                            <img src="<?= esc($navbarAvatarUrl) ?>" alt="<?= esc($navbarPersName) ?>"
                                class="w-px-40 h-auto rounded-circle" style="object-fit:cover; width: 40px; height: 40px;" />
                        </div>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end shadow-lg border-0 rounded-4 mt-2 p-2" style="min-width: 220px;">
                        <li class="p-2 border-bottom mb-1">
                            <div class="d-flex align-items-center gap-2">
                                <img src="<?= esc($navbarAvatarUrl) ?>" class="rounded-circle" style="width: 36px; height: 36px; object-fit: cover;" />
                                <div class="text-truncate">
                                    <span class="fw-bold text-dark d-block text-truncate fs-7"><?= esc($navbarPersName) ?></span>
                                    <small class="text-muted fs-8"><?= esc($_SESSION['status'] ?? 'Admin') ?></small>
                                </div>
                            </div>
                        </li>
                        <li class="d-md-none">
                            <a class="dropdown-item rounded-3 py-2" href="<?= base_url('User/BudgetSummary') ?>">
                                <i class="bx bx-globe me-2 text-primary"></i>
                                <span class="align-middle">มุมมองผู้ใช้งาน</span>
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item rounded-3 py-2 text-danger" href="<?= base_url('/LogoutOfficerPersonnel') ?>">
                                <i class="bx bx-power-off me-2"></i>
                                <span class="align-middle fw-semibold">ออกจากระบบ</span>
                            </a>
                        </li>
                    </ul>
                </li>
            </ul>
        </div>
    </nav>
    <!-- / Navbar -->
