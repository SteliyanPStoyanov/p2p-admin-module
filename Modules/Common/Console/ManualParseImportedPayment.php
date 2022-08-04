<?php

namespace Modules\Common\Console;

use \Exception;
use Modules\Common\Console\CommonCommand;
use Modules\Common\Entities\ImportedPayment;
use Modules\Common\Entities\Task;
use Modules\Common\Observers\ImportedPaymentObserver;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;

class ManualParseImportedPayment extends CommonCommand
{
    private $importService;
    private $limit = 1000;

    protected $name = 'script:imported-deposit:parse';
    protected $signature = 'script:imported-deposit:parse {id}';
    protected $description = 'Manual handling of imported deposits';

    public function __construct()
    {
        parent::__construct();
    }


    public function handle()
    {
        $this->log("----- START");
        $start = microtime(true);


        $importedPaymentId = (int) $this->argument('id');
        if (empty($importedPaymentId)) {
            die('Wrong param');
        }


        $payment = ImportedPayment::where([
            ['imported_payment_id', '=', $importedPaymentId],
        ])->first();
        if ('new' != $payment->status) {
            die('We can handle only payments in status NEW(' . $payment->status . ')');
        }


        $task = Task::where([
        ['imported_payment_id',
            '=',
            $payment->imported_payment_id]
        ])->first();

        if (!empty($payment->transaction_id)) {
            die('There is a task for the payment, task #' . $task->task_id);
        }


        $observer = \App::make(ImportedPaymentObserver::class);


        list($investor, $iban, $bic) = $observer->resolveBasis($payment->basis);

        dump(
            'Investor #' . $investor->investor_id,
            'IBAN:' . $iban,
            'BIC:' . $bic
        );


        if (empty($investor->investor_id) || empty($iban) || empty($bic)) {
            die('Can not parse payment->basis');
        }



        $this->log('Exec.time: ' . round((microtime(true) - $start), 2) . ' second(s)');
        return true;
    }
}
