<?php

namespace Modules\Admin\Http\Requests;

use Modules\Core\Http\Requests\BaseRequest;

/**
 * Class UploadPayments
 *
 * @package Modules\Admin\Http\Requests
 */
class UploadPayments extends BaseRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'import_file' => 'required|file|mimetypes:text/plain,text/csv,application/vnd.ms-excel,application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ];
    }
}
