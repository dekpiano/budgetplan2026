<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= esc($title) ?></title>
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
            padding: 4px 6px;
        }
        .ledger-table th {
            background-color: #f2f2f2;
            text-align: center;
            font-weight: bold;
        }
        .text-center { text-align: center; }
        .text-end { text-align: right; }
        .fw-bold { font-weight: bold; }
        
        .no-print {
            margin-bottom: 15px;
        }
        @media print {
            .no-print { display: none !important; }
            body { padding: 0; }
        }
    </style>
</head>
<body>

    <div class="no-print" style="text-align: right;">
        <button onclick="window.print()" style="padding: 8px 18px; font-size: 14px; font-weight: bold; background: #0d6efd; color: #fff; border: none; border-radius: 6px; cursor: pointer;">
            🖨️ สั่งพิมพ์เอกสาร (Print / PDF)
        </button>
        <button onclick="window.close()" style="padding: 8px 14px; font-size: 14px; background: #6c757d; color: #fff; border: none; border-radius: 6px; cursor: pointer; margin-left: 5px;">
            ปิดหน้าต่าง
        </button>
    </div>

    <div class="header-title">ทะเบียนคุมเงินงบประมาณรายจ่าย</div>
    <div class="header-subtitle">โรงเรียนสวนกุหลาบวิทยาลัย (จิรประวัติ) นครสวรรค์</div>

    <table class="meta-table">
        <tr>
            <td style="width: 50%;"><b>ปีงบประมาณ พ.ศ.</b> <?= esc($project->budget_year) ?></td>
            <td style="width: 50%;"><b>ยุทธศาสตร์</b> <?= esc($project->strategy_name ?: '-') ?></td>
        </tr>
        <tr>
            <td><b>หมวด / โครงการ</b> <?= esc($project->project_code ? $project->project_code . ' ' : '') ?><?= esc($project->project_name) ?></td>
            <td><b>กลยุทธ์</b> <?= esc($project->strategy_sub ?: '-') ?></td>
        </tr>
    </table>

    <table class="ledger-table">
        <thead>
            <tr>
                <th rowspan="2" style="width: 100px;">วัน/เดือน/ปี</th>
                <th rowspan="2" style="width: 90px;">ที่เอกสาร</th>
                <th rowspan="2">รายการ</th>
                <th rowspan="2" style="width: 95px;">เพิ่ม/ลด</th>
                <th rowspan="2" style="width: 95px;">ยืมเงิน</th>
                <th colspan="2">เงินงบประมาณรายจ่าย</th>
                <th rowspan="2" style="width: 95px;">เบิก</th>
                <th rowspan="2" style="width: 100px;">คงเหลือ</th>
            </tr>
            <tr>
                <th style="width: 90px;">ส่งใช้เงินสด</th>
                <th style="width: 90px;">ส่งใช้ใบสำคัญ</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($ledger as $row): ?>
                <?php
                    $displayDate = '-';
                    if (!empty($row->doc_date) && $row->doc_date !== '0000-00-00') {
                        $time = strtotime($row->doc_date);
                        if ($time !== false && $time > 0) {
                            $d = date('j', $time);
                            $m = date('n', $time);
                            $y = (int)date('Y', $time);
                            if ($y < 2400) {
                                $y += 543;
                            }
                            $thaiMonths = ['', 'ม.ค.', 'ก.พ.', 'มี.ค.', 'เม.ย.', 'พ.ค.', 'มิ.ย.', 'ก.ค.', 'ส.ค.', 'ก.ย.', 'ต.ค.', 'พ.ย.', 'ธ.ค.'];
                            $displayDate = $d . '-' . $thaiMonths[$m] . '-' . substr((string)$y, 2);
                        }
                    }
                ?>
                <tr>
                    <td class="text-center"><?= $displayDate ?></td>
                    <td class="text-center"><?= esc($row->doc_no ?: '-') ?></td>
                    <td><?= esc($row->description) ?></td>
                    <td class="text-end"><?= $row->amount_budget_adjust > 0 ? number_format($row->amount_budget_adjust, 2) : '' ?></td>
                    <td class="text-end"><?= $row->amount_borrow > 0 ? number_format($row->amount_borrow, 2) : '' ?></td>
                    <td class="text-end"><?= $row->amount_return_cash > 0 ? number_format($row->amount_return_cash, 2) : '' ?></td>
                    <td class="text-end"><?= $row->amount_return_receipt > 0 ? number_format($row->amount_return_receipt, 2) : '' ?></td>
                    <td class="text-end"><?= $row->amount_direct_withdraw > 0 ? number_format($row->amount_direct_withdraw, 2) : '' ?></td>
                    <td class="text-end fw-bold"><?= number_format($row->balance, 2) ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
        <tfoot>
            <tr class="fw-bold" style="background-color: #f9f9f9;">
                <td colspan="3" class="text-center">รวมทั้งสิ้น</td>
                <td class="text-end"><?= number_format($totals['adjust'], 2) ?></td>
                <td class="text-end"><?= number_format($totals['borrow'], 2) ?></td>
                <td class="text-end"><?= number_format($totals['return_cash'], 2) ?></td>
                <td class="text-end"><?= number_format($totals['return_receipt'], 2) ?></td>
                <td class="text-end"><?= number_format($totals['direct_withdraw'], 2) ?></td>
                <td class="text-end"><?= number_format($totals['last_balance'], 2) ?></td>
            </tr>
        </tfoot>
    </table>

</body>
</html>
