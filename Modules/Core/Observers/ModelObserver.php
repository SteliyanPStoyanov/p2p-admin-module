<?php

namespace Modules\Core\Observers;

use Auth;
use Illuminate\Support\Facades\Log;
use Modules\Common\Entities\Task;
use Modules\Common\Interfaces\HistoryInterface;
use Modules\Core\Interfaces\LoggerInterface;
use Modules\Core\Models\BaseModel;
use Modules\Core\Models\InvestorLogger;
use Modules\Core\Models\SystemLogger;
use Modules\Core\Traits\BaseModelTrait;
use Schema;

class ModelObserver
{
    const ACTION_CREATE = 'create';
    const ACTION_EDIT = 'edit';
    const ACTION_DELETE = 'delete';

    /**
     * @param BaseModel $baseModel
     *
     * @return void
     */
    public function creating($baseModel)
    {
        if (!Schema::hasColumn($baseModel->getTable(), 'created_by')) {
            return;
        }

        // could be set mannualy (Example: unit test user)
        if (empty($baseModel->created_by)) {
            $baseModel->created_by = BaseModelTrait::getAdminId();
            if (BaseModelTrait::getInvestorId()) {
                $baseModel->created_by = BaseModelTrait::getInvestorId();
            }

        }
    }

    /**
     * Handle the base model "created" event.
     *
     * @param BaseModel $baseModel
     *
     * @return void
     */
    public function created($baseModel)
    {
        $this->log($baseModel, self::ACTION_CREATE);

        $this->createHistoryEntity($baseModel);
    }

    /**
     * @param BaseModel $baseModel
     *
     * @return void
     */
    public function updating($baseModel)
    {
        // could be skipped, since some entities dont have such fields
        if (!Schema::hasColumn($baseModel->getTable(), 'updated_by')) {
            return;
        }

        $baseModel->updated_by = BaseModelTrait::getAdminId();
        if (
            false == ($baseModel instanceof Task)
            && BaseModelTrait::getInvestorId()
        ) {
            $baseModel->updated_by = BaseModelTrait::getInvestorId();
        }
    }

    /**
     * Handle the base model "updated" event.
     *
     * @param BaseModel $baseModel
     *
     * @return void
     */
    public function updated($baseModel)
    {
        $this->log($baseModel, self::ACTION_EDIT);

        $this->createHistoryEntity($baseModel);
    }

    /**
     * Handle the base model "deleted" event.
     *
     * @param BaseModel $baseModel
     *
     * @return void
     */
    public function deleted($baseModel)
    {
        $this->log($baseModel, self::ACTION_DELETE);

        $this->createHistoryEntity($baseModel);
    }

    /**
     * @param $model
     * @param string $action
     */
    protected function log($model, string $action)
    {
        if (!($model instanceof LoggerInterface)) {
            return;
        }

        $log = [
            'object_prev_state' => $model->getOriginal(),
            'object_cur_state' => $model->getAttributes(),
            'changes' => $model->getChanges(),
            'administrator_id' => BaseModelTrait::getAdminId(),
            'action' => $action,
            'table' => $model->getTable()
        ];
        try {
            $systemLogger = new SystemLogger();
            $systemLogger->log($log);

            if (!empty($model->loan_id) || !empty($model->investor_id)) {
                $log['loan_id'] = $model->loan_id ?? '';
                $log['investor_id'] = $model->investor_id ?? '';
                $investorLogger = new InvestorLogger();
                $investorLogger->log($log);
            }
        } catch (\Throwable $e) {
            Log::channel('mongo')->error($e->getMessage());
        }
    }

    /**
     * @param BaseModel $baseModel
     *
     * @return bool
     */
    protected function createHistoryEntity($baseModel): bool
    {
        if (
            !($baseModel instanceof HistoryInterface)
            || !$baseModel->getHistoryClass()
        ) {
            return false;
        }

        $foreignKey = $this->getHistoryForeignKey($baseModel);
        if (empty($foreignKey)) {
            return false;
        }

        $changes = $baseModel->getChanges();
        if ($baseModel->wasRecentlyCreated) {
            $changes = $baseModel->getAttributes();
        }

        foreach ($changes as $key => $change) {
            $originalValue = $baseModel->getOriginal($key);
            if ($baseModel->wasRecentlyCreated) {
                $originalValue = null;
            }

            if (
                $originalValue === null
                && $baseModel->getAttribute($key) === null
            ) {
                continue;
            }

            if (
                $key === $foreignKey
                || $key === $baseModel->getKeyName()
                || !in_array($key, $baseModel->getFillable())
            ) {
                continue;
            }

            $createdByType = 'administrator';
            $createdBy = BaseModelTrait::getAdminId();
            if (
                Auth::guest()
                || request()->user() == Auth::guard('investor')->user()
            ) {
                $createdByType = 'investor';
                $createdBy = request()->user('investor')->investor_id ?? $baseModel->{$foreignKey};
            }
            $data = [
                'investor_id' => $baseModel->{$foreignKey},
                'created_by' => $createdBy,
                'created_by_type' => $createdByType,
                'key' => $baseModel->getTable() . '.' . $key,
                'old_value' => $originalValue,
                'new_value' => $baseModel->getAttribute($key),
                'user_type' => str_replace('_id', '', $foreignKey),
            ];

            $historyClass = \App::make($baseModel->getHistoryClass());
            $historyClass->fill($data);

            try {
                $historyClass->save();
            } catch (\Throwable $err) {
                Log::channel('registration')->error(
                    'Failed to save change log. ' . $err->getMessage()
                );
            }
        }

        return true;
    }

    /**
     * We are checking for field existence in model
     *
     * @param  $baseModel
     *
     * @return string|null
     */
    private function getHistoryForeignKey($baseModel): ?string
    {
        if (!empty($baseModel->investor_id)) {
            return 'investor_id';
        }

        if (!empty($baseModel->administrator_id)) {
            return 'administrator_id';
        }

        return null;
    }
}
