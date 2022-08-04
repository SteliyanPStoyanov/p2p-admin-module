<?php


namespace Modules\Admin\Http\Requests;


use Modules\Core\Http\Requests\BaseRequest;

class InvestorCommentRequest extends BaseRequest
{
    public function rules()
    {
        return [
            'comment' => 'nullable|max:255',
            'investor_id' => 'required|numeric',
        ];
    }

}
