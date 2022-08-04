<?php

namespace Modules\Admin\Http\Requests;

use Illuminate\Validation\Rule;
use Modules\Core\Http\Requests\BaseRequest;

/**
 * Class AdministratorEditRequest
 *
 * @package Modules\Admin\Http\Requests
 */
class AdministratorEditRequest extends BaseRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        //Remove duplicates in permissions
        $this->request->set('permissions', array_unique($this->input('permissions', [])));
        $rules = [
            'username' => [
                'required',
                'min:2',
                Rule::unique('administrator')
                    ->ignore($this->id, 'administrator_id')
            ],
            'first_name' => 'required|min:2',
            'middle_name' => 'min:2|nullable',
            'last_name' => 'required|min:2',
            'phone' => $this->getConfiguration('requestRules.phone'),
            'email' => $this->getConfiguration('requestRules.email'),
            'permissions' => 'nullable|array',
            'permissions.*' => 'numeric|exists:permission,id',
            'roles' => 'nullable|array',
            'roles.*' => 'numeric|exists:role,id',
            'avatar' => 'image|mimes:jpeg,jpg,png|max:1024|nullable',
        ];

        if ($this->filled('password')) {
            $rules['password'] = 'required | confirmed';
        }

        return $rules;
    }

    /**
     * @return array|string[]
     */
    public function messages()
    {
        $messages = [
            'roles.required' => __('messages.AdminRole'),
            'permissions.required' => __('messages.AdminPermission'),
        ];

        return $messages;
    }
}
