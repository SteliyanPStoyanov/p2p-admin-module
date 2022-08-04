<?php

namespace Modules\Common\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class StrategiesBalanceExport implements FromView, WithStyles, WithColumnWidths
{

    private $customData;

    public function __construct($customData)
    {
        $this->customData = $customData;
    }

    /**
     * @inheritDoc
     */
    public function view(): View
    {
        return view('common::exports.strategies_balance', [
            'data' => (object) $this->customData,
        ]);
    }

    public function columnWidths(): array
    {
        return [
            'A' => 13,
            'B' => 13,
            'C' => 25,
            'D' => 15,
            'E' => 16,
            'F' => 13,
            'G' => 13,
            'H' => 15,
            'I' => 30,
            'J' => 27,
            'K' => 30,
            'L' => 25,
            'M' => 15,
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            'A:M' => [
                'font' => [
                    'size' => 10,
                ],
            ],
            'A1:M1' => [
                'font' => [
                    'bold' => true,
                ],
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_RIGHT,
                ],
            ],
        ];
    }
}
