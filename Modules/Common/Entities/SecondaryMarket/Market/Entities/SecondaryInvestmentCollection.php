<?php
declare(strict_types=1);

namespace Modules\Common\Entities\SecondaryMarket\Market\Entities;

use Countable;
use Illuminate\Support\Collection;
use InvalidArgumentException;

class SecondaryInvestmentCollection implements SecondaryInvestmentCollectionInterface, Countable
{
    private Collection $investments;

    public function __construct()
    {
        $this->investments = collect([]);
    }

    public function add(SecondaryInvestmentInterface $investment): void
    {
        $this->checkSecondaryInvestmentId($investment);

        $this->investments->put(
            $investment->getMarketSecondaryId(), $investment
        );
    }

    public function get(): Collection
    {
        return $this->investments;
    }

    public function delete(int $id): void
    {
        $this->investments->forget($id);
    }

    private function checkSecondaryInvestmentId(SecondaryInvestmentInterface $investment): void
    {
        if (!is_int($investment->getMarketSecondaryId()) || $investment->getMarketSecondaryId() <= 0)
        {
            throw new InvalidArgumentException("Investment has to be saved into secondary market first");
        }
    }

    public function count()
    {
        return count($this->investments);
    }

    public function asArray(): array
    {
        $investments = [];
        foreach ($this->investments as $investment) {
            $investments[] = $investment->asArray();
        }

        return $investments;
    }
}
