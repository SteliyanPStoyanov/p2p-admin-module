<?php

namespace Modules\Common\Services;

use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Modules\Common\Repositories\RestoreHashRepository;
use Modules\Core\Services\BaseService;

class RestoreHashService extends BaseService
{
    private RestoreHashRepository $restoreHashRepository;

    /**
     * @param RestoreHashRepository $restoreHashRepository
     */
    public function __construct(
        RestoreHashRepository $restoreHashRepository

    ) {
        $this->restoreHashRepository = $restoreHashRepository;

        parent::__construct();
    }

    /**
     * @param int $investorId
     *
     * @return \Modules\Common\Entities\RestoreHash
     */
    public function create(int $investorId)
    {
        $data['hash'] = base64_encode(Hash::make(Str::random(10)));
        $data['valid_till'] = Carbon::now()->addMinutes(config('profile.valid_till'));
        $data['investor_id'] = $investorId;
        $data['used'] = 0;

        return $this->restoreHashRepository->create($data);
    }

    /**
     * @param string $hash
     *
     * @return bool
     */
    public function checkHashExist(string $hash)
    {
        $checkHashExist = $this->restoreHashRepository->getByHash($hash);

        if (empty($checkHashExist->hash)) {
            return false;
        }

        return true;
    }

    /**
     * @param string $hash
     *
     * @return bool
     */
    public function isUsedHash(string $hash)

    {
        $checkHashUsed = $this->restoreHashRepository->getByHash($hash);

        return $checkHashUsed->used == 1 ? false : true;
    }

    /**
     * @param string $hash
     *
     * @return bool
     */
    public function getHash(string $hash)
    {
        return $this->restoreHashRepository->getByHash($hash);
    }

    /**
     * @param string $hash
     *
     * @return bool
     */
    public function useHash(string $hash)
    {
        $data['used'] = 1;

        return $this->restoreHashRepository->update($hash ,$data);
    }

}

