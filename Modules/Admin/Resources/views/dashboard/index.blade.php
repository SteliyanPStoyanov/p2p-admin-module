@extends('layouts.app')

@section('style')
    <link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/morris.js/0.5.1/morris.css">
@endsection

@section('content')

    <div class="row admin-dashboard-sistem-info">
        <div class="col-lg-3 col-md-6 mb-5 ml-3">
            <div class="row border-bottom">
                <div class="col-lg-7 col-md-12">
                    {{__('common.RegisteredUser')}}:
                </div>
                <div class="col-lg-5 col-md-12">
                    {{$allRegisteredInvestors['count'] ?? 0}}
                </div>
            </div>
            <div class="row border-bottom">
                <div class="col-lg-7 col-md-12">
                    {{__('common.VerifiedUser')}}:
                </div>
                <div class="col-lg-5 col-md-12">
                    {{$investorCountByStatus['verified']  ?? 0}}
                </div>
            </div>
            <div class="row border-bottom">
                <div class="col-lg-7 col-md-12">
                    {{__('common.UsersWithDepositedFunds')}}:
                </div>
                <div class="col-lg-5 col-md-12">
                    {{$investorsWithDeposit['count'] ?? 0}}
                </div>
            </div>
            <div class="row border-bottom">
                <div class="col-lg-7 col-md-12">
                    {{__('common.FundsInWalletsUninvested')}}:
                </div>
                <div class="col-lg-5 col-md-12">
                    {{amountReport($walletSum['uninvested'] ,'€') ?? amount(0)}}
                </div>
            </div>
            <div class="row border-bottom">
                <div class="col-lg-7 col-md-12">
                    {{__('common.FundsInWalletsInvested')}}:
                </div>
                <div class="col-lg-5 col-md-12">
                    {{amountReport($walletSum['invested'] ,'€') ?? amount(0)}}
                </div>
            </div>
            <div class="row border-bottom">
                <div class="col-lg-7 col-md-12">
                    {{__('common.TotalFundsInWallets')}}:
                </div>
                <div class="col-lg-5 col-md-12">
                    {{amountReport($walletSum['total_amount'] ,'€') ?? amount(0)}}
                </div>
            </div>
        </div>
    </div>

    <div class="row charts-container" id="container-row">

        <div class="col-lg-12 col-md-12 mb-3">


            <select class="form-control float-right" id="chartsDayLimit" style="width: 250px;">
                <option value="{{\Modules\Admin\Entities\Administrator::ADMINISTRATOR_STATISTIC_DAYS[0]}}"
                        @if(\Modules\Admin\Entities\Administrator::ADMINISTRATOR_STATISTIC_DAYS[0] == $administrator['statistic_days'])
                        selected
                        @endif
                        class="item ">Last 7 Days
                </option>
                <option value="{{\Modules\Admin\Entities\Administrator::ADMINISTRATOR_STATISTIC_DAYS[1]}}"
                        @if(\Modules\Admin\Entities\Administrator::ADMINISTRATOR_STATISTIC_DAYS[1] == $administrator['statistic_days'])
                        selected
                        @endif
                        class="item ">Last 14 Days
                </option>
                <option value="{{\Modules\Admin\Entities\Administrator::ADMINISTRATOR_STATISTIC_DAYS[2]}}"
                        @if(\Modules\Admin\Entities\Administrator::ADMINISTRATOR_STATISTIC_DAYS[2] == $administrator['statistic_days'])
                        selected
                        @endif
                        class="item ">Last 30 Days
                </option>
                @php
                    $date = Carbon\Carbon::parse(\Modules\Admin\Entities\Administrator::ADMINISTRATOR_STATISTIC_DAYS[3]);
                     $now = Carbon\Carbon::now();

                     $diff = $date->diffInDays($now);
                @endphp
                <option value="{{$diff}}"
                        @if($diff == $administrator['statistic_days'])
                        selected
                        @endif
                        class="item ">All Time
                </option>
            </select>
        </div>
        <div class="col-lg-6 col-md-12">
            <div class="card">
                <div class="card-body">
                    @if(get_device()->isMobile() == false) <h4 class="card-title">Users</h4> @endif
                    <canvas id="RegisteredUsersPerDay"
                            @if(get_device()->isMobile() == true) height="380px" @endif></canvas>
                </div>
            </div>
        </div>
        <div class="col-lg-6 col-md-12">
            <div class="card">
                <div class="card-body">
                    @if(get_device()->isMobile() == false)<h4 class="card-title">Amount</h4>@endif
                    <canvas id="AmountPerDay" @if(get_device()->isMobile() == true) height="380px" @endif></canvas>
                </div>
            </div>
        </div>
    </div>

@endsection
@push('scripts')
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.9.4/Chart.min.js"></script>
    <script>

        let UsersPerDay = $('#RegisteredUsersPerDay');
        let AmountPerDay = $('#AmountPerDay');

        let chart = new Chart(UsersPerDay, {
                type: 'bar',
                plugins: [{
                    beforeInit: function (chart, options) {
                        chart.legend.afterFit = function () {
                            this.height = this.height + 20;
                        };
                    }
                }],
                data: {
                    datasets: [{
                        label: 'Registered users per day',
                        backgroundColor: 'rgba(255, 99, 132, 0.4)',
                        data: []
                    }, {
                        label: 'Verified users per day',
                        backgroundColor: 'rgba(255, 206, 86, 0.4)',
                        data: []
                    }, {
                        label: 'Deposit per day',
                        backgroundColor: 'rgba(153, 102, 255, 0.4)',
                        data: []
                    }]
                },
                options: {
                    layout: {
                        padding: 0,
                    },
                    responsive: true,
                    maintainAspectRatio: true,
                    tooltips: {
                        displayColors: false,
                    },
                    legend: {
                        display: true,
                    }
                    ,
                    title: {
                        display: true,
                        text: 'Registered Users Per Day',
                        fontStyle: 'normal',
                        fontSize: '14',
                        fontColor: '#000',
                        padding: '15'
                    }
                    ,
                    scales: {
                        xAxes: [{
                            type: 'time',
                            position: 'bottom',
                            time: {
                                displayFormats: {'day': 'DD-MM-YY'},
                                tooltipFormat: 'DD-MM-YY',
                                unit: 'day',
                                isoWeekday: true,
                            },
                            gridLines: {
                                tickMarkLength: 15,
                                display: false
                            },
                            ticks: {
                                maxRotation: 45,
                                minRotation: 45,
                                maxTicksLimit: 7,
                                minTicksLimit: 7,
                            },
                            offset: true,
                            autoSkip: true,
                        }],
                        yAxes: [{
                            gridLines: {
                                display: false
                            },
                            ticks: {
                                maxTicksLimit: 7,
                                minTicksLimit: 7,
                                autoSkip: true,

                            }
                        }]
                    }
                }
            }
        );

        let chart2 = new Chart(AmountPerDay, {
                type: 'bar',
                plugins: [{
                    beforeInit: function (chart, options) {
                        chart.legend.afterFit = function () {
                            this.height = this.height + 20;
                        };
                    }
                }],
                data: {
                    datasets: [{
                        label: 'Amount invested today',
                        backgroundColor: 'rgba(255, 99, 132, 0.4)',
                        data: []
                    }, {
                        label: 'Amount deposited today',
                        backgroundColor: 'rgba(255, 206, 86, 0.4)',
                        data: []
                    }, {
                        label: 'Amount withdrawn today',
                        backgroundColor: 'rgba(153, 102, 255, 0.4)',
                        data: []
                    }]
                },
                options: {
                    layout: {
                        padding: 0,
                    },
                    responsive: true,
                    maintainAspectRatio: true,
                    tooltips: {
                        displayColors: false,
                    },
                    legend: {
                        display: true,
                    }
                    ,
                    title: {
                        display: true,
                        text: 'Amount Per Day',
                        fontStyle: 'normal',
                        fontSize: '14',
                        fontColor: '#000',
                        padding: '15'
                    }
                    ,
                    scales: {
                        xAxes: [{
                            type: 'time',
                            position: 'bottom',
                            time: {
                                displayFormats: {'day': 'DD-MM-YY'},
                                tooltipFormat: 'DD-MM-YY',
                                unit: 'day',
                                isoWeekday: true,
                            },
                            gridLines: {
                                tickMarkLength: 15,
                                display: false
                            },
                            ticks: {
                                maxRotation: 45,
                                minRotation: 45,
                                maxTicksLimit: 7,
                                minTicksLimit: 7,
                            },
                            offset: true,
                            autoSkip: true,
                        }],
                        yAxes: [{
                            gridLines: {
                                display: false
                            },
                            ticks: {
                                maxTicksLimit: 7,
                                minTicksLimit: 7,
                                autoSkip: true,
                                callback: function (value, index, values) {
                                    if (value === Math.round(value)) {
                                        return '€ ' + value.toString().replace(/\B(?=(\d{3})+(?!\d))/g, " ").slice(0, -4);
                                    } else {
                                        return null
                                    }
                                }
                            }
                        }]
                    }
                }
            }
        );

        $('#chartsDayLimit').change(function () {
            let days = $(this).val();
            ajax_chart(chart, '{{ route('admin.dashboard.registeredPerDay')}}', days);
            ajax_chart(chart2, '{{ route('admin.dashboard.transactionPerDay')}}', days);
        });

        statisticDay({{$administrator['statistic_days']}});

        function statisticDay(days) {
            ajax_chart(chart, '{{ route('admin.dashboard.registeredPerDay')}}', days);
            ajax_chart(chart2, '{{ route('admin.dashboard.transactionPerDay')}}', days);
        }

        // function to update our chart
        function ajax_chart(chart, url, days) {
            let data =
                {
                    'days':
                    days
                };
            $.getJSON(url, data).done(function (response) {

                chart.data.datasets[0].data = response[0]; // or you can iterate for multiple datasets
                chart.data.datasets[1].data = response[1]; // or you can iterate for multiple datasets
                chart.data.datasets[2].data = response[2]; // or you can iterate for multiple datasets

                chart.update(); // finally update our chart
            });
        }

    </script>
@endpush
