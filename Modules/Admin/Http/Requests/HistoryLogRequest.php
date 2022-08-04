<?php

namespace Modules\Admin\Http\Requests;
use Modules\Core\Http\Requests\BaseRequest;
use Modules\Core\Interfaces\ListSearchInterface;

class HistoryLogRequest extends BaseRequest implements ListSearchInterface
{
    public function rules()
    {
        return [
            'command'   => 'nullable',
            'createdAt' => 'nullable',
            'limit'     => 'nullable|numeric'
        ];
    }
}
