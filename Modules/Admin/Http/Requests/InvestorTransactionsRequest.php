<?php


namespace Modules\Admin\Http\Requests;


use Modules\Common\Entities\Transaction;
use Modules\Core\Http\Requests\BaseRequest;
use Modules\Core\Interfaces\ListSearchInterface;
use Modules\Core\Traits\DateBuilderTrait;

class InvestorTransactionsRequest extends BaseRequest implements ListSearchInterface
{
    public function rules()
    {
        return [
            'createdAt' => 'nullable|regex:' . DateBuilderTrait::$dateRangeRegex,
            'type' => 'nullable|in:' . implode(',', Transaction::getTypes()),
            'amount' => 'nullable|array',
            'amount.from' => 'nullable|' . $this->getConfiguration('requestRules.amountRegex'),
            'amount.to' => 'nullable|' . $this->getConfiguration('requestRules.amountRegex'),
        ];
    }
}
