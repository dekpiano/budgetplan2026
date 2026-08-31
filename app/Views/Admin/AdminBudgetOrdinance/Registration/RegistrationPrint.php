<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>รายงานทะเบียนคุมค่าลงทะเบียนอบรม/สัมมนา ประจำปีงบประมาณ <?= esc($selected_year); ?></title>
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
        .font-monospace { font-family: monospace, sans-serif; }
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
        .no-print {
            margin-bottom: 15px;
            text-align: right;
        }
        .btn-print {
            background-color: #FB8C00;
            color: white;
            border: none;
            padding: 8px 18px;
            border-radius: 6px;
            cursor: pointer;
            font-size: 14px;
            font-weight: bold;
        }
        .btn-print:hover {
            background-color: #e65100;
        }
        @media print {
            .no-print {
                display: none !important;
            }
            body {
                padding: 0;
            }
        }
    </style>
</head>
<body>

    <div class="no-print">
        <button class="btn-print" onclick="window.print()">🖨️ พิมพ์เอกสาร / บันทึกเป็น PDF</button>
    </div>

    <div class="header-title">ทะเบียนคุมงบประมาณเงินข้อบัญญัติ (ค่าลงทะเบียนอบรม / สัมมนา)</div>
    <div class="header-subtitle">โรงเรียนสวนกุหลาบวิทยาลัย (จิรประวัติ) นครสวรรค์</div>

    <table class="meta-table">
        <tr>
            <td style="width: 50%;"><b>ปีงบประมาณ:</b> พ.ศ. <?= esc($selected_year); ?></td>
            <td style="width: 50%; text-align: right;"><b>หน่วยงาน:</b> กลุ่มบริหารงบประมาณและแผนงาน</td>
        </tr>
        <tr>
            <td><b>งบประมาณจัดสรร:</b> <?= number_format($stats->total_budget, 2); ?> บาท</td>
            <td style="text-align: right;"><b>เบิกจ่ายแล้ว:</b> <?= number_format($stats->total_spent, 2); ?> บาท | <b>คงเหลือ:</b> <?= number_format($stats->remaining, 2); ?> บาท</td>
        </tr>
    </table>

    <table class="ledger-table">
        <thead>
            <tr>
                <th rowspan="2" style="width: 35px;">ลำดับ</th>
                <th rowspan="2" style="width: 85px;">วัน เดือน ปี</th>
                <th rowspan="2" style="width: 100px;">เลขที่ฎีกา/เอกสาร</th>
                <th rowspan="2">โครงการ / หลักสูตรฝึกอบรม</th>
                <th rowspan="2" style="width: 140px;">หน่วยงานผู้จัด / สถานที่</th>
                <th rowspan="2" style="width: 140px;">ผู้ขอเบิก / ผู้เข้าร่วม</th>
                <th colspan="3">การเบิกจ่ายงบประมาณ (บาท)</th>
                <th rowspan="2" style="width: 90px;">หมายเหตุ</th>
            </tr>
            <tr>
                <th style="width: 90px;">ค่าลงทะเบียน/คน</th>
                <th style="width: 95px;">ยอดเบิกจ่าย</th>
                <th style="width: 95px;">งบคงเหลือ</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($items)) : ?>
                <?php $idx = 1; foreach ($items as $item) : ?>
                    <tr>
                        <td class="text-center"><?= $idx++; ?></td>
                        <td class="text-center"><?= thaidate_format($item->doc_date, 'numeric'); ?></td>
                        <td>
                            <?php if (!empty($item->dika_no)) : ?>
                                <div><b>ฎีกา:</b> <?= esc($item->dika_no); ?></div>
                            <?php endif; ?>
                            <?php if (!empty($item->doc_no)) : ?>
                                <small><b>หนังสือ:</b> <?= esc($item->doc_no); ?></small>
                            <?php endif; ?>
                        </td>
                        <td>
                            <div><?= esc($item->description); ?></div>
                            <?php if (!empty($item->training_start_date)) : ?>
                                <small style="color: #444;">
                                    วันที่อบรม: <?= thaidate_range_format($item->training_start_date, $item->training_end_date, 'short'); ?>
                                    (<?= $item->training_days; ?> วัน)
                                </small>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php if (!empty($item->organizer)) : ?>
                                <div><?= esc($item->organizer); ?></div>
                            <?php endif; ?>
                            <?php if (!empty($item->location)) : ?>
                                <small style="color: #555;"><?= esc($item->location); ?></small>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php if (!empty($item->requester_name)) : ?>
                                <div><?= esc($item->requester_name); ?></div>
                            <?php endif; ?>
                            <?php if (!empty($item->attendee_names)) : ?>
                                <small style="color: #555;"><?= esc($item->attendee_names); ?></small>
                            <?php endif; ?>
                            <?php if ($item->attendee_count > 0 && $item->entry_type !== 'adjust') : ?>
                                <small>(<?= $item->attendee_count; ?> คน)</small>
                            <?php endif; ?>
                        </td>
                        <td class="text-end font-monospace">
                            <?php if ($item->fee_per_person > 0) : ?>
                                <?= number_format($item->fee_per_person, 2); ?>
                            <?php elseif ($item->entry_type === 'adjust') : ?>
                                <span style="color: #e65100;">+<?= number_format($item->amount_budget_adjust, 2); ?></span>
                            <?php else : ?>
                                -
                            <?php endif; ?>
                        </td>
                        <td class="text-end font-monospace">
                            <?php if ($item->amount_withdraw > 0) : ?>
                                <?= number_format($item->amount_withdraw, 2); ?>
                            <?php else : ?>
                                -
                            <?php endif; ?>
                        </td>
                        <td class="text-end font-monospace fw-bold">
                            <?= number_format($item->balance, 2); ?>
                        </td>
                        <td>
                            <?php if (!empty($item->receipt_no)) : ?>
                                <div>ใบเสร็จ: <?= esc($item->receipt_no); ?></div>
                            <?php endif; ?>
                            <?php if (!empty($item->notes)) : ?>
                                <small><?= esc($item->notes); ?></small>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else : ?>
                <tr>
                    <td colspan="10" class="text-center" style="padding: 20px;">ไม่มีข้อมูลรายการในปีงบประมาณ พ.ศ. <?= esc($selected_year); ?></td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>

    <div class="signature-section">
        <div class="signature-box">
            <br><br>
            ลงชื่อ..........................................................ผู้จัดทำ/เจ้าหน้าที่<br>
            (..........................................................)<br>
            ตำแหน่ง..........................................................
        </div>
        <div class="signature-box">
            <br><br>
            ลงชื่อ..........................................................ผู้ตรวจ/หัวหน้ากลุ่มงาน<br>
            (..........................................................)<br>
            ตำแหน่ง..........................................................
        </div>
    </div>

</body>
</html>
