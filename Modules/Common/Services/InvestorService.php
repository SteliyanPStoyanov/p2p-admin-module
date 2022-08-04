<?php

namespace Modules\Common\Services;

use Carbon\Carbon;
use Illuminate\Http\File;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Modules\Admin\Entities\Setting;
use Modules\Common\Entities\Agreement;
use Modules\Common\Entities\BankAccount;
use Modules\Common\Entities\BlockedAmountHistory;
use Modules\Common\Entities\Document;
use Modules\Common\Entities\DocumentType;
use Modules\Common\Entities\FileType;
use Modules\Common\Entities\ImportedPayment;
use Modules\Common\Entities\Investor;
use Modules\Common\Entities\InvestorBonus;
use Modules\Common\Entities\Loan;
use Modules\Common\Entities\Task;
use Modules\Common\Entities\Transaction;
use Modules\Common\Entities\Wallet;
use Modules\Common\Repositories\AgreementRepository;
use Modules\Common\Repositories\BankAccountRepository;
use Modules\Common\Repositories\DocumentRepository;
use Modules\Common\Repositories\FileRepository;
use Modules\Common\Repositories\InvestorBonusRepository;
use Modules\Common\Repositories\InvestorCompanyRepository;
use Modules\Common\Repositories\InvestorInstallmentRepository;
use Modules\Common\Repositories\InvestorRepository;
use Modules\Common\Repositories\TaskRepository;
use Modules\Common\Repositories\WalletRepository;
use Modules\Communication\Entities\EmailTemplate;
use Modules\Communication\Services\EmailService;
use Modules\Core\Exceptions\ProblemException;
use Modules\Core\Services\BaseService;
use Modules\Core\Services\CacheService;
use Modules\Core\Services\StorageService;
use Throwable;

class InvestorService extends BaseService
{
    const CODE_ALREADY_SENT_WITHDRAW_REQUEST = 87;

    private InvestorRepository $investorRepository;
    private BankAccountRepository $bankAccountRepository;
    private FileRepository $fileRepository;
    private DocumentRepository $documentRepository;
    private TaskRepository $taskRepository;
    private WalletRepository $walletRepository;
    protected ?StorageService $storageService;
    protected TransactionService $transactionService;
    protected EmailService $emailService;
    protected AgreementRepository $agreementRepository;
    protected InvestorBonusRepository $investorBonusRepository;
    private InvestorInstallmentRepository $investorInstallmentRepository;
    protected BlockedAmountHistoryService $blockedAmountHistoryService;
    private InvestorCompanyRepository $investorCompanyRepository;

    /**
     * InvestorService constructor.
     *
     * @param InvestorRepository $investorRepository
     * @param FileRepository $fileRepository
     * @param DocumentRepository $documentRepository
     * @param TaskRepository $taskRepository
     * @param StorageService $storageService
     * @param WalletRepository $walletRepository
     * @param BankAccountRepository $bankAccountRepository
     * @param TransactionService $transactionService
     * @param EmailService $emailService
     * @param AgreementRepository $agreementRepository
     * @param InvestorBonusRepository $investorBonusRepository
     * @param InvestorInstallmentRepository $investorInstallmentRepository
     * @param BlockedAmountHistoryService $blockedAmountHistoryService
     * @param InvestorCompanyRepository $investorCompanyRepository
     */
    public function __construct(
        InvestorRepository $investorRepository,
        FileRepository $fileRepository,
        DocumentRepository $documentRepository,
        TaskRepository $taskRepository,
        StorageService $storageService,
        WalletRepository $walletRepository,
        BankAccountRepository $bankAccountRepository,
        TransactionService $transactionService,
        EmailService $emailService,
        AgreementRepository $agreementRepository,
        InvestorBonusRepository $investorBonusRepository,
        InvestorInstallmentRepository $investorInstallmentRepository,
        BlockedAmountHistoryService $blockedAmountHistoryService,
        InvestorCompanyRepository $investorCompanyRepository
    ) {
        $this->investorRepository = $investorRepository;
        $this->fileRepository = $fileRepository;
        $this->documentRepository = $documentRepository;
        $this->taskRepository = $taskRepository;
        $this->storageService = $storageService;
        $this->walletRepository = $walletRepository;
        $this->bankAccountRepository = $bankAccountRepository;
        $this->transactionService = $transactionService;
        $this->emailService = $emailService;
        $this->agreementRepository = $agreementRepository;
        $this->investorBonusRepository = $investorBonusRepository;
        $this->investorInstallmentRepository = $investorInstallmentRepository;
        $this->blockedAmountHistoryService = $blockedAmountHistoryService;
        $this->investorCompanyRepository = $investorCompanyRepository;

        parent::__construct();
    }

    /**
     * @param string $email
     * @param int|null $parentId
     *
     * @return Investor
     */
    public function create(string $email, int $parentId = null)
    {
        $data['email'] = $email;
        $data['email_notification'] = $email;
        $data['status'] = Investor::INVESTOR_STATUS_UNREGISTERED;

        if (!empty($parentId)) {
            $data['referral_id'] = $parentId;
        }

        return $this->investorRepository->create($data);
    }


    /**
     * @param int $investorId
     * @param string $password
     *
     * @return mixed|Investor
     */
    public function restorePassword(int $investorId, string $password)
    {
        $data['password'] = Hash::make($password);

        $investor = $this->investorRepository->getById($investorId);

        return $this->investorRepository->update($investor, $data);
    }

    /**
     * @param string $email
     *
     * @return mixed
     */
    public function emailExist(string $email)
    {
        return $this->investorRepository->isExists($email);
    }

    /**
     * @param string $email
     *
     * @return mixed
     */
    public function getByEmail(string $email)
    {
        return $this->investorRepository->getByEmail($email);
    }

    /**
     * @param int $id
     *
     * @return Investor
     *
     * @throws ProblemException
     */
    public function getById(int $id)
    {
        if (!$investor = $this->investorRepository->getById($id)) {
            throw new ProblemException(__('common.InvestorNotFound'));
        }

        return $investor;
    }

    /**
     * @param int $investorId
     * @param array $data
     *
     * @return mixed|Investor|null
     * @throws ProblemException
     */
    public function stepUpdate(int $investorId, array $data)
    {
        $data['password'] = Hash::make($data['password']);
        $dataInvestor = [
            'first_name' => $data['first_name'],
            'last_name' => $data['last_name'],
            'type' => $data['type'],
            'password' => $data['password'],
            'status' => Investor::INVESTOR_STATUS_REGISTERED,
            'verification_data' => json_encode($data),
            'referral_hash' => $this->generateReferralLink($investorId),
        ];
        $investor = $this->getById($investorId);

        $this->addAgreements($data, $investor);

        return $this->investorRepository->update(
            $investor,
            $dataInvestor
        );
    }

    protected function addAgreements(array $data, Investor $investor)
    {
        $this->agreementRepository->addInvestorAgreement(
            $investor,
            Agreement::USER_AGREEMENT_ID,
            (int)!empty($data['agreement'])
        );

        $this->agreementRepository->addInvestorAgreement(
            $investor,
            Agreement::RECEIVE_MARKETING_COMMUNICATION_ID,
            (int)!empty($data['marketing'])
        );
    }

    private function generateReferralLink(int $investorId)
    {
        $str_result = $investorId . Investor::INVESTOR_HASH_LINK_SYMBOLS;
        return substr(
            str_shuffle($str_result),
            0,
            Investor::SIZE_HASH_LINK
        );
    }

    /**
     * @param array $data
     *
     * @return mixed
     */
    public function verifyInvestor(array $data)
    {
        $investor = Auth::guard('investor')->user();

        $additionalData = [
            'verification_data' => json_encode($data),
        ];

        unset($data['day'], $data['month'], $data['year']);

        $dataInvestor = array_merge($data, $additionalData);

        return $this->investorRepository->update($investor, $dataInvestor);
    }

    /**
     * @param int $investorId
     * @param array $files
     * @param int $documentTypeId
     *
     * @return \Modules\Common\Entities\Document
     */
    public function savePersonalDoc(int $investorId, array $files, int $documentTypeId)
    {
        $i = 0;
        foreach ($files as $key => $file) {
            $i++;
            $fileStore = $this->storageService->uploadPersonalDoc($investorId, $file, $i);

            $fileData['hash'] = Hash::make($file->getClientOriginalName());
            $fileData['file_path'] = $fileStore[0];
            $fileData['file_size'] = $file->getSize();
            $fileData['file_type'] = $file->getClientMimeType();
            $fileData['file_type_id'] = $key === FileType::SELFIE_NAME ? FileType::SELFIE_ID : $documentTypeId;
            $fileData['file_name'] = $fileStore[1];

            $savedFile = $this->fileRepository->create($fileData);

            $document['document_type_id'] = $key === FileType::SELFIE_NAME ? DocumentType::DOCUMENT_TYPE_SELFIE_ID : $documentTypeId;
            $document['investor_id'] = $investorId;
            $document['file_id'] = $savedFile->file_id;
            $document['name'] = $savedFile->file_name;
            $document['description'] = $savedFile->file_name;

            $documentSave = $this->documentRepository->create($document);
        }

        return $documentSave;
    }

    /**
     * @return mixed
     */
    public function confirmVerify()
    {
        $investor = Auth::guard('investor')->user();

        // check if already done
        if ($investor->hasActiveVerificationTask()) {
            return false;
        }

        // check if already verified
        if ($investor->isVerified()) {
            return false;
        }

        $task['investor_id'] = $investor->investor_id;
        $task['task_type'] = Task::TASK_TYPE_VERIFICATION;
        $task['status'] = Task::TASK_STATUS_NEW;

        $this->taskRepository->create($task);

        $dataInvestor['status'] = Investor::INVESTOR_STATUS_AWAITING_VERIFICATION;

        return $this->investorRepository->update($investor, $dataInvestor);
    }

    /**
     * @param int $length
     * @param array $data
     *
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function getByWhereConditions(
        int $length,
        array $data
    ) {
        $order = $this->getOrderConditions($data);
        unset($data['order']);
        if (empty($order)) {
            $order = ['active' => 'DESC', 'investor_id' => 'DESC'];
        }

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
            'investor'
        );

        return $this->investorRepository->getAll(
            $length,
            $whereConditions,
            $order
        );
    }

    /**
     * @param int $length
     * @param array $data
     *
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function getByWhereConditionsReferrals(
        int $length,
        array $data
    ) {
        if (!empty($data['limit'])) {
            $length = $data['limit'];
            unset($data['limit']);
        }

        $whereConditions = $this->getWhereConditionsReferrals(
            $data,
            [
                'a.first_name',
                'a.middle_name',
                'a.last_name'
            ],
            'a'
        );

        return $this->investorRepository->getReferrals($length, $whereConditions);
    }

    /**
     * @param int $length
     * @param int $investorId
     * @param array $data
     *
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator|\Illuminate\Support\Collection
     */
    public function getInvestorTransactions(
        int $length,
        int $investorId,
        array $data,
        bool $addLoanId = true
    ) {
        $whereConditions = $this->getWhereConditionsTransactions(
            $data,
            [],
            't'
        );

        return $this->investorRepository->investorTransactions($length, $investorId, $whereConditions, $addLoanId);
    }

    /**
     * @param array $data
     * @param array|string[] $names
     * @param string $prefix
     *
     * @return array
     */
    public function getWhereConditionsTransactions(
        array $data,
        array $names = ['name'],
        $prefix = ''
    ): array {
        $where = [];
        $where['t.active'] = '1';
        $where['t.deleted'] = '0';

        if (!empty($data['amount'])) {
            if (!empty($data['amount']['from'])) {
                $where[] = [
                    't.amount',
                    '>=',
                    $data['amount']['from'],
                ];
            }

            if (!empty($data['amount']['to'])) {
                $where[] = [
                    't.amount',
                    '<=',
                    $data['amount']['to'],
                ];
            }

            unset($data['amount']);
        }

        return array_merge($where, parent::getWhereConditions($data, $names, $prefix));
    }

    /**
     * @param int $length
     * @param array $data
     * @param int $investorId
     *
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator|\Illuminate\Support\Collection
     */
    public function getInvestorChangeLogs(
        int $length,
        array $data,
        int $investorId
    ) {
        $whereConditions = $this->getWhereInvestorChangeLogs(
            $data,
            [],
            'cl'
        );

        return $this->investorRepository->investorChangeLogs($length, $investorId, $whereConditions);
    }

    /**
     * @param array $data
     * @param array|string[] $names
     * @param string $prefix
     *
     * @return array
     */
    public function getWhereInvestorChangeLogs(
        array $data,
        array $names = ['name'],
        string $prefix = ''
    ) {
        $where = [];

        if (!empty($data['createdAt'])) {
            $where[] = [
                'cl.created_at',
                '>=',
                dbDate($data['createdAt'], '00:00:00'),
            ];

            $where[] = [
                'cl.created_at',
                '<=',
                dbDate($data['createdAt'], '23:59:59'),
            ];
        }

        unset($data['createdAt']);

        return array_merge($where, parent::getWhereConditions($data, $names, $prefix));
    }

    /**
     * @param array $data
     * @param array|string[] $names
     * @param string $prefix
     *
     * @return array
     */
    public
    function getWhereConditionsReferrals(
        array $data,
        array $names = ['name'],
        $prefix = ''
    ) {
        $where = [];

        if (!empty($data['deposit'])) {
            if (!empty($data['deposit']['from'])) {
                $where['deposit_from'] = $data['deposit']['from'];
            }

            if (!empty($data['deposit']['to'])) {
                $where['deposit_to'] = $data['deposit']['to'];
            }

            unset($data['deposit']);
        }

        if (!empty($data['invested'])) {
            if (!empty($data['invested']['from'])) {
                $where['invested_from'] = $data['invested']['from'];
            }

            if (!empty($data['invested']['to'])) {
                $where['invested_to'] = $data['invested']['to'];
            }

            unset($data['invested']);
        }

        if (!empty($data['referrals_count'])) {
            if (!empty($data['referrals_count']['from'])) {
                $where['referrals_count_from'] = $data['referrals_count']['from'];
            }

            if (!empty($data['referrals_count']['to'])) {
                $where['referrals_count_to'] = $data['referrals_count']['to'];
            }

            unset($data['referrals_count']);
        }

        return array_merge($where, parent::getWhereConditions($data, $names, $prefix));
    }

    /**
     * @return string[]
     */
    public function getStatuses()
    {
        return $this->investorRepository->getStatuses();
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
        if (!empty($data['status'])) {
            $where[] = [
                'investor.status',
                '=',
                $data['status']
            ];

            unset($data['status']);
        }

        if (!empty($data['type'])) {
            $where[] = [
                'investor.type',
                '=',
                $data['type']
            ];

            unset($data['type']);
        }
        if (!empty($data['type'])) {
            $where[] = [
                'investor.type',
                '=',
                $data['type']
            ];

            unset($data['type']);
        }

        if (!empty($data['total_amount'])) {
            if (!empty($data['total_amount']['from'])) {
                $where[] = [
                    'wallet.total_amount',
                    '>=',
                    $data['total_amount']['from'],
                ];
            }

            if (!empty($data['total_amount']['to'])) {
                $where[] = [
                    'wallet.total_amount',
                    '<=',
                    $data['total_amount']['to'],
                ];
            }

            unset($data['total_amount']);
        }

        if (!empty($data['createdAt'])) {
            if (!empty($data['createdAt']['from'])) {
                $where[] = [
                    'investor.created_at',
                    '>=',
                    dbDate($data['createdAt']['from'], '00:00:00'),
                ];
            }

            if (!empty($data['createdAt']['to'])) {
                $where[] = [
                    'investor.created_at',
                    '<=',
                    dbDate($data['createdAt']['to'], '23:59:59'),
                ];
            }

            unset($data['createdAt']);
        }

        if (!empty($data['uninvested_amount'])) {
            if (!empty($data['uninvested_amount']['from'])) {
                $where[] = [
                    'wallet.uninvested',
                    '>=',
                    $data['uninvested_amount']['from'],
                ];
            }

            if (!empty($data['uninvested_amount']['to'])) {
                $where[] = [
                    'wallet.uninvested',
                    '<=',
                    $data['uninvested_amount']['to'],
                ];
            }

            unset($data['uninvested_amount']);
        }

        return array_merge($where, parent::getWhereConditions($data, $names, $prefix));
    }

    /**
     * @param float $amount
     * @param int $bankAccountId
     *
     * @return Task|false
     *
     * @throws ProblemException|Throwable
     */
    public function makeWithdrawTask(float $amount, int $bankAccountId)
    {
        $investorId = Auth::guard('investor')->user()->investor_id;

        $investor = $this->investorRepository->getById($investorId);

        if (!$investor->canWithdraw($amount)) {
            throw new ProblemException(
                __('common.cantWithdrawMoreThanYourUninvested'),
                '',
                self::CODE_ALREADY_SENT_WITHDRAW_REQUEST
            );
        }

        $wallet = $investor->wallet();

        $task['investor_id'] = $investor->investor_id;
        $task['wallet_id'] = $wallet->wallet_id;
        $task['currency_id'] = $wallet->currency_id;
        $task['bank_account_id'] = $bankAccountId;
        $task['amount'] = $amount;
        $task['task_type'] = Task::TASK_TYPE_WITHDRAW;
        $task['status'] = Task::TASK_STATUS_NEW;

        if ($investor->withdrawNotificationChecked()) {
            $additionalData = [
                'timestamp' => Carbon::now(),
                'Transaction' => [
                    'amount' => $amount,
                ]
            ];
            $this->emailService->sendEmail(
                $investor,
                EmailTemplate::TEMPLATE_SEEDER_ARRAY['withdrawal_template']['id'],
                $investor->email_notification ?: $investor->email,
                Carbon::now(),
                $additionalData
            );
        }

        try {
            DB::beginTransaction();

            $task = $this->taskRepository->create($task);

            $this->blockedAmountHistoryService->create(
                [
                    'investor_id' => $investor->getId(),
                    'wallet_id' => $wallet->getId(),
                    'task_id' => $task->getId(),
                    'amount' => $amount,
                    'status' => BlockedAmountHistory::STATUS_BLOCKED,
                ]
            );

            $this->walletRepository->walletUpdate(
                $wallet->investor_id,
                [
                    'uninvested' => $wallet->uninvested - $amount,
                    'blocked_amount' => $wallet->blocked_amount + $amount,
                ]
            );

            DB::commit();
        } catch (Throwable $e) {
            DB::rollBack();

            return false;
        }

        return $task;
    }

    /**
     * @param object $investors
     * @param float $bonusAmount
     */
    public function createTaskWithReferralBonus(object $investors, float $bonusAmount)
    {
        foreach ($investors as $investor) {
            $investor = $this->investorRepository->getById($investor->investor_id);
            $task['investor_id'] = $investor->investor_id;
            $task['wallet_id'] = $investor->wallet()->wallet_id;
            $task['currency_id'] = $investor->wallet()->currency_id;
            $task['bank_account_id'] = $investor->mainBankAccount()->bank_account_id ?? null;
            $task['amount'] = $bonusAmount;
            $task['task_type'] = Task::TASK_TYPE_BONUS_PAYMENT;
            $task['status'] = Task::TASK_STATUS_NEW;
            if ($investor->status != Task::TASK_STATUS_NEW) {
                $this->taskRepository->create($task);
            }
        }
    }

    /**
     * @param Investor $investor
     * @param array $data
     *
     * @return mixed|Investor
     * @throws ProblemException
     */
    public function update(Investor $investor, array $data)
    {
        if (!empty($data['bank_account_id'])) {
            $this->bankAccountRepository->update($investor->investor_id, $data['bank_account_id']);

            unset($data['bank_account_id']);
        }

        if (!empty($data['new-password'])) {
            $data['password'] = Hash::make($data['new-password']);
            unset($data['old-password'], $data['new-password'], $data['repeat-password']);

            $this->emailService->sendEmail(
                $investor,
                EmailTemplate::TEMPLATE_SEEDER_ARRAY['password_changed']['id'],
                $investor->email_notification ?: $investor->email
            );
        }

        $this->refreshInvestorAgreements($data, $investor);

        if ($investor->email !== $data['email']) {
            $emailIsExist = $this->investorRepository->isExists($data['email']);

            if ($emailIsExist) {
                throw new ProblemException(__('common.EmailIsExist'));
            } else {
                $sendEmail = $this->emailService->sendEmail(
                    $investor,
                    EmailTemplate::TEMPLATE_SEEDER_ARRAY['email_changed']['id'],
                    $investor->email,
                    Carbon::now(),
                );

                if ($sendEmail == false) {
                    Log::channel('email_service')->error(
                        'Failed to send email for change email: ' . $investor->investor_id
                    );
                }
            }
        }

        return $this->investorRepository->update($investor, $data);
    }

    /**
     * @param array $data
     * @param Investor $investor
     */
    public function refreshInvestorAgreements(array $data, Investor $investor)
    {
        $this->agreementRepository->refreshAgreement(
            $investor,
            Agreement::RECEIVE_FUNDS_NOTIFICATION_ID,
            (int)!empty($data['add-funds'])
        );

        $this->agreementRepository->refreshAgreement(
            $investor,
            Agreement::WITHDRAW_REQUEST_NOTIFICATION_ID,
            (int)!empty($data['withdrawal-made'])
        );

        $this->agreementRepository->refreshAgreement(
            $investor,
            Agreement::NEW_DEVICE_NOTIFICATION,
            (int)!empty($data['new-device'])
        );
    }

    /**
     * @param int $investorId
     * @param array $data
     *
     * @return Wallet
     *
     * @throws ProblemException
     */
    public function prepareDataAndAddFunds(int $investorId, array $data): Wallet
    {
        if ($this->transactionService->transactionExistByBankTransaction($data['bank_transaction_id'])) {
            throw new ProblemException(__('common.BankTransactionExists'));
        }

        $investor = $this->getById($investorId);
        $wallet = $investor->wallet();

        if (!empty($data['bank_account_iban'])) {
            $bankAccount = $investor->bankAccounts->where('iban', $data['bank_account_iban'])->first();

            if (empty($bankAccount)) {
                $bankAccount = $this->bankAccountRepository->create(
                    $data['bank_account_iban'],
                    $investor,
                    (bool)empty($investor->mainBankAccount())
                );
            }
        } else {
            if (!empty($data['bank_account_id'])) {
                $bankAccount = $investor->bankAccounts->where('bank_account_id', $data['bank_account_id'])->first();
            }
        }

        if (empty($bankAccount)) {
            throw new ProblemException(
                __('common.BankAccountNotFound')
                . ', i#' . $investorId . ' params: ' . json_encode($data)
            );
        }

        $this->addFunds(
            $investor,
            $wallet,
            $bankAccount,
            (float)$data['amount'],
            (string)$data['bank_transaction_id']
        );

        $wallet->refresh();

        return $wallet;
    }

    public function addFunds(
        Investor $investor,
        Wallet $wallet,
        BankAccount $bankAccount,
        float $amount,
        string $bankTransactionId
    ): Transaction {
        try {
            DB::beginTransaction();

            // Create transaction
            $transaction = $this->transactionService->deposit(
                $amount,
                $investor->getId(),
                $wallet->getId(),
                $wallet->currency_id,
                $bankAccount->getId(),
                $bankTransactionId
            );

            // Update wallet
            $wallet->addFunds($transaction->amount);

            $additionalData = [
                'timestamp' => Carbon::now(),
                'Transaction' => [
                    'amount' => $amount,
                    'transaction_id' => $transaction->transaction_id
                ]
            ];

            if ($investor->addFundNotificationChecked()) {
                $this->emailService->sendEmail(
                    $investor,
                    EmailTemplate::TEMPLATE_SEEDER_ARRAY['deposit_template']['id'],
                    $investor->email_notification ?: $investor->email,
                    Carbon::now(),
                    $additionalData
                );
            }

            DB::commit();
        } catch (Throwable $exception) {
            DB::rollBack();
            throw new ProblemException(__('common.WalletNotUpdated'));
        }

        return $transaction;
    }

    /**
     * @param array $days
     * @param string $status
     *
     * @return \Illuminate\Support\Collection
     */
    public function recall(array $days, string $status)
    {
        return $this->investorRepository->getAllDaysRecall($days, $status);
    }

    /**
     * @param string $hash
     *
     * @return bool
     */
    public function checkHashExist(string $hash)
    {
        $checkHashExist = $this->investorRepository->getByHash($hash);

        if (empty($checkHashExist->referral_hash)) {
            return false;
        }

        return true;
    }

    /**
     * @param string $hash
     */
    public function getByHash(string $hash)
    {
        return $this->investorRepository->getByHash($hash);
    }

    /**
     * @param int $investorId
     * @param array $data
     */
    public function investorRecallUpdate(int $investorId, array $data)
    {
        return $this->investorRepository->investorUpdate($investorId, $data);
    }

    public function getCalculatedReferralsForBonus(Carbon $date): array
    {
        try {
            $bonusDaysCount = (int)\SettingFacade::getSettingValue(
                Setting::BONUS_DAYS_COUNT_FOR_CHECK_KEY
            );

            $minInvestedAmount = (int)\SettingFacade::getSettingValue(
                Setting::BONUS_MIN_INVESTED_AMOUNT_KEY
            );

            $bonusPercent = (int)\SettingFacade::getSettingValue(
                Setting::BONUS_PERCENT_KEY
            );

            $sendBonusAfterDays = (int)\SettingFacade::getSettingValue(
                Setting::BONUS_DAYS_COUNT_FOR_SEND_KEY
            );

            $result = $this->investorRepository->getInvestorWithBonuses(
                $bonusDaysCount,
                $minInvestedAmount,
                $bonusPercent,
                $date
            );

            if (empty($result)) {
                return [];
            }

            foreach ($result as $row) {
                $data = [
                    'investor_id' => $row->investor_id,
                    'from_investor_id' => $row->child_id,
                    'amount' => $row->amount,
                    'date' => Carbon::parse($row->registration_date)->addDays($sendBonusAfterDays),
                ];

                $this->bonusesMassInsert($data);
            }
        } catch (Throwable $e) {
            Log::channel('bonus_for_investor')->error('Error: ' . $e->getMessage());
            return [];
        }

        return $result;
    }

    public function createBonusTasks(): int
    {
        $result = $this->investorRepository->getInvestorsBonuses();
        if (empty($result)) {
            return 0;
        }

        $created = 0;
        foreach ($result as $row) {
            $success = $this->createTaskFromBonus(
                $row->investor_id,
                $row->amount,
                $row->investor_bonus_id,
                $row->wallet_id,
                $row->currency_id,
                $row->bank_account_id,
            );

            if ($success) {
                $created++;
            }
        }

        return $created;
    }

    public function bonusesMassInsert(array $data): bool
    {
        return InvestorBonus::insert($data);
    }

    public function createTaskFromBonus(
        int $investorId,
        float $bonusAmount,
        int $investorBonusId,
        int $walletId,
        int $currencyId,
        int $bankAccountId = null
    ) {
        $task = [];

        $task['investor_id'] = $investorId;
        $task['task_type'] = Task::TASK_TYPE_BONUS_PAYMENT;
        $task['status'] = Task::TASK_STATUS_NEW;
        $task['amount'] = $bonusAmount;
        $task['wallet_id'] = $walletId;
        $task['currency_id'] = $currencyId;
        $task['bank_account_id'] = $bankAccountId;
        $task['investor_bonus_id'] = $investorBonusId;

        $this->taskRepository->create($task);
        $this->investorBonusRepository->updateHandledInvestorBonus($investorBonusId);

        return true;
    }

    /**
     * @param array $data
     */
    public function investorComment(array $data)
    {
        $this->investorRepository->investorComment($data);
    }

    /**
     * @param int $investorId
     * @param Loan $loan
     *
     * @return float|int
     */
    public function myLoanShare(int $investorId, Loan $loan)
    {
        $investorShare = $this->investorInstallmentRepository->investorShare(
            $investorId,
            $loan->loan_id
        );

        return [
            'percent' => $investorShare->share / $loan->remaining_principal * 100,
            'share' => $investorShare->share
        ];
    }

    /**
     * @param int|null $investorId
     * @param Loan $loan
     *
     * @return array
     */
    public function investorsLoanShare(?int $investorId, Loan $loan)
    {
        $investorShare = $this->investorInstallmentRepository->investorsShare(
            $investorId,
            $loan->loan_id
        );

        return [
            'percent' => $investorShare->share / $loan->remaining_principal * 100,
            'share' => $investorShare->share,
            'count' => $investorShare->count
        ];
    }

    public function getInstallmentsOutstandingAmount(int $investorId)
    {
        return $this->investorInstallmentRepository->getInstallmentsOutstandingAmount($investorId);
    }

    /**
     * @param int $investorId
     *
     * @return mixed
     * @throws ProblemException
     */
    public function investorReferrals(int $investorId)
    {
        try {
            return $this->investorRepository->getInvestorReferrals($investorId);
        } catch (\Throwable $e) {
            throw new ProblemException(
                __('common.InvestorReferralError')
            );
        }
    }

    /**
     * @param Investor $investor
     * @param string $iban
     * @param string $bic
     *
     * @return BankAccount
     *
     * @throws ProblemException
     */
    public function prepareBankAccount(
        Investor $investor,
        string $iban,
        string $bic
    ) {
        // prepare bank account
        $bankAccount = $investor->bankAccounts->where('iban', $iban)->first();

        if (empty($bankAccount)) {
            $bankAccount = $this->bankAccountRepository->create(
                $iban,
                $investor,
                (bool)empty($investor->mainBankAccount()),
                $bic
            );
        }
        if (empty($bankAccount->bank_account_id)) {
            throw new ProblemException(
                'resolvePayment()/matchDeposit(): ' . __('common.BankAccountNotFound')
            );
        }

        if (
            !empty($bic)
            && (empty($bankAccount->bic) || $bankAccount->bic != $bic)
        ) {
            $bankAccount = $this->bankAccountRepository->updateBic($bankAccount, $bic);
        }

        return $bankAccount;
    }

    /**
     * @throws Throwable
     * @throws ProblemException
     */
    public function makePayment(
        Investor $investor,
        BankAccount $bankAccount,
        ImportedPayment $payment,
        ?Task $task = null
    ) {
        try {
            DB::beginTransaction();

            $transaction = $this->addFunds(
                $investor,
                $investor->wallet(),
                $bankAccount,
                $payment->amount,
                $payment->bank_transaction_id
            );

            if (!empty($task)) {
                $this->taskRepository->finalize($task);
            }

            $this->transactionService->markImportedPaymentHandled(
                $payment,
                $transaction
            );

            DB::commit();

            // remove cache of profile/overview since we change sums
            (new CacheService)->remove(config('profile.profileDashboard') . $investor->getId());
        } catch (Throwable $e) {
            DB::rollBack();

            if (empty($task)) {
                throw $e;
            }

            return false;
        }

        return true;
    }

    /**
     * @param int $investorId
     * @param array $validated
     */
    public function addCompany(int $investorId, array $validated)
    {
        $data = [
            'investor_id' => $investorId,
            'name' => $validated['company_name'],
            'number' => $validated['company_number'],
        ];

        $this->investorCompanyRepository->create($data);
    }

    /**
     * @param int $investorId
     * @param array $files
     * @return Document
     * @throws ProblemException
     */
    public function saveCompanyDocs(int $investorId, array $filesPath): Document
    {
        $documentSave = [];
        $i = 0;
        foreach ($filesPath as $filePath) {
            $i++;

            $file = new File(\Storage::path($filePath));

            $fileStore = $this->storageService->uploadCompanyDoc($investorId, $file, $i);

            $fileData['hash'] = Hash::make($file->getFilename());
            $fileData['file_path'] = $fileStore[0];
            $fileData['file_size'] = $file->getSize();
            $fileData['file_type'] = $file->getMimeType();
            $fileData['file_type_id'] = FileType::COMPANY_ID;
            $fileData['file_name'] = $fileStore[1];

            $savedFile = $this->fileRepository->create($fileData);

            $document['document_type_id'] = DocumentType::DOCUMENT_TYPE_ID_COMPANY;
            $document['investor_id'] = $investorId;
            $document['file_id'] = $savedFile->file_id;
            $document['name'] = $savedFile->file_name;
            $document['description'] = $savedFile->file_name;

            $documentSave = $this->documentRepository->create($document);
        }

        return $documentSave;
    }
}
