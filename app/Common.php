<?php

/**
 * The goal of this file is to allow developers a location
 * where they can overwrite core procedural functions and
 * replace them with their own. This file is loaded during
 * the bootstrap process and is called during the framework's
 * execution.
 *
 * This can be looked at as a `master helper` file that is
 * loaded early on, and may also contain additional functions
 * that you'd like to use throughout your entire application
 *
 * @see: https://codeigniter.com/user_guide/extending/common.html
 */

if (!function_exists('thaidate_format')) {
    /**
     * แปลงวันที่สากล (Y-m-d) เป็นวันที่ภาษาไทย (พ.ศ.)
     * @param string|null $dateStr เช่น '2026-08-28'
     * @param string $format 'short' (28 ส.ค. 2569), 'full' (28 สิงหาคม 2569), 'numeric' (28/08/2569)
     * @return string
     */
    function thaidate_format(?string $dateStr, string $format = 'full'): string
    {
        if (empty($dateStr) || $dateStr === '0000-00-00') {
            return '-';
        }

        $cleaned = trim($dateStr);
        $d = null; $m = null; $y = null;

        if (preg_match('/^(\d{4})-(\d{2})-(\d{2})$/', $cleaned, $matches)) {
            $y = (int)$matches[1];
            $m = (int)$matches[2];
            $d = (int)$matches[3];
        } elseif (preg_match('/^(\d{1,2})\/(\d{1,2})\/(\d{4})$/', $cleaned, $matches)) {
            $d = (int)$matches[1];
            $m = (int)$matches[2];
            $y = (int)$matches[3];
        } else {
            $t = strtotime($cleaned);
            if (!$t) return '-';
            $d = (int)date('j', $t);
            $m = (int)date('n', $t);
            $y = (int)date('Y', $t);
        }

        if ($y < 2400) {
            $y += 543;
        }

        $thaiMonthsShort = ['', 'ม.ค.', 'ก.พ.', 'มี.ค.', 'เม.ย.', 'พ.ค.', 'มิ.ย.', 'ก.ค.', 'ส.ค.', 'ก.ย.', 'ต.ค.', 'พ.ย.', 'ธ.ค.'];
        $thaiMonthsFull = ['', 'มกราคม', 'กุมภาพันธ์', 'มีนาคม', 'เมษายน', 'พฤษภาคม', 'มิถุนายน', 'กรกฎาคม', 'สิงหาคม', 'กันยายน', 'ตุลาคม', 'พฤศจิกายน', 'ธันวาคม'];

        if ($format === 'full') {
            return "{$d} " . ($thaiMonthsFull[$m] ?? '') . " {$y}";
        } elseif ($format === 'numeric') {
            return sprintf('%02d/%02d/%04d', $d, $m, $y);
        }

        return "{$d} " . ($thaiMonthsShort[$m] ?? '') . " {$y}";
    }
}

if (!function_exists('thaidate_range_format')) {
    /**
     * แปลงช่วงวันที่เดินทาง (Start - End) เป็นรูปแบบทางการไทย
     * @param string|null $startStr
     * @param string|null $endStr
     * @return string
     */
    function thaidate_range_format(?string $startStr, ?string $endStr, string $format = 'full'): string
    {
        if (empty($startStr) || $startStr === '0000-00-00') {
            return '-';
        }

        if (empty($endStr) || $endStr === '0000-00-00' || $endStr === $startStr) {
            return thaidate_format($startStr, $format);
        }

        // Parse Start Date
        $sClean = trim($startStr);
        $sd = null; $sm = null; $sy = null;
        if (preg_match('/^(\d{4})-(\d{2})-(\d{2})$/', $sClean, $matches)) {
            $sy = (int)$matches[1]; $sm = (int)$matches[2]; $sd = (int)$matches[3];
        } else {
            $t = strtotime($sClean);
            if ($t) { $sd = (int)date('j', $t); $sm = (int)date('n', $t); $sy = (int)date('Y', $t); }
        }

        // Parse End Date
        $eClean = trim($endStr);
        $ed = null; $em = null; $ey = null;
        if (preg_match('/^(\d{4})-(\d{2})-(\d{2})$/', $eClean, $matches)) {
            $ey = (int)$matches[1]; $em = (int)$matches[2]; $ed = (int)$matches[3];
        } else {
            $t = strtotime($eClean);
            if ($t) { $ed = (int)date('j', $t); $em = (int)date('n', $t); $ey = (int)date('Y', $t); }
        }

        if (!$sd || !$ed) {
            return thaidate_format($startStr, $format);
        }

        if ($sy < 2400) $sy += 543;
        if ($ey < 2400) $ey += 543;

        $thaiMonthsShort = ['', 'ม.ค.', 'ก.พ.', 'มี.ค.', 'เม.ย.', 'พ.ค.', 'มิ.ย.', 'ก.ค.', 'ส.ค.', 'ก.ย.', 'ต.ค.', 'พ.ย.', 'ธ.ค.'];
        $thaiMonthsFull = ['', 'มกราคม', 'กุมภาพันธ์', 'มีนาคม', 'เมษายน', 'พฤษภาคม', 'มิถุนายน', 'กรกฎาคม', 'สิงหาคม', 'กันยายน', 'ตุลาคม', 'พฤศจิกายน', 'ธันวาคม'];
        $months = ($format === 'full') ? $thaiMonthsFull : $thaiMonthsShort;

        if ($sy === $ey && $sm === $em) {
            return "{$sd} - {$ed} " . ($months[$sm] ?? '') . " {$sy}";
        } elseif ($sy === $ey) {
            return "{$sd} " . ($months[$sm] ?? '') . " - {$ed} " . ($months[$em] ?? '') . " {$sy}";
        }

        return "{$sd} " . ($months[$sm] ?? '') . " {$sy} - {$ed} " . ($months[$em] ?? '') . " {$ey}";
    }
}

if (!function_exists('get_budget_year')) {
    /**
     * คำนวณปีงบประมาณไทย (พ.ศ.) อัตโนมัติจากวันที่
     * รอบปีงบประมาณไทย: 1 ตุลาคม ของปีก่อนหน้า - 30 กันยายน ของปีปัจจุบัน
     * เช่น:
     *  - วันที่ 2025-10-01 ถึง 2026-09-30 => ปีงบประมาณ 2569
     *  - วันที่ 2026-10-01 ถึง 2027-09-30 => ปีงบประมาณ 2570
     *
     * @param string|null $dateStr วันที่ (เช่น '2026-08-31', '31/08/2569') หากไม่ระบุจะใช้วันปัจจุบัน
     * @return string ปีงบประมาณ พ.ศ. เช่น '2569'
     */
    function get_budget_year(?string $dateStr = null): string
    {
        if (empty($dateStr) || $dateStr === '0000-00-00') {
            $t = time();
            $y = (int)date('Y', $t);
            $m = (int)date('n', $t);
        } else {
            $cleaned = trim($dateStr);
            $y = null; $m = null;

            if (preg_match('/^(\d{4})-(\d{2})-(\d{2})$/', $cleaned, $matches)) {
                $y = (int)$matches[1];
                $m = (int)$matches[2];
            } elseif (preg_match('/^(\d{1,2})\/(\d{1,2})\/(\d{4})$/', $cleaned, $matches)) {
                $m = (int)$matches[2];
                $y = (int)$matches[3];
            } else {
                $t = strtotime($cleaned);
                if (!$t) {
                    $t = time();
                }
                $y = (int)date('Y', $t);
                $m = (int)date('n', $t);
            }

            // หากส่งปี พ.ศ. มา (> 2400) ให้แปลงเป็น ค.ศ. ก่อนคำนวณรอบปีงบประมาณ
            if ($y > 2400) {
                $y -= 543;
            }
        }

        // หากเป็นเดือน 10 (ต.ค.), 11 (พ.ย.), 12 (ธ.ค.) นับเป็นปีงบประมาณถัดไป
        $fiscalYear = ($m >= 10) ? ($y + 543 + 1) : ($y + 543);
        return (string)$fiscalYear;
    }
}

if (!function_exists('natural_code_compare')) {
    /**
     * เปรียบเทียบรหัส (เช่น รหัสโครงการ 1.1.2 vs 1.1.10, ยุทธศาสตร์ 1 vs 10) แบบธรรมชาติ (Natural Order)
     */
    function natural_code_compare(?string $a, ?string $b): int
    {
        $aStr = trim((string)$a);
        $bStr = trim((string)$b);
        if ($aStr === $bStr) return 0;
        if ($aStr === '' || $aStr === '-') return 1; // รหัสว่างให้อยู่ท้ายสุด
        if ($bStr === '' || $bStr === '-') return -1;
        return strnatcasecmp($aStr, $bStr);
    }
}

if (!function_exists('natural_code_sort_key')) {
    /**
     * แปลงรหัสโครงการให้เป็น Sort Key สำหรับ data-order ในตาราง DataTables
     * เช่น '1.1.2' => '00001.00001.00002', '1.1.10' => '00001.00001.00010'
     */
    function natural_code_sort_key(?string $code): string
    {
        $str = trim((string)$code);
        if ($str === '' || $str === '-') return '99999';
        return preg_replace_callback('/\d+/', function ($m) {
            return str_pad($m[0], 5, '0', STR_PAD_LEFT);
        }, $str);
    }
}

if (!function_exists('sort_projects_naturally')) {
    /**
     * จัดเรียง array ของข้อมูลตามรหัส (เช่น project_code, strategy_no, tactic_no) แบบธรรมชาติ
     */
    function sort_projects_naturally(array &$items, string $field = 'project_code'): void
    {
        usort($items, function ($a, $b) use ($field) {
            $codeA = is_object($a) ? ($a->$field ?? '') : ($a[$field] ?? '');
            $codeB = is_object($b) ? ($b->$field ?? '') : ($b[$field] ?? '');
            return natural_code_compare($codeA, $codeB);
        });
    }
}

if (!function_exists('build_sub_items_tree')) {
    /**
     * จัดโครงสร้างรายการกิจกรรมย่อย (sub_items) ให้เป็นลำดับชั้น (แม่-ลูก: กิจกรรมหลัก -> กิจกรรมย่อย)
     * รองรับทั้งข้อมูลที่มี parent_id และข้อมูลเก่าที่อิงตาม item_no เช่น 10.1 และ 10.1.2
     */
    function build_sub_items_tree(array $subItems): array
    {
        if (empty($subItems)) {
            return [];
        }

        $itemsById = [];
        $isObject = is_object(reset($subItems));

        foreach ($subItems as $item) {
            $id = $isObject ? (int)($item->id ?? 0) : (int)($item['id'] ?? 0);
            if ($isObject) {
                $item->children = [];
                $item->is_parent = false;
            } else {
                $item['children'] = [];
                $item['is_parent'] = false;
            }
            if ($id > 0) {
                $itemsById[$id] = $item;
            }
        }

        $parents = [];
        $children = [];

        // ตรวจสอบว่ามีรายการที่ระบุ parent_id หรือไม่
        $hasExplicitParent = false;
        foreach ($subItems as $item) {
            $parentId = $isObject ? ($item->parent_id ?? null) : ($item['parent_id'] ?? null);
            if (!empty($parentId)) {
                $hasExplicitParent = true;
                break;
            }
        }

        if ($hasExplicitParent) {
            foreach ($subItems as $item) {
                $parentId = $isObject ? ($item->parent_id ?? null) : ($item['parent_id'] ?? null);
                $parentId = !empty($parentId) ? (int)$parentId : null;

                if ($parentId && isset($itemsById[$parentId])) {
                    $children[$parentId][] = $item;
                } else {
                    $id = $isObject ? (int)($item->id ?? 0) : (int)($item['id'] ?? 0);
                    $parents[$id] = $item;
                }
            }
        } else {
            // โครงสร้างเดิม: จับคู่ตาม item_no เช่น 10.1 เป็นแม่ของ 10.1.1, 10.1.2
            $sorted = $subItems;
            sort_projects_naturally($sorted, 'item_no');

            foreach ($sorted as $item) {
                $itemNo = trim($isObject ? ($item->item_no ?? '') : ($item['item_no'] ?? ''));
                $matchedParentId = null;

                if (!empty($itemNo)) {
                    foreach ($parents as $pId => $pItem) {
                        $pNo = trim($isObject ? ($pItem->item_no ?? '') : ($pItem['item_no'] ?? ''));
                        if (!empty($pNo) && strpos($itemNo, $pNo . '.') === 0) {
                            $matchedParentId = $pId;
                            break;
                        }
                    }
                }

                if ($matchedParentId !== null) {
                    $children[$matchedParentId][] = $item;
                } else {
                    $id = $isObject ? (int)($item->id ?? 0) : (int)($item['id'] ?? 0);
                    $parents[$id] = $item;
                }
            }
        }

        // เชื่อมโยงลูกเข้าหากิจกรรมหลัก และคำนวณงบประมาณรวม
        foreach ($children as $pId => $childList) {
            sort_projects_naturally($childList, 'item_no');
            if (isset($parents[$pId])) {
                $childSum = 0.0;
                $childSpent = 0.0;
                foreach ($childList as $ch) {
                    $b = (float)($isObject ? ($ch->allocated_budget ?? 0) : ($ch['allocated_budget'] ?? 0));
                    $s = (float)($isObject ? ($ch->total_spent ?? 0) : ($ch['total_spent'] ?? 0));
                    $childSum += $b;
                    $childSpent += $s;
                }

                if ($isObject) {
                    $parents[$pId]->children = $childList;
                    $parents[$pId]->is_parent = true;
                    if ($childSum > 0 || count($childList) > 0) {
                        $parents[$pId]->allocated_budget = $childSum;
                        if (isset($parents[$pId]->total_spent)) {
                            $parents[$pId]->total_spent = $childSpent;
                            $parents[$pId]->remaining = $childSum - $childSpent;
                            $parents[$pId]->percent = $childSum > 0 ? min(100, round(($childSpent / $childSum) * 100, 1)) : 0;
                        }
                    }
                } else {
                    $parents[$pId]['children'] = $childList;
                    $parents[$pId]['is_parent'] = true;
                    if ($childSum > 0 || count($childList) > 0) {
                        $parents[$pId]['allocated_budget'] = $childSum;
                    }
                }
            }
        }

        $result = array_values($parents);
        sort_projects_naturally($result, 'item_no');
        return $result;
    }
}

if (!function_exists('format_item_lines')) {
    /**
     * จัดรูปแบบข้อความที่มีหัวข้อย่อยหรือรายการ (เช่น 1. ... 2. ... หรือ ๑. ... ๒. ... หรือ - ... • ...)
     * ให้แต่ละข้อขึ้นบรรทัดใหม่อย่างเป็นระเบียบสวยงาม
     *
     * @param string|null $text
     * @return string
     */
    function format_item_lines(?string $text): string
    {
        if (empty($text)) {
            return '';
        }

        // แปลง \r\n หรือ \r ให้เป็น \n มาตรฐาน
        $t = str_replace(["\r\n", "\r"], "\n", $text);

        // ขึ้นบรรทัดใหม่ก่อนข้อเลข เช่น ' 2. ', ' 2) ', ' ๒. ', ' ๒) '
        // โดยไม่ตัดเลขทศนิยม เช่น ' 2.5 ล้านบาท' หรือ 'เกรด 3.75'
        $t = preg_replace('/([^\n\r])\s+(?=(\d{1,2}|[๑-๙]{1,2})[\.\)](\s|[^\d\.\s]))/u', "$1\n", $t);

        // ดักจับเครื่องหมาย bullet เช่น ' - ' หรือ ' • ' หรือ ' * '
        $t = preg_replace('/([^\n\r])\s+(?=[-•*]\s+)/u', "$1\n", $t);

        return trim($t);
    }
}


