<?php

namespace Modules\Admin\Http\Requests;

use Modules\Core\Http\Requests\BaseRequest;
use Modules\Core\Interfaces\ListSearchInterface;

/**
 * Class InvestorReferralModalRequest
 * @package Modules\Admin\Http\Requests
 */
class InvestorReferralModalRequest extends BaseRequest implements ListSearchInterface
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'investor_id' => 'nullable|numeric',
        ];
    }
}
