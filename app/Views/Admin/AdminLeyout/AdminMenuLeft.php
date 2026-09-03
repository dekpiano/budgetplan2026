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
                <?php if (auth_is_admin()) : ?>
                <li class="menu-item <?php echo ($uri->getSegment(2) == "Home"?"active":"")?>">
                    <a href="<?=base_url('Admin/Home');?>" class="menu-link">
                        <i class="menu-icon tf-icons bx bx-home-circle"></i>
                        <div data-i18n="Analytics">หน้าแรก (แดชบอร์ด)</div>
                    </a>
                </li>
                <?php endif; ?>

                <!-- ระบบงบประมาณ -->
                <?php if (auth_can_access_system('budget_ledger') || auth_can_manage_plan() || auth_is_superadmin()) : ?>
                <li class="menu-header small text-uppercase">
                    <span class="menu-header-text">ระบบงบประมาณ</span>
                </li>
                <?php if (auth_can_access_system('budget_ledger')) : ?>
                <li class="menu-item <?php echo ($uri->getSegment(2) == "BudgetLedger" && $uri->getSegment(3) != "Structure" ? "active" : "")?>">
                    <a href="<?=base_url('Admin/BudgetLedger');?>" class="menu-link">
                        <i class="menu-icon tf-icons bx bx-book-content"></i>
                        <div data-i18n="Layouts">ทะเบียนคุมเงินงบประมาณ</div>
                    </a>
                </li>
                <?php endif; ?>
                <?php if (auth_is_superadmin() || auth_can_access_system('budget_ordinance')) : ?>
                <li class="menu-item <?php echo ($uri->getSegment(2) == "BudgetOrdinance" ? "active open" : "")?>">
                    <a href="javascript:void(0);" class="menu-link menu-toggle">
                        <i class="menu-icon tf-icons bx bx-book-bookmark text-warning"></i>
                        <div data-i18n="Layouts" class="fw-semibold">ทะเบียนคุมงบประมาณเงินข้อบัญญัติ</div>
                    </a>
                    <ul class="menu-sub">
                        <li class="menu-item <?php echo ($uri->getSegment(2) == "BudgetOrdinance" && $uri->getSegment(3) == "Oil" ? "active" : "")?>">
                            <a href="<?=base_url('Admin/BudgetOrdinance/Oil');?>" class="menu-link">
                                <i class="bx bx-gas-pump fs-6 me-2 text-warning"></i>
                                <div data-i18n="Layouts">เบิกค่าน้ำมันเชื้อเพลิง</div>
                            </a>
                        </li>
                        <li class="menu-item <?php echo ($uri->getSegment(2) == "BudgetOrdinance" && $uri->getSegment(3) == "Travel" ? "active" : "")?>">
                            <a href="<?=base_url('Admin/BudgetOrdinance/Travel');?>" class="menu-link">
                                <i class="bx bx-briefcase fs-6 me-2 text-warning"></i>
                                <div data-i18n="Layouts">ค่าใช้จ่ายเดินทางไปราชการ</div>
                            </a>
                        </li>
                        <li class="menu-item <?php echo ($uri->getSegment(2) == "BudgetOrdinance" && $uri->getSegment(3) == "Registration" ? "active" : "")?>">
                            <a href="<?=base_url('Admin/BudgetOrdinance/Registration');?>" class="menu-link">
                                <i class="bx bx-award fs-6 me-2 text-warning"></i>
                                <div data-i18n="Layouts">ค่าลงทะเบียนอบรม/สัมมนา</div>
                            </a>
                        </li>
                    </ul>
                </li>
                <?php endif; ?>
                <?php if (auth_can_manage_plan() || auth_is_superadmin()) : ?>
                <li class="menu-item <?php echo ($uri->getSegment(2) == "BudgetLedger" && $uri->getSegment(3) == "Structure" ? "active" : "")?>">
                    <a href="<?=base_url('Admin/BudgetLedger/Structure');?>" class="menu-link">
                        <i class="menu-icon tf-icons bx bx-sitemap text-info"></i>
                        <div data-i18n="Layouts">ตั้งค่ายุทธศาสตร์และโครงการ</div>
                    </a>
                </li>
                <?php endif; ?>
                <?php endif; ?>

                <!-- ระบบจัดซื้อจัดจ้าง -->
                <?php if (auth_can_access_system('procurement') || auth_can_access_system('registry_purchase') || auth_can_access_system('registry_hire')) : ?>
                <li class="menu-header small text-uppercase">
                    <span class="menu-header-text">ระบบจัดซื้อจัดจ้าง</span>
                </li>
                <?php if (auth_can_access_system('procurement')) : ?>
                <li class="menu-item <?php echo ($uri->getSegment(2) == "Procurement" && $uri->getSegment(3) == "Process" ? "active" : "")?>">
                    <a href="<?=base_url('User/Procurement/Process')?>" class="menu-link">
                        <i class="menu-icon tf-icons bx bx-cart"></i>
                        <div data-i18n="Layouts">ขั้นตอนจัดซื้อ / จัดจ้าง</div>
                    </a>
                </li>
                <?php endif; ?>
                <?php if (auth_can_access_system('registry_purchase')) : ?>
                <li class="menu-item <?php echo ($uri->getSegment(2) == "Registry" && $uri->getSegment(3) == "Purchase" ? "active" : "")?>">
                    <a href="<?=base_url('Admin/Registry/Purchase');?>" class="menu-link">
                        <i class="menu-icon tf-icons bx bx-cart-alt"></i>
                        <div data-i18n="Layouts">ทะเบียนคุมใบสั่งซื้อ</div>
                    </a>
                </li>
                <?php endif; ?>
                <?php if (auth_can_access_system('registry_hire')) : ?>
                <li class="menu-item <?php echo ($uri->getSegment(2) == "Registry" && $uri->getSegment(3) == "Hire" ? "active" : "")?>">
                    <a href="<?=base_url('Admin/Registry/Hire');?>" class="menu-link">
                        <i class="menu-icon tf-icons bx bx-spreadsheet"></i>
                        <div data-i18n="Layouts">ทะเบียนคุมใบสั่งจ้าง</div>
                    </a>
                </li>
                <?php endif; ?>
                <?php endif; ?>

                <!-- ระบบการเงิน -->
                <?php if (auth_can_access_system('money_receipt')) : ?>
                <li class="menu-header small text-uppercase">
                    <span class="menu-header-text">ระบบการเงิน</span>
                </li>
                <li class="menu-item <?php echo ($uri->getSegment(2) == "Procurement" && $uri->getSegment(3) == "MoneyReceipt" ? "active" : "")?>">
                    <a href="<?=base_url('User/Procurement/MoneyReceipt')?>" class="menu-link">
                        <i class="menu-icon tf-icons bx bx-file"></i>
                        <div data-i18n="Layouts">ใบสำคัญรับเงินตอบแทน</div>
                    </a>
                </li>
                <?php endif; ?>

                <!-- ตั้งค่าระบบ -->
                <?php if (auth_can_manage_plan() || auth_is_superadmin()) : ?>
                <li class="menu-header small text-uppercase">
                    <span class="menu-header-text">ตั้งค่าระบบ</span>
                </li>
                <?php if (auth_can_manage_plan()) : ?>
                <li class="menu-item <?php echo ($uri->getSegment(2) == "BudgetLedger" && $uri->getSegment(3) == "Structure" ? "active" : "")?>">
                    <a href="<?=base_url('Admin/BudgetLedger/Structure');?>" class="menu-link">
                        <i class="menu-icon tf-icons bx bx-sitemap text-info"></i>
                        <div data-i18n="Layouts">ตั้งค่ายุทธศาสตร์และโครงการ</div>
                    </a>
                </li>
                <?php endif; ?>
                <?php if (auth_is_superadmin()) : ?>
                <li class="menu-item <?php echo ($uri->getSegment(2) == "Rloes" ? "active" : "")?>">
                    <a href="<?=base_url('Admin/Rloes/Setting');?>" class="menu-link">
                        <i class="menu-icon tf-icons bx bx-shield-quarter text-warning"></i>
                        <div data-i18n="Analytics">กำหนดสิทธิ์การใช้งาน</div>
                    </a>
                </li>
                <?php endif; ?>
                <?php endif; ?>
            </ul>
        </aside>
        <!-- / Menu -->