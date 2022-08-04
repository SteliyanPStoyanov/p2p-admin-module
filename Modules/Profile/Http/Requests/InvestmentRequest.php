<?php

namespace Modules\Profile\Http\Requests;

use Modules\Admin\Entities\Setting;
use Modules\Core\Http\Requests\BaseRequest;

class InvestmentRequest extends BaseRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $minAmount = \SettingFacade::getMinAmountForInvest();
        return [
            'amount' => 'required|min:' . $minAmount . '|' . $this->getConfiguration(
                    'requestRules.amountRegex'
                ),
        ];
    }
}
