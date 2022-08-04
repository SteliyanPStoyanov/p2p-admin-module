<?php

namespace Modules\Common\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Events\AfterSheet;

class WalletExport implements FromCollection, WithHeadings, ShouldAutoSize, WithEvents
{
    /**
     * @var Collection
     */
    private Collection $wallet;

    /**
     * LoanExport constructor.
     *
     * @param Collection $wallet
     */
    public function __construct(Collection $wallet)
    {
        $this->wallet = $wallet;
    }

    /**
     * @return Collection
     */
    public function collection()
    {
        return $this->wallet;
    }

    public function headings(): array
    {
        return ["Transaction ID", "Date Time", "Amount", "Type", "Details", "From", "To"];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $cellRange = 'A1:W1'; // All headers
                $event->sheet->getDelegate()->getStyle($cellRange)->getFont()->setSize(14);
            },
        ];
    }

}
