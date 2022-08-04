<?php

namespace Modules\Common\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class LoanOutstandingAmountExport implements FromView, WithColumnWidths, WithStyles
{
    private $customData;

    public function __construct($customData)
    {
        $this->customData = $customData;
    }

    public function view(): View
    {

        return view('common::exports.loans_outstanding', [
            'data' => (object) $this->customData,
        ]);
    }

    public function columnWidths(): array
    {
        return [
            'A' => 20,
            'B' => 20,
            'C' => 20,
            'D' => 20,
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            'A:Q' => [
                'font' => [
                    'size' => 10,
                ],
            ],
            'A1:Q1' => [
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
