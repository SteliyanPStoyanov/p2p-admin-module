<?php

namespace Modules\Admin\Http\Requests;

use Modules\Core\Http\Requests\BaseRequest;

class InvestorLoginLogSearchRequest extends BaseRequest
{
    public function rules()
    {
        return [
            'id' => 'nullable|numeric',
            'investor_id' => 'nullable|numeric',
            'ip' => 'nullable|string',
            'active' => 'nullable|numeric|in:0,1',
            'limit' => 'nullable|numeric',
        ];
    }
}
