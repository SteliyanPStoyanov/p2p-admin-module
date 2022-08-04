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
class LoanSearchRequest extends BaseRequest implements ListSearchInterface
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
            'unlisted' => 'nullable|in:0,1',
            'type' => 'nullable|in:' . implode(',', array_keys(Loan::getTypesWithLabels())),
            'status' => 'nullable|in:' . implode(',', Loan::getMainStatuses()),
            'payment_status' => 'nullable|in:' . implode(',', Loan::getPaymentStatuses()),
            'final_payment_status' => 'nullable|in:' . implode(',', Loan::getFinalPaymentStatuses()),
            'interest_rate_percent' => 'nullable|array',
            'interest_rate_percent.from' => 'nullable|numeric',
            'interest_rate_percent.to' => 'nullable|numeric',
            'period' => 'nullable|array',
            'period.from' => 'nullable|numeric',
            'period.to' => 'nullable|numeric',
            'originator' => 'nullable|numeric',
            'createdAt.from' => 'nullable|date_format:' . Setting::SHOW_FORMAT,
            'createdAt.to' => 'nullable|date_format:' . Setting::SHOW_FORMAT,
            'order' => 'nullable|array',
            'order.loan.country_id' => 'nullable|in:asc,desc',
            'order.loan.loan_id' => 'nullable|in:asc,desc',
            'order.loan.created_at' => 'nullable|in:asc,desc',
            'order.loan.type' => 'nullable|in:asc,desc',
            'order.loan.lender_id' => 'nullable|in:asc,desc',
            'order.loan.amount' => 'nullable|in:asc,desc',
            'order.loan.amount_available' => 'nullable|in:asc,desc',
            'order.loan.interest_rate_percent' => 'nullable|in:asc,desc',
            'order.loan.period' => 'nullable|in:asc,desc',
            'order.loan.status' => 'nullable|in:asc,desc',
            'order.loan.payment_status' => 'nullable|in:asc,desc',
            'order.loan.final_payment_status' => 'nullable|in:asc,desc',
            'order.loan.unlisted' => 'nullable|in:asc,desc',
            'order.invested_sum' => 'nullable|in:asc,desc',
            'order.invested_percent' => 'nullable|in:asc,desc',
            'limit' => 'nullable|numeric',
        ];
    }
}
