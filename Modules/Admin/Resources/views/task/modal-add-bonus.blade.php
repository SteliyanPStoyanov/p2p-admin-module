<div class="modal fade check-modal" id="modal-{{$task->task_id}}" tabindex="-1" role="dialog"
     aria-labelledby="modal-{{$task->task_id}}" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    {{__('common.addBonus')}}
                </h5>
                <form action="{{route('admin.tasks.exit-task', $task->task_id)}}">
                    <button type="submit" class="close" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </form>
            </div>
            <div class="modal-body">

                <form action="{{route('admin.task.addBonus',$task->task_id)}}" method="POST">
                    {{ admin_csrf_field() }}
                    <div class="mb-3"><b>{{__('common.BonusPayment')
                                                }}</b></div>
                    @forelse($task->investor->investorBonus() as $type => $amount)
                        <div class="mb-1">{{__('common.'.$type)}}:
                            {{amount($amount)}}
                        </div>
                    @empty
                        <p>{{__('common.NoBonus')}}</p>
                    @endforelse

                    <input type="text" id="bankTransactionID-{{$task->task_id}}"
                           name="bank_transaction_id"
                           class="form-control" hidden>
                    <button type="submit" class="btn btn-success mt-4" style="width: 45%">
                        {{__('common.Completed')}}
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
