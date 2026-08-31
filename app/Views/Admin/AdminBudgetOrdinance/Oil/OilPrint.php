<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>รายงานทะเบียนคุมค่าน้ำมันเชื้อเพลิง ประจำปีงบประมาณ <?= esc($selected_year); ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Sarabun:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        @page {
            size: A4 landscape;
            margin: 12mm 10mm 12mm 10mm;
        }
        * {
            box-sizing: border-box;
            font-family: 'Sarabun', sans-serif;
        }
        body {
            background-color: #fff;
            color: #000;
            margin: 0;
            padding: 15px;
            font-size: 13px;
        }
        .header-title {
            text-align: center;
            font-size: 16px;
            font-weight: bold;
            margin-bottom: 3px;
        }
        .header-subtitle {
            text-align: center;
            font-size: 14px;
            margin-bottom: 12px;
        }
        .meta-table {
            width: 100%;
            margin-bottom: 10px;
            border-collapse: collapse;
            font-size: 13px;
        }
        .meta-table td {
            padding: 2px 4px;
            vertical-align: top;
        }
        .ledger-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 12px;
        }
        .ledger-table th, .ledger-table td {
            border: 1px solid #333;
            padding: 5px 6px;
        }
        .ledger-table th {
            background-color: #f2f2f2;
            text-align: center;
            font-weight: bold;
        }
        .text-center { text-align: center; }
        .text-end { text-align: right; }
        .text-start { text-align: left; }
        .fw-bold { font-weight: bold; }
        .signature-section {
            margin-top: 25px;
            width: 100%;
            display: table;
        }
        .signature-box {
            display: table-cell;
            width: 50%;
            text-align: center;
            vertical-align: top;
            font-size: 13px;
        }
        .print-btn-container {
            text-align: right;
            margin-bottom: 15px;
        }
        .btn-print {
            background-color: #FB8C00;
            color: #fff;
            border: none;
            padding: 8px 18px;
            border-radius: 6px;
            cursor: pointer;
            font-size: 14px;
            font-weight: 500;
        }
        .btn-print:hover { background-color: #e65100; }
        @media print {
            .print-btn-container { display: none; }
            body { padding: 0; }
        }
    </style>
</head>
<body>

    <div class="print-btn-container">
        <button class="btn-print" onclick="window.print();">🖨️ พิมพ์เอกสาร</button>
    </div>

    <div class="header-title">ทะเบียนคุมงบประมาณเงินข้อบัญญัติ (ค่าน้ำมันเชื้อเพลิง)</div>
    <div class="header-subtitle">โรงเรียนสวนกุหลาบวิทยาลัย (จิรประวัติ) นครสวรรค์</div>

    <table class="meta-table">
        <tr>
            <td style="width: 50%;"><b>ปีงบประมาณ:</b> พ.ศ. <?= esc($selected_year); ?></td>
            <td style="width: 50%; text-align: right;"><b>วันที่พิมพ์:</b> <?= date('d/m/') . (date('Y') + 543); ?></td>
        </tr>
        <tr>
            <td><b>งบประมาณจัดสรรรวม:</b> <?= number_format($stats->total_budget, 2); ?> บาท</td>
            <td style="text-align: right;"><b>เบิกจ่ายแล้ว:</b> <?= number_format($stats->total_spent, 2); ?> บาท | <b>คงเหลือ:</b> <?= number_format($stats->remaining, 2); ?> บาท</td>
        </tr>
    </table>

    <table class="ledger-table">
        <thead>
            <tr>
                <th style="width: 35px;">ที่</th>
                <th style="width: 80px;">วัน เดือน ปี</th>
                <th style="width: 95px;">เลขที่เอกสาร/ฎีกา</th>
                <th>รายการ / วัตถุประสงค์ / สถานที่ไปราชการ</th>
                <th style="width: 140px;">ทะเบียนรถ / คนขับ / ผู้ขอ</th>
                <th style="width: 70px;">จำนวนลิตร</th>
                <th style="width: 95px;">งบจัดสรร (+)</th>
                <th style="width: 95px;">เบิกจ่าย (-)</th>
                <th style="width: 95px;">คงเหลือ</th>
                <th style="width: 90px;">หมายเหตุ</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($items)): ?>
                <?php 
                $i = 1; 
                foreach ($items as $row): 
                    $docDateTh = thaidate_format($row->doc_date);
                ?>
                    <tr>
                        <td class="text-center"><?= $i++; ?></td>
                        <td class="text-center"><?= $docDateTh; ?></td>
                        <td class="text-center">
                            <?= esc($row->dika_no ?: $row->doc_no ?: '-'); ?>
                        </td>
                        <td>
                            <div class="fw-bold"><?= esc($row->description ?: 'เบิกค่าน้ำมัน'); ?></div>
                            <?php if (!empty($row->destination)): ?>
                                <small style="color:#555;">(สถานที่: <?= esc($row->destination); ?>)</small>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php if (!empty($row->car_license)): ?>
                                <b><?= esc($row->car_license); ?></b><br>
                            <?php endif; ?>
                            <small><?= esc($row->driver_name ?: $row->requester_name ?: '-'); ?></small>
                        </td>
                        <td class="text-end">
                            <?= (float)$row->fuel_liters > 0 ? number_format($row->fuel_liters, 2) : '-'; ?>
                        </td>
                        <td class="text-end">
                            <?= (float)$row->amount_budget_adjust > 0 ? number_format($row->amount_budget_adjust, 2) : '-'; ?>
                        </td>
                        <td class="text-end">
                            <?= (float)$row->amount_withdraw > 0 ? number_format($row->amount_withdraw, 2) : '-'; ?>
                        </td>
                        <td class="text-end fw-bold">
                            <?= number_format($row->balance, 2); ?>
                        </td>
                        <td>
                            <small><?= esc($row->notes ?: ($row->gas_station ? 'ปั๊ม: ' . $row->gas_station : '-')); ?></small>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="10" class="text-center" style="padding: 20px;">ไม่มีข้อมูลการเบิกค่าน้ำมันในปีงบประมาณนี้</td>
                </tr>
            <?php endif; ?>
        </tbody>
        <tfoot>
            <tr style="background-color: #f9f9f9; font-weight: bold;">
                <td colspan="5" class="text-center">รวมทั้งสิ้น</td>
                <td class="text-end"><?= number_format($stats->total_liters, 2); ?> ลิตร</td>
                <td class="text-end"><?= number_format($stats->total_budget, 2); ?></td>
                <td class="text-end"><?= number_format($stats->total_spent, 2); ?></td>
                <td class="text-end"><?= number_format($stats->remaining, 2); ?></td>
                <td></td>
            </tr>
        </tfoot>
    </table>

    <div class="signature-section">
        <div class="signature-box">
            <br><br>
            ลงชื่อ..............................................................ผู้จัดทำ<br>
            (..............................................................)<br>
            ตำแหน่ง..............................................................<br>
            วันที่ ......./......./...........
        </div>
        <div class="signature-box">
            <br><br>
            ลงชื่อ..............................................................ผู้ตรวจ/หัวหน้ากลุ่มงาน<br>
            (..............................................................)<br>
            ตำแหน่ง..............................................................<br>
            วันที่ ......./......./...........
        </div>
    </div>

</body>
</html>
