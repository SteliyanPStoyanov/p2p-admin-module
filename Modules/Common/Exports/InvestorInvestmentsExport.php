<?php

namespace Modules\Common\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class InvestorInvestmentsExport implements FromView, WithStyles, WithColumnWidths
{

    private $investments;

    public function __construct($investments)
    {
        $this->investments = $investments;
    }

    /**
     * @inheritDoc
     */
    public function view(): View
    {
        return view('common::exports.investments', [
            'investments' => (object) $this->investments,
        ]);
    }

    public function columnWidths(): array
    {
        return [
            'A' => 23,
            'B' => 15,
            'C' => 15,
            'D' => 15,
            'E' => 23,
            'F' => 23,
            'G' => 23,
            'H' => 23,
            'I' => 23,
            'J' => 23,
            'K' => 23,
            'L' => 23,
            'M' => 23,
            'N' => 23,
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            'A:M' => [
                'font' => [
                    'size' => 10,
                ],
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_LEFT,
                ],
            ],
            'A1:M1' => [
                'font' => [
                    'bold' => true,
                ],
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                ],
            ],
        ];
    }
}
