<?php

namespace Modules\Common\Console;

use Carbon\Carbon;
use DB;
use Modules\Common\Entities\Country;
use Modules\Common\Entities\CronLog;
use Modules\Common\Entities\Currency;
use Modules\Common\Entities\File;
use Modules\Common\Entities\FileType;
use Modules\Common\Entities\Loan;
use Modules\Common\Entities\Originator;
use Modules\Common\Entities\Transaction;
use Modules\Common\Services\LogService;
use Modules\Communication\Entities\Email;
use Modules\Communication\Entities\EmailTemplate;
use Modules\Core\Services\StorageService;
use Throwable;

class AppChecker extends CommonCommand
{
    protected const WEBSITE_URL = 'http://193.8.4.24';
    protected $name = 'script:check-app';
    protected $signature = 'script:check-app';
    protected $logChannel = 'app_checker';
    protected $description = 'Check website and scripts';
    protected $actions = [
        'checkDbConnection' => 'Check DB connection',
        'checkWebsiteResponse' => 'Check website response',
        'checkAdminResponse' => 'Check admin response',
        'checkImportLoans' => 'Check import loans script',
        'checkImportInstallments' => 'Check import installments script',
        'checkImportRepaidInstallments' => 'Check import repaid installments script',
        'checkImportRepaidLoans' => 'Check import repaid loans script',
        'checkImportedUnlistedLoans' => 'Check import unlisted loans script',
        'checkDistributeLoan' => 'Check distribute loans',
        'checkDistributeInstallments' => 'Check distribute installments',
        'checkDailySettlement' => 'Check daily settlements',
        'checkPaymentStatus' => 'Check payment statuses',
        'checkMaturityRefresh' => 'Check maturity refresh',
        'checkInterestRefresh' => 'Check interest refresh',
        'checkAutoRebuy' => 'Check auto rebuy',
        'checkArhiver' => 'Check archiver',
        'checkVerifyRegisterRecalls' => 'Check verify and register recall',
    ];
    protected $searchSentences = [
        '/' => 'Start generating high returns on your savings today.',
        '/login' => 'Log in to your account',
        '/register' => 'Create an account',
        '/invest' => 'Primary market',
    ];
    protected string $adminSearchSentence = 'Try to login into Afranga admin pane!';
    protected LogService $logService;


    public function __construct(LogService $logService) {
        $this->logService = $logService;
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     *
     * @throws \Exception
     */
    public function handle()
    {
        $this->log("----- START");
        $log = $this->logService->createCronLog($this->getNameForDb());
        $start = microtime(true);

        $bar = $this->output->createProgressBar(count($this->actions));
        $bar->start();

        try {
            foreach ($this->actions as $action => $message) {
                $result = $this->{$action}();

                sleep(1);
                $bar->advance();

                $result ?
                    $this->info(' ' . $message . ' -  OK') :
                    $this->error(' ' . $message . ' - NOT OK');
            }
        } catch (Throwable $e) {
            $this->log(
                'Something failed. Error' . $e->getMessage()
                . ', file: ' . $e->getFile() . ', line: ' . $e->getLine()
            );
        }

        $bar->finish();

        $log->finish($start, null, null, null);
        $this->log('Exec.time: ' . round((microtime(true) - $start), 2) . ' second(s)');

        return true;
    }

    public function checkDbConnection()
    {
        $result = DB::selectOne(
            '
                SELECT COUNT(l.loan_id) AS count FROM loan AS l;
            '
        );

        return (bool)$result->count > 0;
    }

    public function checkWebsiteResponse()
    {
        $appUrl = getenv('APP_URL');

        if (strpos($appUrl, 'localhost')) {
            $appUrl = self::WEBSITE_URL;
        }

        foreach ($this->searchSentences as $key => $searchSentence) {
            $response = file_get_contents($appUrl . $key);
            if (strpos($response, $searchSentence) === false) {
                return false;
            }
        }

        return true;
    }

    public function checkAdminResponse()
    {
        $appUrl = getenv('APP_URL');

        if (strpos($appUrl, 'localhost')) {
            $appUrl = self::WEBSITE_URL;
        }

        $response = file_get_contents($appUrl . '/admin/login');

        return (bool)strpos($response, $this->adminSearchSentence);
    }

    public function checkImportLoans()
    {
        $files = File::whereDate(
            'created_at',
            '>=',
            Carbon::yesterday()
                ->setTime(0, 0, 0)
        )
            ->whereDate('created_at', '<=', Carbon::yesterday()->setTime(23, 59, 59))
            ->where('file_type_id', FileType::NEW_LOANS_ID)
            ->where('deleted', 0)
            ->get();

        $importLoans = \App::make(ImportLoans::class);
        $importLoansName = $importLoans->getNameForDb();

        foreach ($files as $file) {
            $importedLoanWithCronLog = CronLog::where(
                [
                    'command' => $importLoansName,
                    'file' => $file
                ]
            )->get();

            if ($importedLoanWithCronLog->count() < 1) {
                return false;
            }
        }

        return true;
    }

    public function checkImportInstallments()
    {
        $loansWithoutInstallment = DB::selectOne(
            '
                SELECT
                    l.loan_id,
                    i.installment_id
                FROM
                     loan AS l
                LEFT JOIN installment AS i ON i.loan_id = l.loan_id
                WHERE l.created_at BETWEEN :today_from AND :today_to
                AND i.installment_id IS NULL
            ',
            [
                'today_from' => Carbon::today()->setTime(0, 0, 0),
                'today_to' => Carbon::today()->setTime(23, 59, 59),
            ]
        );

        return (bool)$loansWithoutInstallment == null;
    }

    public function checkImportRepaidInstallments()
    {
        $repaidInstallments = DB::selectOne(
            '
                SELECT
                    ri.repaid_installment_id
                FROM
                    repaid_installment AS ri
                LEFT JOIN installment AS i ON ri.lender_installment_id = i.lender_installment_id
                WHERE (i.paid = 0
                OR ri.handled = 0)
                AND ri.created_at BETWEEN :today_from AND :today_to
            ',
            [
                'today_from' => Carbon::today()->setTime(0, 0, 0),
                'today_to' => Carbon::today()->setTime(23, 59, 59),
            ]
        );

        return (bool)$repaidInstallments == null;
    }

    public function checkImportRepaidLoans()
    {
        $repaidLoans = DB::selectOne(
            '
                SELECT
                    rl.repaid_loan_id
                FROM
                    repaid_loan AS rl
                LEFT JOIN loan AS l ON rl.lender_id = l.lender_id
                WHERE
                    rl.created_at BETWEEN :today_from AND :today_to
                AND
                    (
                        l.unlisted = 0
                        OR l.unlisted_at NOT BETWEEN :today_from AND :today_to
                        OR rl.handled = 0
                    )
            ',
            [
                'today_from' => Carbon::today()->setTime(0, 0, 0),
                'today_to' => Carbon::today()->setTime(23, 59, 59),
            ]
        );

        return (bool)$repaidLoans == null;
    }

    public function checkImportedUnlistedLoans()
    {
        $files = File::whereDate(
            'created_at',
            '>=',
            Carbon::yesterday()
                ->setTime(0, 0, 0)
        )
            ->whereDate('created_at', '<=', Carbon::yesterday()->setTime(23, 59, 59))
            ->where('file_type_id', FileType::UNLISTED_LOANS_ID)
            ->where('deleted', 0)
            ->get();

        $importLoans = \App::make(ImportUnlistedLoans::class);
        $importLoansName = $importLoans->getNameForDb();

        foreach ($files as $file) {
            $importedLoanWithCronLog = CronLog::where(
                [
                    'command' => $importLoansName,
                    'file' => $file
                ]
            )->get();

            if ($importedLoanWithCronLog->count() < 1) {
                return false;
            }
        }

        return true;
    }

    public function checkDistributeLoan()
    {
        $repaidLoansCount = DB::selectOne(
            '
                SELECT
                    COUNT(rl.lender_id)
                FROM
                    repaid_loan AS rl
                WHERE
                    rl.created_at BETWEEN :today_from AND :today_to
            ',
            [
                'today_from' => Carbon::today()->setTime(0, 0, 0),
                'today_to' => Carbon::today()->setTime(23, 59, 59),
            ]
        )->count;

        $loansUnlistedCount = DB::selectOne(
            '
                SELECT
                    COUNT(l.loan_id)
                FROM
                    loan AS l
                WHERE
                    l.unlisted_at BETWEEN :today_from AND :today_to
            ',
            [
                'today_from' => Carbon::today()->setTime(0, 0, 0),
                'today_to' => Carbon::today()->setTime(23, 59, 59),
            ]
        )->count;

        if ($repaidLoansCount != $loansUnlistedCount) {
            return false;
        }

        $loansUnlistedWithInvestmentsCount = DB::selectOne(
            '
                SELECT
                    COUNT(l.loan_id)
                FROM
                    loan AS l
                INNER JOIN
                    investment AS i ON i.loan_id = l.loan_id
                WHERE
                    l.unlisted_at BETWEEN :today_from AND :today_to
            ',
            [
                'today_from' => Carbon::today()->setTime(0, 0, 0),
                'today_to' => Carbon::today()->setTime(23, 59, 59),
            ]
        )->count;

        $transactionsCount = \DB::selectOne(
            "
                SELECT
                    COUNT(t.transaction_id)
                FROM
                    transaction AS t
                WHERE
                    t.created_at BETWEEN :today_from AND :today_to
                AND (t.type = '" . Transaction::TYPE_REPAYMENT . "' OR t.type = '" . Transaction::TYPE_EARLY_REPAYMENT . "')
                AND t.active = 1 AND t.deleted = 0
            ",
            [
                'today_from' => Carbon::today()->setTime(0, 0, 0),
                'today_to' => Carbon::today()->setTime(23, 59, 59),
            ]
        )->count;

        if ($loansUnlistedWithInvestmentsCount != $transactionsCount) {
            return false;
        }

        $investorInstallmentNotPaid = \DB::selectOne(
            '
                SELECT
                    ii.investor_installment_id
                FROM
                    investor_installment AS ii
                INNER JOIN
                    installment AS i ON i.installment_id = ii.installment_id
                INNER JOIN
                    loan AS l ON ii.loan_id = l.loan_id
                INNER JOIN
                    repaid_loan AS rl ON l.lender_id = rl.lender_id
                WHERE
                    (ii.paid = 0 OR i.paid = 0)
                AND
                    rl.created_at BETWEEN :today_from AND :today_to
            ',
            [
                'today_from' => Carbon::today()->setTime(0, 0, 0),
                'today_to' => Carbon::today()->setTime(23, 59, 59),
            ]
        );

        return (bool)$investorInstallmentNotPaid == null;
    }

    public function checkDistributeInstallments()
    {
        $repaidInstallmentsCount = DB::selectOne(
            '
                SELECT
                    COUNT(ri.lender_id)
                FROM
                    repaid_installment AS ri
                WHERE
                    ri.created_at BETWEEN :today_from AND :today_to
            ',
            [
                'today_from' => Carbon::today()->setTime(0, 0, 0),
                'today_to' => Carbon::today()->setTime(23, 59, 59),
            ]
        )->count;

        $installmentsCount = DB::selectOne(
            '
                SELECT
                    COUNT(i.installment_id)
                FROM
                    installment AS i
                WHERE
                    i.paid_at BETWEEN :today_from AND :today_to
            ',
            [
                'today_from' => Carbon::today()->setTime(0, 0, 0),
                'today_to' => Carbon::today()->setTime(23, 59, 59),
            ]
        )->count;

        if ($repaidInstallmentsCount != $installmentsCount) {
            return false;
        }

        $transactionsCount = \DB::selectOne(
            "
                SELECT
                    COUNT(t.transaction_id)
                FROM
                    transaction AS t
                WHERE
                    t.created_at BETWEEN :today_from AND :today_to
                AND (t.type = '" . Transaction::TYPE_REPAYMENT . "' OR t.type = '" . Transaction::TYPE_EARLY_REPAYMENT . "')
                 AND t.active = 1 AND t.deleted = 0
            ",
            [
                'today_from' => Carbon::today()->setTime(0, 0, 0),
                'today_to' => Carbon::today()->setTime(23, 59, 59),
            ]
        )->count;

        $installmentsWithInvestmentCount = DB::selectOne(
            '
                SELECT
                    COUNT(i.installment_id)
                FROM
                    installment AS i
                INNER JOIN
                    loan AS l ON i.loan_id = l.loan_id
                INNER JOIN
                    investment AS inv ON inv.loan_id = l.loan_id
                WHERE
                    i.paid_at BETWEEN :today_from AND :today_to
            ',
            [
                'today_from' => Carbon::today()->setTime(0, 0, 0),
                'today_to' => Carbon::today()->setTime(23, 59, 59),
            ]
        )->count;

        if ($installmentsWithInvestmentCount != $transactionsCount) {
            return false;
        }

        $investorInstallmentNotPaid = \DB::selectOne(
            '
                SELECT
                    ii.investor_installment_id
                FROM
                    investor_installment AS ii
                INNER JOIN
                    installment AS i ON i.installment_id = ii.installment_id
                INNER JOIN
                    loan AS l ON ii.loan_id = l.loan_id
                INNER JOIN
                    repaid_installment AS ri ON l.lender_id = ri.lender_id
                WHERE
                    (ii.paid = 0 OR i.paid = 0)
                AND
                    ri.created_at BETWEEN :today_from AND :today_to
            ',
            [
                'today_from' => Carbon::today()->setTime(0, 0, 0),
                'today_to' => Carbon::today()->setTime(23, 59, 59),
            ]
        );

        return (bool)$investorInstallmentNotPaid == null;
    }

    public function checkDailySettlement()
    {
        $fileName = 'settlement_' . Carbon::today()->format('Y-m-d');
        $pathToFolder = StorageService::PATH_TO_SETTLEMENT;
        $storageService = \App::make(StorageService::class);

        return $storageService::hasFile($pathToFolder . $fileName . '.xlsx');
    }

    public function checkPaymentStatus()
    {
        // TODO: check in nefin

        $paymentStatusCommand = \App::make(DailyPaymentStatusRefresh::class);
        $cronLog = CronLog::where(
            [
                'command' => $paymentStatusCommand->getNameForDb()
            ]
        )->whereDate(
            'created_at',
            '>=',
            Carbon::today()->setTime(0, 0, 0)
        )
            ->whereDate('created_at', '<=', Carbon::today()->setTime(23, 59, 59))->first();

        if (empty($cronLog)) {
            return false;
        }

        $checkPaymentStatuses = [
            Loan::PAY_STATUS_CURRENT => 0,
            Loan::PAY_STATUS_1_15 => [1, 15],
            Loan::PAY_STATUS_16_30 => [16, 30],
            Loan::PAY_STATUS_31_60 => [31, 60],
            Loan::PAY_STATUS_LATE => [61, PHP_INT_MAX],
        ];

        foreach ($checkPaymentStatuses as $paymentStatus => $days) {
            $result = DB::selectOne(
                "
                    SELECT
                        l.loan_id
                    FROM
                        loan AS l
                    WHERE
                        l.payment_status = :payment_status
                    AND
                      l.overdue_days " . (!is_array($days) ? "!= " . $days : "BETWEEN " . implode(' AND ', $days)) . "
                ",
                [
                    'payment_status' => $paymentStatus,
                ]
            );

            if ($result != null) {
                return false;
            }
        }

        return true;
    }

    public function checkMaturityRefresh()
    {
        $query = DB::selectOne(
            '
                SELECT
                    p.portfolio_id
                FROM portfolio AS p
                WHERE
                    p.date < :today
            ',
            [
                'today' => Carbon::today(),
            ]
        );

        return $query == null;
    }

    public function checkInterestRefresh()
    {
        $query = DB::selectOne(
            '
                SELECT
                    l.loan_id
                FROM loan AS l
                WHERE
                  l.interest_updated_at BETWEEN :today_from AND :today_to
            ',
            [
                'today_from' => Carbon::today()->setTime(0, 0, 0),
                'today_to' => Carbon::today()->setTime(23, 59, 59),
            ]
        );

        return $query != null;
    }

    public function checkAutoRebuy()
    {
        $autoRebuyCommand = \App::make(DailyAutoRebuy::class);
        $cronLog = CronLog::where(
            [
                'command' => $autoRebuyCommand->getNameForDb()
            ]
        )->whereDate(
            'created_at',
            '>=',
            Carbon::today()->setTime(0, 0, 0)
        )
            ->whereDate('created_at', '<=', Carbon::today()->setTime(23, 59, 59))->first();

        return !empty($cronLog);
    }

    public function checkArhiver()
    {
        //Check wallets
        $wallets = DB::selectOne(
            '
                SELECT
                    wh.wallet_history_id
                FROM wallet_history AS wh
                WHERE
                  wh.archived_at BETWEEN :today_from AND :today_to
            ',
            [
                'today_from' => Carbon::today()->setTime(0, 0, 0),
                'today_to' => Carbon::today()->setTime(23, 59, 59),
            ]
        );
        if ($wallets == null) {
            return false;
        }

        //Check portfolios
        $portfolios = DB::selectOne(
            '
                SELECT
                    ph.portfolio_history_id
                FROM portfolio_history AS ph
                WHERE
                  ph.archived_at BETWEEN :today_from AND :today_to
            ',
            [
                'today_from' => Carbon::today()->setTime(0, 0, 0),
                'today_to' => Carbon::today()->setTime(23, 59, 59),
            ]
        );
        if ($portfolios == null) {
            return false;
        }

        //Check investor_installments
        $investorInstallments = DB::selectOne(
            '
                SELECT
                    ii.investor_installment_id
                FROM investor_installment AS ii
                INNER JOIN loan AS l ON l.loan_id = ii.loan_id
                WHERE
                  l.status != :status_active
            ',
            [
                'status_active' => Loan::STATUS_ACTIVE,
            ]
        );
        if ($investorInstallments !== null) {
            return false;
        }

        return true;
    }

    public function checkVerifyRegisterRecalls()
    {
        $verifyRecall = DB::selectOne(
            '
                SELECT
                    e.email_template_id
                FROM email AS e
                WHERE
                  e.send_at BETWEEN :today_from AND :today_to
                AND e.email_template_id = :template_id
            ',
            [
                'today_from' => Carbon::today()->setTime(0, 0, 0),
                'today_to' => Carbon::today()->setTime(23, 59, 59),
                'template_id' => EmailTemplate::TEMPLATE_SEEDER_ARRAY['continue_verification']['id'],
            ]
        );

        if ($verifyRecall == null) {
            return false;
        }

        $registerRecall = DB::selectOne(
            '
                SELECT
                    e.email_template_id
                FROM email AS e
                WHERE
                  e.send_at BETWEEN :today_from AND :today_to
                AND e.email_template_id = :template_id
            ',
            [
                'today_from' => Carbon::today()->setTime(0, 0, 0),
                'today_to' => Carbon::today()->setTime(23, 59, 59),
                'template_id' => EmailTemplate::TEMPLATE_SEEDER_ARRAY['continue_registration']['id'],
            ]
        );

        return $registerRecall != null;
    }
}
