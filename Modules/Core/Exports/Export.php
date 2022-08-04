<?php

namespace Modules\Core\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

abstract class Export implements FromCollection, ShouldAutoSize, WithMapping, WithHeadings
{
}
