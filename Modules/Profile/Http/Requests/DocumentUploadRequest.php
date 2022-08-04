<?php

namespace Modules\Profile\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Request;
use Modules\Core\Http\Requests\BaseRequest;

class DocumentUploadRequest extends BaseRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(Request $request)
    {
        $validation = [
            'document_type_id' => 'required|numeric',
            'document_file.front' => 'required|mimes:jpeg,bmp,png,gif,pdf|max:' . $this->getConfiguration('requestRules.maxFileSize'),
            'document_file.selfie' => 'required|mimes:jpeg,bmp,png,gif,pdf|max:' . $this->getConfiguration('requestRules.maxFileSize')
        ];
        if ($request->document_type_id == 1) {
            $validation['document_file.back'] = 'required|mimes:jpeg,bmp,png,gif,pdf|max:' . $this->getConfiguration(
                    'requestRules.maxFileSize'
                );
        }


        return $validation;
    }

    protected function failedValidation(Validator $validator)
    {
        return back()->withErrors($validator->errors());
    }

    public function messages()
    {
        return [
            'document_file.front.required' => __('common.UploadDocumentFieldIsRequired'),
            'document_file.back.required' => __('common.UploadDocumentFieldIsRequired'),
            'document_file.selfie.required' => __('common.SelfieFileRequired'),
            'document_file.front.max' => __('common.UploadDocumentMaxFilesize'),
            'document_file.back.max' => __('common.UploadDocumentMaxFilesize'),
            'document_file.selfie.max' => __('common.UploadDocumentMaxFilesize'),
            'document_file.front.mimes' => __('common.UploadDocumentMimes'),
            'document_file.back.mimes' => __('common.UploadDocumentMimes'),
            'document_file.selfie.mimes' => __('common.UploadDocumentMimes'),
        ];
    }
}
