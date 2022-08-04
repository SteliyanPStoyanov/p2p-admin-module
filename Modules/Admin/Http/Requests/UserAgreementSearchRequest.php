<?php

namespace Modules\Admin\Http\Requests;

use Illuminate\Validation\Rule;
use Modules\Admin\Entities\Setting;
use Modules\Common\Entities\ContractTemplate;
use Modules\Core\Http\Requests\BaseRequest;

class UserAgreementSearchRequest extends BaseRequest
{
    /**
     * @return string[]
     */
    public function rules()
    {
        return [
            'name' => 'nullable',
            'type' => 'nullable|in:' . implode(',', ContractTemplate::getTypes()),
            'createdAt.from' => 'nullable|date_format:' . Setting::SHOW_FORMAT,
            'createdAt.to' => 'nullable|date_format:' . Setting::SHOW_FORMAT,
        ];
    }
}
