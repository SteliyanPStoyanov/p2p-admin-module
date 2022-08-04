<?php

namespace Modules\Common\Console;

use Modules\Common\Repositories\ArchiveRepository;
use Modules\Common\Services\LogService;
use Throwable;

class DailyArchiver extends CommonCommand
{
    protected $name = 'script:daily-archive';
    protected $signature = 'script:daily-archive';
    protected $logChannel = 'daily_archiver';
    protected $description = 'Archive wallet, portfolio, investor_installment, registration_attempt, blocked_ip tables.';

    protected ArchiveRepository $archiveRepository;
    protected LogService $logService;

    /**
     * Create a new command instance.
     *
     * @param ArchiveRepository $archiveRepository
     * @param LogService $logService
     */
    public function __construct(
        ArchiveRepository $archiveRepository,
        LogService  $logService
    ) {
        $this->archiveRepository = $archiveRepository;
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

        $archivedEntries = 0;

        try {

            $archivedEntries += $w = $this->archiveRepository->wallets();
            $this->log("wallets: " . $w);

            $archivedEntries += $p = $this->archiveRepository->portfolios();
            $this->log("portfolios: " . $p);

            $archivedEntries += $ii = $this->archiveRepository->investorInstallments();
            $this->log("investor installment: " . $ii);

            $archivedEntries += $ra = $this->archiveRepository->registrationAttempts();
            $this->log("registration attempts: " . $ra);

            $archivedEntries += $la = $this->archiveRepository->loginAttempts();
            $this->log("login attempts: " . $la);

            $archivedEntries += $bi = $this->archiveRepository->blockedIps();
            $this->log("blocked ips: " . $bi);

            $archivedEntries += $ul = $this->archiveRepository->unlistedLoans();
            $this->log("unlisted loans(not handled): " . $ul);

        } catch (Throwable $e) {
            $this->log(
                'Archiving failed. Error' . $e->getMessage()
                . ', file: ' . $e->getFile() . ', line: ' . $e->getLine()
            );
        }
        $msg = 'Total archived: ' . $archivedEntries. '(w: ' . $w . ', p: ' . $p
            . ', ii: ' . $ii . ', ra: ' . $ra . ', la: ' . $la . ', bi: ' . $bi
            . ', ul: ' . $ul . ')';
        $log->finish($start, $archivedEntries, $archivedEntries, $msg);
        $this->log('Total archived: ' . $archivedEntries);
        $this->log('Exec.time: ' . round((microtime(true) - $start), 2) . ' second(s)');

        return true;
    }
}
