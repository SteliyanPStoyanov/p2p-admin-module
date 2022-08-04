<?php


namespace Modules\Common\Imports;


use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class NewLoansImport implements ToCollection, WithHeadingRow
{
    public function collection(Collection $collection): Collection
    {
        return $collection->pluck(['credit_id', 'interest_rate']);
    }
}
