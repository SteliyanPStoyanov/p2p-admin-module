<?php

namespace Modules\Communication\Http\Requests;

use Illuminate\Validation\Rule;
use Modules\Core\Http\Requests\BaseRequest;

class EmailTemplateEditRequest extends BaseRequest
{
    /**
     * @return string[]
     */
    public function rules()
    {
        return [
            'title' => [
                'required',
                'min:2',
                Rule::unique('email_template')
                    ->ignore($this->id, 'email_template_id')
            ],
            'body' => 'required',
            'description' => 'required',
            'text' => 'required|string',
        ];
    }
}
