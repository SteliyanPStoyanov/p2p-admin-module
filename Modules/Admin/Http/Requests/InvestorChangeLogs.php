<?php

namespace Modules\Admin\Http\Requests;

use Modules\Admin\Entities\Setting;
use Modules\Core\Http\Requests\BaseRequest;
use Modules\Core\Interfaces\ListSearchInterface;

class InvestorChangeLogs extends BaseRequest implements ListSearchInterface
{
    public function rules()
    {
        return [
            'createdAt' => 'nullable|date_format:' . Setting::SHOW_FORMAT,
            'key' => 'nullable|string',
            'new_value' => 'nullable|string ',
        ];
    }
}
