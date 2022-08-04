<?php

declare(strict_types=1);

namespace Modules\Common\Entities\SecondaryMarket\Cart;


use Illuminate\Support\Collection;
use Modules\Common\Entities\CartSecondaryLoans;
use Modules\Common\Entities\Investment;
use Modules\Common\Entities\Loan;
use Modules\Common\Entities\Originator;
use Modules\Common\Entities\SecondaryMarket\Cart\Entities\CartLoanBuyer;
use Modules\Common\Entities\SecondaryMarket\Cart\Entities\CartLoanBuyerInterface;
use Modules\Common\Entities\MarketSecondary;
use Modules\Common\Entities\SecondaryMarket\Cart\Entities\CartLoanInterface;
use Modules\Common\Entities\SecondaryMarket\Cart\Entities\CartLoansCollection;

class CartLoanBuyerBuilder
{
    public function buildNewSingle(array $loan): CartLoanBuyerInterface
    {
        $investment = Investment::find($loan['investmentId']);

        if (!isset($loan['percent_bought']) || !$loan['percent_bought']) {
            if(!isset($investment) || !$investment) {
                throw new \Exception('Can not find investment with ID '.$loan['investmentId']);
            }

            if(!isset($investment->amount) || ! $investment->amount) {
                throw new \Exception('Investment id: '.$loan['investmentId'].' has no amount');
            }

            $loan['percent_bought'] = CartLoanHelper::calculatePercentBought(
                (float)$loan['principalForSale'],
                (float)$investment->amount
            );
        }

        return CartLoanBuyer::buildNew(
            $loan['cartId'],
            Loan::with('country')->find($loan['loanId']),
            $investment,
            Originator::find($loan['originatorId']),
            MarketSecondary::find($loan['marketSecondaryId']),
            (float)$loan['principalForSale'],
            (float)$loan['premium'],
            (float)$loan['percent_bought'],
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
        if(!isset($loan->investment) || !$loan->investment) {
            throw new \Exception('Can not find investment with ID '.$loan->investment_id);
        }

        if(!isset($loan->investment->amount) || ! $loan->investment->amount) {
            throw new \Exception('Investment id: '.$loan->investment_id.' has no amount');
        }

        $percentBought = CartLoanHelper::calculatePercentBought(
            (float)$loan->principalForSale,
            (float)$loan->investment->amount
        );

        return CartLoanBuyer::build(
            $loan->cart_loan_id,
            $loan->cart_secondary_id,
            $loan->loan,
            $loan->investment,
            $loan->originator,
            $loan->marketSecondary,
            (float)$loan->principal_for_sale,
            (float)$loan->premium,
            (float)$loan->price,
            (float)$percentBought,
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
        try {
            $cartLoansCollection = new CartLoansCollection();
            foreach ($loans as $loan) {
                $cartLoansCollection->add(
                    $this->createSingle($loan)
                );
            }

            return $cartLoansCollection;
        } catch (\Exception $e) {
            echo $e->getMessage() . " " . $e->getFile() . " " . $e->getLine();
        }
    }

    /**
     * @param array $loan
     * @return CartLoanInterface
     */
    public function createSingleFromArray(array $loan): CartLoanInterface
    {
        $investment = Investment::find($loan['investmentId']);

        if(!isset($investment) || !$investment) {
            throw new \Exception('Can not find investment with ID '.$loan['investmentId']);
        }

        if(!isset($investment->amount) || ! $investment->amount) {
            throw new \Exception('Investment id: '.$loan['investmentId'].' has no amount');
        }

        if (!isset($loan['percent_bought']) || !$loan['percent_bought']) {
            $loan['percent_bought'] = CartLoanHelper::calculatePercentBought(
                (float)$loan['principalForSale'],
                (float)$investment->amount
            );
        }

        $price = CartLoanHelper::calculatePrice((float)$loan['premium'], (float)$loan['principalForSale']);

        return CartLoanBuyer::build(
            (int)$loan['cart_loan_id'],
            (int)$loan['cart_secondary_id'],
            Loan::with('country')->find($loan['loanId']),
            $investment,
            Originator::find($loan['originatorId']),
            MarketSecondary::find($loan['marketSecondaryId']),
            (float)$loan['principalForSale'],
            (float)$loan['premium'],
            (float)$price,
            (float)$loan['percent_bought'],
            (string)$loan['filters'],
            (bool)$loan['status'],
            (string)$loan['reason']
        );
    }
}
