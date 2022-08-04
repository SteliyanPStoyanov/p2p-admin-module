<?php

namespace Tests\Unit;

use App;
use Artisan;
use Carbon\Carbon;
use DB;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Modules\Common\Entities\Investor;
use Modules\Common\Entities\Transaction;
use Modules\Common\Entities\Wallet;
use Modules\Common\Services\InvestorService;
use Modules\Common\Services\TransactionService;
use Tests\TestCase;
use Tests\Traits\TestDataTrait;

class AccountStatementTest extends TestCase
{
    use TestDataTrait;
    use WithoutMiddleware;

    protected $investorService;
    protected $transactionService;
    protected string $createdAtFrom = '01.01.2021';
    protected string $createdAtTo = '13.05.2021';

    protected function setUp(): void
    {
        parent::setUp();
        $this->investorService = App::make(InvestorService::class);
        $this->transactionService = App::make(TransactionService::class);
    }

    public function testSummaryDetailsTotalIncome()
    {
        $investor = $this->prepareInvestor('investor_email' . time() . '@test.bg');
        $investor->status = Investor::INVESTOR_STATUS_UNREGISTERED;
        $investor->created_at = Carbon::yesterday();
        $investor->save();

        $wallet = $this->prepareWallet($investor);
        $this->prepareWalletHistory($investor, $wallet);
        $this->prepareTransaction($investor, $wallet);

        $transactionsSum = $this->transactionService->transactionsSum(
            $investor,
            $this->createdAtFrom,
            '05.13.2021',
            null
        );

        $summaryTableData = Transaction::getTransactionsKeyProfile($transactionsSum);

        $filterData['createdAt']['from'] = dbDate(
            $this->createdAtFrom
        );
        $filterData['createdAt']['to'] = dbDate(
            $this->createdAtTo
        );

        $transactionList = $this->transactionService->transactionList(
            $investor,
            $filterData,
            null
        );

        $transactionListType = self::listSumByType($transactionList);

        //Deposited match
        $this->assertEquals(
            array_sum(array_column($transactionListType, 'deposit')),
            $summaryTableData['Deposited funds']->sum
        );
        $this->assertEquals(
            $wallet->deposit,
            $summaryTableData['Deposited funds']->sum
        );
        $this->assertEquals(
            $wallet->deposit,
            array_sum(array_column($transactionListType, 'deposit'))
        );


        $this->assertEquals(
            (array_sum(array_column($transactionListType, 'installment_repayment_interest'))
                + array_sum(array_column($transactionListType, 'repayment_interest'))),
            $summaryTableData['Interest received']->sum
        );

        $this->assertEquals(
            array_sum(array_column($transactionListType, 'early_repayment_interest')),
            $summaryTableData['Interest received from early repayment']->sum
        );

        $this->assertEquals(
            (array_sum(array_column($transactionListType, 'buyback_overdue_interest'))
                + array_sum(array_column($transactionListType, 'buyback_manual_interest'))),
            $summaryTableData['Interest received from loan repurchase']->sum
        );

        $this->assertEquals(
            array_sum(array_column($transactionListType, 'investment')),
            $summaryTableData['Investments in loans']->sum
        );

        $this->assertEquals(
            array_sum(array_column($transactionListType, 'early_repayment_late_interest')),
            $summaryTableData['Late interest from early repayment']->sum
        );

        $this->assertEquals(
            (array_sum(array_column($transactionListType, 'installment_repayment_late_interest'))
                + array_sum(array_column($transactionListType, 'repayment_late_interest'))),
            $summaryTableData['Late interest received']->sum
        );

        $this->assertEquals(
            array_sum(array_column($transactionListType, 'buyback_overdue_late_interest')),
            $summaryTableData['Late interest received from loan repurchase']->sum
        );

        $this->assertEquals(
            (array_sum(array_column($transactionListType, 'installment_repayment_principal'))
                + array_sum(array_column($transactionListType, 'repayment_principal'))),
            $summaryTableData['Principal received']->sum
        );

        $this->assertEquals(
            array_sum(array_column($transactionListType, 'early_repayment_principal')),
            $summaryTableData['Principal received from early repayment']->sum
        );

        $this->assertEquals(
            (array_sum(array_column($transactionListType, 'buyback_overdue_principal'))
                + array_sum(array_column($transactionListType, 'buyback_manual_principal'))),
            $summaryTableData['Principal received from loan repurchase']->sum
        );

        //Withdrawn funds
        $this->assertEquals(
            array_sum(array_column($transactionListType, 'withdraw')),
            $summaryTableData['Withdrawn funds']->sum
        );
        $this->assertEquals(
            $wallet->withdraw,
            $summaryTableData['Withdrawn funds']->sum
        );
        $this->assertEquals(
            array_sum(array_column($transactionListType, 'withdraw')),
            $wallet->withdraw,
        );

        //Withdrawn income

        $incomeInterestDetails = array_sum(array_column($transactionListType, 'installment_repayment_interest'))
            + array_sum(array_column($transactionListType, 'installment_repayment_late_interest'))
            + array_sum(array_column($transactionListType, 'early_repayment_interest'))
            + array_sum(array_column($transactionListType, 'buyback_overdue_interest'))
            + array_sum(array_column($transactionListType, 'buyback_overdue_late_interest'))
            + array_sum(array_column($transactionListType, 'early_repayment_late_interest'))
            + array_sum(array_column($transactionListType, 'repayment_late_interest'))
            + array_sum(array_column($transactionListType, 'repayment_interest'))
            + array_sum(array_column($transactionListType, 'buyback_manual_interest'));

        $this->assertEquals(
            $wallet->income,
            $incomeInterestDetails
        );

        $incomeInterestSummary = $summaryTableData['Interest received']->sum
            + $summaryTableData['Interest received from early repayment']->sum
            + $summaryTableData['Interest received from loan repurchase']->sum
            + $summaryTableData['Late interest from early repayment']->sum
            + $summaryTableData['Late interest received from loan repurchase']->sum
            + $summaryTableData['Late interest received']->sum;

        $this->assertEquals(
            $wallet->income,
            $incomeInterestSummary
        );

        //Interest
        $interestDetails = array_sum(array_column($transactionListType, 'installment_repayment_interest'))
            + array_sum(array_column($transactionListType, 'early_repayment_interest'))
            + array_sum(array_column($transactionListType, 'buyback_overdue_interest'))
            + array_sum(array_column($transactionListType, 'repayment_interest'))
            + array_sum(array_column($transactionListType, 'buyback_manual_interest'));

        $this->assertEquals(
            $wallet->interest,
            $interestDetails
        );

        $interestSummary = $summaryTableData['Interest received']->sum
            + $summaryTableData['Interest received from early repayment']->sum
            + $summaryTableData['Interest received from loan repurchase']->sum;

        $this->assertEquals(
            $wallet->interest,
            $interestSummary
        );

        $this->assertEquals(
            $interestDetails,
            $interestSummary
        );

        //Late Interest
        $lateInterestDetails = array_sum(array_column($transactionListType, 'installment_repayment_late_interest'))
            + array_sum(array_column($transactionListType, 'buyback_overdue_late_interest'))
            + array_sum(array_column($transactionListType, 'early_repayment_late_interest'))
            + array_sum(array_column($transactionListType, 'repayment_late_interest'));

        $this->assertEquals(
            $wallet->late_interest,
            $lateInterestDetails
        );

        $lateInterestSummary = $summaryTableData['Late interest from early repayment']->sum
            + $summaryTableData['Late interest received from loan repurchase']->sum
            + $summaryTableData['Late interest received']->sum;

        $this->assertEquals(
            $wallet->late_interest,
            $lateInterestSummary
        );

        $this->assertEquals(
            $lateInterestDetails,
            $lateInterestSummary
        );


        DB::table('wallet_history')->where('investor_id', $investor->investor_id)->delete();
        DB::table('transaction')->where('investor_id', $investor->investor_id)->delete();
        $this->removeTestData($investor);
    }


    /**
     * @param Investor $investor
     * @return Wallet
     */
    public
    function prepareWallet(
        Investor $investor
    ): Wallet {
        $str = file_get_contents(__DIR__ . '/sql/wallet.sql');
        $patternInvestorId = '/103720/';
        $sql = preg_replace($patternInvestorId, $investor->investor_id, $str);
        DB::unprepared($sql);
        DB::statement(
            "SELECT setval('wallet_wallet_id_seq', (SELECT MAX(wallet_id) FROM wallet));"
        );

        return $investor->wallet();
    }

    /**
     * @param Investor $investor
     * @param Wallet $wallet
     */
    public
    function prepareWalletHistory(
        Investor $investor,
        Wallet $wallet
    ) {
        $str = file_get_contents(__DIR__ . '/sql/walletHistory.sql');
        $patternInvestorId = '/103720/';
        $patternWalletId = '/10145/';
        $sql = preg_replace($patternInvestorId, $investor->investor_id, $str);
        $sql = preg_replace($patternWalletId, $wallet->wallet_id, $sql);
        DB::unprepared($sql);
        DB::statement(
            "SELECT setval('wallet_history_wallet_history_id_seq', (SELECT MAX(wallet_history_id) FROM wallet_history));"
        );
    }

    /**
     * @param Investor $investor
     * @param Wallet $wallet
     */
    public
    function prepareTransaction(
        Investor $investor,
        Wallet $wallet
    ) {
        $str = file_get_contents(__DIR__ . '/sql/transaction.sql');
        $patternInvestorId = '/103720/';
        $patternWalletId = '/10145/';
        $sql = preg_replace($patternInvestorId, $investor->investor_id, $str);
        $sql = preg_replace($patternWalletId, $wallet->wallet_id, $sql);
        DB::unprepared($sql);
        DB::statement(
            "SELECT setval('transaction_transaction_id_seq', (SELECT MAX(transaction_id) FROM transaction));"
        );
    }


    /**
     * @param $transactionList
     * @return array
     */
    public function listSumByType($transactionList): array
    {
        $total = [];
        foreach ($transactionList as $transaction) {
            if ($transaction->fin_type != 'amount') {
                $lavelKey = $transaction->type . '_' . $transaction->fin_type;
            } else {
                $lavelKey = $transaction->type;
            }

            if ($transaction->type == Transaction::TYPE_PROFILE_INTEREST) {
                $total[] = [$lavelKey => $transaction->accrued_interest + $transaction->interest + $transaction->late_interest];
            } elseif ($transaction->type == Transaction::TYPE_PROFILE_PRINCIPAL) {
                $total[] = [$lavelKey => $transaction->principal];
            } else {
                $total[] = [$lavelKey => $transaction->amount];
            }
        }

        return $total;
    }

}
