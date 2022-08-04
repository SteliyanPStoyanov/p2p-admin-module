<?php
declare(strict_types=1);

namespace Tests\Unit\Modules\Common\Entities\SecondaryMarket\Cart\Entities;

use InvalidArgumentException;
use Modules\Common\Entities\SecondaryMarket\Cart\Entities\CartLoan;
use PHPUnit\Framework\TestCase;

class CartLoanTest extends TestCase
{
    public function testNewCartLoan(): void
    {
        $cartId = 1;
        $loan = $this->createMock('Modules\Common\Entities\Loan');
        $investment = $this->createMock('Modules\Common\Entities\Investment');
        $originator = $this->createMock('Modules\Common\Entities\Originator');
        $principal = 10.00;
        $premium = 11.00;
        $filters = json_encode(['filters' => '']);
        $status = true;
        $reason = '';

        $cartLoan = CartLoan::new(
            $cartId,
            $loan,
            $investment,
            $originator,
            $principal,
            $premium,
            $filters,
            $status,
            $reason
        );

        $this->assertInstanceOf(
            'Modules\Common\Entities\SecondaryMarket\Cart\Entities\CartLoan',
            $cartLoan
        );
    }

    public function testCreateCartLoan(): void
    {
        $cartLoanId = 1;
        $cartId = 1;
        $loan = $this->createMock('Modules\Common\Entities\Loan');
        $investment = $this->createMock('Modules\Common\Entities\Investment');
        $originator = $this->createMock('Modules\Common\Entities\Originator');
        $principal = 10.00;
        $premium = 11.0;
        $price = 11.1;
        $filters = json_encode(['filters' => '']);
        $status = true;
        $reason = '';

        $cartLoan = CartLoan::create(
            $cartLoanId,
            $cartId,
            $loan,
            $investment,
            $originator,
            $principal,
            $premium,
            $price,
            $filters,
            $status,
            $reason
        );

        $this->assertInstanceOf(
            'Modules\Common\Entities\SecondaryMarket\Cart\Entities\CartLoan',
            $cartLoan
        );
    }

    public function testFailedStatusWithNoReason(): void
    {
        $this->expectException(InvalidArgumentException::class);

        $cartLoanId = 1;
        $cartId = 1;
        $loan = $this->createMock('Modules\Common\Entities\Loan');
        $investment = $this->createMock('Modules\Common\Entities\Investment');
        $originator = $this->createMock('Modules\Common\Entities\Originator');
        $principal = 10.00;
        $premium = 11.0;
        $price = 12.00;
        $filters = json_encode(['filters' => '']);
        $status = false;
        $reason = '';

        CartLoan::create(
            $cartLoanId,
            $cartId,
            $loan,
            $investment,
            $originator,
            $principal,
            $premium,
            $price,
            $filters,
            $status,
            $reason
        );
    }

    public function testNewCartLoanPriceCalculation(): void
    {
        $cartId = 1;
        $loan = $this->createMock('Modules\Common\Entities\Loan');
        $investment = $this->createMock('Modules\Common\Entities\Investment');
        $originator = $this->createMock('Modules\Common\Entities\Originator');
        $principal = 10.00;
        $premium = 11.0;
        $filters = json_encode(['filters' => '']);
        $status = true;
        $reason = '';

        $cartLoan = CartLoan::new(
            $cartId,
            $loan,
            $investment,
            $originator,
            $principal,
            $premium,
            $filters,
            $status,
            $reason
        );

        $expectedPrice = $principal + ($principal * $premium / 100);

        $this->assertEquals(
            $expectedPrice,
            $cartLoan->getPrice()
        );
    }

    public function testWrongPricePositivePremium(): void
    {
        $this->expectException(InvalidArgumentException::class);

        $cartLoanId = 1;
        $cartId = 1;
        $loan = $this->createMock('Modules\Common\Entities\Loan');
        $investment = $this->createMock('Modules\Common\Entities\Investment');
        $originator = $this->createMock('Modules\Common\Entities\Originator');
        $principal = 10.00;
        $premium = 11.0;
        $price = 12.00;
        $filters = json_encode(['filters' => '']);
        $status = true;
        $reason = '';

        CartLoan::create(
            $cartLoanId,
            $cartId,
            $loan,
            $investment,
            $originator,
            $principal,
            $premium,
            $price,
            $filters,
            $status,
            $reason
        );
    }

    public function testPriceNegativePremium(): void
    {
        $cartLoanId = 1;
        $cartId = 1;
        $loan = $this->createMock('Modules\Common\Entities\Loan');
        $investment = $this->createMock('Modules\Common\Entities\Investment');
        $originator = $this->createMock('Modules\Common\Entities\Originator');
        $principal = 10.00;
        $premium = -11.0;
        $price = 8.9;
        $filters = json_encode(['filters' => '']);
        $status = true;
        $reason = '';

        $cartLoan = CartLoan::create(
            $cartLoanId,
            $cartId,
            $loan,
            $investment,
            $originator,
            $principal,
            $premium,
            $price,
            $filters,
            $status,
            $reason
        );

        $expectedPrice = 8.9;

        $this->assertEquals(
            $expectedPrice,
            $cartLoan->getPrice()
        );
    }

    public function testWrongPositivePremium(): void
    {
        $this->expectException(InvalidArgumentException::class);

        $cartLoanId = 1;
        $cartId = 1;
        $loan = $this->createMock('Modules\Common\Entities\Loan');
        $investment = $this->createMock('Modules\Common\Entities\Investment');
        $originator = $this->createMock('Modules\Common\Entities\Originator');
        $principal = 10.00;
        $premium = 16.0;
        $price = 11.6;
        $filters = json_encode(['filters' => '']);
        $status = true;
        $reason = '';

        CartLoan::create(
            $cartLoanId,
            $cartId,
            $loan,
            $investment,
            $originator,
            $principal,
            $premium,
            $price,
            $filters,
            $status,
            $reason
        );
    }

    public function testWrongNegativePremium(): void
    {
        $this->expectException(InvalidArgumentException::class);

        $cartLoanId = 1;
        $cartId = 1;
        $loan = $this->createMock('Modules\Common\Entities\Loan');
        $investment = $this->createMock('Modules\Common\Entities\Investment');
        $originator = $this->createMock('Modules\Common\Entities\Originator');
        $principal = 10.00;
        $premium = -16.0;
        $price = 8.4;
        $filters = json_encode(['filters' => '']);
        $status = true;
        $reason = '';

        CartLoan::create(
            $cartLoanId,
            $cartId,
            $loan,
            $investment,
            $originator,
            $principal,
            $premium,
            $price,
            $filters,
            $status,
            $reason
        );
    }
}
