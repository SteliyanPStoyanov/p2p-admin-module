<?php
declare(strict_types=1);

namespace Tests\Unit\Modules\Common\Entities\SecondaryMarket\Market\Entities;

use InvalidArgumentException;
use Modules\Common\Entities\Investment;
use Modules\Common\Entities\Investor;
use Modules\Common\Entities\Loan;
use Modules\Common\Entities\Originator;
use Modules\Common\Entities\SecondaryMarket\Market\Entities\SecondaryInvestment;
use PHPUnit\Framework\TestCase;

class SecondaryInvestmentTest extends TestCase
{
    public function testNew(): void
    {
        $investor = $this->createMock(Investor::class);
        $loan = $this->createMock(Loan::class);
        $investment = $this->createMock(Investment::class);
        $originator = $this->createMock(Originator::class);
        $principal_for_sale = 10.00;
        $premium = 1.0;
        $price = 10.10;
        $active = true;

        $secondaryInvestment = SecondaryInvestment::new(
            $investor,
            $loan,
            $investment,
            $originator,
            $principal_for_sale,
            $premium,
            $price,
            $active
        );

        $this->assertInstanceOf('Modules\Common\Entities\SecondaryMarket\Market\Entities\SecondaryInvestment', $secondaryInvestment);
    }

    public function testCreate(): void
    {
        $market_secondary_id = 1;
        $investor = $this->createMock(Investor::class);
        $loan = $this->createMock(Loan::class);
        $investment = $this->createMock(Investment::class);
        $originator = $this->createMock(Originator::class);
        $principal_for_sale = 10.00;
        $premium = 1.0;
        $price = 10.10;
        $active = true;

        $secondaryInvestment = SecondaryInvestment::create(
            $market_secondary_id,
            $investor,
            $loan,
            $investment,
            $originator,
            $principal_for_sale,
            $premium,
            $price,
            $active
        );

        $this->assertInstanceOf('Modules\Common\Entities\SecondaryMarket\Market\Entities\SecondaryInvestment', $secondaryInvestment);
    }

    public function testNewWrongPrice(): void
    {
        $this->expectException(InvalidArgumentException::class);

        $investor = $this->createMock(Investor::class);
        $loan = $this->createMock(Loan::class);
        $investment = $this->createMock(Investment::class);
        $originator = $this->createMock(Originator::class);
        $principal_for_sale = 10.00;
        $premium = 1.0;
        $price = 10.0;
        $active = true;

        $secondaryInvestment = SecondaryInvestment::new(
            $investor,
            $loan,
            $investment,
            $originator,
            $principal_for_sale,
            $premium,
            $price,
            $active
        );
    }

    public function testCreateWrongPrice(): void
    {
        $this->expectException(InvalidArgumentException::class);

        $market_secondary_id = 1;
        $investor = $this->createMock(Investor::class);
        $loan = $this->createMock(Loan::class);
        $investment = $this->createMock(Investment::class);
        $originator = $this->createMock(Originator::class);
        $principal_for_sale = 10.00;
        $premium = 1.0;
        $price = 10.0;
        $active = true;

        $secondaryInvestment = SecondaryInvestment::create(
            $market_secondary_id,
            $investor,
            $loan,
            $investment,
            $originator,
            $principal_for_sale,
            $premium,
            $price,
            $active
        );
    }
}
