<?php
$UrlMenuMain = $UrlMenuMain ?? '';
$UrlMenuSub = $UrlMenuSub ?? '';
?>
<!-- Layout wrapper -->
<div class="layout-wrapper layout-content-navbar">
    <div class="layout-container">
        <!-- Menu -->

        <aside id="layout-menu" class="layout-menu menu-vertical menu bg-menu-theme">
            <div class="app-brand demo">
                <a href="<?= base_url() ?>" class="app-brand-link">
                    <span class="app-brand-logo demo">
                        <img src="https://skj.ac.th/uploads/logoSchool/LogoSKJ_4.png" alt="" width="40">
                    </span>
                    <span class="app-brand-text menu-text fw-bolder ms-2">สกจ.งบประมาณและแผน</span>
                </a>

                <a href="<?= base_url() ?>" class="layout-menu-toggle menu-link text-large ms-auto d-block d-xl-none">
                    <i class="bx bx-chevron-left bx-sm align-middle"></i>
                </a>
            </div>

            <div class="menu-inner-shadow"></div>

            <ul class="menu-inner py-1">
                <!-- Dashboard -->
                <li class="menu-item <?php echo ($UrlMenuMain == "Main" && empty($UrlMenuSub)) ? "active" : "" ?>">
                    <a href="<?= base_url(); ?>" class="menu-link">
                        <i class="menu-icon tf-icons bx bx-home-circle text-primary"></i>
                        <div data-i18n="Analytics">หน้าแรก (ภาพรวม)</div>
                    </a>
                </li>

                <!-- ระบบควบคุมงบประมาณ -->
                <li class="menu-header small text-uppercase">
                    <span class="menu-header-text">ระบบควบคุมงบประมาณ</span>
                </li>
                <li class="menu-item <?php echo ($UrlMenuSub == "BudgetSummary") ? "active" : "" ?>">
                    <a href="<?= base_url('User/BudgetSummary'); ?>" class="menu-link">
                        <i class="menu-icon tf-icons bx bx-book-content text-info"></i>
                        <div data-i18n="Analytics">สรุปโครงการและงบประมาณ</div>
                    </a>
                </li>
                <li class="menu-item <?php echo $UrlMenuSub == "Purchase" ? "active" : "" ?>">
                    <a href="<?= base_url('User/Registry/Purchase'); ?>" class="menu-link">
                        <i class="menu-icon tf-icons bx bx-cart-alt text-warning"></i>
                        <div data-i18n="Analytics">ทะเบียนคุมใบสั่งซื้อ</div>
                    </a>
                </li>
                <li class="menu-item <?php echo $UrlMenuSub == "Hire" ? "active" : "" ?>">
                    <a href="<?= base_url('User/Registry/Hire'); ?>" class="menu-link">
                        <i class="menu-icon tf-icons bx bx-spreadsheet text-success"></i>
                        <div data-i18n="Analytics">ทะเบียนคุมใบสั่งจ้าง</div>
                    </a>
                </li>

                <!-- งานพัสดุและจัดซื้อจัดจ้าง -->
                <li class="menu-header small text-uppercase">
                    <span class="menu-header-text">งานพัสดุและจัดซื้อจัดจ้าง</span>
                </li>
                <li class="menu-item <?php echo $UrlMenuSub == "Process" ? "active" : "" ?>">
                    <a href="<?= base_url('User/Procurement/Process'); ?>" class="menu-link">
                        <i class="menu-icon tf-icons bx bx-shopping-bag text-danger"></i>
                        <div data-i18n="Analytics">ขั้นตอนจัดซื้อ / จัดจ้าง</div>
                    </a>
                </li>

                <!-- งานการเงินและบัญชี -->
                <li class="menu-header small text-uppercase">
                    <span class="menu-header-text">งานการเงินและบัญชี</span>
                </li>
                <li class="menu-item <?php echo $UrlMenuSub == "MoneyReceipt" ? "active" : "" ?>">
                    <a href="<?= base_url('User/Procurement/MoneyReceipt'); ?>" class="menu-link">
                        <i class="menu-icon tf-icons bx bx-receipt text-primary"></i>
                        <div data-i18n="Analytics">ใบสำคัญรับเงินตอบแทน</div>
                    </a>
                </li>

                <!-- ประชาสัมพันธ์ -->
                <li class="menu-header small text-uppercase"><span class="menu-header-text">ข้อมูลเผยแพร่</span></li>
                <li class="menu-item <?php echo ($UrlMenuSub == "News") ? "active" : "" ?>">
                    <a href="<?= base_url('News'); ?>" class="menu-link"><i
                            class="menu-icon tf-icons bx bx-news text-primary"></i>
                        <div>ประชาสัมพันธ์ / เอกสารเผยแพร่</div>
                    </a>
                </li>

                <!-- หมวดหมู่แผนงาน -->
                <li class="menu-header small text-uppercase">
                    <span class="menu-header-text">งานนโยบายและแผน</span>
                </li>
                <li class="menu-item <?php echo (isset($UrlMenuSub) && $UrlMenuSub == "Plan") ? "active" : "" ?>">
                    <a href="<?= base_url('User/Plan'); ?>" class="menu-link">
                        <i class="menu-icon tf-icons bx bx-bar-chart-alt-2 text-info"></i>
                        <div data-i18n="Analytics">แผนดำเนินงานและแผนปฏิบัติการ</div>
                    </a>
                </li>

                <!-- สำหรับเจ้าหน้าที่ / ผู้ดูแลระบบ -->
                <?php if (auth_is_admin()): ?>
                    <li class="menu-header small text-uppercase">
                        <span class="menu-header-text">สำหรับเจ้าหน้าที่</span>
                    </li>
                    <li class="menu-item">
                        <a href="<?= base_url('Admin/Home'); ?>" class="menu-link bg-label-warning text-dark fw-semibold">
                            <i class="menu-icon tf-icons bx bxs-dashboard text-warning"></i>
                            <div data-i18n="Analytics">แดชบอร์ดเจ้าหน้าที่</div>
                        </a>
                    </li>
                    <li class="menu-item">
                        <a href="<?= base_url('Admin/BudgetLedger'); ?>" class="menu-link">
                            <i class="menu-icon tf-icons bx bx-edit-alt text-primary"></i>
                            <div data-i18n="Analytics">จัดการทะเบียนคุมงบประมาณ</div>
                        </a>
                    </li>
                <?php else: ?>
                    <li class="menu-header small text-uppercase">
                        <span class="menu-header-text">ระบบเจ้าหน้าที่</span>
                    </li>
                    <li class="menu-item <?php echo $UrlMenuMain == "LoginOfficerPersonnel" ? "active" : "" ?>">
                        <a href="<?= base_url('login'); ?>" class="menu-link">
                            <i class="menu-icon tf-icons bx bx-log-in-circle text-primary"></i>
                            <div data-i18n="Analytics">เข้าสู่ระบบเจ้าหน้าที่</div>
                        </a>
                    </li>
                <?php endif; ?>
            </ul>
        </aside>
        <!-- / Menu -->

        <!-- Modal 1-->
        <div class="modal fade" id="modalToggle" aria-labelledby="modalToggleLabel" tabindex="-1" style="display: none;"
            aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">

                    <div class="modal-body">
                        <div class="authentication-inner">
                            <!-- Register -->

                            <h4 class="mb-2">Welcome to Login SKJ E-Office 👋</h4>
                            <p class="mb-4">สำหรับเจ้าหน้าที่</p>


                            <div class="d-flex justify-content-center">
                                <?php //echo $GoogleButton; ?>
                            </div>

                            <!-- /Register -->
                        </div>
                    </div>
                </div>
            </div>
        </div>