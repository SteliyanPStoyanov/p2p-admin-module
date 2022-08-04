<?php
declare(strict_types=1);

namespace Modules\Common\Entities\SecondaryMarket\Cart\Entities;

use Illuminate\Support\Collection;

interface CartCollectionInterface
{
    public function add(CartInterface $cart): void;

    public function get(): Collection;

    public function delete(int $id): void;
}
