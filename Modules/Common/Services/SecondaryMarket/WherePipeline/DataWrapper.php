<?php
declare(strict_types=1);

namespace Modules\Common\Services\SecondaryMarket\WherePipeline;

use Illuminate\Database\Eloquent\Builder;
use Modules\Common\Entities\MarketSecondary;

/**
 * Class used for compiling all where conditions in final,
 * where array, which is set to builder
 */
class DataWrapper
{
    private Builder $marketSecondary;
    private array $data;
    private array $where;
    private int $investorId;
    private bool $isAdmin;

    public function __construct(Builder $marketSecondary, array $data, int $investorId, bool $isAdmin = false)
    {
        $this->marketSecondary = $marketSecondary;

        $this->data = $data;

        $this->investorId = $investorId;

        $this->where = [];

        $this->isAdmin = $isAdmin;
    }

    /**
     * @return Builder
     */
    public function getMarketSecondary(): Builder
    {
        return $this->marketSecondary;
    }

    /**
     * @param Builder $builder
     */
    public function setMarketSecondary(Builder $marketSecondary): void
    {
        $this->marketSecondary = $marketSecondary;
    }

    public function compile(): Builder
    {
        foreach ($this->getWhere() as $k => $where) {
            if (isset($where['whereIn']) && $where['whereIn'] == 1) {
                unset($where['whereIn']);
                $this->builder->whereIn($k, $where);
                continue;
            }

            if (isset($where['whereRaw']) && $where['whereRaw'] == 1 && $this->investorId > 0) {
                unset($where['whereRaw']);
                $this->builder->whereRaw($where[0], [$this->investorId]);
                continue;
            }

            $this->builder->where([$where]);
        }

        return $this->builder;
    }

    /**
     * @return array
     */
    public function getData(): array
    {
        return $this->data;
    }

    /**
     * @param array $data
     */
    public function setData(array $data): void
    {
        $this->data = $data;
    }

    /**
     * @return array
     */
    public function getWhere(): array
    {
        return $this->where;
    }

    /**
     * @param array $where
     */
    public function setWhere(array $where): void
    {
        $this->where = $where;
    }

    public function isAdmin(): bool
    {
        return $this->isAdmin;
    }
}
