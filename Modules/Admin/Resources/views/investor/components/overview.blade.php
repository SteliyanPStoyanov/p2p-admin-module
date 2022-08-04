<div class="container mw-100">
    <div class="row">
        <div class="col-lg-4 d-inline-block float-left">
            <div class="card">
                <h3 class="card-header pt-4 pl-4"><b>{{__('common.PersonalDetails')}}</b></h3>
                <div class="card-body">
                    <p class="card-text">
                        <strong>{{__('common.FirstName')}}</strong>:
                        {{$investor->first_name}}
                    </p>
                    <p class="card-text">
                        <strong>{{__('common.MiddleName')}}</strong>:
                        {{$investor->middle_name}}
                    </p>
                    <p class="card-text">
                        <strong>{{__('common.LastName')}}</strong>:
                        {{$investor->last_name}}
                    </p>

                    <p class="card-text">
                        <strong>{{__('common.Email')}}</strong>:
                        {{$investor->email}}
                    </p>
                    <p class="card-text">
                        <strong>{{__('common.Phone')}}</strong>:
                        {{$investor->phone}}
                    </p>
                    <p class="card-text">
                        <strong>{{__('common.DateOfBirth')}}</strong>:
                        {{$investor->birth_date}}
                    </p>
                    <p class="card-text">
                        <strong>{{__('common.Country')}}</strong>:
                        {{$investor->country->name ?? ''}}
                    </p>
                    <p class="card-text">
                        <strong>{{__('common.PostalCode')}}</strong>:
                        {{$investor->postcode}}
                    </p>
                    <p class="card-text">
                        <strong>{{__('common.Address')}}</strong>:
                        {{$investor->address}}
                    </p>
                    <p class="card-text">
                        <strong>{{__('common.Citizenship')}}</strong>:
                        {{$investor->investorCitizenship->name ?? ''}}
                    </p>
                    <p class="card-text">
                        <strong>{{__('common.InvestorType')}}</strong>:
                        {{$investor->type}}
                    </p>
                    <hr/>
                    <h3>{{__('common.Agreements')}}</h3>
                    <p>
                    @foreach($investor->contracts as $contract)
                        <p class="text-primary">
                            <a href="{{ route('admin.investor.downloadAgreement', $contract->investor_contract_id) }}">
                                {{ $contract->template->name . ' ' . __('common.concludedOn') . ' ' . date_format($contract->created_at, 'd-m-Y') }}
                            </a>
                        </p>
                        @endforeach
                        </p>
                </div>
            </div>
        </div>
        <div class="col-lg-4 d-inline-block float-left">
            <div class="card" style="min-height: 350px">
                <h3 class="card-header pt-4 pl-4"><b>{{__('common.RepaymentInformation')}}</b></h3>
                <div class="card-body">
                    <h3><strong>{{__('common.WalletSummary')}}</strong></h3>
                    <p class="card-text">
                        <strong>{{__('common.DepositedAmount')}}</strong>:
                        {{amount($investor->wallet()->deposit ?? 0)}}
                    </p>
                    <p class="card-text">
                        <strong>{{__('common.WithDrawnAmount')}}</strong>:
                        {{amount($investor->wallet()->withdraw  ?? 0)}}
                    </p>
                    <p class="card-text">
                        <strong>{{__('common.EarnedIncome')}}</strong>:
                        {{amount($investor->earnedIncome() ?? 0)}}
                    </p>

                    <p class="card-text">
                        <strong>{{__('common.TotalBalance')}}</strong>:
                        {{amount($investor->totalBalance() ?? 0)}}
                    </p>
                    <p class="card-text">
                        <strong>{{__('common.UninvestedFunds')}}</strong>:
                        {{amount($investor->wallet()->uninvested  ?? 0)}}
                    </p>
                    <p class="card-text">
                        <strong>{{__('common.InvestedFunds')}}</strong>:
                        {{amount($investor->wallet()->invested  ?? 0)}}
                    </p>
                </div>

            </div>
            @if($investor->type == \Modules\Common\Entities\Investor::TYPE_COMPANY)
                @include('admin::investor.components.company')
            @endif


        </div>
        <div class="col-lg-4 d-inline-block float-left">
            <div class="card" style="min-height: 350px">
                <h3 class="card-header pt-4 pl-4"><b>{{__('common.PaymentInfo')}}</b></h3>
                <div class="card-body">
                    <p class="card-text">
                        <strong>{{__('common.Iban')}}</strong>:
                        {{$investor->mainBankAccount()->iban ?? ''}}
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="form-group col-lg-4">
    <form id="investorReferralsForm"
          action="{{ route('admin.investors-comment') }}" method="POST">
        {{ admin_csrf_field() }}
        <label class="font-weight-bold">{{__('common.Comment')}}:</label>
        <input name="investor_id" value="{{$investor->investor_id}}" hidden>
        <textarea class="form-control w-100 mb-2" name="comment"
                  id="investorComment" cols="30" rows="5">{{$investor->comment ?? ''}}</textarea>
        <button type="submit" class="btn btn-cyan">{{__('common.Save')}}</button>
    </form>
</div>
