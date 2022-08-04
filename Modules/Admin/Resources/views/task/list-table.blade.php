<table class="table">
    <thead>
    <tr>
        <th scope="col">{{__('common.Id')}}</th>
        <th scope="col">{{__('common.InvestorId')}}</th>
        <th scope="col">{{__('common.Name')}}</th>
        <th scope="col">{{__('common.TaskType')}}</th>
        <th scope="col">{{__('common.Amount')}}</th>
        <th scope="col">{{__('common.Status')}}</th>
        <th scope="col">{{__('common.CreatedAt')}}</th>
        <th scope="col">{{__('common.Actions')}}</th>
    </tr>
    </thead>
    <tbody>

    @foreach($tasks as $task)
        <tr>
            <td>{{ $task->task_id }}</td>
            <td>
                @if(!empty($task->investor_id))
                    <a target="_blank"
                       href="{{ route('admin.investors.overview', $task->investor_id) }}">{{ $task->investor_id }}</a>
                @endif
            </td>
            <td>
                @if(!empty($task->investor_id))
                    @php
                        $investor = $task->investor;
                    @endphp
                    @if(!empty($investor->first_name))
                        {{ $investor->first_name . (!empty($investor->middle_name) ? ' ' . $investor->middle_name : '')}} {{ $investor->last_name }}
                    @endif
                @endif
            </td>
            <td>{{ $task->task_type }}</td>
            <td>{{ $task->amount }}</td>
            <td class="status">{{ $task->status }}
                @if($task->status === \Modules\Common\Entities\Task::TASK_STATUS_PROCESSING)
                    by {{$task->getUpdateAdmin()}}
                @endif </td>
            <td>{{ formatDate($task->created_at, 'd.m.y H:i:s') ?? ''}}</td>
            <td class="tableRow">
                @php
                    $activeBunch = null;
                    if (isset($task->investor_id)){
                        $activeBunch = $task->investor->getInvestmentBunch();
                    }
                @endphp
                @if( $task->status !=\Modules\Common\Entities\Task::TASK_STATUS_DONE)
                    <button style="min-width: 170px; float: left;" class="btn btn-primary process-task"
                            data-taskId="{{$task->task_id}}" data-taskType="{{$task->task_type}}"
                            @if(
                                ($task->status != \Modules\Common\Entities\Task::TASK_STATUS_NEW
                                && $task->processing_by != Auth::user()->administrator_id)
                                 || ( $activeBunch
                                && $task->task_type == \Modules\Common\Entities\Task::TASK_TYPE_WITHDRAW)
                            )
                            disabled
                        @endif
                    >
                        @if($activeBunch && $task->task_type == \Modules\Common\Entities\Task::TASK_TYPE_WITHDRAW)
                            {{__('common.CurrentInvesting')}}
                        @else
                            {{__('common.'.ucfirst(Str::camel($task->task_type)))}}
                        @endif
                    </button>
                @endif
                <a href="#" style="float: left; margin-left: 10px;"
                   data-href="{{route('admin.tasks.delete', $task->task_id)}}" role="button"
                   aria-pressed="true" title="Delete" data-toggle="modal" data-target="#confirm-delete"
                   class="btn btn-danger btn-extra-sm"
                   button-type="delete">
                    <span><i aria-hidden="true" class="fa fa-trash"></i></span>
                </a>
            </td>
        </tr>
    @endforeach
    </tbody>
    <tfoot>
    <tr id="pagination-nav">
        <td colspan="10">
            {{ $tasks->links() }}
        </td>
    </tr>
    </tfoot>
</table>
