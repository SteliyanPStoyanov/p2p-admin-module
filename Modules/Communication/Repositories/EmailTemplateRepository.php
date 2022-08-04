<?php

namespace Modules\Communication\Repositories;

use Exception;
use Illuminate\Support\Facades\DB;
use Modules\Communication\Entities\EmailTemplate;
use Modules\Core\Repositories\BaseRepository;

class EmailTemplateRepository extends BaseRepository
{
    protected EmailTemplate $emailTemplate;

    public function __construct(EmailTemplate $emailTemplate)
    {
        $this->emailTemplate = $emailTemplate;
    }

    /**
     * @param int $limit
     * @param array $where
     * @param array|string[] $order
     * @param bool $showDeleted
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function getAll(
        int $limit,
        array $where = [],
        array $order = ['email_template_id' => 'DESC'],
        bool $showDeleted = false
    ) {
        $builder = DB::table('email_template');
        $builder->select(
            DB::raw(
                '
            email_template.*
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

        $records = $this->emailTemplate->hydrate($result->all());
        $result->setCollection($records);

        return $result;
    }

    /**
     * [getById description]
     *
     * @param int $emailTemplateId [description]
     *
     * @return EmailTemplate
     */
    public function getById(int $emailTemplateId): EmailTemplate
    {
        return EmailTemplate::where(
            'email_template_id',
            '=',
            $emailTemplateId
        )->first();
    }

    /**
     * @param array $data
     *
     * @return EmailTemplate
     */
    public function create(array $data)
    {
        $emailTemplate = new EmailTemplate();
        $emailTemplate->fill($data);
        $emailTemplate->save();

        return $emailTemplate;
    }

    /**
     * @param EmailTemplate $emailTemplate
     * @param array $data
     *
     * @return EmailTemplate
     */
    public function edit(EmailTemplate $emailTemplate, array $data)
    {
        $emailTemplate->fill($data);
        $emailTemplate->save();

        return $emailTemplate;
    }

    /**
     * @param EmailTemplate $emailTemplate
     * @throws Exception
     */
    public function delete(EmailTemplate $emailTemplate)
    {
        $emailTemplate->delete();
    }

    /**
     * @param EmailTemplate $emailTemplate
     */
    public function enable(EmailTemplate $emailTemplate)
    {
        $emailTemplate->enable();
    }

    /**
     * @param EmailTemplate $emailTemplate
     */
    public function disable(EmailTemplate $emailTemplate)
    {
        $emailTemplate->disable();
    }
}

