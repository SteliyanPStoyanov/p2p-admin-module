<?php

namespace Modules\Admin\Http\Requests;

use Modules\Admin\Entities\Setting;
use Modules\Common\Entities\Investor;
use Modules\Common\Entities\Loan;
use Modules\Core\Http\Requests\BaseRequest;
use Modules\Core\Interfaces\ListSearchInterface;
use Modules\Core\Traits\DateBuilderTrait;

/**
 * Class WalletSearchRequest
 *
 * @package Modules\Admin\Http\Requests
 */
class WalletSearchRequest extends BaseRequest implements ListSearchInterface
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'investor_id' => 'nullable|numeric',
            'createdAt.from' => 'nullable|date_format:' . Setting::SHOW_FORMAT,
            'createdAt.to' => 'nullable|date_format:' . Setting::SHOW_FORMAT,
            'name' => 'nullable|string',
            'type' => 'nullable|in:' . implode(',', Investor::getTypes()),
            'total_amount' => 'nullable|array',
            'total_amount.from' => 'nullable|' . $this->getConfiguration('requestRules.amountRegex'),
            'total_amount.to' => 'nullable|' . $this->getConfiguration('requestRules.amountRegex'),
            'invested' => 'nullable|array',
            'invested.from' => 'nullable|' . $this->getConfiguration('requestRules.amountRegex'),
            'invested.to' => 'nullable|' . $this->getConfiguration('requestRules.amountRegex'),
            'uninvested' => 'nullable|array',
            'uninvested.from' => 'nullable|' . $this->getConfiguration('requestRules.amountRegex'),
            'uninvested.to' => 'nullable|' . $this->getConfiguration('requestRules.amountRegex'),
            'order' => 'nullable|array',
            'order.wallet.wallet_id' => 'nullable|in:asc,desc',
            'order.wallet.created_at' => 'nullable|in:asc,desc',
            'order.investor.first_name' => 'nullable|in:asc,desc',
            'order.wallet.investor_id' => 'nullable|in:asc,desc',
            'order.wallet.total_amount' => 'nullable|in:asc,desc',
            'order.wallet.invested' => 'nullable|in:asc,desc',
            'order.wallet.uninvested' => 'nullable|in:asc,desc',
            'order.investor.type' => 'nullable|in:asc,desc',
            'limit' => 'nullable|numeric',
        ];
    }
}
