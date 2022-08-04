<?php

namespace Modules\Admin\Http\Requests;

use Modules\Admin\Entities\Setting;
use Modules\Common\Entities\Transaction;
use Modules\Core\Http\Requests\BaseRequest;
use Modules\Core\Interfaces\ListSearchInterface;
use Modules\Core\Traits\DateBuilderTrait;

/**
 * Class TransactionSearchRequest
 *
 * @package Modules\Admin\Http\Requests
 */
class TransactionSearchRequest extends BaseRequest implements ListSearchInterface
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'transaction_id' => 'nullable|numeric',
            'loan_id' => 'nullable|numeric',
            'createdAt.from' => 'nullable|date_format:' . Setting::SHOW_FORMAT,
            'createdAt.to' => 'nullable|date_format:' . Setting::SHOW_FORMAT,
            'amount' => 'nullable|array',
            'amount.from' => 'nullable|' . $this->getConfiguration('requestRules.amountRegex'),
            'amount.to' => 'nullable|' . $this->getConfiguration('requestRules.amountRegex'),
            'from' => 'nullable|string',
            'to' => 'nullable|string',
            'type.*' => 'nullable|in:' . implode(',', Transaction::getTypes()),
            'order' => 'nullable|array',
            'order.transaction.transaction_id' => 'nullable|in:asc,desc',
            'order.transaction.created_at' => 'nullable|in:asc,desc',
            'order.transaction.amount' => 'nullable|in:asc,desc',
            'order.transaction.type' => 'nullable|in:asc,desc',
            'order.from' => 'nullable|in:asc,desc',
            'order.to' => 'nullable|in:asc,desc',
            'limit' => 'nullable|numeric',
        ];
    }
}
