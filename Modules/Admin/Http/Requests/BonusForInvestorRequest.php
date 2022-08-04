<?php

namespace Modules\Admin\Http\Requests;

use Modules\Core\Http\Requests\BaseRequest;

class BonusForInvestorRequest extends BaseRequest
{

    public function rules()
    {
        return [
            'bonusAmount' => 'required|' . $this->getConfiguration('requestRules.amountRegex'),
        ];
    }
}
