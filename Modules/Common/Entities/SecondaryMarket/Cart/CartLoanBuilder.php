<?php
declare(strict_types=1);

namespace Modules\Common\Entities\SecondaryMarket\Cart;

use Illuminate\Support\Collection;
use Modules\Common\Entities\CartSecondaryLoans;
use Modules\Common\Entities\Investment;
use Modules\Common\Entities\Loan;
use Modules\Common\Entities\Originator;
use Modules\Common\Entities\SecondaryMarket\Cart\Entities\CartLoan;
use Modules\Common\Entities\SecondaryMarket\Cart\Entities\CartLoanInterface;
use Modules\Common\Entities\SecondaryMarket\Cart\Entities\CartLoansCollection;

class CartLoanBuilder
{
    /**
     * @param array $loan
     * @return CartLoanInterface
     */
    public function buildNewSingle(array $loan): CartLoanInterface
    {
        $investment = Investment::find($loan['investmentId']);

        if (!isset($loan['percent_on_sell']) || !$loan['percent_on_sell']) {
            $loan['percent_on_sell'] = CartLoanHelper::calculatePercentOnSell((float)$loan['principalForSale'], (float)$investment->amount);
        }

        if (! isset($loan['percent_sold']) || ! $loan['percent_sold']) {
            $loan['percent_sold'] = 0;
        }

        return CartLoan::new(
            $loan['cartId'],
            Loan::with('country')->find($loan['loanId']),
            $investment,
            Originator::find($loan['originatorId']),
            (float)$loan['principalForSale'],
            (float)$loan['premium'],
            (float)$loan['percent_on_sell'],
            (float)$loan['percent_sold'],
            (string)$loan['filters'],
            (bool)$loan['status'],
            (string)$loan['reason']
        );
    }

    /**
     * @return CartLoansCollection
     */
    public function buildNewCollection(): CartLoansCollection
    {
        // ...
    }

    /**
     * @param array $loan
     * @return CartLoanInterface
     */
    public function createSingleFromArray(array $loan): CartLoanInterface
    {
        $investment = Investment::find($loan['investmentId']);

        if (!isset($loan['percent_on_sell']) || ! $loan['percent_on_sell']) {
            $loan['percent_on_sell'] = CartLoanHelper::calculatePercentOnSell((float)$loan['principal_for_sale'], (float)$investment->amount);
        }

        if (!isset($loan['percent_sold']) || ! $loan['percent_sold']) {
            $loan['percent_sold'] = 0;
        }

        return CartLoan::create(
            (int)$loan['cart_loan_id'],
            (int)$loan['cart_secondary_id'],
            Loan::with('country')->find($loan['loanId']),
            $investment,
            Originator::find($loan['originatorId']),
            (float)$loan['principal_for_sale'],
            (float)$loan['premium'],
            (float)$loan['price'],
            (float)$loan['percent_on_sell'],
            (float)$loan['percent_sold'],
            (string)$loan['filters'],
            (bool)$loan['status'],
            (string)$loan['reason']
        );
    }

    /**
     * @param CartSecondaryLoans $loan
     * @return CartLoanInterface
     */
    public function createSingle(CartSecondaryLoans $loan): CartLoanInterface
    {
        if (! $loan->percent_on_sell) {
            $loan->percent_on_sell = CartLoanHelper::calculatePercentOnSell($loan->principal_for_sale, $loan->investment->amount);
        }

        if (! $loan->percent_sold) {
            $loan->percent_sold = 0;
        }

        return CartLoan::create(
            $loan->cart_loan_id,
            $loan->cart_secondary_id,
            $loan->loan,
            $loan->investment,
            $loan->originator,
            (float)$loan->principal_for_sale,
            (float)$loan->premium,
            (float)$loan->price,
            (float)$loan->percent_on_sell,
            (float)$loan->percent_sold,
            (string)$loan['filters'],
            (bool)$loan->status,
            (string)$loan->reason
        );
    }

    /**
     * @param Collection $loans
     * @return CartLoansCollection
     */
    public function buildCollection(Collection $loans): CartLoansCollection
    {
        $cartLoansCollection = new CartLoansCollection();

        foreach ($loans as $loan) {
            $cartLoansCollection->add(
                $this->createSingle($loan)
            );
        }

        return $cartLoansCollection;
    }
}
