<div class="modal fade check-modal" id="modal-{{$task->task_id}}" tabindex="-1" role="dialog"
     aria-labelledby="modal-{{$task->task_id}}" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    {{ __('common.MatchDeposit') }}
                </h5>
                <form action="{{route('admin.tasks.exit-task', $task->task_id)}}">
                    <button type="submit" class="close" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </form>
            </div>
            <div class="modal-body" >
                <form  action="{{ route('admin.tasks.match-deposit', $task->getId()) }}" method="POST">
                    {{ admin_csrf_field() }}
                            <div class="mb-3"><b>{{__('common.MatchingUnsuccessful')}}</b></div>
                            <div class="mb-1">
                                <div class="row">
                                    <div class="col-3"><b>{{__('common.Amount')}}</b>:</div>
                                    <div class="col-9">{{amount($task->amount)}}</div>
                                </div>
                            </div>
                            <div class="mb-1">
                                 <div class="row">
                                     <div class="col-3"><b>{{__('common.Name')}}</b>:</div>
                                     <div class="col-9">{{ getNameFromBasis($task->importedPayment->basis) }}</div>
                                 </div>
                            </div>
                            <div class="mb-1">
                                <div class="row">
                                    <div class="col-3"><b>{{__('common.Iban')}}</b>:</div>
                                    <div class="col-9">{{ $task->importedPayment->iban }}</div>
                                </div>
                            </div>
                            <div class="mb-1">
                                <div class="row">
                                    <div class="col-3"><b>{{__('common.Bic')}}</b>:</div>
                                    <div class="col-9">{{ $task->importedPayment->bic }}</div>
                                </div>
                            </div>
                            <div class="mb-1">
                                <div class="row">
                                    <div class="col-3"><b>{{__('common.TransferId')}}</b>:</div>
                                    <div class="col-9">{{ $task->importedPayment->bank_transaction_id }}</div>
                                </div>
                            </div>
                            <div class="mb-1">
                                <div class="row">
                                    <div class="col-3"><b>{{__('common.PaymentReason')}}</b>:</div>
                                    <div class="col-9">{{ $task->importedPayment->basis }}</div>
                                </div>
                            </div>
                            <div class="mb-4">
                                <div class="row">
                                    <div class="col-3" >
                                        <b>{{__('common.InvestorId')}}</b>:
                                    </div>
                                    <div class="col-9">
                                        <input type="number" class="form-control" name="investor_id">
                                    </div>
                                </div>
                            </div>
                        <button type="submit" class="btn btn-success" style="width: 45%">
                            {{__('common.Confirm')}}
                        </button>
                </form>
                <form style="width: 45%; float: right; margin-top: -35px;" action="{{route('admin.tasks.cancel-task', $task->task_id)}}">
                    <button type="submit" class="btn btn-danger w-100">
                        {{__('common.Cancel')}}
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
