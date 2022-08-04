<?php

namespace Modules\Admin\Http\Requests;

use Modules\Core\Http\Requests\BaseRequest;

class SettingEditRequest extends BaseRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name' => $this->getConfiguration('requestRules.name'),
            'description' => 'required|string|min:2|max:100',
            'default_value' => 'required|string|min:1|max:50',
        ];
    }
}
