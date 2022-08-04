<?php

namespace Modules\Common\Services;

use Carbon\Carbon;
use Illuminate\Support\Collection;
use Modules\Common\Entities\Currency;
use Modules\Common\Entities\ImportedPayment;
use Modules\Common\Entities\Transaction;
use Modules\Core\Services\BaseService;

class DocumentParser extends BaseService
{
    public function __construct()
    {

        parent::__construct();
    }

    public function parseImportedPayments(string $filePath)
    {
        $sheets = \Excel::toCollection(null, $filePath);

        $importedPayments = [];
        foreach ($sheets as $sheet) {
            foreach ($sheet as $row) {
                if (!$this->isImportedPayment($row)) {
                    continue;
                }

                // Is there transaction with this bank transaction ID
                if (Transaction::existsById($row[1]) || ImportedPayment::existsById($row[1])) {
                    continue;
                }

                $importedPayment = new ImportedPayment();
                $importedPayment->fill(
                    [
                        'bank_transaction_id' => $row[1],
                        'bank_transaction_date' => Carbon::parse($row[6]),
                        'basis' => $row[2],
                        'currency_id' => Currency::ID_EUR,
                        'amount' => preg_replace('/\s+/', '', $row[4]),
                        'type' => ImportedPayment::TYPE_IN,
                        'status' => ImportedPayment::STATUS_NEW,
                        'created_at' => Carbon::parse($row[0]),

                        'transaction_id' => null,
                        'investor_id' => null,
                        'wallet_id' => null,
                        'iban' => null,
                        'bic' => null,
                    ]
                );
                $importedPayment->save();
                $importedPayments[] = $importedPayment;
            }
        }

        return $importedPayments;
    }

    protected function isImportedPayment(Collection $row)
    {
        if (
            preg_match(ImportedPayment::DATE_REGEX, $row[0])
            && preg_match(ImportedPayment::REASON_REGEX, $row[2])
            && preg_match(ImportedPayment::CURRENCY_REGEX, $row[3])
            && preg_match(ImportedPayment::AMOUNT_REGEX, $row[4])
            && preg_match(ImportedPayment::TYPE_REGEX, $row[5])
            && preg_match(ImportedPayment::DATE_REGEX, $row[6])
            && preg_match(ImportedPayment::AMOUNT_REGEX, $row[7])
        ) {
            return true;
        }

        return false;
    }
}
