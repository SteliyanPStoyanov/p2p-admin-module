<div class="container mw-100">
    <div class="row">
        <div class="col-lg-6">
            <div class="card position-relative">
                <h3 class="card-header pt-4 pl-4"><b>{{__('common.WalletSummary')}}</b></h3>
                <div class="card-body">
                    <p class="card-text">
                    <div class="row">
                        <div class="col-4"><strong>{{__('common.DepositedAmount')}}</strong>:</div>
                        <div class="col-8">{{amount($investor->wallet()->deposit ?? 0)}}</div>
                    </div>
                    </p>
                    <p class="card-text">
                    <div class="row">
                        <div class="col-4"><strong>{{__('common.WithDrawnAmount')}}</strong>:</div>
                        <div class="col-8">{{amount($investor->wallet()->withdraw  ?? 0)}}</div>
                    </div>
                    </p>
                    <p class="card-text">
                    <div class="row">
                        <div class="col-4"><strong>{{__('common.EarnedIncome')}}</strong>:</div>
                        <div class="col-8">{{amount($investor->earnedIncome() ?? 0)}}</div>
                    </div>
                    </p>

                    <p class="card-text">
                    <div class="row">
                        <div class="col-4"><strong>{{__('common.TotalBalance')}}</strong>:</div>
                        <div class="col-8">{{amount($investor->totalBalance() ?? 0)}}</div>
                    </div>
                    </p>
                    <p class="card-text">
                    <div class="row">
                        <div class="col-4"><strong>{{__('common.UninvestedFunds')}}</strong>:</div>
                        <div class="col-8">{{amount($investor->wallet()->uninvested  ?? 0)}}</div>
                    </div>
                    </p>
                    <p class="card-text">
                    <div class="row">
                        <div class="col-4"><strong>{{__('common.InvestedFunds')}}</strong>:</div>
                        <div class="col-8">{{amount($investor->wallet()->invested  ?? 0)}}</div>
                    </div>
                    </p>
                </div>
            </div>
        </div>
        <div class="col-lg-6">
            <button class="btn btn-cyan " data-toggle="modal"
                    data-target="#fundsModal"
                    style="top: 3%; right: 2%; padding: .4rem 2rem;">{{__('common.AddFunds')}}</button>
        </div>
    </div>
    <div class=row">
        <div class="col-lg-12 pl-0">
            <div class="card" style="min-height: 350px">
                <h3 class="card-header pt-4 pl-4"><strong>{{__('common.HistoryMovements')}}</strong></h3>
                <div class="table-responsive">
                    <div id="table-invests">
                        <form id="transactionsLog" class="form-inline card-body"
                              action="{{ route('admin.administrators.list') }}"
                              method="GET">
                            {{ admin_csrf_field() }}
                            <div class="form-row w-100">
                                <div class="form-group col-lg-2">
                                    <input type="text" autocomplete="off" name="createdAt" class="form-control w-100"
                                           id="createdAt-wallet"
                                           value="{{ session($cacheKey . '.createdAt') }}"
                                           placeholder="{{__('common.FilterByDateTime')}}">
                                </div>
                                <div class="form-group col-lg-2">
                                    <input type="number" autocomplete="off" name="amount[from]"
                                           class="form-control w-100"
                                           id="AmountFrom"
                                           value="{{ session($cacheKey . '.amount.from') }}"
                                           placeholder="{{__('common.AmountFrom')}}">
                                </div>
                                <div class="form-group col-lg-2">
                                    <input type="number" autocomplete="off" name="amount[to]" class="form-control w-100"
                                           id="AmountTo"
                                           value="{{ session($cacheKey . '.amount.to') }}"
                                           placeholder="{{__('common.AmountTo')}}">
                                </div>
                                <div class="form-group col-lg-2">
                                    <select class="form-control" name="type" id="">
                                        <option value="">{{__('common.SelectByTransactionType')}}</option>
                                        @foreach($transactionTypes as $transactionType)
                                            <option
                                                value="{{$transactionType}}"
                                                @if($transactionType == session($cacheKey . '.type'))
                                                selected
                                                @endif
                                            >
                                                {{\Modules\Common\Entities\Transaction::getAdminLabel($transactionType)}}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="clearfix"></div>
                                <div class="col-lg-12 mt-4">
                                    <a href="#"
                                       id="export-wallet"
                                       class="form-control btn-success  mr-1" style="position: absolute; right: 282px;bottom: 0px;z-index: 10;">Export</a>
                                    <x-btn-filter/>
                                </div>
                            </div>
                        </form>
                        <div class="table-responsive">
                            <div id="investorWalletTransactions">
                                @include('admin::investor.components.wallet-list-table')
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        {{--MODAL--}}
        <div class="modal fade" id="fundsModal" tabindex="-1" role="dialog" aria-labelledby="addFundsModalLabel"
             aria-hidden="true">

            @if (session('fail'))
                @push('scripts')
                    <script type="text/javascript">
                        $(document).ready(function () {
                            let hash = window.location.hash;
                            let tabWalet = '#wallet';
                            if (hash.indexOf(tabWalet) !== -1) {
                                $('#fundsModal').modal('show');
                                $("#cstm-danger-alert").hide();
                            }
                        });
                    </script>
                @endpush
            @endif

            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">{{__('common.AddFunds')}}</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body mb-5">
                        <div class="bold" style="bottom:-40px; color: red">
                            {{ session('errors') ?? session('fail') }}
                        </div>

                        <h3 class="text-center mb-5">
                            {{__('common.TheIncomingTransfer')}}
                            <br>
                            {{__('common.Name')}}
                            <strong>: {{$investor->fullName()}}</strong>
                        </h3>

                        <form action="{{route('admin.investors.add-funds', $investor->investor_id)}}#wallet"
                              method="POST" class="form-control border-0 pb-2" id="addFundsForm">
                            {{ admin_csrf_field() }}
                            <div class="form-group">
                                <label class="form-inline w-50 float-left" for="deposited-amount">
                                    {{__('common.DepositedAmount')}}:
                                </label>
                                <input type="text" name="amount"
                                       class="form-control form-inline w-50 float-left" id="deposited-amount">
                            </div>
                            <br>
                            <br>
                            <div class="form-group">
                                <label class="form-inline w-50 float-left" for="transfer-id">
                                    {{__('common.TransferId')}}:
                                </label>
                                <input type="text" name="bank_transaction_id"
                                       class="form-control form-inline w-50 float-left" id="transfer-id">
                            </div>
                            <br>
                            <br>
                            @if(!empty($investor->mainBankAccount()))
                                <div class="form-group">
                                    <label class="form-inline w-50 float-left" for="bank_account_id">
                                        {{__('common.Iban')}}:
                                    </label>
                                    <select class="form-control w-50" name="bank_account_id"
                                            id="bank_account_id">
                                        <option value="">{{__('common.SelectIban')}}</option>
                                        @foreach($investor->bankAccounts as $bankAccount)
                                            <option
                                                @if($investor->mainBankAccount() && $investor->mainBankAccount() == $bankAccount)
                                                selected
                                                @endif
                                                value="{{$bankAccount->bank_account_id}}">
                                                {{$bankAccount->iban}}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            @endif

                            <div class="form-group" id="newIbanGroup">
                                <label class="form-inline w-50 float-left" for="new_bank_account_id">
                                    {{__('common.NewIban')}}:
                                </label>
                                <input type="text" name="bank_account_iban"
                                       class="form-control form-inline w-50 float-left"
                                       id="new_bank_account_id">
                            </div>
                            <br>
                            <br>
                            <div class="form-group text-center">
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                <button type="submit" id="addFundsButton" class="btn btn-primary addFunds">Add funds</button>
                            </div>
                        </form>
                        <div class="clearfix"></div>
                        <div class="mt-5"></div>
                    </div>
                </div>
            </div>
        </div>
        {{--END MODAL--}}

    </div>
</div>

@push('scripts')

    <script type="text/javascript" src="{{ assets_version(asset('js/jsGrid.js')) }}"></script>
    <script>
        var url = document.location.toString();
        if (url.split('#')[1] === 'wallet') {
            loadSimpleDataGrid('{{ route('admin.investors-wallet-transaction-refresh', $investor->investor_id) }}', $("#transactionsLog"), $("#investorWalletTransactions"));
        }
        loadDateRangePicker($("#createdAt-wallet"));

        $("#bank_account_id").change(function (e) {
            if (this.value !== '') {
                $("#newIbanGroup").hide();
            } else {
                $("#newIbanGroup").show();
            }
        });

         $("#export-wallet").on('click', function () {
            window.location.href = '{{ route('admin.investor.export' , $investor->investor_id) }}?' + $('#transactionsLog').serialize();
         });

        $("#fundsModal").on('shown.bs.modal', function(){
            $("#deposited-amount").focus();
        });
        $(document).ready(function (){
            $("#addFundsForm").on('submit', function (event) {
                $("#addFundsButton").prop('disabled', true);
            });
        });
    </script>
@endpush
