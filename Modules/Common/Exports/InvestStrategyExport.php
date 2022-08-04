<?php

namespace Modules\Common\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;

class InvestStrategyExport implements FromCollection
{
    /**
     * @var Collection
     */
    private Collection $investStrategy;

    /**
     * LoanExport constructor.
     *
     * @param Collection $investStrategy
     */
    public function __construct(Collection $investStrategy)
    {
        $this->investStrategy = $investStrategy;
    }

    /**
     * @return Collection
     */
    public function collection()
    {
        return $this->investStrategy;
    }
}
