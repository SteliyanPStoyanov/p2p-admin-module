<?php

namespace Modules\Common\Services;

use Carbon\Carbon;
use Modules\Common\Repositories\BlockedIpRepository;
use Modules\Core\Services\BaseService;

class BlockedIpService extends BaseService
{
    private BlockedIpRepository $blockedIpRepository;

    /**
     * @param BlockedIpRepository $blockedIpRepository
     */
    public function __construct(
        BlockedIpRepository $blockedIpRepository

    ) {
        $this->blockedIpRepository = $blockedIpRepository;

        parent::__construct();
    }

    /**
     * @param string $ip
     * @param int $hours
     * @param string $reason
     */
    public function create(string $ip, int $hours, string $reason)
    {
        $data['blocked_till'] = Carbon::now()->addHours($hours);
        $data['ip'] = $ip;
        $data['reason'] = $reason;

        $this->blockedIpRepository->create($data);
    }

    /**
     * @param string $ip
     * @param string $reason
     *
     * @return bool
     */
    public function blockedTill(string $ip, string $reason)
    {
        return $this->getByIpReason($ip, $reason);
    }

    /**
     * @param string $ip
     * @param string $reason
     * @return mixed
     */
    public function getByIpReason(string $ip, string $reason)
    {
        return $this->blockedIpRepository->get($ip, $reason);
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
            'blocked_ip'
        );

        return $this->blockedIpRepository->getAll($length, $whereConditions);
    }

    protected function getWhereConditions(
        array $data,
        array $names = ['name'],
        $prefix = ''
    ) {
        $where = [];

        if (!empty($data['ip'])) {
            $where[] = [
                'blocked_ip.ip',
                '=',
                $data['ip'],
            ];
        }
        unset($data['ip']);

        if (!empty($data['email'])) {
            if (!empty($data['email'])) {
                $where[] = [
                    'login_attempt.email',
                    '=',
                    $data['email'],
                ];
            }
            unset($data['email']);
        }

        if (!array_key_exists('active', $data) || $data['active'] == null || $data['active'] == 1) {
            $where[] = [
                'blocked_ip.active',
                '=',
                1,
            ];
            unset($data['active']);
        }

        return array_merge($where, parent::getWhereConditions($data, $names, $prefix));
    }

    public function delete(int $investorLoginLogId)
    {
        $blockedIp = $this->blockedIpRepository->getById($investorLoginLogId);
        $this->blockedIpRepository->delete($blockedIp);
    }

    public function deleteAll()
    {
        $this->blockedIpRepository->deleteAll();
    }

}
