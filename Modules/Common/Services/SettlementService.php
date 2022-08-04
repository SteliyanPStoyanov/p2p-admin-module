<?php

namespace Modules\Common\Services;

use Carbon\Carbon;
use Modules\Common\Entities\Originator;
use Modules\Common\Entities\Transaction;
use Modules\Common\Entities\Investment;
use Modules\Common\Entities\InvestorInstallment;
use Modules\Common\Entities\Settlement;
use Modules\Common\Entities\Wallet;
use Modules\Core\Services\BaseService;

class SettlementService extends BaseService
{
    public function __construct()
    {
        parent::__construct();
    }

    public function getDepositForYesterday(): float
    {
        $yesterday = Carbon::yesterday();
        return $this->getDepositForDate($yesterday->format('Y-m-d'));
    }

    public function getDepositForDate(string $date): float
    {
        return (new Transaction)->getDepositForDate($date);
    }

    public function getRebuyAmountsForYesterday(Originator $originator): array
    {
        $yesterday = Carbon::yesterday();
        return $this->getRebuyAmountsForDate($yesterday->format('Y-m-d'), $originator);
    }

    public function getRebuyAmountsForDate(string $date, Originator $originator): array
    {
        return (new Transaction)->getRebuyAmountsForDate($date, $originator);
    }

    public function getRebuyAmountsForPeriod(string $from, string $to, Originator $originator): array
    {
        return (new Transaction)->getRebuyAmountsForPeriod($from, $to, $originator);
    }

    public function getRepaidAmountsForYesterday(Originator $originator): array
    {
        $yesterday = Carbon::yesterday();
        return $this->getRepaidAmountsForDate($yesterday->format('Y-m-d'), $originator);
    }

    public function getRepaidAmountsForDate(string $date, Originator $originator): array
    {
        return (new Transaction)->getRepaidAmountsForDate($date, $originator);
    }

    public function getRepaidAmountsForPeriod(string $from, string $to, Originator $originator): array
    {
        return (new Transaction)->getRepaidAmountsForPeriod($from, $to, $originator);
    }

    public function getInvestedAmountForDate(string $date, Originator $originator): float
    {
        return (new Transaction)->getInvestedAmountForDate($date, $originator);
    }

    public function getInvestedAmountForPeriod(string $from, string $to, Originator $originator): float
    {
        return (new Transaction)->getInvestedAmountForPeriod($from, $to, $originator);
    }

    public function getOutstandingBalanceFromTransaction(Originator $originator, string $to, string $from = null): float
    {
        return (new Transaction)->getOutstandingBalance($originator, $to, $from);
    }

    public function getInvestmentCount(string $date, Originator $originator): int
    {
        return Investment::where([
            ['created_at', '>=', $date . ' 00:00:00'],
            ['created_at', '<=', $date . ' 23:59:59'],
        ])->whereHas('loan', function ($builder) use ($originator) {
            $builder->where('originator_id', $originator->originator_id);
        })->count();
    }

    public function getInvestmentCountForPeriod(string $from, string $to, Originator $originator): int
    {
        return Investment::where([
            ['created_at', '>=', $from . ' 00:00:00'],
            ['created_at', '<=', $to . ' 23:59:59'],
        ])->whereHas('loan', function ($builder) use ($originator) {
            $builder->where('originator_id', $originator->originator_id);
        })->count();
    }

    public function getAverageInvestmentForDate(string $date, Originator $originator): float
    {
        $res = Investment::where([
            ['created_at', '>=', $date . ' 00:00:00'],
            ['created_at', '<=', $date . ' 23:59:59'],
        ])->whereHas('loan', function ($builder) use ($originator) {
            $builder->where('originator_id', $originator->originator_id);
        })->avg('amount');

        if (!empty($res)) {
            return $res;
        }

        return 0.00;
    }

    public function getAverageInvestmentForPeriod(string $from, string $to, Originator $originator): float
    {
        $res = Investment::where([
            ['created_at', '>=', $from . ' 00:00:00'],
            ['created_at', '<=', $to . ' 23:59:59'],
        ])->whereHas('loan', function ($builder) use ($originator) {
            $builder->where('originator_id', $originator->originator_id);
        })->avg('amount');

        if (!empty($res)) {
            return $res;
        }

        return 0.00;
    }

    public function getOutstandingBalance(Originator $originator): float
    {
        return InvestorInstallment::where([
            ['paid', '=', '0']
        ])->whereHas(
            'loanRelation',
            function ($builder) use ($originator) {
                $builder->where('originator_id', $originator->originator_id);
            }
        )->sum('principal');
    }

    public function getOutstandingBalanceForDate(
        Originator $originator,
        Carbon $openBalanceDate
    ): float
    {
        $row = Settlement::where([
            ['date', '=', $openBalanceDate],
            ['originator_id', '=', $originator->originator_id]
        ])->first();

        if (!empty($row->settlement_id)) {
            return $row->close_balance;
        }

        return 0.00;
    }

    public function saveSettlement(string $reportDate, array $data)
    {
        $data['date'] = $reportDate;

        $row = new Settlement();
        $row->fill($data);
        $row->save();

        return $row;
    }

    public function getUninvestedFundsForDate(): float
    {
        return Wallet::where([
            ['active', '=', '1'],
            ['deleted', '=', '0'],
        ])->sum('uninvested');
    }
}
