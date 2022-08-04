<?php

namespace Modules\Admin\Http\Requests;

use Modules\Admin\Entities\Setting;
use Modules\Core\Http\Requests\BaseRequest;
use Modules\Core\Interfaces\ListSearchInterface;

/**
 * Class InvestStrategySearchRequest
 *
 * @package Modules\Admin\Http\Requests
 */
class InvestStrategyHistorySearchRequest extends BaseRequest implements ListSearchInterface
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name' => 'nullable|string|max:50',
            'investor_id' => 'nullable|numeric',
            'priority' => 'nullable|numeric',
            'min_amount' => 'nullable|' . $this->getConfiguration('requestRules.amountRegex'),
            'max_amount' => 'nullable|' . $this->getConfiguration('requestRules.amountRegex'),
            'min_interest_rate' => 'nullable|numeric',
            'max_interest_rate' => 'nullable|numeric',
            'min_loan_period' => 'nullable|numeric',
            'max_loan_period' => 'nullable|numeric',
            'type' => 'nullable',
            'active' => 'nullable|numeric',
            'deleted' => 'nullable|numeric',
            'payment_status' => 'nullable',
            'created_at.from' => 'nullable|date_format:' . Setting::SHOW_FORMAT,
            'created_at.to' => 'nullable|date_format:' . Setting::SHOW_FORMAT,
            'archived_at.from' => 'nullable|date_format:' . Setting::SHOW_FORMAT,
            'archived_at.to' => 'nullable|date_format:' . Setting::SHOW_FORMAT,
            'order' => 'nullable|array',
            'limit' => 'nullable|numeric',
        ];
    }
}
