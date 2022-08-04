<?php

namespace Modules\Communication\Http\Requests;

use Modules\Core\Http\Requests\BaseRequest;

class EmailSendRequest extends BaseRequest
{
    /**
     * @return string[]
     */
    public function rules()
    {
        return [
            'email_template_id' => 'required',
            'investor_id' => 'required|numeric',
            'email' => $this->getConfiguration('requestRules.email'),
        ];
    }
}
