<?php

namespace Modules\Admin\Http\Requests;

use Illuminate\Validation\Rule;
use Modules\Common\Entities\ContractTemplate;
use Modules\Core\Http\Requests\BaseRequest;

class UserAgreementCrudRequest extends BaseRequest
{
    /**
     * @return string[]
     */
    public function rules()
    {
        return [
            'name' => 'required|string|min:2',
            'version' => 'required|string|min:2',
            'type' => 'required|in:' . implode(',', ContractTemplate::getTypes()),
            'text' => 'required|string',
            'start_date' => 'required|date_format:Y-m-d',
        ];
    }
}
