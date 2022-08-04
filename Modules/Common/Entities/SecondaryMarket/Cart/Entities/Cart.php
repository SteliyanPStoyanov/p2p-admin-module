<?php
declare(strict_types=1);

namespace Modules\Common\Entities\SecondaryMarket\Cart\Entities;

use InvalidArgumentException;
use Modules\Common\Entities\Investor;

class Cart implements CartInterface
{
    /**
     * @var int
     */
    private int $cart_id;

    /**
     * @var Investor
     */
    private Investor $investor;

    /**
     * @var string
     */
    private string $type;

    /**
     * @var CartLoansCollection
     */
    private CartLoansCollection $loans;

    /**
     * @param Investor $investor
     * @param string $type
     * @param CartLoansCollection $loans
     * @return static
     */
    public static function new(Investor $investor, string $type, CartLoansCollection $loans): self
    {
        return new self(
            0, // fake id
            $investor,
            $type, // sell, buy
            $loans
        );
    }

    /**
     * @param int $cart_id
     * @param Investor $investor
     * @param string $type
     * @param CartLoansCollection $loans
     * @return static
     */
    public static function create(int $cart_id, Investor $investor, string $type, CartLoansCollection $loans): self
    {
        return new self(
            $cart_id,
            $investor,
            $type,
            $loans
        );
    }

    public function __construct(int $cart_id, Investor $investor, string $type, CartLoansCollection $loans)
    {
        $this->cart_id = $cart_id;

        $this->investor = $investor;

        $this->type = $type;

        $this->loans = $loans;

        $this->checkType();
    }

    /**
     * @return int
     */
    public function getCartId(): int
    {
        return $this->cart_id;
    }

    /**
     * @return Investor
     */
    public function getInvestor(): Investor
    {
        return $this->investor;
    }

    /**
     * @return string
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * @return CartLoansCollection
     */
    public function getLoans(): CartLoansCollection
    {
        return $this->loans;
    }

    /**
     * @param CartLoansCollection $loans
     */
    public function setLoans(CartLoansCollection $loans): void
    {
        $this->loans = $loans;
    }

    public function getAsArray(): array
    {
        return [
            'cart_id' => $this->getCartId(),
            'investor' => $this->getInvestor(),
            'type' => $this->getType(),
            'loans' => $this->getLoans(),
        ];
    }

    /**
     * @throws InvalidArgumentException
     */
    private function checkType(): void
    {
        if ($this->type != 'sell' && $this->type != 'buy') {
            throw new InvalidArgumentException("Invalid type provided");
        }
    }



//    private function checkFilters(): void
//    {
////        if (empty($this->filters) && $this->type == 'buyer') {
////            throw new InvalidArgumentException("filters can not be empty ");
////        }
//    }
}
