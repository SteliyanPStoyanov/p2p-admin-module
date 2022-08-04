<?php

namespace Modules\Admin\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\ValidationException;
use Modules\Common\Entities\Task;

/**
 * Class TaskModalRequest
 * @package Modules\Admin\Http\Requests
 */
class TaskModalRequest extends FormRequest
{
    /**
     * @return array
     */
    public function rules(): array
    {
        return [
            'task_id' => 'nullable|numeric|exists:task,task_id',
            'task_type' => 'nullable|in:' . implode(
                    ',',
                    [
                        Task::TASK_TYPE_VERIFICATION,
                        Task::TASK_TYPE_WITHDRAW,
                        Task::TASK_TYPE_BONUS_PAYMENT,
                        Task::TASK_TYPE_FIRST_DEPOSIT,
                        Task::TASK_TYPE_MATCH_DEPOSIT,
                        Task::TASK_TYPE_NOT_VERIFIED,
                        Task::TASK_TYPE_REJECTED_VERIFICATION,
                    ]
                ),
        ];
    }

    /**
     * @param Validator $validator
     * @throws ValidationException
     */
    protected function failedValidation(Validator $validator)
    {
         if ($this->wantsJson() || $this->ajax()) {
            throw new HttpResponseException(response()->json($validator->errors(), 403));
        }
        parent::failedValidation($validator);
    }
}
