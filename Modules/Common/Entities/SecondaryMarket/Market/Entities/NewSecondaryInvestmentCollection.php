<?php
declare(strict_types=1);

namespace Modules\Common\Entities\SecondaryMarket\Market\Entities;

use Countable;
use Illuminate\Support\Collection;
use InvalidArgumentException;

class NewSecondaryInvestmentCollection implements SecondaryInvestmentCollectionInterface, Countable
{
    private Collection $investments;

    public function __construct()
    {
        $this->investments = collect([]);
    }

    public function add(SecondaryInvestmentInterface $investment): void
    {
        $this->investments->push(
            $investment
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
