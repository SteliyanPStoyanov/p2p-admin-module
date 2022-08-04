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

class AccountStatementExport implements WithColumnFormatting, FromView, WithColumnWidths, WithStyles
{
    private $accountStatements;

    public function __construct($accountStatements)
    {
        $this->accountStatements = $accountStatements;
    }

    public function view(): View
    {
        return view(
            'common::exports.account-statements',
            [
                'accountStatements' => (object)$this->accountStatements
            ]
        );
    }


    public function columnWidths(): array
    {
        return [
            'A' => 23,
            'B' => 14,
            'C' => 55,
            'D' => 16
        ];
    }

    public function columnFormats(): array
    {
        return [
            'A' => 'yyyy/mm/dd hh:mm:ss',
            'D' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            'A:T' => [
                'font' => [
                    'size' => 11,
                ],
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                    'vertical' => Alignment::VERTICAL_CENTER,
                ],
            ],
            'A1:T1' => [
                'font' => [
                    'bold' => false,
                    'size' => 12,
                ],
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                    'vertical' => Alignment::VERTICAL_CENTER,
                ],
            ],
            'C' => [

                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_LEFT,
                    'vertical' => Alignment::VERTICAL_CENTER,
                ],
            ],
        ];
    }
}
