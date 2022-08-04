<?php

namespace Modules\Admin\Http\Requests;

use Modules\Common\Entities\Loan;
use Modules\Core\Http\Requests\BaseRequest;
use Modules\Core\Interfaces\ListSearchInterface;

/**
 * Class ReBuyingLoanSearchRequest
 * @package Modules\Admin\Http\Requests
 */
class ReBuyingLoanSearchRequest extends BaseRequest implements ListSearchInterface
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'country_id' => 'nullable|exists:country,country_id',
            'loan_id' => 'nullable|numeric',
            'lender_id' => 'nullable|numeric',
            'type' => 'nullable|in:' . implode(',', Loan::getTypes()),
            'status' => 'nullable|in:' . implode(',', Loan::getFinalStatuses()),
            'payment_status' => 'nullable|in:' . implode(',', Loan::getPaymentStatuses()),
            'interest_rate_percent' => 'nullable|array',
            'interest_rate_percent.from' => 'nullable|numeric',
            'interest_rate_percent.to' => 'nullable|numeric',
            'period' => 'nullable|array',
            'period.from' => 'nullable|numeric',
            'period.to' => 'nullable|numeric',
        ];
    }
}
