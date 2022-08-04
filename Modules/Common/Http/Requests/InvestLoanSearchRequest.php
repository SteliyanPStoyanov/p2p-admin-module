<?php

namespace Modules\Common\Http\Requests;

use Modules\Core\Http\Requests\BaseRequest;
use Modules\Core\Interfaces\ListSearchInterface;

/**
 * Class InvestLoanSearchRequest
 *
 * @package Modules\Common\Http\Requests
 */
class InvestLoanSearchRequest extends BaseRequest implements ListSearchInterface
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'interest_rate_percent.from' => 'nullable|numeric',
            'interest_rate_percent.to' => 'nullable|numeric',
            'period.from' => 'nullable|numeric',
            'period.to' => 'nullable|numeric',
            'created_at.from' => 'nullable|date_format:d.m.Y',
            'created_at.to' => 'nullable|date_format:d.m.Y',
            'amount_available.from' => 'nullable|' . $this->getConfiguration('requestRules.amountRegex'),
            'amount_available.to' => 'nullable|' . $this->getConfiguration('requestRules.amountRegex'),
            'my_investment' => 'nullable|string',
            'payment_status.*' => 'nullable|string',
            'order.*' => 'nullable',
            'limit' => 'nullable|numeric|in:10,25,50,100,250',
            'loan.type' => 'nullable|string',
        ];
    }
}
