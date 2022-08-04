<?php

namespace Modules\Common\Observers;

use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\Mail;
use Modules\Common\Entities\Currency;
use Modules\Common\Entities\ImportedPayment;
use Modules\Common\Entities\Investor;
use Modules\Common\Entities\Task;
use Modules\Common\Repositories\BankAccountRepository;
use Modules\Common\Repositories\TaskRepository;
use Modules\Common\Services\InvestorService;
use Modules\Common\Services\TransactionService;
use Throwable;

class ImportedPaymentObserver
{
    protected TaskRepository $taskRepository;
    protected BankAccountRepository $bankAccountRepository;
    protected InvestorService $investorService;
    protected TransactionService $transactionService;

    /**
     * ImportedPaymentObserver constructor.
     *
     * @param TaskRepository $taskRepository
     * @param BankAccountRepository $bankAccountRepository
     * @param InvestorService $investorService
     * @param TransactionService $transactionService
     */
    public function __construct(
        TaskRepository $taskRepository,
        BankAccountRepository $bankAccountRepository,
        InvestorService $investorService,
        TransactionService $transactionService
    ) {
        $this->taskRepository = $taskRepository;
        $this->bankAccountRepository = $bankAccountRepository;
        $this->investorService = $investorService;
        $this->transactionService = $transactionService;
    }

    /**
     * @param ImportedPayment $importedPayment
     */
    public function created(ImportedPayment $importedPayment)
    {
        $this->resolvePayment($importedPayment);
    }

    /**
     * @param ImportedPayment $payment
     *
     * @return false|ImportedPayment|Task
     */
    public function resolvePayment(ImportedPayment $payment)
    {
        list($investor, $iban, $bic) = $this->resolveBasis($payment->basis);

        if (empty($iban)) {
            $payment->status = ImportedPayment::STATUS_WARNING;
            $payment->save();

            $this->sendWarningEmail('IBAN does not exists in imported payment id: ' . $payment->getId());
            return false;
        }

        try {
            $payment->iban = $iban;
            $payment->bic = $bic;

            // manual validation if no investor found
            if (empty($investor->investor_id)) {
                $payment->save();
                return $this->createNotMatchedTask($payment);
            }

            $bankAccount = $this->investorService->prepareBankAccount(
                $investor,
                $iban,
                $bic
            );
            if (empty($bankAccount->bank_account_id)) {
                throw new Exception("No bank account for investor #" . $investor->getId());
            }

            $wallet = $investor->wallet();
            if (empty($wallet->wallet_id)) {
                throw new Exception("No wallet for investor #" . $investor->getId());
            }

            $payment->fill([
                'investor_id' => $investor->getId(),
                'wallet_id' => $wallet->getId(),
            ]);
            $payment->save();

            if (!$wallet->hasDeposit()) {
                return $this->taskRepository->createFirstDepositTask(
                    $payment,
                    $wallet,
                    $bankAccount
                );
            }

            $madePayment = $this->investorService->makePayment(
                $investor,
                $bankAccount,
                $payment
            );

            if (!$madePayment) {
                $payment->status = ImportedPayment::STATUS_WARNING;
                $payment->save();

                $this->sendWarningEmail(
                    'Failed to make payment: Inv#' . $investor->getId()
                    . ', ba#' . $bankAccount->getId()
                    . ', ip#' . $payment->getId()
                );
                return false;
            }

        } catch (Throwable $e) {
            \Log::channel('importing_payments')->error(
                $e->getMessage() . ' ImportedPaymentId: ' . $payment->getId()
            );

            $this->sendWarningEmail(
                $e->getMessage() . ' ImportedPaymentId: ' . $payment->getId()
            );

            return false;
        }


        return $payment;
    }

    /**
     * @param string $basis
     *
     * @return array
     */
    public function resolveBasis(string $basis): array
    {
        $basis = $this->clearBasis($basis);

        preg_match(ImportedPayment::INVESTOR_ID_REGEX, $basis, $matches);
        $investor = $this->getInvestorFromMatch($matches);

        preg_match(ImportedPayment::BIC_REGEX, $basis, $matches);
        $bic = $this->extractBic($matches);

        preg_match(ImportedPayment::IBAN_REGEX, $basis, $matches);
        $iban = $this->extractIban($matches);

        return [$investor, $iban, $bic];
    }

    /**
     * @param array $match
     * @return Investor|null
     */
    public function getInvestorFromMatch(array $match): ?Investor
    {
        $investorId = $this->extractInvestorId($match);
        if (empty($investorId)) {
            return null;
        }

        try {
            return $this->investorService->getById($investorId);

        } catch (Throwable $e) {
            return null;
        }
    }

    /**
     * @param array $match
     * @return int|null
     */
    public function extractInvestorId(array $match): ?int
    {
        if (isset($match[7])) {
            return (int) $match[7];
        }

        if (isset($match[5])) {
            return (int) $match[5];
        }

        return null;
    }

    /**
     * @param array $match
     * @return string|null
     */
    public function extractIban(array $match): ?string
    {
        if (!empty($match[4])) {
            return $match[4];
        }

        return null;
    }

    /**
     * @param array $match
     * @return string|null
     */
    public function extractBic(array $match): ?string
    {
        if (!empty($match[5])) {
            return $match[5];
        }

        return null;
    }

    /**
     * @param string $basis
     * @return string
     */
    public function clearBasis(string $basis = ''): string
    {
        if (empty($basis)) {
            return $basis;
        }

        return preg_replace(ImportedPayment::PAYMENT_STARTER_REGEX, '', $basis);
    }

    /**
     * @param ImportedPayment $payment
     *
     * @return Task
     */
    protected function createNotMatchedTask(ImportedPayment $payment)
    {
        return $this->taskRepository->create(
            [
                'amount' => $payment->amount,
                'task_type' => Task::TASK_TYPE_MATCH_DEPOSIT,
                'status' => Task::TASK_STATUS_NEW,
                'currency' => Currency::ID_EUR,
                'imported_payment_id' => $payment->getId(),
            ]
        );
    }

    /**
     * @param string $text
     */
    private function sendWarningEmail(string $text)
    {
        $env = strtoupper(env('APP_ENV'));

        $to = config('mail.log_monitor')['receivers'];
        $from = config('mail.log_monitor')['sender'];
        $title = 'Importing payments(' . $env . '): ' . Carbon::today()->format('Y-m-d');

        Mail::send([], [], function($message) use($to, $from, $title, $text) {
            $message->from($from['from'], $from['name']);
            $message->to($to);
            $message->subject($title);
            $message->setBody($text, 'text/html');
        });
    }
}
