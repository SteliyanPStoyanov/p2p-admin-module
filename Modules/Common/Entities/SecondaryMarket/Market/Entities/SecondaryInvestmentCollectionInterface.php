<?php
declare(strict_types=1);

namespace Modules\Common\Entities\SecondaryMarket\Market\Entities;

use Illuminate\Support\Collection;

interface SecondaryInvestmentCollectionInterface
{
    public function add(SecondaryInvestmentInterface $investment): void;
    public function get(): Collection;
    public function delete(int $id): void;
    public function asArray(): array;
}
