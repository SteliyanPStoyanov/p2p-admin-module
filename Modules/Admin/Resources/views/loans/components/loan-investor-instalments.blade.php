<div class="accordion investor-instalments">
    @foreach($loan->getInvestors() as $investor)
        <div class="card">
            <div class="card-header" id="heading{{$investor->investor_id}}">
                <h5 class="mb-0 accordion-title-select">
                    <button class="btn btn-link" data-toggle="collapse" data-target="#collapse{{$investor->investor_id}}"
                            aria-expanded="false" aria-controls="collapse{{$investor->investor_id}}">
                        <i class="fa fa-caret-right rotate"></i>
                        <span class="pr-5">{{__('common.InvestorId')}}
                            <a href="{{route('admin.investors.overview',$investor->investor_id)}}">#{{$investor->investor_id}}</a>
                        </span>
                        <span class="pr-5">
                            {{__('common.Names')}} # {{ $investor->investor_names }}
                        </span>
                        <span class="pr-5">
                            {{__('common.Amount')}}
                            {{ amount($investor->total_amount) }}
                        </span>
                        <span class="pr-5">
                                            {{__('common.Percent')}} # {{ number_format($investor->total_percent,1) }} %
                        </span>
                    </button>
                </h5>
            </div>
            <div id="collapse{{$investor->investor_id}}" class="collapse" aria-labelledby="heading{{$investor->investor_id}}"
                 data-parent=".accordion">
                <div class="accordion-investment pb-3">
                    @foreach($investor->investments($loan->loan_id) as $investment)
                        <div class="card ml-5">
                            <div class="card-header" id="heading-investment{{$investment->investment_id}}">
                                <h5 class="mb-0 accordion-title-select">
                                    <button class="btn btn-link" data-toggle="collapse"
                                            data-target="#collapse-investment{{$investment->investment_id}}"
                                            aria-expanded="false"
                                            aria-controls="collapse-investment{{$investment->investment_id}}">
                                        <i class="fa fa-caret-right rotate"></i>
                                        <span class="pr-5">{{__('common.InvestmentId')}}
                                            #{{$investment->investment_id}}
                                        </span>
                                        <span class="pr-5">
                                        {{__('common.Amount')}}
                                            {{ amount($investment->amount) }}
                                         </span>
                                        <span class="pr-5">
                                            {{__('common.Percent')}} # {{ number_format($investment->percent,1) }} %
                                        </span>
                                        <span class="pr-5">
                                            {{__('common.Date')}} # {{ $investment->created_at }}
                                        </span>
                                    </button>
                                </h5>
                            </div>
                            <div id="collapse-investment{{$investment->investment_id}}" class="collapse"
                                 aria-labelledby="heading-investment{{$investment->investment_id}}"
                                 data-parent=".accordion-investment">
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="table">
                                            <thead>
                                            <tr>
                                                <th scope="col">{{__('common.DueDate')}}</th>
                                                <th scope="col">{{__('common.InstalmentID')}}</th>
                                                <th scope="col">{{__('common.Days')}}</th>
                                                <th scope="col">{{__('common.Principal')}}</th>
                                                <th scope="col">{{__('common.AccruedInterest')}}</th>
                                                <th scope="col">{{__('common.Interest')}}</th>
                                                <th scope="col">{{__('common.LateInterest')}}</th>
                                                <th scope="col">{{__('common.Total')}}</th>
                                                <th scope="col">{{__('common.PaidAt')}}</th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            @foreach($investment->getInvestorInstallments() as $installment)
                                                @php
                                                    $inst = $installment->installment();
                                                    $dueDate = $inst->due_date;
                                                @endphp
                                                <tr>
                                                    <td>{{ showDate($dueDate) }}</td>
                                                    <td>{{ $installment->investor_installment_id }}</td>
                                                    <td>{{ $installment->days }}</td>
                                                    <td>{{ amount($installment->principal) }}</td>
                                                    <td>{{ amount($installment->accrued_interest) }}</td>
                                                    <td>{{ amount($installment->interest) }}</td>
                                                    <td>{{ amount($installment->late_interest )}}</td>
                                                    <td>{{ amount($installment->total) }}</td>
                                                    <td>{{ $installment->paid_at }}</td>
                                                </tr>
                                            @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>

                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    @endforeach
</div>
