<?php

namespace Modules\Admin\Http\Requests;

use Modules\Core\Http\Requests\BaseRequest;
use Modules\Core\Interfaces\ListSearchInterface;

class InvestorReferralsSearchRequest extends BaseRequest implements ListSearchInterface
{
    public function rules()
    {
        return [
            'investor_id' => 'nullable|numeric',
            'name' => 'nullable|string|max:50',
            'email' => $this->getConfiguration('requestRules.emailNullable'),
            'deposit' => 'nullable|array',
            'deposit.from' => 'nullable|' . $this->getConfiguration('requestRules.amountRegex'),
            'deposit.to' => 'nullable|' . $this->getConfiguration('requestRules.amountRegex'),
            'invested' => 'nullable|array',
            'invested.from' => 'nullable|' . $this->getConfiguration('requestRules.amountRegex'),
            'invested.to' => 'nullable|' . $this->getConfiguration('requestRules.amountRegex'),
            'referrals_count' => 'nullable|array',
            'referrals_count.from' => 'nullable|numeric',
            'referrals_count.to' => 'nullable|numeric',
            'limit' => 'nullable|numeric',

        ];
    }
}
