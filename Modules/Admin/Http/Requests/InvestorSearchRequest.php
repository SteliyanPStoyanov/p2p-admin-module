<?php

namespace Modules\Admin\Http\Requests;

use Modules\Admin\Entities\Setting;
use Modules\Core\Http\Requests\BaseRequest;
use Modules\Core\Interfaces\ListSearchInterface;
use Modules\Core\Traits\DateBuilderTrait;

/**
 * Class InvestorSearchRequest
 *
 * @package Modules\Admin\Http\Requests
 */
class InvestorSearchRequest extends BaseRequest implements ListSearchInterface
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'active' => 'nullable|numeric|in:0,1',
            'name' => 'nullable|string|max:50',
            'email' => $this->getConfiguration('requestRules.emailNullable'),
            'investor_id' => 'nullable|numeric',
            'createdAt.from' => 'nullable|date_format:' . Setting::SHOW_FORMAT,
            'createdAt.to' => 'nullable|date_format:' . Setting::SHOW_FORMAT,
            'status' => 'nullable|in:unregistered,registered,awaiting,verification,verified,rejected_verification,awaiting_documents',
            'type' => 'nullable|in:individual,company',
            'total_amount' => 'nullable|array',
            'total_amount.from' => 'nullable|' . $this->getConfiguration('requestRules.amountRegex'),
            'total_amount.to' => 'nullable|' . $this->getConfiguration('requestRules.amountRegex'),
            'uninvested_amount' => 'nullable|array',
            'uninvested_amount.from' => 'nullable|' . $this->getConfiguration('requestRules.amountRegex'),
            'uninvested_amount.to' => 'nullable|' . $this->getConfiguration('requestRules.amountRegex'),
            'order' => 'nullable|array',
            'order.investor.investor_id' => 'nullable|in:asc,desc',
            'order.investor.created_at' => 'nullable|in:asc,desc',
            'order.investor.first_name' => 'nullable|in:asc,desc',
            'order.investor.type' => 'nullable|in:asc,desc',
            'order.wallet.total_amount' => 'nullable|in:asc,desc',
            'order.wallet.uninvested' => 'nullable|in:asc,desc',
            'limit' => 'nullable|numeric',
        ];
    }
}
