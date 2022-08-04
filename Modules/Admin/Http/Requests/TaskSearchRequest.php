<?php

namespace Modules\Admin\Http\Requests;

use Modules\Admin\Entities\Setting;
use Modules\Common\Entities\Task;
use Modules\Core\Http\Requests\BaseRequest;
use Modules\Core\Interfaces\ListSearchInterface;
use Modules\Core\Traits\DateBuilderTrait;

/**
 * Class TaskSearchRequest
 *
 * @package Modules\Admin\Http\Requests
 */
class TaskSearchRequest extends BaseRequest implements ListSearchInterface
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'task_id' => 'nullable|numeric',
            'name' => 'nullable|string|max:50',
            'createdAt.from' => 'nullable|date_format:' . Setting::SHOW_FORMAT,
            'createdAt.to' => 'nullable|date_format:' . Setting::SHOW_FORMAT,
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
            'status' => 'nullable|in:' . implode(
                    ',',
                    [
                        Task::TASK_STATUS_NEW,
                        Task::TASK_STATUS_DONE,
                        Task::TASK_STATUS_PROCESSING
                    ]
                ),
            'amount' => 'nullable|array',
            'amount.from' => 'nullable|' . $this->getConfiguration('requestRules.amountRegex'),
            'amount.to' => 'nullable|' . $this->getConfiguration('requestRules.amountRegex'),
            'limit' => 'nullable|numeric',
        ];
    }
}
