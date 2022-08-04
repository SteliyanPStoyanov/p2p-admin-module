<?php

namespace Modules\Common\Console;

use Illuminate\Console\Command;
use Carbon\Carbon;
use Modules\Common\Entities\Investor;
use Modules\Common\Repositories\InvestorRepository;
use Modules\Common\Services\ImportService;
use Modules\Common\Services\InvestorService;
use Modules\Common\Services\LogService;
use Modules\Communication\Entities\EmailTemplate;
use Modules\Communication\Services\EmailService;

class DailyVerifyRecall extends CommonCommand
{
    protected EmailService $emailService;
    protected InvestorService $investorService;
    protected LogService $logService;

    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'script:daily-verify-recall';

    protected $logChannel = 'daily_verify_recall';
    /**
     * @var string
     */
    protected $signature = 'script:daily-verify-recall';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send email to user with status registered.';

    /**
     * DailyVerifyRecall constructor.
     *
     * @param EmailService $emailService
     * @param InvestorService $investorService
     * @param LogService $logService
     */
    public function __construct(
        EmailService $emailService,
        InvestorService $investorService,
        LogService $logService
    ) {
        $this->emailService = $emailService;
        $this->investorService = $investorService;
        $this->logService = $logService;

        parent::__construct();
    }

    /**
     * @return bool
     */
    public function handle()
    {
        $this->log("----- START");
        $log = $this->logService->createCronLog($this->getNameForDb());
        $start = microtime(true);

        $emailTemplateId = EmailTemplate::TEMPLATE_SEEDER_ARRAY['continue_verification']['id'];
        $status = Investor::INVESTOR_STATUS_REGISTERED;
        $additionalData = [$status . '_recall_at' => Carbon::now()];

        $emailSendCount = 0;
        $investors = $this->investorService->recall(
            Investor::INVESTOR_CONTINUE_VERIFICATION_DAYS,
            $status
        )->chunkById(
            config('common.investorEmailChunk'),
            function ($investors) use ($additionalData, $emailTemplateId, $status, $log, $start, &$emailSendCount) {
                if (empty($investors)) {
                    $log->finish($start, 0, 0, 'There are no investors to send');
                    return true;
                }
                foreach ($investors as $investor) {
                    $sendEmail = $this->emailService->sendEmail(
                        $investor->investor_id,
                        $emailTemplateId,
                        $investor->email,
                        Carbon::now(),
                        $additionalData
                    );

                    if ($sendEmail == true) {
                        $this->investorService->investorRecallUpdate(
                            $investor->investor_id,
                            [$status . '_recall_at' => Carbon::now()]
                        );
                    }
                }
                $emailSendCount += count($investors);
            },
            'investor.investor_id',
            'investor_id'
        );

        $log->finish($start, $emailSendCount, 0, 'Email send: ' . $emailSendCount);
        $this->log('Email send: ' . $emailSendCount);
        $this->log('Exec.time: ' . round((microtime(true) - $start), 2) . ' second(s)');

        return true;
    }
}
