<?php

namespace Modules\Common\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class WalletsExport implements FromView, WithColumnWidths, WithStyles
{
    private $customData;

    public function __construct($customData)
    {
        $this->customData = $customData;
    }

    public function view(): View
    {
        return view('common::exports.wallets', [
            'data' => (object) $this->customData,
        ]);
    }

    public function columnWidths(): array
    {
        return [
            'A' => 13,
            'B' => 15,
            'C' => 15,
            'D' => 13,
            'E' => 15,
            'F' => 20,
            'G' => 20,
            'H' => 31,
            'I' => 24,
            'J' => 15,
            'K' => 15,
            'L' => 17,
            'M' => 15,
            'N' => 22,
            'O' => 15,
            'P' => 15,
            'Q' => 16,
            'R' => 16,
            'S' => 20,
            'T' => 20,
            'U' => 20,
            'V' => 20,
            'W' => 20,
            'X' => 20,
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            'A:X' => [
                'font' => [
                    'size' => 10,
                ],
            ],
            'A1:X1' => [
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
