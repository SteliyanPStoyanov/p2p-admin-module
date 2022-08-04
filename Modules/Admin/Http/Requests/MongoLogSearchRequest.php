<?php

namespace Modules\Admin\Http\Requests;
use Modules\Core\Http\Requests\BaseRequest;
use Modules\Core\Interfaces\ListSearchInterface;
use Modules\Core\Traits\DateBuilderTrait;

class MongoLogSearchRequest extends BaseRequest implements ListSearchInterface
{
    public function rules()
    {
        return [
            'table' => 'nullable|string|max:30',
            'action' => 'nullable|string|in:create,edit,delete',
            'investor_id' => 'nullable|numeric',
            'loan_id' => 'nullable|numeric',
            'created_at' => 'nullable|regex:' . DateBuilderTrait::$dateRangeRegex,
            'limit'     => 'nullable|numeric',
        ];
    }
}
