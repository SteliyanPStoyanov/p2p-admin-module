<?php
declare(strict_types=1);

namespace Tests\Unit\Modules\Common\Entities\SecondaryMarket\Market;

use Modules\Common\Entities\Investment;
use Modules\Common\Entities\Investor;
use Modules\Common\Entities\Loan;
use Modules\Common\Entities\Originator;
use Modules\Common\Entities\SecondaryMarket\Market\SecondaryInvestmentBuilder;
use PHPUnit\Framework\TestCase;

class SecondaryInvestmentBuilderTest extends TestCase
{
    public function testBuildNew(): void
    {
        $investor = $this->createMock(Investor::class);
        $loan = $this->createMock(Loan::class);
        $investment = $this->createMock(Investment::class);
        $originator = $this->createMock(Originator::class);
        $principal_for_sale = 10.00;
        $premium = 1.0;
        $price = 10.10;
        $active = true;

        $builder = new SecondaryInvestmentBuilder();
        $secondaryInvestment = $builder->buildNew(
            $investor,
            $loan,
            $investment,
            $originator,
            $principal_for_sale,
            $premium,
            $price,
            $active
        );


        $this->assertInstanceOf(
            'Modules\Common\Entities\SecondaryMarket\Market\Entities\SecondaryInvestment',
            $secondaryInvestment
        );
    }
}
