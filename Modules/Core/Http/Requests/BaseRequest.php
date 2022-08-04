<?php

namespace Modules\Core\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Modules\Core\Exceptions\JsonException;
use Modules\Core\Interfaces\ListSearchInterface;
use Modules\Core\Traits\DateBuilderTrait;
use Modules\Core\Traits\ValidationTrait;

class BaseRequest extends FormRequest
{
    use DateBuilderTrait, ValidationTrait;

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    protected function failedValidation(Validator $validator)
    {
        if ($this instanceof ListSearchInterface) {
            throw new JsonException($validator->getMessageBag(), 400);
        }

        parent::failedValidation($validator);
    }
}
