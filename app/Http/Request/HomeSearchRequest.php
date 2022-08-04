<?php

namespace App\Http\Request;

use Modules\Core\Http\Requests\BaseRequest;
use Modules\Core\Interfaces\ListSearchInterface;

class HomeSearchRequest extends BaseRequest implements ListSearchInterface
{
    public function rules()
    {
        return [
            'interest_rate_percent.from' => 'nullable|numeric',
            'interest_rate_percent.to' => 'nullable|numeric',
            'period.from' => 'nullable|numeric',
            'period.to' => 'nullable|numeric',
            'created_at.from' => 'nullable',
            'created_at.to' => 'nullable',
            'amount_available.from' => 'nullable|numeric',
            'amount_available.to' => 'nullable|numeric',
            'my_investment.include' => 'nullable|string',
            'my_investment.exclude' => 'nullable|string',
            'payment_status.*' => 'nullable|string',
            'order.*' => 'nullable',
            'limit' => 'nullable|numeric',
            'loan.type' => 'nullable|string',

        ];
    }
}
