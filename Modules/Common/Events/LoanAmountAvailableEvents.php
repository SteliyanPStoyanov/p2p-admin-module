<?php

namespace Modules\Common\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Modules\Common\Entities\Installment;
use Modules\Common\Entities\Investment;
use Modules\Common\Entities\Loan;

class LoanAmountAvailableEvents
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public Loan $loan;
    public ?Investment $investment;
    public ?Installment $installment;

    /**
     * LoanAmountAvailableEvents constructor.
     * @param Loan $loan
     * @param Investment|null $investment
     * @param Installment|null $installment
     */
    public function __construct(
        Loan $loan,
        Investment $investment = null,
        Installment $installment = null
    ) {
        $this->loan = $loan;
        $this->investment = $investment;
        $this->installment = $installment;
    }
}
