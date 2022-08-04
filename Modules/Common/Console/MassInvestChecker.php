<?php

namespace Modules\Common\Console;

use Carbon\Carbon;
use Illuminate\Support\Facades\Mail;
use Modules\Common\Services\InvestService;

/**
 * search transactions without investment_id
 * search loan_amount_available without investment_id
 * search investments without investor plan
 * search investments without loan contracts
 * send report
 */
class MassInvestChecker extends CommonCommand
{
    const PERIOD = 20; // minutes - set to 20min, cause it's run every 15min

    private $data = [];

    protected $name = 'script:mass-invest:checker';
    protected $signature = 'script:mass-invest:checker';
    protected $logChannel = 'mass_invest_checker';
    protected $description = 'Search lost records without links, send report';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle(InvestService $service)
    {
        $this->log("----- Mass Invest Checker -----");
        $start = microtime(true);
        $investorId = null;


        $investments1 = $service->getLostInvestmentsVsTransactions($investorId, self::PERIOD);
        if (!empty($investments1)) {
            $this->addData($investments1, 'link with transaction');
        }

        $investments2 = $service->getLostInvestmentsVsLoanAmountStats($investorId, self::PERIOD);
        if (!empty($investments2)) {
            $this->addData($investments2, 'link with loan_amount_available');
        }

        $investments3 = $service->getLostInvestmentsVsLoanContracts($investorId, self::PERIOD);
        if (!empty($investments3)) {
            $this->addData($investments3, 'loan contract not created');
        }

        $investments4 = $service->getLostInvestmentsVsInvestorPlans($investorId, self::PERIOD);
        if (!empty($investments4)) {
            $this->addData($investments4, 'investor plan not created');
        }


        if (empty($this->data)) {
            $this->log("No lost records, nothing todo");
        } else {
            $this->sendReport();
            $this->log("Report has been sent. Total lost records: " . count($this->data));
        }


        $this->log('Exec.time: ' . round((microtime(true) - $start), 2) . ' second(s)');
    }

    private function addData(array $investments, string $reason): void
    {
        foreach ($investments as $investment) {

            if (!isset($this->data[$investment->investment_id])) {
                $this->data[$investment->investment_id] = (object) [
                    'id' => $investment->investment_id,
                    'investor' => $investment->investor_id,
                    'loan' => $investment->loan_id,
                    'amount' => $investment->amount,
                    'date' => $investment->created_at,
                    'reasons' => [
                        $reason
                    ],
                ];

                continue;
            }

            array_push(
                $this->data[$investment->investment_id]->reasons,
                $reason
            );
        }
    }

    private function sendReport()
    {
        $env = strtoupper(env('APP_ENV'));
        $to = config('mail.log_monitor')['receivers'];
        $from = config('mail.log_monitor')['sender'];
        $date = (Carbon::now())->format('Y-m-d');
        $title = 'Mass-invest: Lost Investments (' . $env . '): ' . $date;
        $html = $this->getHtml();

        Mail::send([], [], function($message) use($to, $from, $title, $html) {
            $message->from($from['from'], $from['name']);
            $message->to($to);
            $message->subject($title);
            $message->setBody($html, 'text/html');
        });
    }

    private function getHtml(): string
    {
        $html = '
            <html>
            <table>
                <tr>
                    <td>Investment</td>
                    <td>Investor</td>
                    <td>Loan</td>
                    <td>Amount</td>
                    <td>Date</td>
                    <td>Reason</td>
                </tr>
                ';

        foreach ($this->data as $row) {
            $html .= '
                <tr>
                    <td>' . $row->id . '</td>
                    <td>' . $row->investor . '</td>
                    <td>' . $row->loan . '</td>
                    <td>' . $row->amount . '€</td>
                    <td>' . $row->date->format('Y-m-d_H:i:s') . '</td>
                    <td>' . implode(', ', $row->reasons) . '</td>
                </tr>
            ';
        }

        $html .= '
            </table>
            </html>
        ';

        return $html;
    }
}
