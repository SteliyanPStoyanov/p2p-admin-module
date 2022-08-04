<?php

namespace Modules\Common\Services;

use Auth;
use Carbon\Carbon;
use Modules\Common\Repositories\InvestorLoginLogRepository;
use Modules\Communication\Entities\EmailTemplate;
use Modules\Communication\Services\EmailService;
use Modules\Core\Services\BaseService;

class InvestorLoginLogService extends BaseService
{
    private InvestorLoginLogRepository $investorLoginLogRepository;
    private EmailService $emailService;
    private InvestorService $investorService;

    /**
     * InvestorLoginLogService constructor.
     *
     * @param InvestorLoginLogRepository $investorLoginLogRepository
     * @param EmailService $emailService
     * @param InvestorService $investorService
     */
    public function __construct(
        InvestorLoginLogRepository $investorLoginLogRepository,
        EmailService $emailService,
        InvestorService $investorService

    ) {
        $this->investorLoginLogRepository = $investorLoginLogRepository;
        $this->emailService = $emailService;
        $this->investorService = $investorService;

        parent::__construct();
    }

    /**
     * @param string $device
     * @param int $investorId
     * @param string|null $emailSend
     *
     * @throws \Modules\Core\Exceptions\ProblemException
     */
    public function create(string $device, int $investorId, string $emailSend = null)
    {
        $data['investor_id'] = $investorId;
        $data['device'] = $device;
        $data['ip'] = request()->ip();

        $isExist = $this->investorLoginLogRepository->isExists($investorId, $data['ip']);

        if ($isExist == false) {
            $investor = $this->investorService->getById($investorId);
            $additionalData = [
                'location' => request()->ip(),
                'timestamp' => Carbon::now()
            ];
            if ($emailSend == true) {
                $this->emailService->sendEmail(
                    $investor,
                    EmailTemplate::TEMPLATE_SEEDER_ARRAY['login_template']['id'],
                    $investor->email,
                    Carbon::now(),
                    $additionalData
                );
            }
        }

        $this->investorLoginLogRepository->create($data);
    }

    public function getByWhereConditions(int $length, array $data)
    {
        if (!empty($data['limit'])) {
            $length = $data['limit'];
            unset($data['limit']);
        }

        $whereConditions = $this->getWhereConditions(
            $data,
            [
            ],
            'investor_login_log'
        );

        return $this->investorLoginLogRepository->getAll($length, $whereConditions);
    }

    protected function getWhereConditions(
        array $data,
        array $names = ['name'],
        $prefix = ''
    ) {
        $where = [];

        if (!empty($data['investor_id'])) {
            if (!empty($data['investor_id'])) {
                $where[] = [
                    'investor_login_log.investor_id',
                    '=',
                    $data['investor_id'],
                ];
            }

            if (!empty($data['ip'])) {
                $where[] = [
                    'investor_login_log.ip',
                    '=',
                    $data['ip'],
                ];
            }
            unset($data['ip']);
        }

        return array_merge($where, parent::getWhereConditions($data, $names, $prefix));
    }

    public function delete(int $investorLoginLogId)
    {
        $investorLoginLog = $this->investorLoginLogRepository->getById($investorLoginLogId);
        $this->investorLoginLogRepository->delete($investorLoginLog);
    }
}
