<?php
declare(strict_types=1);

namespace Tests\Unit\Modules\Common\Entities\SecondaryMarket\Market\Entities;

use Modules\Common\Entities\Investment;
use Modules\Common\Entities\Investor;
use Modules\Common\Entities\Loan;
use Modules\Common\Entities\Originator;
use Modules\Common\Entities\SecondaryMarket\Market\Entities\NewSecondaryInvestmentCollection;
use Modules\Common\Entities\SecondaryMarket\Market\Entities\SecondaryInvestment;
use Modules\Common\Entities\SecondaryMarket\Market\Entities\SecondaryInvestmentCollection;
use PHPUnit\Framework\TestCase;

class NewSecondaryInvestmentCollectionTest extends TestCase
{
    public function testAdd(): void
    {
        $secondaryInvestmentCollection = new NewSecondaryInvestmentCollection();

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

        $secondaryInvestmentCollection->add($secondaryInvestment);

        $this->assertInstanceOf(
            'illuminate\Support\Collection',
            $secondaryInvestmentCollection->get()
        );

        $this->assertInstanceOf(
            'Modules\Common\Entities\SecondaryMarket\Market\Entities\SecondaryInvestment',
            $secondaryInvestmentCollection->get()->first()
        );
    }

    public function testDelete(): void
    {
        $secondaryInvestmentCollection = new NewSecondaryInvestmentCollection();

        for ($i = 1; $i <= 3; ++$i) {
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

            $secondaryInvestmentCollection->add($secondaryInvestment);
        }

        $this->assertEquals(
            3,
            $secondaryInvestmentCollection->count()
        );

        $secondaryInvestmentCollection->delete(
            $secondaryInvestmentCollection->count() - 1
        );

        $this->assertEquals(
            2,
            $secondaryInvestmentCollection->count()
        );
    }
}
