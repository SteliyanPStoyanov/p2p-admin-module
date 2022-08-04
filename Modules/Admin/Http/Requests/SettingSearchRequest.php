<?php

namespace Modules\Admin\Http\Requests;

use Modules\Core\Http\Requests\BaseRequest;
use Modules\Core\Interfaces\ListSearchInterface;
use Modules\Core\Traits\DateBuilderTrait;

class SettingSearchRequest extends BaseRequest implements ListSearchInterface
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name' => $this->getConfiguration('requestRules.nameNullable'),
            'description' => 'nullable|string|max:100',
            'default_value' => 'nullable|string|max:50',
            'setting_type_id' => 'nullable|exists:setting_type,setting_type_id',
            'active' => 'nullable|numeric|in:0,1',
            'createdAt' => 'nullable|regex:' . DateBuilderTrait::$dateRangeRegex,
            'updatedAt' => 'nullable|regex:' . DateBuilderTrait::$dateRangeRegex,
        ];
    }
}
