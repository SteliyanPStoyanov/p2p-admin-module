<?php

namespace Modules\Common\Services;

use Carbon\Carbon;
use Modules\Common\Entities\RegistrationAttempt;
use Modules\Common\Repositories\RegistrationAttemptRepository;
use Modules\Core\Services\BaseService;

class RegistrationAttemptService extends BaseService
{
    private RegistrationAttemptRepository $registrationAttemptRepository;
    protected BlockedIpService $blockedIpService;

    /**
     * RegistrationAttemptService constructor.
     *
     * @param RegistrationAttemptRepository $registrationAttemptRepository
     * @param BlockedIpService $blockedIpService
     */
    public function __construct(
        RegistrationAttemptRepository $registrationAttemptRepository,
        BlockedIpService $blockedIpService

    ) {
        $this->registrationAttemptRepository = $registrationAttemptRepository;
        $this->blockedIpService = $blockedIpService;

        parent::__construct();
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
            'registration_attempt'
        );

        return $this->registrationAttemptRepository->getAll($length, $whereConditions);
    }

    protected function getWhereConditions(
        array $data,
        array $names = ['name'],
        $prefix = ''
    ) {
        $where = [];
        if (!empty($data['email'])) {
            if (!empty($data['email'])) {
                $where[] = [
                    'registration_attempt.email',
                    '=',
                    $data['email'],
                ];
            }
            unset($data['email']);
        }
        if (!empty($data['ip'])) {
            $where[] = [
                'registration_attempt.ip',
                '=',
                $data['ip'],
            ];
            unset($data['ip']);
        }

        if (!array_key_exists('active', $data) || $data['active'] == null || $data['active'] == 1) {
            $where[] = [
                'registration_attempt.active',
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

        $this->registrationAttemptRepository->create($data);
    }

    /*
     *
     */
    public function attemptCount(string $ip, string $reason)
    {
        $attemptCount = RegistrationAttempt::where(
            'datetime',
            '>',
            Carbon::now()->subMinute(config('profile.attempt_time'))
        )->where(
            'ip',
            '=',
            $ip
        )->count();

        if ($attemptCount >= config('profile.attempt_count')) {
            $this->blockedIpService->create($ip, config('profile.blocked_time'), $reason);
            return true;
        }
        return false;
    }


    public function delete(int $loginAttemptId)
    {
        $loginAttempt = $this->registrationAttemptRepository->getById($loginAttemptId);
        $this->registrationAttemptRepository->delete($loginAttempt);
    }

    public function deleteAll()
    {
        $this->registrationAttemptRepository->deleteAll();
    }
}
