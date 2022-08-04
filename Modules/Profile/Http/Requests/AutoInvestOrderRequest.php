<?php

namespace Modules\Profile\Http\Requests;

use Modules\Core\Http\Requests\BaseRequest;
use Modules\Core\Interfaces\ListSearchInterface;

/**
 * Class InvestLoanSearchRequest
 *
 * @package Modules\Common\Http\Requests
 */
class AutoInvestOrderRequest extends BaseRequest implements ListSearchInterface
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'direction'      => 'required|in:up,down',
            'priority'       => 'required|numeric',
            'strategyId'       => 'required|numeric',
        ];
    }
}
