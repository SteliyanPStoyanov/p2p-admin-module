<?php

namespace Modules\Profile\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Request;
use Modules\Common\Entities\Loan;
use Modules\Core\Http\Requests\BaseRequest;

class AutoInvestRequest extends BaseRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @param Request $request
     * @return array
     */
    public function rules(Request $request): array
    {
        $minAmount = \SettingFacade::getMinAmountForInvest();
        return [
            'name' => 'required|string|min:5',
            'min_amount' => 'required|min:' . $minAmount . '|' . $this->getConfiguration('requestRules.amountRegex'),
            'max_amount' => 'nullable|min:' . $request['min_amount'] . '|' . $this->getConfiguration('requestRules.amountRegex'),

            'min_interest_rate' => 'nullable|numeric|min:0',
            'max_interest_rate' => 'nullable|numeric|max:100|min:0',
            'min_loan_period' => 'nullable|integer|min:0',
            'max_loan_period' => 'nullable|integer|min:0|max:100',

            'max_portfolio_size' => 'required|numeric',
            'loan_type.*' => 'nullable|string|in:' . implode(',', Loan::getTypes()),
            'loan_payment_status.*' => 'nullable|string|in:' . implode(',', Loan::getPaymentStatuses()),
            'reinvest' => 'required|in:0,1',
            'include_invested' => 'required|in:0,1',
            'agreed' => 'required|in:1',
        ];
    }

    /**
     * @param Validator $validator
     * @return \Illuminate\Http\RedirectResponse|void
     */
    protected function failedValidation(Validator $validator)
    {
        return back()->withErrors($validator->errors());
    }

    /**
     * @return array
     */
    public function messages(): array
    {
        return [
            'name.required' => __('common.FillInTheField'),
            'min_amount.required' => __('common.FillInTheField'),
            'max_portfolio_size.required' => __('common.FillInTheField'),
            'reinvest.required' => __('common.FillInTheField'),
            'agreed.required' => __('common.PleaseAcceptAssignmentAgreement'),
        ];
    }
}
