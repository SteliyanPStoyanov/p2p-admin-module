<?php

namespace Modules\Profile\Http\Requests;

use Carbon\Carbon;
use Modules\Core\Http\Requests\BaseRequest;
use Modules\Core\Interfaces\ListSearchInterface;

/**
 * Class InvestLoanSearchRequest
 *
 * @package Modules\Common\Http\Requests
 */
class ProfileTransactionSearchRequest extends BaseRequest implements ListSearchInterface
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'createdAt.from' => 'nullable|date_format:d.m.Y',
            'createdAt.to' => 'nullable|date_format:d.m.Y',
            'limit' => 'nullable|numeric',
            'type.*' => 'nullable',
        ];
    }

    public function validationData()
    {
        $data = parent::validationData();

        // change date format before validation
        // some browsers overwrite out format
        try {
            $input = parent::all();
            $newData = [];

            if (!empty($input['createdAt']['from']) && !preg_match("/([0-9]{2}\.[0-9]{2}\.[0-9]{4})/", $input['createdAt']['from'])) {
                $from = Carbon::parse($input['createdAt']['from']);
                // $this->createdAt['from'] = $from->format('d.m.Y');
                $newData['createdAt']['from'] = $from->format('d.m.Y');
            }

            if (!empty($input['createdAt']['to'])  && !preg_match("/([0-9]{2}\.[0-9]{2}\.[0-9]{4})/", $input['createdAt']['to'])) {
                $to = Carbon::parse($input['createdAt']['to']);
                // $this->createdAt['to'] = $to->format('d.m.Y');
                $newData['createdAt']['to'] = $to->format('d.m.Y');
            }

            if (!empty($newData)) {
                return array_merge($data, $newData);
            }


        } catch (\Throwable $e) {
            // nothing
        }

        return parent::validationData();
    }
}
