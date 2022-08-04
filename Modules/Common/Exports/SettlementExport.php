<?php

namespace Modules\Common\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class SettlementExport implements FromView, WithColumnWidths, WithColumnFormatting, WithStyles
{
    private $customData;
    private $reportType;

    public function __construct($customData, $type = 'Daily')
    {
        $this->customData = $customData;
        $this->reportType = $type;
    }

    public function view(): View
    {
        return view('common::exports.settlement', [
            'data' => (object) $this->customData,
            'reportType' => $this->reportType,
        ]);
    }

    public function columnFormats(): array
    {
        return [
            'B' => NumberFormat::FORMAT_NUMBER_00,
        ];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 42,
            'B' => 15,
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            'A' => [
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_LEFT,
                ],
            ],
            'B' => [
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_RIGHT,
                ],
            ],
            'A1' => [
                'font' => [
                    'bold' => true,
                ],
            ],
            'A7' => [
                'font' => [
                    'bold' => true,
                ],
            ],
            'A9' => [
                'font' => [
                    'bold' => true,
                ],
            ],
            'A15' => [
                'font' => [
                    'bold' => true,
                ],
            ],
            'A22' => [
                'font' => [
                    'bold' => true,
                ],
            ],
            'B3:B9'  => [
                'font' => [
                    'bold' => true,
                ],
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_RIGHT,
                ],
            ],
            'B15'  => [
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
