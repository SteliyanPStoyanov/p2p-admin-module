<?php

namespace Modules\Admin\Http\Requests;

use Illuminate\Validation\Rule;
use Modules\Core\Http\Requests\BaseRequest;

/**
 * Class AddFundsRequest
 *
 * @package Modules\Admin\Http\Requests
 */
class AddFundsRequest extends BaseRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'amount' => 'required|' . $this->getConfiguration('requestRules.amountRegex'),
            'bank_transaction_id' => 'required',
            'bank_account_id' => [
                Rule::requiredIf(empty($this->input('bank_account_iban'))),
            ],
            'bank_account_iban' => [
                Rule::requiredIf(empty($this->input('bank_account_id'))),
            ],
        ];
    }
}
