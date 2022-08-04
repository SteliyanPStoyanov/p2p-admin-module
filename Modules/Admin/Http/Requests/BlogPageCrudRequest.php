<?php


namespace Modules\Admin\Http\Requests;


use Modules\Admin\Entities\Setting;
use Modules\Core\Http\Requests\BaseRequest;

class BlogPageCrudRequest extends BaseRequest
{
    public function rules()
    {
        return [
            'title' => 'required|string',
            'date' => 'nullable|date',
            'tags' => 'nullable|string',
            'images.*' => 'image|mimes:jpeg,jpg,png|max:5024|nullable',
            'content' => 'required|string',
            'administrator_id' => 'required|numeric',
        ];
    }
}
