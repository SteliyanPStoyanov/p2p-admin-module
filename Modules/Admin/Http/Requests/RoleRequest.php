<?php

namespace Modules\Admin\Http\Requests;

use Modules\Admin\Entities\Role;
use Modules\Core\Http\Requests\BaseRequest;

class RoleRequest extends BaseRequest
{
    /**
     * @return string[]
     */
    public function rules()
    {
        return [
            'name'       => 'required|string|min:5|max:50',
            'permissions' => 'required|array',
            'permissions.*' => 'exists:permission,id',
            'priority' =>
                'required|numeric|min:' . Role::PRIORITY_MIN . '|max:' . \Auth::user()
                ->getMaxPriority(),
        ];
    }
}
