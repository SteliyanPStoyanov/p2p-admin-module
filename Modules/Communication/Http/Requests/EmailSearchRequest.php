<?php

namespace Modules\Communication\Http\Requests;

use Modules\Core\Http\Requests\BaseRequest;
use Modules\Core\Interfaces\ListSearchInterface;
use Modules\Core\Traits\DateBuilderTrait;

class EmailSearchRequest extends BaseRequest implements ListSearchInterface
{
    /**
     * @return array
     */
    public function rules()
    {
        return [
            'title' => 'nullable',
            'type' => 'nullable',
            'sender_from' => 'nullable',
            'send_at' => 'nullable|regex:' . DateBuilderTrait::$dateRangeRegex,
            'limit'   => 'nullable|numeric',
        ];
    }
}

