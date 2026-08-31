<?php
$title       = $title ?? 'ระบบบริหารงานงบประมาณและแผน';
$description = $description ?? 'โรงเรียนสวนกุหลาบวิทยาลัย (จิรประวัติ) นครสวรรค์';
$full_url    = $full_url ?? (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http') . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
$uri         = $uri ?? service('uri');
?>
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
    <link href="https://fonts.googleapis.com/css2?family=K2D:wght@200;300;400;500;600;700;800&family=Plus+Jakarta+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">

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
            --skj-orange-dark: #e65100;
            --skj-orange-hover: #f57c00;
            --skj-orange-light: #ffe0b2;
            --skj-orange-soft: rgba(251, 140, 0, 0.08);
            --skj-orange-glow: rgba(251, 140, 0, 0.15);
            --skj-dark: #1e1e24;
            --skj-bg: #faf9f6;
            --skj-sidebar-bg: #ffffff;
        }

        /* ─── Warm Orange Ambient Background ─── */
        body, .layout-wrapper, .layout-container, .content-wrapper {
            background-color: #fbf9f5 !important;
            background-image: 
                radial-gradient(at 10% 10%, rgba(251, 140, 0, 0.05) 0px, transparent 45%),
                radial-gradient(at 90% 15%, rgba(255, 171, 0, 0.05) 0px, transparent 40%),
                radial-gradient(at 85% 85%, rgba(251, 140, 0, 0.04) 0px, transparent 50%),
                radial-gradient(at 15% 90%, rgba(255, 152, 0, 0.04) 0px, transparent 45%) !important;
            background-attachment: fixed !important;
            background-size: 100% 100% !important;
            font-family: 'K2D', 'Plus Jakarta Sans', sans-serif !important;
            color: #4a5568;
        }

        /* ─── Buttons (Orange Theme) ─── */
        .btn-primary {
            background: linear-gradient(135deg, #FB8C00 0%, #F57C00 100%) !important;
            border-color: #FB8C00 !important;
            color: #ffffff !important;
            box-shadow: 0 4px 12px rgba(251, 140, 0, 0.25) !important;
            transition: all 0.2s ease;
        }
        .btn-primary:hover, .btn-primary:focus, .btn-primary:active {
            background: linear-gradient(135deg, #F57C00 0%, #E65100 100%) !important;
            border-color: #E65100 !important;
            color: #ffffff !important;
            box-shadow: 0 6px 16px rgba(251, 140, 0, 0.35) !important;
            transform: translateY(-1px);
        }
        .btn-outline-primary {
            color: #FB8C00 !important;
            border-color: #FB8C00 !important;
            transition: all 0.2s ease;
        }
        .btn-outline-primary:hover, .btn-outline-primary:focus, .btn-outline-primary:active {
            background-color: #FB8C00 !important;
            border-color: #FB8C00 !important;
            color: #ffffff !important;
            box-shadow: 0 4px 12px rgba(251, 140, 0, 0.25) !important;
            transform: translateY(-1px);
        }

        /* ─── Text & Badge Overrides ─── */
        .text-primary {
            color: #FB8C00 !important;
        }
        .bg-primary {
            background-color: #FB8C00 !important;
        }
        .bg-label-primary {
            background-color: rgba(251, 140, 0, 0.12) !important;
            color: #e65100 !important;
        }
        .bg-primary-soft {
            background-color: rgba(251, 140, 0, 0.1) !important;
            color: #FB8C00 !important;
        }

        /* ─── Form Inputs ─── */
        .form-control:focus, .form-select:focus {
            border-color: #FB8C00 !important;
            box-shadow: 0 0 0 0.25rem rgba(251, 140, 0, 0.18) !important;
        }
        .form-check-input:checked {
            background-color: #FB8C00 !important;
            border-color: #FB8C00 !important;
        }

        /* ─── DataTables & Pagination ─── */
        .pagination .page-item.active .page-link,
        .page-item.active .page-link {
            background-color: #FB8C00 !important;
            border-color: #FB8C00 !important;
            color: #ffffff !important;
            box-shadow: 0 2px 6px rgba(251, 140, 0, 0.3) !important;
        }
        .pagination .page-link:hover {
            color: #FB8C00 !important;
        }

        /* ─── Sidebar ─── */
        .layout-menu {
            border-right: 1px solid rgba(251, 140, 0, 0.08) !important;
            box-shadow: 2px 0 16px rgba(251, 140, 0, 0.02) !important;
            background-color: var(--skj-sidebar-bg) !important;
        }
        .app-brand {
            border-bottom: 1px solid rgba(251, 140, 0, 0.06);
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
            color: var(--skj-orange-dark) !important;
            font-weight: 700;
        }
        .layout-wrapper:not(.layout-horizontal) .bg-menu-theme .menu-inner > .menu-item.active:before {
            background: var(--skj-orange) !important;
        }
        .bg-menu-theme .menu-inner > .menu-item.active > .menu-link {
            color: var(--skj-orange-dark) !important;
            background-color: var(--skj-orange-glow) !important;
        }
        .menu-header {
            margin-top: 1.25rem !important;
            padding-left: 1.6rem !important;
            font-weight: 700;
            font-size: 0.7rem;
            letter-spacing: 1px;
            text-transform: uppercase;
            color: #a0aec0 !important;
        }
        .menu-inner-shadow {
            display: none !important;
        }

        /* ─── Navbar ─── */
        .layout-navbar {
            background: rgba(255, 255, 255, 0.92) !important;
            backdrop-filter: blur(16px) saturate(180%) !important;
            -webkit-backdrop-filter: blur(16px) saturate(180%) !important;
            border: 1px solid rgba(251, 140, 0, 0.1) !important;
            box-shadow: 0 4px 20px rgba(251, 140, 0, 0.04) !important;
            border-radius: 16px !important;
            margin-top: 10px !important;
        }
        .layout-navbar .navbar-nav-right {
            padding: 0 8px;
        }

        /* ─── Cards (Global) ─── */
        .card {
            border-radius: 18px !important;
            border: 1px solid rgba(251, 140, 0, 0.08) !important;
            background-color: #ffffff;
            box-shadow: 0 4px 20px rgba(251, 140, 0, 0.03) !important;
            transition: transform 0.25s ease, box-shadow 0.25s ease, border-color 0.25s ease;
        }
        .card:hover {
            border-color: rgba(251, 140, 0, 0.2) !important;
        }

        /* ─── Select2 Orange Highlight ─── */
        .select2-container--default .select2-results__option--highlighted[aria-selected] {
            background-color: #FB8C00 !important;
            color: #ffffff !important;
        }

        /* ─── Scrollbar ─── */
        ::-webkit-scrollbar {
            width: 6px;
        }
        ::-webkit-scrollbar-track {
            background: transparent;
        }
        ::-webkit-scrollbar-thumb {
            background: rgba(251, 140, 0, 0.2);
            border-radius: 10px;
        }
        ::-webkit-scrollbar-thumb:hover {
            background: rgba(251, 140, 0, 0.4);
        }

        /* ─── Global Minimalist Animations ─── */
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(16px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .animate-fade-in-up {
            animation: fadeInUp 0.6s cubic-bezier(0.16, 1, 0.3, 1) forwards;
        }

        /* ─── Global Preloader ─── */
        #skj-preloader {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: var(--skj-bg);
            z-index: 999999;
            display: flex;
            justify-content: center;
            align-items: center;
            transition: opacity 0.5s cubic-bezier(0.16, 1, 0.3, 1), visibility 0.5s;
        }
        .skj-loader-spinner {
            width: 48px;
            height: 48px;
            border: 3px solid var(--skj-orange-glow);
            border-top-color: var(--skj-orange);
            border-radius: 50%;
            animation: skjSpin 0.9s cubic-bezier(0.5, 0, 0.5, 1) infinite;
        }
        @keyframes skjSpin {
            to { transform: rotate(360deg); }
        }
        body.page-loaded #skj-preloader {
            opacity: 0;
            visibility: hidden;
        }

        /* ─── Global Responsive Single Screen Table Fit (No Horizontal Scroll) ─── */
        .table-fit-screen,
        .table-responsive > .table:not(.table-scroll-explicit) {
            width: 100% !important;
            table-layout: auto;
            white-space: normal !important;
        }
        .table-fit-screen th, .table-fit-screen td,
        .table-responsive > .table:not(.table-scroll-explicit) th,
        .table-responsive > .table:not(.table-scroll-explicit) td {
            white-space: normal !important;
            word-wrap: break-word !important;
            word-break: break-word !important;
            padding: 0.55rem 0.45rem !important;
            vertical-align: middle !important;
        }
        .table-fit-screen th,
        .table-responsive > .table:not(.table-scroll-explicit) th {
            font-size: 0.82rem !important;
            font-weight: 700 !important;
            letter-spacing: 0.2px;
        }
        .table-fit-screen td,
        .table-responsive > .table:not(.table-scroll-explicit) td {
            font-size: 0.83rem !important;
        }
        .table-responsive {
            scrollbar-width: thin;
            scrollbar-color: rgba(251, 140, 0, 0.2) transparent;
            overflow-x: auto;
        }
        .table-responsive::-webkit-scrollbar {
            height: 5px;
        }
        .table-responsive::-webkit-scrollbar-thumb {
            background: rgba(251, 140, 0, 0.2);
            border-radius: 10px;
        }

        /* ─── SweetAlert2 Always on Top ─── */
        .swal2-container {
            z-index: 9999999 !important;
        }
    </style>
</head>

<body style="font-family:'K2D', sans-serif">
    <!-- Global Preloader -->
    <div id="skj-preloader">
        <div class="skj-loader-spinner"></div>
    </div>