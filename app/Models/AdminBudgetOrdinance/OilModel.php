<?php

namespace App\Models\AdminBudgetOrdinance;

use CodeIgniter\Model;

class OilModel extends Model
{
    protected $table            = 'tb_budget_ordinance_oil';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'object';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'budget_year',
        'doc_date',
        'dika_no',
        'doc_no',
        'order_no',
        'description',
        'destination',
        'car_license',
        'car_type',
        'driver_name',
        'requester_name',
        'fuel_type',
        'fuel_liters',
        'fuel_price_per_liter',
        'amount_budget_adjust',
        'amount_withdraw',
        'balance',
        'gas_station',
        'receipt_no',
        'notes',
        'entry_type',
        'created_by',
        'updated_by',
        'created_at',
        'updated_at'
    ];

    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    /**
     * ดึงรายการตามปีงบประมาณ เรียงตามวันที่
     */
    public function getItemsByYear(string $year)
    {
        return $this->where('budget_year', $year)
            ->orderBy('doc_date', 'ASC')
            ->orderBy('id', 'ASC')
            ->findAll();
    }

    /**
     * ดึงปีงบประมาณทั้งหมดที่มีในระบบ (พร้อมรวมปีงบประมาณปัจจุบันอัตโนมัติ)
     */
    public function getDistinctYears(): array
    {
        $currentYear = get_budget_year();
        $rows = $this->select('budget_year')
            ->distinct()
            ->orderBy('budget_year', 'DESC')
            ->findAll();

        $yearList = [];
        foreach ($rows as $r) {
            if (!empty($r->budget_year)) {
                $yearList[] = (string)$r->budget_year;
            }
        }
        if (!in_array($currentYear, $yearList)) {
            $yearList[] = $currentYear;
        }
        rsort($yearList);

        $result = [];
        foreach ($yearList as $y) {
            $result[] = (object)['budget_year' => $y];
        }
        return $result;
    }

    /**
     * ตรวจสอบว่าปีงบประมาณนี้ มีการตั้งงบประมาณแล้วหรือไม่
     */
    public function hasBudgetSet(string $year): bool
    {
        $db = \Config\Database::connect();
        $row = $db->table($this->table)
            ->selectSum('amount_budget_adjust', 'total_adjust')
            ->where('budget_year', $year)
            ->get()
            ->getRow();

        return !empty($row) && ((float)($row->total_adjust ?? 0) > 0);
    }

    /**
     * คำนวณสรุปยอดรวมประจำปีงบประมาณ
     */
    public function getSummaryStats(string $year): object
    {
        $db = \Config\Database::connect();
        $builder = $db->table($this->table)->where('budget_year', $year);

        $stats = $builder->select("
            SUM(amount_budget_adjust) as total_budget,
            SUM(amount_withdraw) as total_spent,
            SUM(fuel_liters) as total_liters,
            COUNT(id) as total_records
        ")->get()->getRow();

        $lastItem = $db->table($this->table)
            ->where('budget_year', $year)
            ->orderBy('doc_date', 'DESC')
            ->orderBy('id', 'DESC')
            ->get()
            ->getRow();

        $totalBudget = (float)($stats->total_budget ?? 0);
        $totalSpent  = (float)($stats->total_spent ?? 0);
        $remaining   = $lastItem ? (float)$lastItem->balance : ($totalBudget - $totalSpent);

        return (object)[
            'total_budget'  => $totalBudget,
            'total_spent'   => $totalSpent,
            'remaining'     => $remaining,
            'total_liters'  => (float)($stats->total_liters ?? 0),
            'total_records' => (int)($stats->total_records ?? 0),
        ];
    }

    /**
     * คำนวณยอดคงเหลือต่อเนื่อง (Running Balance) ใหม่ทั้งปีงบประมาณ
     */
    public function recalculateRunningBalance(string $year): void
    {
        $items = $this->where('budget_year', $year)
            ->orderBy('doc_date', 'ASC')
            ->orderBy('id', 'ASC')
            ->findAll();

        $currentBalance = 0.00;
        foreach ($items as $item) {
            $adjust   = (float)$item->amount_budget_adjust;
            $withdraw = (float)$item->amount_withdraw;

            $currentBalance += $adjust;
            $currentBalance -= $withdraw;

            $this->update($item->id, [
                'balance' => $currentBalance
            ]);
        }
    }
}
