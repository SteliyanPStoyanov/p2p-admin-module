<?php

namespace Modules\Common\Services;

use Exception;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Log;
use Modules\Admin\Entities\Administrator;
use Modules\Admin\Entities\Verification;
use Modules\Admin\Repositories\VerificationRepository;
use Modules\Common\Entities\BankAccount;
use Modules\Common\Entities\BlockedAmountHistory;
use Modules\Common\Entities\Currency;
use Modules\Common\Entities\ImportedPayment;
use Modules\Common\Entities\Investor;
use Modules\Common\Entities\Task;
use Modules\Common\Entities\Wallet;
use Modules\Common\Repositories\BankAccountRepository;
use Modules\Common\Repositories\InvestorRepository;
use Modules\Common\Repositories\TaskRepository;
use Modules\Common\Repositories\WalletRepository;
use Modules\Common\Services\TransactionService;
use Modules\Communication\Entities\EmailTemplate;
use Modules\Communication\Services\EmailService;
use Modules\Core\Exceptions\ProblemException;
use Modules\Core\Services\BaseService;
use Modules\Core\Services\CacheService;

use Throwable;

use function Symfony\Component\Translation\t;

class TaskService extends BaseService
{
    protected TaskRepository $taskRepository;
    protected TransactionService $transactionService;
    protected WalletRepository $walletRepository;
    protected VerificationRepository $verificationRepository;
    protected InvestorRepository $investorRepository;
    protected EmailService $emailService;
    protected BlockedAmountHistoryService $blockedAmountHistoryService;
    protected InvestorService $investorService;
    protected BankAccountRepository $bankAccountRepository;

    /**
     * TaskService constructor.
     *
     * @param TaskRepository $taskRepository
     * @param TransactionService $transactionService ;
     * @param WalletRepository $walletRepository
     * @param VerificationRepository $verificationRepository
     * @param InvestorRepository $investorRepository
     * @param EmailService $emailService
     * @param BlockedAmountHistoryService $blockedAmountHistoryService
     * @param InvestorService $investorService
     * @param BankAccountRepository $bankAccountRepository
     */
    public function __construct(
        TaskRepository $taskRepository,
        TransactionService $transactionService,
        WalletRepository $walletRepository,
        VerificationRepository $verificationRepository,
        InvestorRepository $investorRepository,
        EmailService $emailService,
        BlockedAmountHistoryService $blockedAmountHistoryService,
        InvestorService $investorService,
        BankAccountRepository $bankAccountRepository
    ) {
        $this->taskRepository = $taskRepository;
        $this->transactionService = $transactionService;
        $this->walletRepository = $walletRepository;
        $this->verificationRepository = $verificationRepository;
        $this->investorRepository = $investorRepository;
        $this->emailService = $emailService;
        $this->blockedAmountHistoryService = $blockedAmountHistoryService;
        $this->investorService = $investorService;
        $this->bankAccountRepository = $bankAccountRepository;

        parent::__construct();
    }

    /**
     * @param array $file
     */
    public function create(array $file)
    {
        $this->taskRepository->create($file);
    }

    /**
     * @param int $length
     * @param array $data
     *
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function getByWhereConditions(int $length, array $data)
    {
        if (!empty($data['limit'])) {
            $length = $data['limit'];
            unset($data['limit']);
        }

        $whereConditions = $this->getWhereConditions(
            $data,
            [
                'investor.first_name',
                'investor.middle_name',
                'investor.last_name'
            ],
            'task'
        );

        return $this->taskRepository->getAll($length, $whereConditions);
    }

    /**
     * @return string[]
     */
    public function getTaskTypes()
    {
        return $this->taskRepository->getTypes();
    }

    /**
     * @return string[]
     */
    public function getStatuses()
    {
        return $this->taskRepository->getStatuses();
    }

    /**
     * @param int $id
     *
     * @return Task
     *
     * @throws ProblemException
     */
    public function updateProcessBy(int $id)
    {
        $task = $this->getTaskById($id);

        return $this->taskRepository->updateProcessBy($task);
    }

    /**
     * @param Task $task
     *
     * @return Task
     *
     * @throws ProblemException
     */
    public function exitTask(Task $task): Task
    {
        $this->checkTask($task);

        return $this->taskRepository->exitTask($task);
    }

    /**
     * @param int $id
     *
     * @return Task
     *
     * @throws ProblemException
     */
    public function getTaskById(int $id)
    {
        $task = $this->taskRepository->getTaskById($id);
        if (!$task) {
            throw new ProblemException(__('common.TaskNotFound'));
        }

        return $task;
    }

    public function checkInvestorBunch(int $investorId)
    {
        return $this->taskRepository->getInvestorBunch($investorId);
    }

    /**
     * @throws ProblemException
     */
    protected function checkTask(Task $task)
    {
        // already was finished, could not be finished again, needs manual check
        if (!empty($task->done_by)) {
            throw new ProblemException(__('common.CantProcessFinishedTask'));
        }
    }

    /**
     * @param int $id
     * @param array $data
     *
     * @return Task
     *
     * @throws ProblemException
     * @throws Throwable
     */
    public function withdraw(int $id, array $data): Task
    {
        $task = $this->getTaskById($id);

        $this->checkTask($task);

        $wallet = $task->wallet;

        if ($wallet->blocked_amount < $task->amount) {
            throw new ProblemException(__('common.CantWithdrawMoreThanUninvested'));
        }

        DB::beginTransaction();
        try {
            // First create transaction
            $transaction = $this->transactionService->withdraw(
                $task->amount,
                $task->task_id,
                $task->investor_id,
                $wallet->wallet_id,
                $wallet->currency_id,
                $task->bank_account_id,
                $data['bank_transaction_id']
            );

            // Then the wallet
            $this->walletRepository->withdraw($transaction);

            $this->blockedAmountHistoryService->finalize($task, BlockedAmountHistory::STATUS_PAID);

            // Finally we make the task done
            $task = $this->taskRepository->finalize($task);

            $this->emailService->sendEmail(
                $task->investor_id,
                EmailTemplate::TEMPLATE_SEEDER_ARRAY['withdrawal_processed']['id'],
                $task->investor->email_notification ?: $task->investor->email,
                Carbon::now(),
                [
                    'timestamp' => Carbon::now()
                ]
            );

            DB::commit();


            // remove cache of profile/overview since we reduce summs
            (new CacheService)->remove(config('profile.profileDashboard') . $task->investor_id);
        } catch (Throwable $e) {
            DB::rollBack();
            throw new ProblemException(__('common.TaskNotCompleted'));
        }

        return $task;
    }


    /**
     * @return array
     */
    public function getInvestmentBunchesWithActiveWithdrawRequests(): array
    {
        return $this->taskRepository->getInvestmentBunchesWithActiveWithdrawRequests();
    }

    /**
     * @param int $id
     * @param array $data
     *
     * @return Task
     * @throws ProblemException
     * @throws Throwable
     */
    public function addBonus(int $id, array $data): Task
    {
        $task = $this->getTaskById($id);

        $this->checkTask($task);

        $wallet = $task->wallet;
        DB::beginTransaction();
        try {
            // First create transaction
            $transaction = $this->transactionService->bonus(
                $task->amount,
                $task->task_id,
                $task->investor_id,
                $wallet->wallet_id,
                $wallet->currency_id,
                $task->bank_account_id,
                $data['bank_transaction_id']
            );

            // Then the wallet
            $this->walletRepository->withBonus($transaction);

            // Finally we make the task done
            $task = $this->taskRepository->finalize($task);

            DB::commit();

            // remove cache of profile/overview since we update summs
            (new CacheService)->remove(config('profile.profileDashboard') . $task->investor_id);
        } catch (Throwable $e) {
            DB::rollBack();
            throw new ProblemException(__('common.TaskNotCompleted'));
        }

        return $task;
    }

    /**
     * @param int $id
     * @param array $data
     *
     * @return Task
     *
     * @throws ProblemException
     */
    public function verify(int $id, array $data)
    {
        $task = $this->getTaskById($id);
        $this->checkTask($task);

        switch ($data['action']) {
            case 'mark_verified':
                $this->verifyInvestor($task, $data);
                break;
            case 'reject_verification':
                $this->rejectVerifyInvestor($task);
                break;
            case 'request_documents':
                return $this->requestDocumentsInvestor($task, $data);
        }

        return $this->taskRepository->finalize($task);
    }

    /**
     * @param array $data
     * @param array|string[] $names
     * @param string $prefix
     *
     * @return array
     */
    protected function getWhereConditions(
        array $data,
        array $names = ['name'],
        $prefix = ''
    ) {
        $where = [];
        if (!empty($data['amount'])) {
            if (!empty($data['amount']['from'])) {
                $where[] = [
                    'task.amount',
                    '>=',
                    $data['amount']['from'],
                ];
            }

            if (!empty($data['amount']['to'])) {
                $where[] = [
                    'task.amount',
                    '<=',
                    $data['amount']['to'],
                ];
            }

            unset($data['amount']);
        }


        if (!empty($data['createdAt']['from'])) {
            $where[] = [
                'task.created_at',
                '>=',
                dbDate($data['createdAt']['from'], '00:00:00'),
            ];
        }

        if (!empty($data['createdAt']['to'])) {
            $where[] = [
                'task.created_at',
                '<=',
                dbDate($data['createdAt']['to'], '23:59:59'),
            ];
        }

        unset($data['createdAt']);

        return array_merge($where, parent::getWhereConditions($data, $names, $prefix));
    }

    /**
     * @param Task $task
     * @param array $data
     *
     * @return Verification
     */
    protected function verifyInvestor(Task $task, array $data): Verification
    {
        $data['investor_id'] = $task->investor_id;
        $verification = $this->verificationRepository->createOrUpdate($task->investor_id, $data);
        $this->investorRepository->update($task->investor, ['status' => Investor::INVESTOR_STATUS_VERIFIED]);

        $this->emailService->sendEmail(
            $task->investor_id,
            EmailTemplate::TEMPLATE_SEEDER_ARRAY['verification']['id'],
            $task->investor->email_notification ?: $task->investor->email,
            Carbon::now(),
            [
                'timestamp' => Carbon::now()
            ]
        );

        (new CacheService())->remove(config('profile.profileDashboard') . $task->investor_id);

        return $verification;
    }

    /**
     * @param Task $task
     * @return mixed|Investor
     */
    protected function rejectVerifyInvestor(Task $task): Investor
    {
        $this->emailService->sendEmail(
            $task->investor_id,
            EmailTemplate::TEMPLATE_SEEDER_ARRAY['verification_rejected']['id'],
            $task->investor->email_notification ?: $task->investor->email,
            Carbon::now(),
            [
                'timestamp' => Carbon::now()
            ]
        );

        return $this->investorRepository->update(
            $task->investor,
            ['status' => Investor::INVESTOR_STATUS_REJECTED_VERIFICATION]
        );
    }

    /**
     * @param Task $task
     * @param array $data
     *
     * @return Task
     */
    protected function requestDocumentsInvestor(Task $task, array $data)
    {
        $data['investor_id'] = $task->investor_id;
        $this->verificationRepository->createOrUpdate($task->investor_id, $data);

        $this->investorRepository->update(
            $task->investor,
            ['status' => Investor::INVESTOR_STATUS_AWAITING_DOCUMENTS]
        );

        (new CacheService())->remove(config('profile.profileDashboard') . $task->investor_id);

        return $this->taskRepository->updateProcessBy($task);
    }

    /**
     * @param Task $task
     *
     * @return Task
     * @throws Throwable
     */
    public function cancelTask(Task $task): Task
    {
        $this->checkTask($task);

        try {
            DB::beginTransaction();

            if ($task->task_type === Task::TASK_TYPE_WITHDRAW) {
                $total = $task->investor->wallet()->getTotalAmount() - $task->amount;

                if ($total < 0) {
                    $additionalData = [
                        'timestamp' => Carbon::now(),
                        'Transaction' => [
                            'amount' => $task['amount'],
                        ]
                    ];

                    $this->emailService->sendEmail(
                        $task->investor_id,
                        EmailTemplate::TEMPLATE_SEEDER_ARRAY['withdrawal_cancelled_insufficient_funds']['id'],
                        $task->investor->email_notification ?: $task->investor->email,
                        Carbon::now(),
                        $additionalData
                    );
                }

                $this->returnAmount($task);
            }

            $task = $this->taskRepository->cancelTask($task);

            DB::commit();

            (new CacheService())->remove(config('profile.profileDashboard') . $task->investor_id);
        } catch (Throwable $e) {
            DB::rollBack();

            throw new ProblemException(__('common.taskCancelFailed'));
        }

        return $task;
    }

    /**
     * @param $id
     *
     * @throws ProblemException
     */
    public function delete($id)
    {
        $task = $this->getTaskById($id);

        $this->checkTask($task);

        try {
            DB::beginTransaction();

            if ($task->task_type === Task::TASK_TYPE_WITHDRAW) {
                $this->returnAmount($task);
            }
            $this->taskRepository->delete($task);

            DB::commit();

            (new CacheService())->remove(config('profile.profileDashboard') . $task->investor_id);
        } catch (Throwable $e) {
            DB::rollBack();

            throw new ProblemException(__('common.emailTemplateDeletionFailed'));
        }
    }

    /**
     * @param Task $task
     *
     * @throws ProblemException
     */
    protected function returnAmount(Task $task)
    {
        $releaseBlockedAMount = $this->walletRepository->returnBlockedAmount(
            $task->wallet,
            $task->amount
        );

        if (!$releaseBlockedAMount) {
            throw new ProblemException('Can not release blocked amount. Manual check needs.');
        }

        $this->blockedAmountHistoryService->finalize($task, BlockedAmountHistory::STATUS_RETURNED);
    }

    /**
     * @param int $id
     * @param int $investorId
     * @return bool|Task
     * @throws Throwable
     */
    public function matchDeposit(int $id, int $investorId)
    {
        try {
            $task = $this->getTaskById($id);

            if (empty($task->task_id)) {
                throw new Exception("No task found for investor # " . $investorId);
            }

            $this->checkTask($task);

            $investor = $this->investorRepository->getById($investorId);

            if (empty($investor->investor_id)) {
                throw new Exception("Not found investor # " . $investorId);
            }

            $importedPayment = $task->importedPayment;

            $bankAccount = $this->investorService->prepareBankAccount(
                $investor,
                $importedPayment->iban,
                $importedPayment->bic
            );

            if ($investor->status != Investor::INVESTOR_STATUS_VERIFIED) {
                $this->taskRepository->finalize($task);

                switch ($investor->status) {
                    case Investor::INVESTOR_STATUS_REJECTED_VERIFICATION:
                        return $this->createRejectedVerificationTask(
                            $investor,
                            $importedPayment,
                            $bankAccount
                        );
                    default:
                        return $this->createNotVerifiedTask(
                            $investor,
                            $importedPayment,
                            $bankAccount
                        );
                }
            }

            $wallet = $investor->wallet();

            if (empty($wallet->wallet_id)) {
                throw new Exception("No wallet for investor #" . $investorId);
            }

            $importedPayment->investor_id = $investor->getId();
            $importedPayment->wallet_id = $wallet->getId();
            $importedPayment->save();

            if (!$wallet->hasDeposit()) {
                $this->taskRepository->finalize($task);

                return $this->taskRepository->createFirstDepositTask(
                    $importedPayment,
                    $wallet,
                    $bankAccount
                );
            }

            if (!$this->investorService->makePayment(
                $investor,
                $bankAccount,
                $importedPayment,
                $task
            )) {
                throw new ProblemException(__('common.TaskNotCompleted'));
            }
        } catch (Throwable $e) {
            Log::channel('importing_payments')->error(
                'MatchDeposit ' . $e->getMessage()
            );
            return false;
        }

        return true;
    }

    /**
     * @param int $id
     * @return bool|Task
     * @throws ProblemException
     * @throws Throwable
     */
    public function firstDepositOrNotVerified(int $id)
    {
        $task = $this->getTaskById($id);
        $this->checkTask($task);

        $investor = $task->investor;
        $importedPayment = $task->importedPayment;

        $bankAccount = $investor->bankAccounts->where('iban', $importedPayment->iban)->first();
        if (empty($bankAccount)) {
            $bankAccount = $this->bankAccountRepository->create(
                $importedPayment->iban,
                $investor,
                (bool)empty($investor->mainBankAccount()),
                $importedPayment->bic
            );
        }

        if (empty($bankAccount->bic)) {
            $bankAccount = $this->bankAccountRepository->updateBic($bankAccount, $importedPayment->bic);
        }

        if (
            $investor->status != Investor::INVESTOR_STATUS_VERIFIED
            && $task->task_type != Task::TASK_TYPE_NOT_VERIFIED
        ) {
            $this->taskRepository->finalize($task);
            switch ($investor->status) {
                case Investor::INVESTOR_STATUS_REJECTED_VERIFICATION:
                    return $this->createRejectedVerificationTask(
                        $investor,
                        $importedPayment,
                        $bankAccount
                    );
                default:
                    return $this->createNotVerifiedTask(
                        $investor,
                        $importedPayment,
                        $bankAccount
                    );
            }
        }

        if (!$this->investorService->makePayment(
            $investor,
            $bankAccount,
            $importedPayment,
            $task
        )) {
            throw new ProblemException(__('common.TaskNotCompleted'));
        }

        return true;
    }

    /**
     * @param int $id
     *
     * @return Task
     *
     * @throws ProblemException
     */
    public function markDone(int $id)
    {
        $task = $this->getTaskById($id);

        $this->transactionService->markImportedPaymentHandled($task->importedPayment);

        return $this->taskRepository->finalize($task);
    }

    /**
     * @param Investor $investor
     * @param ImportedPayment $importedPayment
     * @param BankAccount $bankAccount
     *
     * @return Task
     */
    protected function createRejectedVerificationTask(
        Investor $investor,
        ImportedPayment $importedPayment,
        BankAccount $bankAccount
    ): Task {
        return $this->taskRepository->create(
            [
                'task_type' => Task::TASK_TYPE_REJECTED_VERIFICATION,
                'investor_id' => $investor->getId(),
                'wallet_id' => $investor->wallet()->getId(),
                'currency_id' => Currency::ID_EUR,
                'bank_account_id' => $bankAccount->getId(),
                'amount' => $importedPayment->amount,
                'status' => Task::TASK_STATUS_NEW,
                'imported_payment_id' => $importedPayment->getId(),
            ]
        );
    }

    /**
     * @param Investor $investor
     * @param ImportedPayment $importedPayment
     * @param BankAccount $bankAccount
     * @return Task
     */
    protected function createNotVerifiedTask(
        Investor $investor,
        ImportedPayment $importedPayment,
        BankAccount $bankAccount
    ): Task {
        return $this->taskRepository->create(
            [
                'task_type' => Task::TASK_TYPE_NOT_VERIFIED,
                'investor_id' => $investor->getId(),
                'wallet_id' => $investor->wallet()->getId(),
                'currency_id' => Currency::ID_EUR,
                'bank_account_id' => $bankAccount->getId(),
                'amount' => $importedPayment->amount,
                'status' => Task::TASK_STATUS_NEW,
                'imported_payment_id' => $importedPayment->getId(),
            ]
        );
    }
}
