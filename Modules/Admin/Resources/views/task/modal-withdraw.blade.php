<div class="modal fade check-modal" id="modal-{{$task->task_id}}" tabindex="-1" role="dialog"
     aria-labelledby="modal-{{$task->task_id}}" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-body">
                <form action="{{route('admin.tasks.exit-task', $task->task_id)}}">
                    <button type="submit" class="close" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </form>
                <form action="{{route('admin.tasks.withdraw',$task->task_id)}}" method="POST">
                    {{ admin_csrf_field() }}
                    <div class="mb-3"><b>{{__('common.Requestedwithdrawal')
                                                }}</b></div>
                    <div class="mb-1">{{__('common.AmountToWithdraw')}}:
                        {{amount($task->amount)}}
                    </div>
                    <div class="mb-1">{{__('common.UninvestedAmount')}}:
                        {{amount($task->investor->wallet()->uninvested)}}
                    </div>
                    <div class="mb-1">{{__('common.BlockedAmount')}}:
                        {{amount($task->investor->wallet()->blocked_amount)}}
                    </div>
                    <div class="mb-4">{{__('common.BalanceAfterTransaction')}}:
                        @php
                            $total = $task->investor->wallet()->getTotalAmount() - $task->amount;
                        @endphp
                        @if($total >= 0)
                            {{amount($total)}}
                        @else
                            <span class="text-danger"> - {{amount(abs($total))}} </span>
                        @endif
                    </div>
                    <div class="mb-3"><b>{{__('PaymentDetails')}}</b></div>
                    <div class="mb-1">
                        <b>{{__('common.InvestorId')}}: </b>{{$task->investor->investor_id}}
                    </div>
                    <div class="mb-1">
                        <b>{{__('common.Beneficiary')}}: </b>{{$task->investor->first_name}}
                        {{$task->investor->middle_name}}
                        {{$task->investor->last_name}}
                    </div>
                    <div class="mb-3">
                        <b>{{__('common.Iban')}}:</b> {{$task->getBankAccount()->iban}}
                    </div>
                    <input type="text" id="bankTransactionID-{{$task->task_id}}"
                           name="bank_transaction_id"
                           class="form-control" hidden>
                    <button type="submit" class="btn btn-success" style="width: 45%" @if($total < 0 ) disabled @endif >
                        {{__('common.Completed')}}
                    </button>
                </form>
                <form style="width: 45%; float: right; margin-top: -35px;"
                      action="{{route('admin.tasks.cancel-task', $task->task_id)}}">
                    <button type="submit" class="btn btn-danger w-100">
                        {{__('common.Cancel')}}
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
