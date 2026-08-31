<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <title>รายงานทะเบียนคุมงบประมาณเงินข้อบัญญัติ - ค่าใช้จ่ายเดินทางไปราชการ ปี <?= esc($selected_year); ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Sarabun:wght@300;400;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Sarabun', sans-serif;
            font-size: 13px;
            color: #000;
            background-color: #fff;
            margin: 0;
            padding: 20px;
        }
        @page {
            size: A4 landscape;
            margin: 12mm 10mm 12mm 10mm;
        }
        .header-title {
            text-align: center;
            font-size: 18px;
            font-weight: bold;
            margin-bottom: 2px;
        }
        .header-subtitle {
            text-align: center;
            font-size: 14px;
            margin-bottom: 12px;
        }
        .meta-table {
            width: 100%;
            margin-bottom: 10px;
            font-size: 13px;
        }
        .table-ledger {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }
        .table-ledger th, .table-ledger td {
            border: 1px solid #333;
            padding: 5px 6px;
            vertical-align: top;
        }
        .table-ledger th {
            background-color: #f2f2f2;
            text-align: center;
            font-weight: bold;
            font-size: 12px;
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

    <div class="header-title">ทะเบียนคุมงบประมาณเงินข้อบัญญัติ (ค่าใช้จ่ายในการเดินทางไปราชการ)</div>
    <div class="header-subtitle">โรงเรียนสวนกุหลาบวิทยาลัย (จิรประวัติ) นครสวรรค์</div>

    <table class="meta-table">
        <tr>
            <td style="width: 50%;"><b>ปีงบประมาณ:</b> พ.ศ. <?= esc($selected_year); ?></td>
            <td style="width: 50%; text-align: right;"><b>วันที่พิมพ์:</b> <?= date('d/m/') . (date('Y') + 543); ?></td>
        </tr>
    </table>

    <table class="table-ledger">
        <thead>
            <tr>
                <th style="width: 35px;">ลำดับ</th>
                <th style="width: 80px;">วัน/เดือน/ปี</th>
                <th style="width: 90px;">เลขที่ฎีกา/เอกสาร</th>
                <th>รายการ / วัตถุประสงค์ / สถานที่ไปราชการ</th>
                <th style="width: 130px;">ช่วงวันเดินทาง / ผู้ขอเบิก</th>
                <th style="width: 95px;" class="text-end">งบจัดสรร (+)</th>
                <th style="width: 95px;" class="text-end">เบิกจ่าย (-)</th>
                <th style="width: 95px;" class="text-end">คงเหลือ</th>
                <th style="width: 80px;">หมายเหตุ</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($items)): ?>
                <?php 
                $index = 1; 
                foreach ($items as $row): 
                    $docDateTh = thaidate_format($row->doc_date);
                ?>
                    <tr>
                        <td class="text-center"><?= $index++; ?></td>
                        <td class="text-center"><?= $docDateTh; ?></td>
                        <td>
                            <?= esc($row->dika_no ? "ฎีกา: $row->dika_no" : ($row->doc_no ? "หนังสือ: $row->doc_no" : "-")); ?>
                        </td>
                        <td>
                            <div class="fw-bold"><?= esc($row->description ?: 'ค่าใช้จ่ายเดินทางไปราชการ'); ?></div>
                            <?php if (!empty($row->destination)): ?>
                                <small>สถานที่: <?= esc($row->destination); ?></small>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php 
                            $travelPeriodPrint = '';
                            if (!empty($row->travel_start_date) && $row->travel_start_date !== '0000-00-00') {
                                $s = explode('-', $row->travel_start_date);
                                if (count($s) === 3) {
                                    $sTh = "{$s[2]}/{$s[1]}/" . ((int)$s[0] > 2400 ? $s[0] : (int)$s[0] + 543);
                                    if (!empty($row->travel_end_date) && $row->travel_end_date !== '0000-00-00' && $row->travel_end_date !== $row->travel_start_date) {
                                        $e = explode('-', $row->travel_end_date);
                                        if (count($e) === 3) {
                                            $eTh = "{$e[2]}/{$e[1]}/" . ((int)$e[0] > 2400 ? $e[0] : (int)$e[0] + 543);
                                            $travelPeriodPrint = "{$sTh} - {$eTh}";
                                        } else {
                                            $travelPeriodPrint = $sTh;
                                        }
                                    } else {
                                        $travelPeriodPrint = $sTh;
                                    }
                                }
                            }
                            ?>
                            <?php if (!empty($travelPeriodPrint)): ?>
                                <small><?= $travelPeriodPrint; ?> (<?= $row->travel_days; ?> วัน)</small><br>
                            <?php endif; ?>
                            <small>ผู้ขอ: <?= esc($row->requester_name ?: '-'); ?></small>
                        </td>
                        <td class="text-end">
                            <?= ((float)$row->amount_budget_adjust > 0) ? number_format($row->amount_budget_adjust, 2) : '-'; ?>
                        </td>
                        <td class="text-end">
                            <?= ((float)$row->amount_withdraw > 0) ? number_format($row->amount_withdraw, 2) : '-'; ?>
                        </td>
                        <td class="text-end fw-bold">
                            <?= number_format($row->balance, 2); ?>
                        </td>
                        <td><small><?= esc($row->notes ?: '-'); ?></small></td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="9" class="text-center" style="padding: 20px;">ไม่มีข้อมูลรายการในปีงบประมาณ พ.ศ. <?= esc($selected_year); ?></td>
                </tr>
            <?php endif; ?>
        </tbody>
        <tfoot>
            <tr style="background-color: #f9f9f9; font-weight: bold;">
                <td colspan="5" class="text-center">รวมทั้งสิ้น</td>
                <td class="text-end"><?= number_format($stats->total_budget, 2); ?></td>
                <td class="text-end"><?= number_format($stats->total_spent, 2); ?></td>
                <td class="text-end"><?= number_format($stats->remaining, 2); ?></td>
                <td></td>
            </tr>
        </tfoot>
    </table>

    <div class="signature-section">
        <div class="signature-box">
            ลงชื่อ.........................................................ผู้รายงาน<br>
            (.........................................................)<br>
            ตำแหน่ง.........................................................<br>
            วันที่ ......./......./.......
        </div>
        <div class="signature-box">
            ลงชื่อ.........................................................ผู้ตรวจ / ผู้อนุมัติ<br>
            (.........................................................)<br>
            ตำแหน่ง ผู้อำนวยการโรงเรียนสวนกุหลาบวิทยาลัย (จิรประวัติ) นครสวรรค์<br>
            วันที่ ......./......./.......
        </div>
    </div>

</body>
</html>
