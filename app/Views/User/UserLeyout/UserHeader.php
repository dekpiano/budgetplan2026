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
    <link href="https://fonts.googleapis.com/css2?family=Sarabun:wght@200;300&display=swap" rel="stylesheet">

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
    
    <!-- Minimalist Custom CSS -->
    <style>
        /* Sidebar Minimalist Style */
        .layout-menu {
            border-right: 1px solid rgba(0, 0, 0, 0.05) !important;
            box-shadow: none !important;
            background-color: #ffffff !important;
        }
        .app-brand {
            border-bottom: 1px solid rgba(0, 0, 0, 0.03);
            margin-bottom: 10px;
        }
        .menu-inner .menu-item .menu-link {
            border-radius: 8px;
            margin: 2px 14px;
            font-size: 0.92rem;
            color: #5c6f84 !important;
            transition: all 0.2s ease;
        }
        .menu-inner .menu-item .menu-link:hover {
            background-color: rgba(47, 128, 237, 0.06) !important;
            color: #2f80ed !important;
        }
        .menu-inner .menu-item.active > .menu-link {
            background-color: rgba(47, 128, 237, 0.1) !important;
            color: #2f80ed !important;
            font-weight: 600;
        }
        .menu-header {
            margin-top: 1.5rem !important;
            padding-left: 1.8rem !important;
            font-weight: 600;
            letter-spacing: 0.8px;
            color: #a3b1c2 !important;
        }
        
        /* Navbar Minimalist Style */
        .layout-navbar {
            background: rgba(255, 255, 255, 0.8) !important;
            backdrop-filter: blur(12px) !important;
            -webkit-backdrop-filter: blur(12px) !important;
            border-bottom: 1px solid rgba(0, 0, 0, 0.04) !important;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.02) !important;
            border-radius: 12px !important;
            margin-top: 12px !important;
            border: none !important;
        }
        .layout-navbar .navbar-nav-right {
            padding: 0 10px;
        }
        
        /* General layout adjustments */
        body {
            background-color: #f8f9fa !important;
        }
    </style>
</head>

<body style="font-family:'Sarabun'">