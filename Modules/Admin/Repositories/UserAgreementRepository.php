<?php

namespace Modules\Admin\Repositories;

use Illuminate\Support\Facades\DB;
use Modules\Common\Entities\ContractTemplate;
use Modules\Common\Entities\InvestorContract;
use Modules\Common\Entities\LoanContract;
use Modules\Communication\Entities\EmailTemplate;
use Modules\Core\Repositories\BaseRepository;

class UserAgreementRepository extends BaseRepository
{
    protected ContractTemplate $contractTemplate;

    public function __construct(ContractTemplate $contractTemplate)
    {
        $this->contractTemplate = $contractTemplate;
    }

    public function getAll(
        int $limit,
        array $where = [],
        array $order = ['contract_template_id' => 'DESC']
    ) {
        $builder = DB::table('contract_template');
        $builder->select(
            DB::raw(
                '
            contract_template.*
            '
            )
        );

        if (!empty($where)) {
            $builder->where($where);
        }
        $builder->where('contract_template.deleted', 0);

        if (!empty($order)) {
            foreach ($order as $key => $direction) {
                $builder->orderBy($key, $direction);
            }
        }

        $result = $builder->paginate($limit);

        $records = $this->contractTemplate->hydrate($result->all());
        $result->setCollection($records);

        return $result;
    }

    /**
     * @param int $contractTemplateId
     *
     * @return mixed
     */
    public function getById(int $contractTemplateId)
    {
        return $this->contractTemplate::where(
            'contract_template_id',
            '=',
            $contractTemplateId
        )->first();
    }

    /**
     * @param array $data
     *
     * @return ContractTemplate
     */
    public function create(array $data)
    {
        $template = new ContractTemplate();
        $template->fill($data);
        $template->active = 0;
        $template->save();

        return $template;
    }

    /**
     * @param ContractTemplate $template
     * @param array $data
     *
     * @return ContractTemplate
     */
    public function edit(ContractTemplate $template, array $data)
    {
        $template->fill($data);
        $template->save();

        return $template;
    }

    /**
     * @param ContractTemplate $template
     *
     * @throws \Exception
     */
    public function delete(ContractTemplate $template)
    {
        $template->delete();
    }

    /**
     * @param ContractTemplate $template
     */
    public function enable(ContractTemplate $template)
    {
        $template->enable();
    }

    public function disableCurrentContract(string $type)
    {
        return $this->contractTemplate::where(
            [
                'type' => $type,
                'active' => 1,
            ]
        )->update(['active' => 0]);
    }

    /**
     * @param string $type
     *
     * @return ContractTemplate
     */
    public function getCurrentUserAgreement(string $type)
    {
        return $this->contractTemplate::where(
            [
                'active' => 1,
                'type' => $type,
            ]
        )->first();
    }

    /**
     * @param array $data
     *
     * @return InvestorContract
     */
    public function createInvestorContract(array $data)
    {
        $investorContract = new InvestorContract();
        $investorContract->fill($data);
        $investorContract->save();

        return $investorContract;
    }

    /**
     * @param array $data
     *
     * @return LoanContract
     */
    public function createLoanContract(array $data)
    {
        $loanContract = new LoanContract();
        $loanContract->fill($data);
        $loanContract->save();

        return $loanContract;
    }

    /**
     * @param string $type
     *
     * @return mixed
     */
    public function getAllContractTemplates(string $type)
    {
        return $this->contractTemplate::where(
            [
                'type' => $type,
            ]
        )->get();
    }
}

