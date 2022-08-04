<?php

namespace Modules\Profile\Http\Requests;

use Modules\Common\Entities\Loan;
use Modules\Core\Http\Requests\BaseRequest;
use Modules\Core\Interfaces\ListSearchInterface;

/**
 * Class InvestLoanSearchRequest
 *
 * @package Modules\Common\Http\Requests
 */
class InvestmentSearchRequest extends BaseRequest implements ListSearchInterface
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'interest_rate_percent.from'    => 'nullable|numeric',
            'interest_rate_percent.to'    => 'nullable|numeric',
            'period.from'    => 'nullable|numeric',
            'period.to'    => 'nullable|numeric',
            'created_at.from' => 'nullable|date_format:d.m.Y',
            'created_at.to' => 'nullable|date_format:d.m.Y',
            'invested_amount.from'    => 'nullable|' . $this->getConfiguration('requestRules.amountRegex'),
            'invested_amount.to'    => 'nullable|' . $this->getConfiguration('requestRules.amountRegex'),
            'loan.status'    => 'nullable|string',
            'loan.type'    => 'nullable|string',
            'payment_status.*'    => 'nullable|string',
            'final_payment_status.*'    => 'nullable|string|in:' . implode(',', Loan::getFinalPaymentStatuses()),
            'order.*'    => 'nullable',
            'limit'      => 'nullable|numeric',
            'investment.listed'      => 'nullable|string',
        ];
    }
}
