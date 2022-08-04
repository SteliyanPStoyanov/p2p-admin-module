@extends('profile::layouts.app')

@section('title',  'Withdraw - ')

@section('content')
    <div class="col-lg-12 pt-4 pb-4 deposit-buttons">
        <a href="{{route('profile.deposit')}}"
           class="btn ui basic button deposit-btn">{{ __('common.AddFunds') }}</a>
        <a href="{{route('profile.withdraw')}}"
           class="btn ui teal button withdraw-btn">{{ __('common.WithdrawFunds') }} </a>
    </div>
    <div class="row withdraw-row">
        <div class="col-lg-4 text-black p-0">
            @if (session('fail'))
                <div class="validation-error-remove">
                    <div
                        class="p-1 my-4 text-error">{{session('fail')}}</div>
                </div>
            @endif
            @if (session('success'))
                <div class="validation-error-remove">
                    <div class="p-1 my-4 text-green">{{session('success')}}</div>
                </div>
            @endif
            <div class="col-12 maxAmountReach">
                <div class="p-1 my-4 text-error">
                    {{ __('common.АmountIsBiggerThenYourBalance') }}
                    {{ amount($investor->wallet()->uninvested) }}
                </div>
            </div>
            <div class="col-12 minAmountReach">
                <div class="p-1 my-4 text-error">
                    {{ __('common.MinimumWithdrawal') }}
                </div>
            </div>
            <div class="overview-card pb-3">
                <div class="card">
                    <div class="card-body row">
                        <div class="col-lg-6 mt-2 mb-4">{{ __('common.AvailableFunds') }} </div>
                        <div class="col-lg-6 mt-2 mb-4 text-right ml-auto">{{ amount($investor->wallet()->uninvested) }}
                        </div>
                        <input id="submitedWithDrawForm" type="hidden" value="0"/>
                        <form method="POST" id="withDrawForm" class="mt-3 mb-2 w-100"
                              action="{{route('profile.withdraw.amount')}}"
                              autocomplete="off">
                            @csrf

                            @if(count($investor->bankAccounts) != 0)
                                <div class="col-12 mb-4">
                                    <label for="bank_account_id">{{ __('common.BankAccount') }}</label>
                                    <select id="bank_account_id" name="bank_account_id" class="form-control">
                                        @foreach($investor->bankAccounts as $backAccount)
                                            <option
                                                value="{{$backAccount->bank_account_id}}">{{$backAccount->iban}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            @endif
                            <div class="col-12 mb-1 ">
                                <input type="number" name="amount" id="amount" class="form-control text-center"
                                       step=".01">
                            </div>
                            <div class="col-12 mt-3">
                                @if (!empty($investorBunch->investment_bunch_id))
                                    <input id="form_submit" class="btn ui teal button w-100 " type="submit"
                                           value="{{ __('common.CurrentInvesting') }}" disabled>
                                @else
                                    <input id="form_submit" class="btn ui teal button w-100 " type="submit"
                                           value="{{ __('common.Withdraw') }}">
                                @endif

                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <div class="row withdraw-text">
                <div class="col-lg-2 text-black p-0">
                    <img class="img-responsive" src="{{ asset('images/icons/bank.svg') }}" width="64px"
                         alt="bank"/>
                </div>
                <div class="col-lg-10 text-black align-self-center">
                <span class="my-auto" style="font-size: 0.9rem;">
                    {{ __('common.TheFundsWillBeTransferredTo') }}
                </span>
                </div>
            </div>
        </div>

    </div>

@endsection

@push('scripts')
    <script>
        let maxAmount = {{$investor->wallet()->uninvested}};
        let maxAmountError = $('.maxAmountReach');
        let minAmountError = $('.minAmountReach');
        let walletSum = {{$walletSum->balance}};

        maxAmountError.hide();
        minAmountError.hide();
        $('#amount').keyup(function () {
            let inputVal = $(this).val();
            if (inputVal > maxAmount) {
                maxAmountError.show();
                $('.validation-error-remove').remove();
            } else {
                maxAmountError.hide();
            }

            if (walletSum > 10) {
                if (inputVal < 10) {
                    minAmountError.show();
                    $('.validation-error-remove').remove();
                } else {
                    minAmountError.hide();
                }
            }

        });

        $('.withdraw-all').click(function (e) {
            e.preventDefault();
            $('#amount').val(maxAmount);
        });
    </script>

    <script>
        $("#withDrawForm").on('submit', function (e) {
            e.preventDefault();
            if ($('#submitedWithDrawForm').val() == 0) {
                this.submit();
                $('#withDrawForm').attr('action', '');
                $('#submitedWithDrawForm').val(1);
                return true;
            }
            return false;
        });
    </script>
@endpush
