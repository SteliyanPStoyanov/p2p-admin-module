<?php

namespace Modules\Communication\Repositories;

use Illuminate\Support\Facades\DB;
use Modules\Communication\Entities\Email;
use Modules\Core\Repositories\BaseRepository;

class EmailRepository extends BaseRepository
{
     protected Email $email;

     public function __construct(Email $email)
    {
        $this->email = $email;
    }

     public function getAll(
        int $limit,
        array $where = [],
        array $order = ['email_id' => 'DESC'],
        bool $showDeleted = false
    ) {
        $builder = DB::table('email');
        $builder->select(
            DB::raw(
                '
            email.*
            '
            )
        );

        if (!empty($where)) {
            $builder->where($where);
        }

        if (!empty($order)) {
            foreach ($order as $key => $direction) {
                $builder->orderBy($key, $direction);
            }
        }

        $result = $builder->paginate($limit);

        $records = $this->email->hydrate($result->all());
        $result->setCollection($records);

        return $result;
    }

    /**
     * [getById description]
     *
     * @param int $emailId [description]
     *
     * @return Email|null
     */
    public function getById(int $emailId)
    {
        $email = Email::where(
            'sms_id',
            '=',
            $emailId
        )->get();

        return $email->first();
    }


    /**
     * @param array $data
     *
     * @return Email
     */
    public function create(array $data)
    {
        $email = new Email();
        $email->fill($data);
        $email->save();

        return $email;
    }
}

