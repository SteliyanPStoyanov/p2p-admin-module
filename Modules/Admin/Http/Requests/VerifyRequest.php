<?php

namespace Modules\Admin\Http\Requests;

use Modules\Core\Http\Requests\BaseRequest;
use Modules\Admin\Entities\Verification;

/**
 * Class VerifyRequest
 * @package Modules\Admin\Http\Requests
 */
class VerifyRequest extends BaseRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name' => 'nullable|in:0,1',
            'birth_date' => 'nullable|in:0,1',
            'address' => 'nullable|in:0,1',
            'citizenship' => 'nullable|in:0,1',
            'photo' => 'nullable|in:0,1',
            'action' => 'required|in:' . implode(
                    ',',
                    [
                        Verification::MARK_VERIFIED,
                        Verification::REJECT_VERIFICATION,
                        Verification::REQUEST_DOCUMENTS,
                    ]
                ),
            'comment' => 'nullable|string',
        ];
    }
}
