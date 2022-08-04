<?php

namespace Modules\Admin\Http\Requests;

use Modules\Admin\Entities\Setting;
use Modules\Core\Http\Requests\BaseRequest;
use Modules\Core\Interfaces\ListSearchInterface;

class BlogPageSearchRequest extends BaseRequest implements ListSearchInterface
{
    public function rules()
    {
        return [
            'title' => 'nullable|string',
            'active' => 'nullable|numeric|in:0,1',
            'deleted' => 'nullable|numeric|in:0,1',
            'tags' => 'nullable|string',
            'content' => 'nullable|string',
            'limit' => 'nullable|numeric',
            'createdAt.from' => 'nullable|date_format:' . Setting::SHOW_FORMAT,
            'createdAt.to' => 'nullable|date_format:' . Setting::SHOW_FORMAT,
            'name' => 'nullable|string|max:50',
        ];
    }

}
