<?php
declare(strict_types=1);

namespace Modules\Common\Entities\SecondaryMarket\Cart\Entities;


use Countable;
use Illuminate\Support\Collection;
use InvalidArgumentException;

class CartLoansCollection implements CartLoansCollectionInterface, Countable
{
    private Collection $loans;

    public function __construct()
    {
        $this->loans = collect([]);
    }

    public function add(CartLoanInterface $loan): void
    {
        $this->checkCartLoanId($loan);

        $this->loans->put(
            $loan->getCartLoanId(), $loan
        );
    }

    public function get(): Collection
    {
        return $this->loans;
    }

    public function getLoanById(int $loanId): ?CartLoanInterface
    {
        return $this->loans->get($loanId);
    }

    public function delete(int $id): void
    {
        $this->loans->forget($id);
    }

    private function checkCartLoanId(CartLoanInterface $loan): void
    {
        if (!is_int($loan->getCartLoanId()) || $loan->getCartLoanId() <= 0)
        {
            throw new InvalidArgumentException("Loan has to be saved into cart first");
        }
    }

    public function count(): int
    {
        return count($this->loans);
    }
}
