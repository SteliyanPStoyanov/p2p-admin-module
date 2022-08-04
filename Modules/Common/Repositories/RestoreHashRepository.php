<?php

namespace Modules\Common\Repositories;

use Modules\Common\Entities\RestoreHash;
use Modules\Core\Repositories\BaseRepository;

class RestoreHashRepository extends BaseRepository
{
    /**
     * @param array $data
     *
     * @return RestoreHash
     */
    public function create(array $data)
    {
        $restoreHash = new RestoreHash();
        $restoreHash->fill($data);
        $restoreHash->save();

        return $restoreHash;
    }

    /**
     * @param string $hash
     *
     * @return mixed
     */
    public function getByHash(string $hash)
    {
        $restoreHash = RestoreHash::where(
            'hash',
            '=',
            $hash
        )->get();

        return $restoreHash->last();
    }

    /**
     * @param string $hash
     * @param array $data
     *
     * @return mixed
     */
    public function update(string $hash, array $data)
    {
        return RestoreHash::where('hash', '=', $hash)->update(array('used' => $data['used']));
    }

}
