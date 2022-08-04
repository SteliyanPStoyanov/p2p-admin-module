<?php

namespace Modules\Common\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class InvestorBonusHandledExport implements FromView, WithColumnWidths, WithStyles
{
    private $customData;

    public function __construct($customData)
    {
        $this->customData = $customData;
    }

    public function view(): View
    {

        return view('common::exports.bonus_tracking', [
            'data' => (object) $this->customData,
        ]);
    }

    public function columnWidths(): array
    {
        return [
            'A' => 13,
            'B' => 22,
            'C' => 13,
            'D' => 22,
            'E' => 12,
            'F' => 12,
            'G' => 12,
            'H' => 30,
            'I' => 30
        ];
    }

    /**
     * @param Worksheet $sheet
     * @return array
     */
    public function styles(Worksheet $sheet): array
    {
        return [
            'A:Q' => [
                'font' => [
                    'size' => 10,
                ],
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_RIGHT,
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
