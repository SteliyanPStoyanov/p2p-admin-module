<?php

namespace Modules\Common\Http\Requests;

use Modules\Core\Http\Requests\BaseRequest;
use Modules\Core\Interfaces\ListSearchInterface;

class MarketSecondaryInvestSingleRequest extends BaseRequest implements ListSearchInterface
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'cart_loan_id' => 'nullable|numeric',
            'loan_id' => 'required|numeric',
            'investment_id' => 'required|numeric',
            'originator_id' => 'required|numeric',
            'market_secondary_id' => 'required|numeric',
            'amount' => 'required|numeric'
        ];
    }
}
