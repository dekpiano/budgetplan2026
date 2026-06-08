<!-- Layout wrapper -->
<div class="layout-wrapper layout-content-navbar">
    <div class="layout-container">
        <!-- Menu -->

        <aside id="layout-menu" class="layout-menu menu-vertical menu bg-menu-theme">
            <div class="app-brand demo">
                <a href="index.html" class="app-brand-link">
                    <span class="app-brand-logo demo">
                        <img src="https://skj.ac.th/uploads/logoSchool/LogoSKJ_4.png" alt="" width="40">
                    </span>
                    <span class="app-brand-text menu-text fw-bolder ms-2">สกจ.งบประมาณและแผน
                        <small>(เจ้าหน้าที่)</small></span>
                </a>

                <a href="javascript:void(0);" class="layout-menu-toggle menu-link text-large ms-auto d-block d-xl-none">
                    <i class="bx bx-chevron-left bx-sm align-middle"></i>
                </a>
            </div>

            <div class="menu-inner-shadow"></div>

            <ul class="menu-inner py-1">
                <!-- Dashboard -->
                <li class="menu-item <?php echo ($uri->getSegment(2) == "Home"?"active":"")?>">
                    <a href="<?=base_url('Admin/Home');?>" class="menu-link">
                        <i class="menu-icon tf-icons bx bx-home-circle"></i>
                        <div data-i18n="Analytics">หน้าแรก (แดชบอร์ด)</div>
                    </a>
                </li>

                <!-- ระบบควบคุมงบประมาณ -->
                <li class="menu-header small text-uppercase">
                    <span class="menu-header-text">ระบบควบคุมงบประมาณ</span>
                </li>
                <li class="menu-item <?php echo ($uri->getSegment(2) == "Registry" && $uri->getSegment(3) == "Purchase" ? "active" : "")?>">
                    <a href="<?=base_url('Admin/Registry/Purchase');?>" class="menu-link">
                        <i class="menu-icon tf-icons bx bx-cart-alt"></i>
                        <div data-i18n="Layouts">ทะเบียนคุมใบสั่งซื้อ</div>
                    </a>
                </li>
                <li class="menu-item <?php echo ($uri->getSegment(2) == "Registry" && $uri->getSegment(3) == "Hire" ? "active" : "")?>">
                    <a href="<?=base_url('Admin/Registry/Hire');?>" class="menu-link">
                        <i class="menu-icon tf-icons bx bx-spreadsheet"></i>
                        <div data-i18n="Layouts">ทะเบียนคุมใบสั่งจ้าง</div>
                    </a>
                </li>

                <!-- งานจัดซื้อจัดจ้าง -->
                <li class="menu-header small text-uppercase">
                    <span class="menu-header-text">งานพัสดุและจัดซื้อจัดจ้าง</span>
                </li>
                <li class="menu-item">
                    <a href="<?=base_url('User/Procurement/Process')?>" class="menu-link">
                        <i class="menu-icon tf-icons bx bx-cart"></i>
                        <div data-i18n="Layouts">ขั้นตอนจัดซื้อ / จัดจ้าง</div>
                    </a>
                </li>

                <!-- งานการเงินและบัญชี -->
                <li class="menu-header small text-uppercase">
                    <span class="menu-header-text">งานการเงินและบัญชี</span>
                </li>
                <li class="menu-item">
                    <a href="<?=base_url('User/Procurement/MoneyReceipt')?>" class="menu-link">
                        <i class="menu-icon tf-icons bx bx-file"></i>
                        <div data-i18n="Layouts">ใบสำคัญรับเงินตอบแทน</div>
                    </a>
                </li>
            </ul>

            <?php if(auth_is_admin()) : ?>
            <div>
                <ul class="menu-inner py-1">
                    <li class="menu-item <?php echo $uri->getSegment(2) == "Rloes"?"active":""?>">
                        <a href="<?=base_url('Admin/Rloes/Setting');?>" class="menu-link">
                            <i class="menu-icon tf-icons bx bx-spreadsheet"></i>
                            <div data-i18n="Analytics">กำหนดสิทธิ์ใช้งาน </div>
                        </a>
                    </li>
                </ul>
            </div>
            <?php endif; ?>
        </aside>
        <!-- / Menu -->