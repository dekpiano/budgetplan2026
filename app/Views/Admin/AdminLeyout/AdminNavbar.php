    <!-- Navbar -->

    <nav class="layout-navbar container-xxl navbar navbar-expand-xl navbar-detached align-items-center bg-navbar-theme"
        id="layout-navbar">
        <div class="layout-menu-toggle navbar-nav align-items-xl-center me-3 me-xl-0 d-xl-none">
            <a class="nav-item nav-link px-0 me-xl-4" href="javascript:void(0)">
                <i class="bx bx-menu bx-sm"></i>
            </a>
        </div>

        <div class="navbar-nav-right d-flex align-items-center" id="navbar-collapse">
            <!-- Search -->
            <div class="navbar-nav align-items-center">
                <div class="nav-item d-flex align-items-center">
                    <i class="bx bx-search fs-4 lh-0"></i>
                    <input type="text" class="form-control border-0 shadow-none" placeholder="Search..."
                        aria-label="Search..." />
                </div>
            </div>
            <!-- /Search -->

            <ul class="navbar-nav flex-row align-items-center ms-auto">
                <!-- Place this tag where you want the button to render. -->
                <li class="nav-item lh-1 me-3">
                    <?=$_SESSION['username'];?> <br>
                    <small class="text-muted"><?=$_SESSION['status'];?></small>
                </li>

                <!-- User -->
                <li class="nav-item navbar-dropdown dropdown-user dropdown">
                    <a class="nav-link dropdown-toggle hide-arrow" href="javascript:void(0);" data-bs-toggle="dropdown">
                        <div class="avatar avatar-online">
                            <?php
                            // ดึงรูปเจ้าหน้าที่จากฐานข้อมูล Personnel ตาม pers_id ของผู้ที่ Login
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
                                } catch (\Throwable $e) {
                                    // หากฐานข้อมูล Personnel ไม่พร้อม ให้ใช้ Avatar สำรองโดยไม่ทำให้ Navbar พัง
                                }
                            }

                            $navbarAvatarUrl = !empty($navbarPersImg)
                                ? 'https://personnel.skj.ac.th/uploads/admin/Personnal/' . rawurlencode(basename($navbarPersImg))
                                : base_url('assets/img/avatars/1.png');
                        ?>
                        <img src="<?= esc($navbarAvatarUrl) ?>" alt="<?= esc($navbarPersName) ?>"
                            class="w-px-40 h-auto rounded-circle" style="object-fit:cover;" />
                        </div>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end">
                      
                       
                        <!-- <li>
                            <div class="dropdown-divider"></div>
                        </li> -->
                        <li>
                            <a class="dropdown-item" href="<?=base_url('/LogoutOfficerPersonnel')?>">
                                <i class="bx bx-power-off me-2"></i>
                                <span class="align-middle">Log Out</span>
                            </a>
                        </li>
                    </ul>
                </li>
                <!--/ User -->
            </ul>
        </div>
    </nav>

    <!-- / Navbar -->