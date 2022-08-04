<?php

namespace Modules\Admin\Http\Requests;

use Modules\Core\Http\Requests\BaseRequest;
use Modules\Core\Interfaces\ListSearchInterface;
use Modules\Core\Traits\DateBuilderTrait;

/**
 * Class AdministratorSearchRequest
 * @package Modules\Admin\Http\Requests
 */
class AdministratorSearchRequest extends BaseRequest implements ListSearchInterface
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'active'    => 'nullable|numeric|in:0,1',
            'name'      => 'nullable|string|max:50',
            'phone'     => $this->getConfiguration('requestRules.phoneSearch'),
            'email'     => $this->getConfiguration('requestRules.emailNullable'),
            'createdAt' => 'nullable|regex:' . DateBuilderTrait::$dateRangeRegex,
            'updatedAt' => 'nullable|regex:' . DateBuilderTrait::$dateRangeRegex,
        ];
    }
}
