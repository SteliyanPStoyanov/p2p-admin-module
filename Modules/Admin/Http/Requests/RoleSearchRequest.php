<?php

namespace Modules\Admin\Http\Requests;

use Modules\Core\Http\Requests\BaseRequest;
use Modules\Core\Interfaces\ListSearchInterface;
use Modules\Core\Traits\DateBuilderTrait;

/**
 * Class RoleSearchRequest
 * @package Modules\Admin\Http\Requests
 */
class RoleSearchRequest extends BaseRequest implements ListSearchInterface
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'active'     => 'nullable|numeric',
            'createdAt'  => 'nullable|regex:' . DateBuilderTrait::$dateRangeRegex,
            'name'       => $this->getConfiguration('requestRules.nameNullable'),
            'updatedAt'  => 'nullable|regex:' . DateBuilderTrait::$dateRangeRegex,
        ];
    }
}
