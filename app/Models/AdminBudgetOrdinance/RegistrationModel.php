<?php

namespace App\Models\AdminBudgetOrdinance;

use CodeIgniter\Model;

class RegistrationModel extends Model
{
    protected $table            = 'tb_budget_ordinance_registration';
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
        'organizer',
        'location',
        'training_start_date',
        'training_end_date',
        'training_days',
        'requester_name',
        'attendee_names',
        'attendee_count',
        'fee_per_person',
        'amount_budget_adjust',
        'amount_withdraw',
        'balance',
        'receipt_no',
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
     * สรุปสถิติ 4 ตัวเลขประจำปีงบประมาณ
     */
    public function getSummaryStats(string $budgetYear): object
    {
        $totalBudget = (float)$this->where('budget_year', $budgetYear)
            ->selectSum('amount_budget_adjust', 'total')
            ->first()->total ?? 0;

        $totalSpent = (float)$this->where('budget_year', $budgetYear)
            ->selectSum('amount_withdraw', 'total')
            ->first()->total ?? 0;

        $totalRecords = $this->where('budget_year', $budgetYear)
            ->where('entry_type', 'withdraw')
            ->countAllResults();

        $totalAttendees = (int)$this->where('budget_year', $budgetYear)
            ->where('entry_type', 'withdraw')
            ->selectSum('attendee_count', 'total')
            ->first()->total ?? 0;

        $totalDays = (int)$this->where('budget_year', $budgetYear)
            ->where('entry_type', 'withdraw')
            ->selectSum('training_days', 'total')
            ->first()->total ?? 0;

        $remaining = $totalBudget - $totalSpent;

        return (object)[
            'total_budget'     => $totalBudget,
            'total_spent'      => $totalSpent,
            'remaining'        => $remaining,
            'total_records'    => $totalRecords,
            'total_attendees'  => $totalAttendees,
            'total_days'       => $totalDays,
        ];
    }
}
