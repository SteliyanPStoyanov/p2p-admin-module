<?php

namespace Modules\Profile\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;
use Modules\Core\Http\Requests\BaseRequest;

class CompanyDocumentUploadRequest extends BaseRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'document_file' => 'required',
            'document_file.*' => 'required'
        ];
    }

    /**
     * @param Validator $validator
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Session\SessionManager|\Illuminate\Session\Store|mixed|void
     * @throws ValidationException
     */
    protected function failedValidation(Validator $validator)
    {
        if ($this->wantsJson() || $this->ajax()) {
            return session('fail', $validator->errors());
        }
        parent::failedValidation($validator);
    }

    /**
     * @return array
     */
    public function messages(): array
    {
        return [
            'document_file.required' => __('common.UploadDocumentFieldIsRequired'),
            'document_file.*.required' => __('common.UploadDocumentFieldIsRequired'),
        ];
    }

}
