<?php

namespace Modules\Common\Repositories;

use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Modules\Common\Entities\BankAccount;
use Modules\Common\Entities\Currency;
use Modules\Common\Entities\ImportedPayment;
use Modules\Common\Entities\Task;
use Modules\Common\Entities\Transaction;
use Modules\Common\Entities\Wallet;
use Modules\Core\Repositories\BaseRepository;

class TaskRepository extends BaseRepository
{
    /**
     * @param  array  $data
     *
     * @return Task
     */
    public function create(array $data)
    {
        $task = new Task();
        $task->fill($data);
        $task->save();

        return $task;
    }

    /**
     * @param  int  $limit
     * @param  array  $where
     * @param  array|string[]  $order
     * @param  bool  $showDeleted
     *
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function getAll(
        int $limit,
        array $where = [],
        array $order = ['task.active' => 'DESC', 'task_id' => 'DESC'],
        bool $showDeleted = false
    ) {
        $where = $this->checkForDeleted($where, $showDeleted, 'task');

        $builder = DB::table('task');
        $builder->select(DB::raw('task.*'));
        $builder->leftJoin('investor', 'investor.investor_id', '=', 'task.investor_id');

        if (!empty($where)) {
            $builder->where($where);
        }

        if (!in_array('task.status', array_column($where, 0))) {
            $builder->whereIn('task.status', [Task::TASK_STATUS_NEW, Task::TASK_STATUS_PROCESSING]);
        }

        if (!empty($order)) {
            foreach ($order as $key => $direction) {
                $builder->orderBy($key, $direction);
            }
        }

        $result = $builder->paginate($limit);
        $records = Task::hydrate($result->all());
        $result->setCollection($records);

        return $result;
    }

    /**
     * @return string[]
     */
    public function getStatuses()
    {
        return [
            Task::TASK_STATUS_NEW,
            Task::TASK_STATUS_PROCESSING,
            Task::TASK_STATUS_DONE,
        ];
    }

    /**
     * @return string[]
     */
    public function getTypes()
    {
        return [
            Task::TASK_TYPE_VERIFICATION,
            Task::TASK_TYPE_WITHDRAW,
            Task::TASK_TYPE_BONUS_PAYMENT,
        ];
    }

    /**
     * @param  int  $id
     *
     * @return Task
     */
    public function getTaskById(int $id)
    {
        return Task::where('task_id', $id)->first();
    }


    public function getTaskByInvestorId(int $investorId)
    {
        return Task::where(
            'investor_id',
            '=',
            $investorId
        )->get();
    }


    /**
     * @param  Task  $task
     *
     * @return Task
     */
    public function updateProcessBy(Task $task)
    {
        $task->processing_at = Carbon::now();
        $task->processing_by = Auth::user()->administrator_id;
        $task->status = Task::TASK_STATUS_PROCESSING;
        $task->save();

        return $task;
    }

    /**
     * @param  Task  $task
     *
     * @return Task
     */
    public function finalize(Task $task)
    {
        $now = Carbon::now();
        $task->status = Task::TASK_STATUS_DONE;
        $task->done_at = $now;
        $task->done_by = Auth::user()->administrator_id;
        $task->time_spent = $now->diffInSeconds($task->processing_at);
        $task->save();

        return $task;
    }

    /**
     * @return array
     */
    public function getInvestmentBunchesWithActiveWithdrawRequests(): array
    {
        $builder = DB::table('investment_bunch as ib');
        $builder->select(DB::raw('ib.*'));
        $builder->leftJoin('task as t', function ($join) {
            $join->on('t.investor_id', '=', 'ib.investor_id');
            $join->where('t.active', '=', '1');
            $join->where('t.deleted', '=', '0');
            $join->whereIn('t.task_type', [Task::TASK_TYPE_WITHDRAW, Task::TASK_STATUS_PROCESSING]);
        });
        $builder->where('ib.finished', '=', '0');
        $rows = $builder->get();

        $result = [];
        foreach ($rows as $row) {
            $result[$row->investor_id] = $row;
        }

        return $result;
    }


    public function getInvestorBunch(int $investorId)
    {
        $builder = DB::table('investment_bunch as ib');
        $builder->select(DB::raw('ib.*'));
        $builder->where(
            [
                'ib.finished' => 0,
                'ib.investor_id' => $investorId
            ]
        );

        return $builder->first();
    }

    /**
     * @param  Task  $task
     *
     * @return Task
     */
    public function exitTask(Task $task): Task
    {
        $task->processing_at = null;
        $task->processing_by = null;
        $task->status = Task::TASK_STATUS_NEW;
        $task->save();

        return $task;
    }

    /**
     * @param  Task  $task
     *
     * @return Task
     */
    public function cancelTask(Task $task): Task
    {
        $task->processing_at = Carbon::now();
        $task->processing_by = Auth::user()->administrator_id;
        $task->status = Task::TASK_STATUS_CANCEL;
        $task->save();

        return $task;
    }

    /**
     * @param  Task  $task
     */
    public function delete(Task $task)
    {
        $task->delete();
    }

    /**
     * @param ImportedPayment $payment
     * @param Wallet $wallet
     * @param BankAccount $bankAccount
     *
     * @return Task
     */
    public function createFirstDepositTask(
        ImportedPayment $payment,
        Wallet $wallet,
        BankAccount $bankAccount
    ) {
        return $this->create(
            [
                'task_type' => Task::TASK_TYPE_FIRST_DEPOSIT,
                'investor_id' => $payment->investor_id,
                'wallet_id' => $wallet->getId(),
                'currency_id' => Currency::ID_EUR,
                'bank_account_id' => $bankAccount->getId(),
                'amount' => $payment->amount,
                'status' => Task::TASK_STATUS_NEW,
                'imported_payment_id' => $payment->getId(),
            ]
        );
    }
}
