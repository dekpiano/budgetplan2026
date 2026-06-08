<!DOCTYPE html>
<html lang="th" class="light-style layout-menu-fixed" dir="ltr" data-theme="theme-default"
    data-assets-path="<?=base_url()?>/assets/" data-template="vertical-menu-template-free">

<head>
    <meta charset="utf-8" />
    <meta name="viewport"
        content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0" />

    <title><?=$title;?> | SKJ บริหารงานงบประมาณและแผน</title>

    <meta name="description" content="<?= $description ?>" />
    <meta
        content="ระบบงาน,E-Office,โรงเรียนสวนกุหลาบวิทยาลัย,โรงเรียน,สวนกุหลาบ,จิรประวัติ,นครสวรรค์,สวนกุหลาบจิรประวัติ,โรงเรียนสวนกุหลาบ"
        name="keywords">
    <meta http-equiv="content-language" content="th" />
    <meta name="robots" content="index, follow" />
    <meta name="revisit-after" content="1 day" />
    <meta name="author" content="Dekpiano" />
    <meta property="og:url" content="<?= $full_url ?>" />
    <meta property="og:title" content="<?=$title;?>" />
    <meta property="og:description" content="<?= $description ?>" />
    <meta property="og:type" content="website" />
    <?php if($uri->getSegment(1) == 'Booking') : ?>
    <meta property="og:image" content="<?=base_url();?>uploads/banner/booking/bannerBooking.png" />
    <?php elseif($uri->getSegment(1) == 'Repair'): ?>
    <meta property="og:image" content="<?=base_url();?>uploads/banner/repair/bannerRepair.jpg" />
    <?php else: ?>
    <meta property="og:image" content="<?=base_url();?>uploads/banner/home/bannerHome.png" />
    <?php endif?>

    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="<?=base_url()?>/assets/img/favicon/favicon.ico" />

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700&family=Sarabun:wght@200;300;400;500;600;700&display=swap" rel="stylesheet">

    <!-- Icons. Uncomment required icon fonts -->
    <link rel="stylesheet" href="<?=base_url()?>/assets/vendor/fonts/boxicons.css" />

    <!-- Core CSS -->
    <link rel="stylesheet" href="<?=base_url()?>/assets/vendor/css/core.css" class="template-customizer-core-css" />
    <link rel="stylesheet" href="<?=base_url()?>/assets/vendor/css/theme-blue.css?v=2"
        class="template-customizer-theme-css" />
    <link rel="stylesheet" href="<?=base_url()?>/assets/css/demo.css?v=1" />
    <!-- <link rel="stylesheet" href="<?=base_url()?>/assets/css/flatpickr.css?v=2" /> -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <!-- Vendors CSS -->
    <link rel="stylesheet" href="<?=base_url()?>/assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.css" />

    <link rel="stylesheet" href="<?=base_url()?>/assets/vendor/libs/apex-charts/apex-charts.css" />

    <!-- DataTables CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.bootstrap5.min.css">


    <!-- Page CSS -->

    <!-- Helpers -->
    <script src="<?=base_url()?>/assets/vendor/js/helpers.js"></script>

    <!--! Template customizer & Theme config files MUST be included after core stylesheets and helpers.js in the <head> section -->
    <!--? Config:  Mandatory theme config file contain global vars & default theme options, Set your preferred theme option in this file.  -->
    <script src="<?=base_url()?>/assets/js/config.js"></script>
    
    <!-- ======================================== -->
    <!-- Orange Luxury Minimalist Theme Override  -->
    <!-- ======================================== -->
    <style>
        :root {
            --skj-orange: #FB8C00;
            --skj-orange-hover: #f57c00;
            --skj-orange-soft: rgba(251, 140, 0, 0.08);
            --skj-orange-glow: rgba(251, 140, 0, 0.12);
            --skj-dark: #1e1e24;
            --skj-bg: #f7f6f3;
            --skj-sidebar-bg: #fefefe;
        }

        /* ─── Body ─── */
        body {
            background-color: var(--skj-bg) !important;
            font-family: 'Sarabun', 'Plus Jakarta Sans', sans-serif !important;
        }

        /* ─── Sidebar ─── */
        .layout-menu {
            border-right: 1px solid rgba(0, 0, 0, 0.04) !important;
            box-shadow: none !important;
            background-color: var(--skj-sidebar-bg) !important;
        }
        .app-brand {
            border-bottom: 1px solid rgba(0, 0, 0, 0.03);
            margin-bottom: 8px;
            padding-bottom: 4px;
        }
        .menu-inner .menu-item .menu-link {
            border-radius: 10px;
            margin: 3px 12px;
            padding: 10px 14px;
            font-size: 0.9rem;
            color: #6b7c93 !important;
            transition: all 0.25s cubic-bezier(0.16, 1, 0.3, 1);
        }
        .menu-inner .menu-item .menu-link:hover {
            background-color: var(--skj-orange-soft) !important;
            color: var(--skj-orange) !important;
        }
        .menu-inner .menu-item.active > .menu-link {
            background-color: var(--skj-orange-glow) !important;
            color: var(--skj-orange) !important;
            font-weight: 600;
        }
        /* Override the vertical active bar to orange */
        .layout-wrapper:not(.layout-horizontal) .bg-menu-theme .menu-inner > .menu-item.active:before {
            background: var(--skj-orange) !important;
        }
        .bg-menu-theme .menu-inner > .menu-item.active > .menu-link {
            color: var(--skj-orange) !important;
            background-color: var(--skj-orange-glow) !important;
        }
        .menu-header {
            margin-top: 1.25rem !important;
            padding-left: 1.6rem !important;
            font-weight: 600;
            font-size: 0.7rem;
            letter-spacing: 1px;
            text-transform: uppercase;
            color: #b0b8c4 !important;
        }
        .menu-inner-shadow {
            display: none !important;
        }

        /* ─── Navbar ─── */
        .layout-navbar {
            background: rgba(255, 255, 255, 0.85) !important;
            backdrop-filter: blur(16px) saturate(180%) !important;
            -webkit-backdrop-filter: blur(16px) saturate(180%) !important;
            border-bottom: 1px solid rgba(0, 0, 0, 0.03) !important;
            box-shadow: 0 1px 12px rgba(0, 0, 0, 0.02) !important;
            border-radius: 14px !important;
            margin-top: 10px !important;
            border: none !important;
        }
        .layout-navbar .navbar-nav-right {
            padding: 0 8px;
        }

        /* ─── Cards (Global) ─── */
        .card {
            border-radius: 16px;
            border: 1px solid rgba(0, 0, 0, 0.03);
        }

        /* ─── Links ─── */
        a {
            transition: color 0.2s ease;
        }

        /* ─── Scrollbar ─── */
        ::-webkit-scrollbar {
            width: 5px;
        }
        ::-webkit-scrollbar-track {
            background: transparent;
        }
        ::-webkit-scrollbar-thumb {
            background: rgba(0, 0, 0, 0.08);
            border-radius: 10px;
        }
        ::-webkit-scrollbar-thumb:hover {
            background: rgba(0, 0, 0, 0.15);
        }
    </style>
</head>

<body style="font-family:'Sarabun'">