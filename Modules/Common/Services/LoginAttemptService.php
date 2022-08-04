<?php

namespace Modules\Common\Services;

use Carbon\Carbon;
use Modules\Admin\Entities\Setting;
use Modules\Common\Entities\Investor;
use Modules\Common\Entities\LoginAttempt;
use Modules\Common\Repositories\LoginAttemptRepository;
use Modules\Communication\Entities\EmailTemplate;
use Modules\Communication\Services\EmailService;
use Modules\Core\Services\BaseService;

class LoginAttemptService extends BaseService
{
    private LoginAttemptRepository $loginAttemptRepository;
    protected BlockedIpService $blockedIpService;
    protected EmailService $emailService;

    /**
     * LoginAttemptService constructor.
     *
     * @param LoginAttemptRepository $loginAttemptRepository
     * @param BlockedIpService $blockedIpService
     * @param EmailService $emailService
     */
    public function __construct(
        LoginAttemptRepository $loginAttemptRepository,
        BlockedIpService $blockedIpService,
        EmailService $emailService
    ) {
        $this->loginAttemptRepository = $loginAttemptRepository;
        $this->blockedIpService = $blockedIpService;
        $this->emailService = $emailService;

        parent::__construct();
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
            ],
            'login_attempt'
        );

        return $this->loginAttemptRepository->getAll($length, $whereConditions);
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

        if (!empty($data['email'])) {
            $where[] = [
                'login_attempt.email',
                '=',
                $data['email'],
            ];
        }

        if (!empty($data['ip'])) {
            $where[] = [
                'login_attempt.ip',
                '=',
                $data['ip'],
            ];
        }
        unset($data['ip']);

        if (!array_key_exists('active', $data) || $data['active'] == null || $data['active'] == 1) {
            $where[] = [
                'login_attempt.active',
                '=',
                1,
            ];
            unset($data['active']);
        }

        return array_merge($where, parent::getWhereConditions($data, $names, $prefix));
    }

    /**
     * @param string $email
     * @param string $device
     * @param string $ip
     */
    public function create(string $email, string $device, string $ip)
    {
        $data['email'] = $email;
        $data['datetime'] = Carbon::now();
        $data['ip'] = $ip;
        $data['device'] = $device;

        $this->loginAttemptRepository->create($data);
    }

    /**
     * @param string $ip
     * @param Investor $investor
     * @param string $reason
     *
     * @return bool
     */
    public function isAttemptCountExceeded(
        string $ip,
        Investor $investor,
        string $reason
    ): bool {
        $maxWrongLoginAttempts = (int)\SettingFacade::getSettingValue(
            Setting::MAX_WRONG_LOGIN_ATTEMPTS_KEY
        );
        $wrongLoginBlockDays = (int)\SettingFacade::getSettingValue(
            Setting::WRONG_LOGIN_BLOCK_DAYS_KEY
        );

        $attemptCount = LoginAttempt::where(
            [
                ['ip', '=', $ip],
                ['deleted', '=', 0],
            ]
        )->count();

        if ($attemptCount >= $maxWrongLoginAttempts) {
            $this->blockedIpService->create($ip, $wrongLoginBlockDays, $reason);

            $this->emailService->sendEmail(
                $investor,
                EmailTemplate::TEMPLATE_SEEDER_ARRAY['wrong_login_attempts']['id'],
                $investor->email,
                Carbon::now()
            );

            return true;
        }

        return false;
    }

    /**
     * @param int $loginAttemptId
     *
     * @throws \Exception
     */
    public function delete(int $loginAttemptId)
    {
        $loginAttempt = $this->loginAttemptRepository->getById($loginAttemptId);
        $this->loginAttemptRepository->delete($loginAttempt);
    }

    public function deleteAll()
    {
        $this->loginAttemptRepository->deleteAll();
    }

}
