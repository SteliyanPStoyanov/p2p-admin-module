<?php

namespace Modules\Profile\Http\Requests;

use Illuminate\Http\Request;
use Modules\Core\Http\Requests\BaseRequest;
use Auth;

class WithdrawRequest extends BaseRequest
{
    /**
     * @param Request $request
     *
     * @return string[]
     */
    public function rules(Request $request)
    {
        $minAmount = config('profile.min_amount');
        $uninvested = Auth::guard('investor')->user()->wallet()->uninvested;
        $invested = Auth::guard('investor')->user()->wallet()->invested;

        if (($uninvested + $invested) < $minAmount) {
            $minAmount = 0.01;
        }

        $rules = [
            'amount' => 'required|min:' . $minAmount . '|max:' . $uninvested . '|' . $this->getConfiguration('requestRules.amountRegex'),
        ];

        if (!empty($request['bank_account_id'])) {
            $rules['bank_account_id'] = 'required|numeric|digits_between:1,18|exists:bank_account,bank_account_id';
        }
        return $rules;
    }
}
