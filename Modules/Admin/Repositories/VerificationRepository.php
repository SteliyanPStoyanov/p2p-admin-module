<?php

namespace Modules\Admin\Repositories;

use Modules\Admin\Entities\Verification;
use \Modules\Core\Repositories\BaseRepository;

class VerificationRepository extends BaseRepository
{
    /**
     * @param array $data
     *
     * @return Verification
     */
    public function create(array $data)
    {
        $verification = new Verification();
        $verification->fill($data);
        $verification->save();

        return $verification;
    }

    /**
     * @param int $investorId
     * @param array $data
     *
     * @return mixed
     */
    function createOrUpdate(int $investorId, array $data)
    {
        return Verification::updateOrCreate([
            'investor_id' => $investorId
        ], $data);
    }
}
