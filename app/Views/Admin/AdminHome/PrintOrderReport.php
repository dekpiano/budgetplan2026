<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= esc($title); ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Sarabun:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Sarabun', sans-serif;
            font-size: 16px;
            color: #000;
            background-color: #fff;
            margin: 0;
            padding: 0;
            line-height: 1.6;
        }

        .container {
            width: 210mm;
            min-height: 297mm;
            padding: 20mm 15mm;
            margin: 0 auto;
            box-sizing: border-box;
            position: relative;
        }

        /* Header Layout */
        .report-header {
            display: flex;
            align-items: center;
            justify-content: center;
            text-align: center;
            position: relative;
            margin-bottom: 25px;
            min-height: 80px;
        }

        .logo-container {
            position: absolute;
            left: 0;
            top: 0;
            width: 80px;
            height: 80px;
        }

        .logo-img {
            width: 100%;
            height: 100%;
            object-fit: contain;
        }

        .header-text {
            flex: 1;
            padding-top: 0;
        }

        .header-title {
            font-size: 22px;
            font-weight: 700;
            margin: 0 0 5px 0;
            line-height: 1.2;
        }

        .header-subtitle {
            font-size: 18px;
            font-weight: 500;
            margin: 0;
            line-height: 1.2;
        }

        /* Meta details */
        .meta-details {
            margin-bottom: 30px;
            font-size: 16px;
            text-align: center;
        }

        .meta-line {
            margin-bottom: 10px;
            word-wrap: break-word;
        }

        .dots {
            border-bottom: 1px dotted #000;
            display: inline-block;
            min-width: 80px;
            text-align: center;
            padding: 0 10px;
            font-weight: 600;
        }

        /* Images Grid Engine */
        .images-section {
            display: block;
            clear: both;
            margin: 20px 0 40px 0;
            width: 100%;
        }

        /* Common image styles */
        .images-section img {
            object-fit: cover;
            background-color: #f8fafc;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0,0,0,0.15);
            border: 1px solid #e2e8f0;
        }

        /* Layout for 1 image */
        .layout-1 {
            display: flex;
            justify-content: center;
        }
        .layout-1 img {
            max-width: 100%;
            max-height: 500px;
        }

        /* Layout for 2 images - default side by side */
        .layout-2 {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            align-items: center;
        }
        .layout-2 img {
            width: 100%;
            max-height: 350px;
        }

        /* Layout for 2 images - both portrait: narrower columns, taller */
        .layout-2-portrait {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            justify-items: center;
            align-items: center;
        }
        .layout-2-portrait img {
            max-width: 100%;
            max-height: 420px;
        }

        /* Layout for 2 images - mixed orientation: portrait left, landscape right stacked */
        .layout-2-mixed {
            display: grid;
            grid-template-columns: 2fr 3fr;
            gap: 20px;
            align-items: center;
        }
        .layout-2-mixed img {
            width: 100%;
            max-height: 400px;
        }

        /* Layout for 3 images (all landscape - vertical stack) */
        .layout-3-stack {
            display: flex;
            flex-direction: column;
            gap: 10px;
            align-items: center;
        }
        .layout-3-stack img {
            max-width: 100%;
            max-height: 170px;
        }

        /* Layout for 3 images (split column style with portrait on left) */
        .layout-3-split {
            display: grid;
            grid-template-columns: 2fr 3fr;
            gap: 15px;
            align-items: stretch;
        }
        .layout-3-split-left {
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .layout-3-split-left img {
            width: 100%;
            max-height: 400px;
        }
        .layout-3-split-right {
            display: flex;
            flex-direction: column;
            justify-content: center;
            gap: 10px;
        }
        .layout-3-split-right img {
            width: 100%;
            max-height: 190px;
        }

        /* Layout for 4 or more images - adaptive grid */
        .layout-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
            align-items: center;
        }
        .layout-grid img {
            width: 100%;
            max-height: 200px;
        }
        /* Portrait image in grid gets special treatment */
        .layout-grid .img-portrait {
            max-height: 250px;
        }
        .layout-grid .img-landscape {
            max-height: 180px;
        }

        /* Signature section */
        .signature-section {
            clear: both;
            display: flex;
            justify-content: space-between;
            margin-top: 60px;
            padding: 0 20px;
            page-break-inside: avoid;
            position: relative;
        }

        .sig-block {
            text-align: center;
            width: 45%;
        }

        .sig-line {
            margin-bottom: 12px;
        }

        .sig-name {
            font-weight: 500;
        }

        /* Print optimization styles */
        @media print {
            @page {
                size: A4 portrait;
                margin: 0;
            }
            body {
                background-color: #fff;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
            .container {
                width: 100%;
                height: auto;
                min-height: 0;
                padding: 15mm 10mm;
                margin: 0;
            }
            .no-print {
                display: none !important;
            }
            /* Box shadow hidden or optimized for printing */
            .images-section img {
                box-shadow: none !important;
                border: 1px solid #000 !important;
            }
            .logo-img {
                border: none !important;
                box-shadow: none !important;
            }
        }

        /* Toolbar class */
        .print-toolbar {
            background-color: #333;
            color: #fff;
            padding: 10px 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 2px 4px rgba(0,0,0,0.2);
            font-family: inherit;
        }
        .print-btn {
            background-color: #ff9f43;
            border: none;
            color: white;
            padding: 8px 16px;
            text-align: center;
            text-decoration: none;
            display: inline-block;
            font-size: 14px;
            margin: 4px 2px;
            cursor: pointer;
            border-radius: 20px;
            font-weight: 600;
            transition: background 0.2s;
        }
        .print-btn:hover {
            background-color: #f39c12;
        }
        .back-btn {
            color: #ccc;
            text-decoration: none;
            font-size: 14px;
        }
        .back-btn:hover {
            color: #fff;
        }
    </style>
</head>
<body>

    <!-- Print Toolbar for Web View -->
    <div class="print-toolbar no-print">
        <a href="javascript:window.close();" class="back-btn">← ปิดหน้าต่างนี้</a>
        <div>
            <button onclick="window.print();" class="print-btn">พิมพ์รายงาน (Print)</button>
        </div>
    </div>

    <?php
        // Helper conversion functions
        function toThaiNumerals($number) {
            $thai = ['๐', '๑', '๒', '๓', '๔', '๕', '๖', '๗', '๘', '๙'];
            $arabic = ['0', '1', '2', '3', '4', '5', '6', '7', '8', '9'];
            return str_replace($arabic, $thai, (string)$number);
        }

        function formatThaiDateLong($dateStr, $useThaiNumerals = true) {
            if (!$dateStr) return '';
            $thaiMonths = [
                1 => 'มกราคม', 2 => 'กุมภาพันธ์', 3 => 'มีนาคม', 4 => 'เมษายน',
                5 => 'พฤษภาคม', 6 => 'มิถุนายน', 7 => 'กรกฎาคม', 8 => 'สิงหาคม',
                9 => 'กันยายน', 10 => 'ตุลาคม', 11 => 'พฤศจิกายน', 12 => 'ธันวาคม'
            ];
            $time = strtotime($dateStr);
            $d = date('j', $time);
            $m = $thaiMonths[intval(date('n', $time))];
            $y = date('Y', $time) + 543;
            
            $result = "$d $m $y";
            if ($useThaiNumerals) {
                $result = toThaiNumerals($result);
            }
            return $result;
        }

        // Calculate fiscal year in Thailand
        $dateObj = date_create($order->order_date);
        $year = intval(date_format($dateObj, "Y"));
        $month = intval(date_format($dateObj, "m"));
        $fiscalYear = ($month >= 10) ? ($year + 1) : $year;
        $buddhistFiscalYear = toThaiNumerals($fiscalYear + 543);

        $orderNoThai = toThaiNumerals($order->order_number);
        $orderDateThai = formatThaiDateLong($order->order_date);
        $deliveryDateThai = $order->delivery_date ? formatThaiDateLong($order->delivery_date) : '........................................';

        // Retrieve images list
        $images = $order->images ? json_decode($order->images, true) : [];
    ?>

    <div class="container">
        
        <!-- Header -->
        <div class="report-header">
            <div class="logo-container">
                <img src="<?= esc($logo_school); ?>" alt="School Logo" class="logo-img">
            </div>
            <div class="header-text">
                <h1 class="header-title">ทะเบียนภาพส่งมอบงาน<?= esc($order->order_type); ?></h1>
                <h2 class="header-subtitle">ประจำปีงบประมาณ <?= $buddhistFiscalYear; ?></h2>
            </div>
        </div>

        <!-- Meta Information -->
        <div class="meta-details">
            <div class="meta-line">
                ใบสั่ง<?= esc($order->order_type); ?>เลขที่ <span class="dots"><?= $orderNoThai; ?></span> 
                ลงวันที่ <span class="dots"><?= $orderDateThai; ?></span>
            </div>
            <div class="meta-line">
                เรื่อง<?= $order->order_type === 'สั่งซื้อ' ? 'ซื้อ' : 'จ้างเหมา'; ?> <span class="dots"><?= esc($order->description); ?></span> 
                กำหนดส่งมอบ <span class="dots"><?= $deliveryDateThai; ?></span>
            </div>
        </div>

        <!-- Images Grid Layout Engine (Adaptive) -->
        <div class="images-section" id="imagesSection">
            <?php if (empty($images)): ?>
                <div style="text-align: center; padding: 50px; border: 1px dashed #ccc; border-radius: 8px; color: #666;">
                    ไม่มีไฟล์รูปภาพส่งมอบงาน
                </div>
            <?php else: ?>
                <?php 
                    $count = count($images);
                    // Resolve file URLs
                    $imageUrls = [];
                    foreach ($images as $img) {
                        $isLocal = strpos($img, 'uploads/') === 0;
                        $imageUrls[] = $isLocal ? base_url($img) : env('upload.server.baseurl') . $img;
                    }
                ?>

                <!-- Container for adaptive layout (JS will rearrange) -->
                <div id="adaptiveImageContainer" data-count="<?= $count; ?>">
                    <?php foreach ($imageUrls as $idx => $url): ?>
                        <img src="<?= esc($url); ?>" alt="delivery-image-<?= $idx + 1; ?>" data-idx="<?= $idx; ?>">
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>

        <!-- Signatures Footer -->
        <div class="signature-section">
            <div class="sig-block">
                <div class="sig-line">ลงชื่อ............................................................<br>(ผู้รับของ)</div>
                <div class="sig-name">(............................................................)</div>
            </div>
            <div class="sig-block">
                <div class="sig-line">ลงชื่อ............................................................<br>(เจ้าหน้าที่)</div>
                <div class="sig-name">(<?= session()->get('username') ?: '............................................................'; ?>)</div>
            </div>
        </div>

    </div>

    <script>
        window.addEventListener('DOMContentLoaded', () => {
            const container = document.getElementById('adaptiveImageContainer');
            if (!container) {
                // No images → just auto-print
                setTimeout(() => window.print(), 500);
                return;
            }

            const imgs = Array.from(container.querySelectorAll('img'));
            const count = parseInt(container.dataset.count, 10);
            let loadedCount = 0;

            function isPortrait(img) {
                return img.naturalHeight > img.naturalWidth;
            }

            function applyAdaptiveLayout() {
                // Classify each image
                const portraits = [];
                const landscapes = [];
                imgs.forEach((img, idx) => {
                    if (isPortrait(img)) {
                        portraits.push({ img, idx });
                    } else {
                        landscapes.push({ img, idx });
                    }
                });

                // Clear container
                container.innerHTML = '';

                if (count === 1) {
                    // Single image
                    container.className = 'layout-1';
                    container.appendChild(imgs[0]);

                } else if (count === 2) {
                    if (portraits.length === 2) {
                        // Both portrait
                        container.className = 'layout-2-portrait';
                        imgs.forEach(img => container.appendChild(img));
                    } else if (portraits.length === 1 && landscapes.length === 1) {
                        // Mixed: portrait first (left), landscape second (right)
                        container.className = 'layout-2-mixed';
                        container.appendChild(portraits[0].img);
                        container.appendChild(landscapes[0].img);
                    } else {
                        // Both landscape
                        container.className = 'layout-2';
                        imgs.forEach(img => container.appendChild(img));
                    }

                } else if (count === 3) {
                    if (portraits.length >= 1) {
                        // Has portrait: split layout
                        container.className = 'layout-3-split';
                        const leftDiv = document.createElement('div');
                        leftDiv.className = 'layout-3-split-left';
                        leftDiv.appendChild(portraits[0].img);

                        const rightDiv = document.createElement('div');
                        rightDiv.className = 'layout-3-split-right';
                        // Put remaining images on right
                        imgs.forEach(img => {
                            if (img !== portraits[0].img) {
                                rightDiv.appendChild(img);
                            }
                        });

                        container.appendChild(leftDiv);
                        container.appendChild(rightDiv);
                    } else {
                        // All landscape: stack vertically
                        container.className = 'layout-3-stack';
                        imgs.forEach(img => container.appendChild(img));
                    }

                } else {
                    // 4 or more: adaptive grid — portraits first (left), landscapes after (right)
                    container.className = 'layout-grid';
                    // Append portraits first, then landscapes
                    portraits.forEach(p => {
                        p.img.classList.add('img-portrait');
                        container.appendChild(p.img);
                    });
                    landscapes.forEach(l => {
                        l.img.classList.add('img-landscape');
                        container.appendChild(l.img);
                    });
                }

                // Delay print slightly to ensure DOM reflow is complete
                setTimeout(() => window.print(), 600);
            }

            // Wait for ALL images to load (needed to read naturalHeight/naturalWidth)
            imgs.forEach(img => {
                if (img.complete && img.naturalWidth > 0) {
                    loadedCount++;
                    if (loadedCount === imgs.length) applyAdaptiveLayout();
                } else {
                    img.addEventListener('load', () => {
                        loadedCount++;
                        if (loadedCount === imgs.length) applyAdaptiveLayout();
                    });
                    img.addEventListener('error', () => {
                        loadedCount++;
                        if (loadedCount === imgs.length) applyAdaptiveLayout();
                    });
                }
            });
        });
    </script>
</body>
</html>
