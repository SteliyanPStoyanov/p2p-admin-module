<?php

namespace Modules\Communication\Http\Requests;

use Modules\Core\Http\Requests\BaseRequest;
use Modules\Core\Interfaces\ListSearchInterface;
use Modules\Core\Traits\DateBuilderTrait;

class EmailTemplateSearchRequest extends BaseRequest implements ListSearchInterface
{
    /**
     * @return array
     */
    public function rules()
    {
        return [
            'key' => 'nullable',
            'type' => 'nullable',
            'active' => 'nullable|numeric|in:0,1',
            'createdAt' => 'nullable|regex:' . DateBuilderTrait::$dateRangeRegex,
            'updatedAt' => 'nullable|regex:' . DateBuilderTrait::$dateRangeRegex,
            'limit'     => 'nullable|numeric',
        ];
    }
}

