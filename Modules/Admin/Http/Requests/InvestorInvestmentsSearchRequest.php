<?php

namespace Modules\Admin\Http\Requests;

use Modules\Admin\Entities\Setting;
use Modules\Common\Entities\Loan;
use Modules\Core\Http\Requests\BaseRequest;
use Modules\Core\Interfaces\ListSearchInterface;

/**
 * Class LoanSearchRequest
 *
 * @package Modules\Admin\Http\Requests
 */
class InvestorInvestmentsSearchRequest extends BaseRequest implements ListSearchInterface
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'investment.loan_id' => 'nullable|numeric',
            'unlisted' => 'nullable|in:0,1',
            'type' => 'nullable|in:' . implode(',', array_keys(Loan::getTypesWithLabels())),
            'status' => 'nullable|in:' . implode(',', Loan::getMainStatuses()),
            'payment_status' => 'nullable|in:' . implode(',', Loan::getPaymentStatuses()),
            'interest_rate_percent' => 'nullable|array',
            'interest_rate_percent.from' => 'nullable|numeric',
            'interest_rate_percent.to' => 'nullable|numeric',
            'period' => 'nullable|array',
            'period.from' => 'nullable|numeric',
            'period.to' => 'nullable|numeric',
            'originator' => 'nullable|numeric',
            'created_at.from' => 'nullable|date_format:' . Setting::SHOW_FORMAT,
            'created_at.to' => 'nullable|date_format:' . Setting::SHOW_FORMAT,
            'loan_created_at.from' => 'nullable|date_format:' . Setting::SHOW_FORMAT,
            'loan_created_at.to' => 'nullable|date_format:' . Setting::SHOW_FORMAT,
            'order.*' => 'nullable',
            'limit' => 'nullable|numeric',
            'market' => 'nullable|numeric',
        ];
    }
}
