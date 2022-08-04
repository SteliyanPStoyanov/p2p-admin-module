<?php

namespace Modules\Profile\Http\Requests;

use Modules\Core\Http\Requests\BaseRequest;

class AutoInvestLoanCountRequest extends BaseRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'min_amount' => 'nullable|' . $this->getConfiguration('requestRules.amountRegex'),
            'max_amount' => 'nullable|' . $this->getConfiguration('requestRules.amountRegex'),
            'min_interest_rate' => 'nullable|numeric|min:0',
            'max_interest_rate' => 'nullable|numeric|max:100',
            'min_loan_period' => 'nullable|numeric|min:0',
            'max_loan_period' => 'nullable|numeric|min:0|max:100',
            'portfolio_size' => 'nullable|numeric',
            'include_invested' => 'nullable|numeric',
            'loan_type.*' => 'nullable|string',
            'loan_payment_status.*' => 'nullable|string'
        ];
    }
}
