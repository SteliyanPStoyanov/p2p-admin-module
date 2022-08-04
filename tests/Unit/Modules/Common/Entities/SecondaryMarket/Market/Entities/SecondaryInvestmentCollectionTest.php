<?php
declare(strict_types=1);

namespace Tests\Unit\Modules\Common\Entities\SecondaryMarket\Market\Entities;

use Modules\Common\Entities\Investment;
use Modules\Common\Entities\Investor;
use Modules\Common\Entities\Loan;
use Modules\Common\Entities\Originator;
use Modules\Common\Entities\SecondaryMarket\Market\Entities\SecondaryInvestment;
use Modules\Common\Entities\SecondaryMarket\Market\Entities\SecondaryInvestmentCollection;
use PHPUnit\Framework\TestCase;

class SecondaryInvestmentCollectionTest extends TestCase
{
    public function testAdd(): void
    {
        $secondaryInvestmentCollection = new SecondaryInvestmentCollection();

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

        $secondaryInvestmentCollection->add($secondaryInvestment);

        $this->assertInstanceOf(
            'illuminate\Support\Collection',
            $secondaryInvestmentCollection->get()
        );

        $this->assertInstanceOf(
            'Modules\Common\Entities\SecondaryMarket\Market\Entities\SecondaryInvestment',
            $secondaryInvestmentCollection->get()[$market_secondary_id]
        );
    }

    public function testDelete(): void
    {
        $secondaryInvestmentCollection = new SecondaryInvestmentCollection();

        for ($i = 1; $i <= 3; ++$i) {
            $market_secondary_id = $i;
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

            $secondaryInvestmentCollection->add($secondaryInvestment);
        }

        $this->assertEquals(
            3,
            $secondaryInvestmentCollection->count()
        );

        $secondaryInvestmentCollection->delete(3);

        $this->assertEquals(
            2,
            $secondaryInvestmentCollection->count()
        );
    }
}
