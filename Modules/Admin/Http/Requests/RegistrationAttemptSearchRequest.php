<?php


namespace Modules\Admin\Http\Requests;


use Modules\Core\Http\Requests\BaseRequest;

class RegistrationAttemptSearchRequest extends BaseRequest
{
    public function rules()
    {
        return [
            'id' => 'nullable|numeric',
            'email' => $this->getConfiguration('requestRules.emailNullable'),
            'ip' => 'nullable|string',
            'active' => 'nullable|numeric|in:0,1',
            'limit' => 'nullable|numeric',
        ];
    }

}
