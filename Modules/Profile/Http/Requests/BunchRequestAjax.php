<?php

namespace Modules\Profile\Http\Requests;

use Modules\Core\Http\Requests\BaseRequest;

class BunchRequestAjax extends BaseRequest
{
    public function rules()
    {
        return [
            'bunchId' => 'required|numeric',
        ];
    }

}
