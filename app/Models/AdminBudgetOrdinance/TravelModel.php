<?php

namespace App\Models\AdminBudgetOrdinance;

use CodeIgniter\Model;

class TravelModel extends Model
{
    protected $table            = 'tb_budget_ordinance_travel';
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
        'travel_start_date',
        'travel_end_date',
        'travel_days',
        'requester_name',
        'traveler_names',
        'traveler_count',
        'allowance_amount',
        'accommodation_amount',
        'transport_amount',
        'other_expenses_amount',
        'amount_budget_adjust',
        'amount_withdraw',
        'balance',
        'notes',
        'entry_type',
        'created_by',
        'updated_by',
        'created_at',
        'updated_at',
    ];

    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    /**
     * ดึงรายการเคลื่อนไหวตามปีงบประมาณ เรียงตามวันที่
     */
    public function getItemsByYear(string $budgetYear)
    {
        return $this->where('budget_year', $budgetYear)
            ->orderBy('doc_date', 'ASC')
            ->orderBy('id', 'ASC')
            ->findAll();
    }

    /**
     * ดึงรายการปีงบประมาณทั้งหมดที่มีในระบบ (พร้อมรวมปีงบประมาณปัจจุบันอัตโนมัติ)
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
     * คำนวณยอดเงินคงเหลือสะสม (Running Balance) ของทั้งปีงบประมาณใหม่
     */
    public function recalculateRunningBalance(string $budgetYear)
    {
        $items = $this->where('budget_year', $budgetYear)
            ->orderBy('doc_date', 'ASC')
            ->orderBy('id', 'ASC')
            ->findAll();

        $runningBalance = 0.00;
        foreach ($items as $item) {
            $adjust = (float)($item->amount_budget_adjust ?? 0);
            $withdraw = (float)($item->amount_withdraw ?? 0);
            $runningBalance += ($adjust - $withdraw);

            $this->update($item->id, [
                'balance' => $runningBalance
            ]);
        }

        return $runningBalance;
    }

    /**
     * ตรวจสอบว่าปีงบประมาณนี้ มีการตั้งงบประมาณแล้วหรือไม่
     */
    public function hasBudgetSet(string $budgetYear): bool
    {
        $row = $this->where('budget_year', $budgetYear)
            ->selectSum('amount_budget_adjust', 'total')
            ->first();

        return !empty($row) && ((float)($row->total ?? 0) > 0);
    }

    /**
     * สรุปสถิติ 4 ตัวเลขประจำปีงบประมาณ
     */
    public function getSummaryStats(string $budgetYear): object
    {
        $builder = $this->where('budget_year', $budgetYear);
        
        $totalBudget = (float)$this->where('budget_year', $budgetYear)
            ->selectSum('amount_budget_adjust', 'total')
            ->first()->total ?? 0;

        $totalSpent = (float)$this->where('budget_year', $budgetYear)
            ->selectSum('amount_withdraw', 'total')
            ->first()->total ?? 0;

        $totalAllowance = (float)$this->where('budget_year', $budgetYear)
            ->selectSum('allowance_amount', 'total')
            ->first()->total ?? 0;

        $totalAccommodation = (float)$this->where('budget_year', $budgetYear)
            ->selectSum('accommodation_amount', 'total')
            ->first()->total ?? 0;

        $totalTransport = (float)$this->where('budget_year', $budgetYear)
            ->selectSum('transport_amount', 'total')
            ->first()->total ?? 0;

        $totalOther = (float)$this->where('budget_year', $budgetYear)
            ->selectSum('other_expenses_amount', 'total')
            ->first()->total ?? 0;

        $totalRecords = $this->where('budget_year', $budgetYear)
            ->where('entry_type', 'withdraw')
            ->countAllResults();

        $totalDays = (int)$this->where('budget_year', $budgetYear)
            ->where('entry_type', 'withdraw')
            ->selectSum('travel_days', 'total')
            ->first()->total ?? 0;

        $totalTravelers = (int)$this->where('budget_year', $budgetYear)
            ->where('entry_type', 'withdraw')
            ->selectSum('traveler_count', 'total')
            ->first()->total ?? 0;

        return (object)[
            'total_budget'         => $totalBudget,
            'total_spent'          => $totalSpent,
            'remaining'            => $totalBudget - $totalSpent,
            'total_allowance'      => $totalAllowance,
            'total_accommodation'  => $totalAccommodation,
            'total_transport'      => $totalTransport,
            'total_other'          => $totalOther,
            'total_records'        => $totalRecords,
            'total_days'           => $totalDays,
            'total_travelers'      => $totalTravelers,
        ];
    }
}
