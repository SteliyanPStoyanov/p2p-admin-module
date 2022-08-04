<?php

namespace Modules\Profile\Http\Requests;

use Modules\Core\Http\Requests\BaseRequest;

class StatisticTypeRequest extends BaseRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'type' => 'required|string',
        ];
    }
}
