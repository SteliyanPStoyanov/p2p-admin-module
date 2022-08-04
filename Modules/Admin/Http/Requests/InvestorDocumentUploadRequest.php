<?php

namespace Modules\Admin\Http\Requests;

use Modules\Core\Http\Requests\BaseRequest;

class InvestorDocumentUploadRequest extends BaseRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'document_type_id' => 'required|numeric',
            'document_file' => 'required',
            'document_file.*' => [
                'file',
                'mimetypes:text/plain,text/csv,application/vnd.ms-excel,application/vnd.openxmlformats-officedocument.spreadsheetml.sheet,application/pdf,application/msword,application/vnd.openxmlformats-officedocument.wordprocessingml.document,image/png,image/jpeg,image/gif,image/bmp',
                'max:' . $this->getConfiguration('requestRules.maxFileSize'),
            ]
//                'image|max:'. $this->getConfiguration('requestRules.maxFileSize'),
        ];
    }
}
