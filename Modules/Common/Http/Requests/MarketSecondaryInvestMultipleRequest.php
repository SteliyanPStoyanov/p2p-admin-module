<?php

namespace Modules\Common\Http\Requests;

use Modules\Core\Http\Requests\BaseRequest;
use Modules\Core\Interfaces\ListSearchInterface;

class MarketSecondaryInvestMultipleRequest extends BaseRequest implements ListSearchInterface
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'cart.*.cart_loan_id' => 'nullable|numeric',
            'cart.*.loan_id' => 'required|numeric',
            'cart.*.investment_id' => 'required|numeric',
            'cart.*.originator_id' => 'required|numeric',
            'cart.*.market_secondary_id' => 'required|numeric',
            'cart.*.amount' => 'required|numeric'
        ];
    }
}
