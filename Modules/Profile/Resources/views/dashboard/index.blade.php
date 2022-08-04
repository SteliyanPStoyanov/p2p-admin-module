@extends('profile::layouts.app')

@section('title',  'Summary - ')

@section('content')
    <div class="col-lg-7 pt-4 pb-4 px-0 hide-verified">
        @if($investor->status != config('profile.statusVerified'))
            @if($investor->status != config('profile.statusAwaitingVerification'))
                <a href="{{route('profile.verify.verify')}}"
                   class="ui teal button btn float-left mr-3 button-wide">{{__('common.Verify')}}</a>
            @endif
            <div class="ui basic button btn button-wide noHover float-left notAllowedHover" data-toggle="tooltip"
                 data-placement="top"
                 title="{{__('common.AddFundsTooltip')}}">
                {{__('common.AddFunds')}}
            </div><br><br>
        @else
            <a href="{{route('profile.deposit')}}"
               class="ui teal button btn btn btn-outline-secondary pl-4 pr-4 add-funds-not-verified">{{__('common.AddFunds')}}</a>
        @endif
        @if($investor->status == config('profile.statusAwaitingVerification'))
            <h4 class="mt-4 font-weight-normal text-black">
               {!! trans('common.statusAwaitingVerificationDescription') !!}
            </h4>
        @endif

        @if($investor->status == config('profile.registered'))
            <h4 class="mt-4">{{__('common.VerifyYourIdentity')}}</h4>
        @endif
        @if($investor->status != config('profile.statusVerified') && $investor->status != config('profile.statusAwaitingVerification'))
            <h3 class="my-4 text-black">{{__('common.VerifyYourIdentity')}}</h3>
            <p class="text-black">{{__('common.AfrangaIsOperatingInComplianceWith')}}</p>
            <p class="text-black">{{__('common.MakeSureThatAll')}}</p>
            <p class="text-black">{{__('common.YouWillBeAbleToAddFunds')}}</p>
        @endif
    </div>
    @if($investor->status == config('profile.statusVerified'))
        <div class="row" id="account-summary">
            <div class="col-lg-12"><h2 class="text-left mt-5 mb-5 text-black pl-0">Account Summary</h2></div>
            <div class="col-lg-3 mt-4">
                <div class="card mb-4 shadow-sm first-column-summary">
                    <div class="card-body p-3">
                        <div class="col-lg-12 p-0">
                            <div class="card border-0">
                                <div class="card-body">
                                    <div class="mb-5">
                                        <h3 class="card-header text-black no-border no-box bg-transparent p-0 m-0">{{__('common.AccountBalance')}}</h3>
                                        <h3 class="card-header text-black no-border no-box bg-transparent mt-1 p-0 h2 font-weight-bold">{{ amount($wallet->total_amount) }}</h3>
                                    </div>
                                    <p class="card-text w-100 float-left">
                                        <span class="float-left">{{__('common.UninvestedFunds')}}</span>
                                        <span class="float-right">{{ amount($wallet->uninvested) }}</span>
                                    </p>
                                    <p class="card-text w-100 float-left">
                                        <span class="float-left">{{__('common.InvestedFunds')}}</span>
                                        <span class="float-right">{{ amount($wallet->invested) }}</span>
                                    </p>
                                    <p class="card-text w-100 float-left text-black mb-5">
                                        <span class="float-left">{{__('common.TotalBalance')}}</span>
                                        <span class="float-right">{{ amount($wallet->total_amount) }}</span>
                                    </p>

                                    <p class="card-text w-100 float-left">
                                        <span class="float-left">{{__('common.InterestIncome')}}</span>
                                        <span class="float-right">{{ amount($wallet->interest) }}</span>
                                    </p>
                                    <p class="card-text w-100 float-left">
                                        <span class="float-left">{{__('common.LateInterestOverview')}}</span>
                                        <span class="float-right">{{ amount($wallet->late_interest) }}</span>
                                    </p>
                                    <p class="card-text w-100 float-left">
                                        <span class="float-left">{{__('common.SecondaryMarket')}}</span>
                                        {{-- TO DO secondary market on launch--}}
                                        <span class="float-right">{{ amount(0) }}</span>
                                    </p>
                                    <p class="card-text w-100 float-left">
                                        <span class="float-left">{{__('common.Bonuses')}}</span>
                                        <span class="float-right">{{ amount($wallet->bonus) }}</span>
                                    </p>
                                    <p class="card-text w-100 float-left text-black">
                                        <span class="float-left">{{__('common.TotalIncome')}}</span>
                                        <span class="float-right">{{ amount($wallet->income) }}</span>
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card shadow-sm">
                    <div class="card-body p-3">
                        <div class="col-lg-12 p-0">
                            <div class="card border-0">
                                <div class="card-body">
                                    @php
                                        $remInvestment = json_decode(json_encode($remainingInvestments) , true);
                                    @endphp
                                    <div class="mb-5">
                                        <h3 class="card-header text-black no-border no-box bg-transparent p-0 m-0">{{__('common.MyInvestments')}}</h3>
                                        <h3 class="card-header text-black no-border no-box bg-transparent myInvestmentTotal mt-1 p-0 h2 font-weight-bold">
                                            {{ amount(array_sum($remInvestment)) }}
                                        </h3>
                                    </div>

                                    <div class="mb-4 w-100 float-left">
                                        <div class="card-text w-100 float-left">
                                        <span class="float-left">
                                                {{payStatus(\Modules\Common\Entities\Loan::PAY_STATUS_CURRENT) }}
                                        </span>
                                            <span class="float-right">
                                                 @if(isset($remInvestment[\Modules\Common\Entities\Loan::PAY_STATUS_CURRENT]))
                                                    {{amount($remInvestment[\Modules\Common\Entities\Loan::PAY_STATUS_CURRENT])}}
                                                @else
                                                    {{amount(0)}}
                                                @endif
                                        </span>
                                        </div>

                                    </div>
                                    <div class="mb-4 w-100 float-left">
                                        <div class="card-text w-100 float-left">
                                        <span class="float-left">
                                                {{payStatus(\Modules\Common\Entities\Loan::PAY_STATUS_1_15) }}
                                        </span>
                                            <span class="float-right">
                                                 @if(isset($remInvestment[\Modules\Common\Entities\Loan::PAY_STATUS_1_15]))
                                                    {{amount($remInvestment[\Modules\Common\Entities\Loan::PAY_STATUS_1_15])}}
                                                @else
                                                    {{amount(0)}}
                                                @endif
                                        </span>
                                        </div>

                                    </div>
                                    <div class="mb-4 w-100 float-left">
                                        <div class="card-text w-100 float-left">
                                        <span class="float-left">
                                                {{payStatus(\Modules\Common\Entities\Loan::PAY_STATUS_16_30) }}
                                        </span>
                                            <span class="float-right">
                                                @if(isset($remInvestment[\Modules\Common\Entities\Loan::PAY_STATUS_16_30]))
                                                    {{amount($remInvestment[\Modules\Common\Entities\Loan::PAY_STATUS_16_30])}}
                                                @else
                                                    {{amount(0)}}
                                                @endif
                                        </span>
                                        </div>
                                    </div>
                                    <div class="mb-4 w-100 float-left">
                                        <div class="card-text w-100 float-left">
                                        <span class="float-left">
                                                {{payStatus(\Modules\Common\Entities\Loan::PAY_STATUS_31_60) }}
                                        </span>
                                            <span class="float-right">
                                                 @if(isset($remInvestment[\Modules\Common\Entities\Loan::PAY_STATUS_31_60]))
                                                    {{amount($remInvestment[\Modules\Common\Entities\Loan::PAY_STATUS_31_60])}}
                                                @else
                                                    {{amount(0)}}
                                                @endif
                                        </span>
                                        </div>

                                    </div>
                                    <div class="mb-4 w-100 float-left">
                                        <div class="card-text w-100 float-left">
                                        <span class="float-left">
                                                {{payStatusCharts(\Modules\Common\Entities\Loan::PAY_STATUS_LATE,true) }}
                                        </span>
                                            <span class="float-right">
                                                @if(isset($remInvestment[\Modules\Common\Entities\Loan::PAY_STATUS_LATE]))
                                                    {{amount($remInvestment[\Modules\Common\Entities\Loan::PAY_STATUS_LATE])}}
                                                @else
                                                    {{amount(0)}}
                                                @endif
                                        </span>
                                        </div>

                                    </div>
                                    <p class="card-text w-100 float-left text-black mb-4">
                                        <span class="float-left">{{__('common.TotalInvestments')}}</span>
                                        <span class="float-right">{{ amount(array_sum($remInvestment)) }}</span>
                                    </p>
                                    <div class="col-lg-12 px-0">
                                        <a href="{{route('profile.deposit')}}"
                                           class="ui teal button btn btn btn-outline-secondary pl-4 pr-4 add-funds-verified w-100">{{__('common.AddFunds')}}</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>

            <div class="col-lg-9 w-75 ml-auto mt-4 position-relative">
                <div class="ui dropdown filter-show-results">
                    <div class="text">{{__('common.Last'.$investor->statistic_days.'Days')}}</div>
                    <i class="dropdown icon ml-1"></i>

                    <div class="menu">
                        <div onclick="statisticDay({{\Modules\Common\Entities\Investor::INVESTOR_STATISTIC_DAYS[0]}})"
                             class="item @if(\Modules\Common\Entities\Investor::INVESTOR_STATISTIC_DAYS[0] == $investor->statistic_days)
                                 active selected
@endif">Last 14 Days
                        </div>
                        <div onclick="statisticDay({{\Modules\Common\Entities\Investor::INVESTOR_STATISTIC_DAYS[1]}})"
                             class="item @if(\Modules\Common\Entities\Investor::INVESTOR_STATISTIC_DAYS[1] == $investor->statistic_days)
                                 active selected
@endif">Last 28 Days
                        </div>
                        <div onclick="statisticDay({{\Modules\Common\Entities\Investor::INVESTOR_STATISTIC_DAYS[2]}})"
                             class="item @if(\Modules\Common\Entities\Investor::INVESTOR_STATISTIC_DAYS[2] == $investor->statistic_days)
                                 active selected
@endif">Last 3 Months
                        </div>
                        <div onclick="statisticDay({{\Modules\Common\Entities\Investor::INVESTOR_STATISTIC_DAYS[3]}})"
                             class="item @if(\Modules\Common\Entities\Investor::INVESTOR_STATISTIC_DAYS[3] == $investor->statistic_days)
                                 active selected
@endif">Last 6 Months
                        </div>
                        <div onclick="statisticDay({{\Modules\Common\Entities\Investor::INVESTOR_STATISTIC_DAYS[4]}})"
                             class="item @if(\Modules\Common\Entities\Investor::INVESTOR_STATISTIC_DAYS[4] == $investor->statistic_days)
                                 active selected
@endif">This Year
                        </div>
                        <div onclick="statisticDay({{\Modules\Common\Entities\Investor::INVESTOR_STATISTIC_DAYS[5]}})"
                             class="item @if(\Modules\Common\Entities\Investor::INVESTOR_STATISTIC_DAYS[5] == $investor->statistic_days)
                                 active selected
@endif">All Time
                        </div>
                    </div>
                </div>
                <div class="card mb-4 shadow-sm first-column-chart-summary">
                    <div class="card-body p-3">
                        <div class="col-lg-12 p-0">
                            <div class="card border-0">
                                <div class="card-body">
                                    <div class="mb-2">
                                        <h3 class="card-header text-black no-border no-box bg-transparent p-0 m-0">
                                            {{__('common.PortfolioStatistics')}}
                                        </h3>
                                    </div>
                                    <div class="row mt-4">
                                        <div class="col-lg-6">
                                            <div class="chart pr-2">
                                                <canvas id="outstandingBalanceChart" height="225px"></canvas>
                                            </div>
                                        </div>
                                        <div class="col-lg-6 ml-auto">
                                            <div class="chart pl-2">
                                                <canvas id="earnedIncomeChart" height="225px"></canvas>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card mb-4 shadow-sm first-column-chart-summary">
                    <div class="card-body p-3">
                        <div class="col-lg-12 p-0">
                            <div class="card border-0">
                                <div class="card-body">
                                    <div class="row mt-4">
                                        <div class="col-lg-6">
                                            <div class="chart pr-2">
                                                <div id="canvasLoanStatusContainer">
                                                    <canvas id="LoanStatusChart" height="225px"></canvas>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-lg-6 ml-auto">
                                            <div class="chart pl-2">
                                                <div id="canvasRemainingTermContainer">
                                                    <canvas id="RemainingTermChart" height="225px"></canvas>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="card-body text-center mt-3 mb-0 pb-0" id="checkbox-parent">
                                            <div class="ui radio checkbox">
                                                <input class="hidden" type="radio"
                                                       name="chartFilterBy" id="chart_by_amount"
                                                       checked
                                                       value="by_amount">
                                                <label class="mr-3" for="chart_by_amount">
                                                    By Amount
                                                </label>
                                            </div>

                                            <div class="ui radio checkbox">
                                                <input class="hidden" type="radio"
                                                       name="chartFilterBy" id="chart_by_number"
                                                       value="by_number">
                                                <label class="mr-3" for="chart_by_number">
                                                    By Number
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif
@endsection

@push('scripts')

    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.1/moment.min.js"
            integrity="sha512-qTXRIMyZIFb8iQcfjXWCO8+M5Tbc38Qi5WzdPOYZHIlZpzBHG3L3by84BBBOiRGiEb7KKtAOAs5qYdUiZiQNNQ=="
            crossorigin="anonymous"></script>
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.9.4/Chart.min.js"></script>
    <script src="{{ assets_version(url('/') . '/dist/js/dropdown.min.js') }}"></script>

    <script>
        $(function () {
            $('.ui.dropdown').dropdown();
        });

        $(document).ready(function () {
            if ($('#account-summary').length)         // use this if you are using class to check
            {
                $('.hide-verified').hide();
            }
        });


        let qualityData = [
            @php
                foreach ($portfolios->quality as $quality){
                echo $quality .',';
                 }
            @endphp
        ];
        let maturityData = [@php
            foreach ($portfolios->maturity as $maturity){
            echo  $maturity .',';
             }
        @endphp];

        let lineChartType = 'line';
        let intersectOption = true;
        Chart.plugins.register({
            afterDraw: function (chart) {
                if (chart.data.datasets[0].data.length === 0) {
                    // No data is present
                    let ctx = chart.chart.ctx;
                    let width = chart.chart.width;
                    let height = chart.chart.height;
                    chart.clear();
                    ctx.save();
                    ctx.textAlign = 'center';
                    ctx.textBaseline = 'middle';
                    ctx.font = "16px normal 'Helvetica Nueue'";
                    // chart.options.title.text <=== gets title from chart
                    // width / 2 <=== centers title on canvas
                    // 18 <=== aligns text 18 pixels from top, just like Chart.js
                    ctx.fillText(chart.options.title.text, width / 2, 18); // <====   ADDS TITLE
                    ctx.fillText('No data to display for selected time period', width / 2, height / 2);
                    ctx.restore();
                }
            }
        });
        if ($(window).width() > 700) {
            Chart.defaults.LineWithLine = Chart.defaults.line;
            Chart.controllers.LineWithLine = Chart.controllers.line.extend({
                draw: function (ease) {
                    Chart.controllers.line.prototype.draw.call(this, ease);

                    if (this.chart.tooltip._active && this.chart.tooltip._active.length) {
                        let activePoint = this.chart.tooltip._active[0],
                            ctx = this.chart.ctx,
                            x = activePoint.tooltipPosition().x,
                            topY = this.chart.legend.bottom,
                            bottomY = this.chart.chartArea.bottom;

                        // draw line
                        ctx.save();
                        ctx.beginPath();
                        ctx.moveTo(x, topY);
                        ctx.lineTo(x, bottomY);
                        ctx.lineWidth = 1;
                        ctx.strokeStyle = '#009193';
                        ctx.stroke();
                        ctx.restore();
                    }
                }
            });
            lineChartType = 'LineWithLine';
            intersectOption = false;
        }


        let ctx = document.getElementById('outstandingBalanceChart').getContext('2d');
        let chart = new Chart(ctx, {
            // The type of chart we want to create
            type: lineChartType, // also try bar or other graph types

            // The data for our dataset
            data: {
                labels: [],
                // Information about the dataset
                datasets: [{
                    backgroundColor: '#59b7b9',
                    borderWidth: 0,
                    data: [],
                }]
            },
            // Configuration options
            options: {
                layout: {
                    padding: 0
                },
                responsive: true,
                maintainAspectRatio: true,
                legend: {
                    display: false,

                },
                elements: {
                    point: {
                        radius: 0.1
                    },
                    arc: {
                        borderWidth: 0
                    }
                },
                tooltips: {
                    intersect: intersectOption,
                    displayColors: false,
                    callbacks: {
                        label: function (tooltipItem, data) {
                            let dataset = data.datasets[tooltipItem.datasetIndex];
                            let currentValue = dataset.data[tooltipItem.index];
                            return '€ ' + currentValue.toString().replace(/\B(?=(\d{3})+(?!\d))/g, " ");
                        }
                    }
                },
                title: {
                    display: true,
                    text: 'Outstanding Investments',
                    fontFamily: 'Nunito Sans',
                    fontStyle: 'normal',
                    fontSize: '14',
                    fontColor: '#000',
                    padding: '15'
                },
                scales: {
                    xAxes: [{
                        gridLines: {
                            display: false,
                            drawBorder: false
                        },
                        ticks: {
                            maxRotation: 45,
                            minRotation: 45,
                            maxTicksLimit: 7,
                            minTicksLimit: 7,
                        },
                        type: 'time',
                        position: 'bottom',
                        time: {
                            displayFormats: {'day': 'DD-MM-YY'},
                            tooltipFormat: 'DD-MM-YY',
                            unit: 'day',
                            isoWeekday: true,
                        },
                    }],
                    yAxes: [{
                        gridLines: {
                            display: false,
                            drawBorder: false,
                        },
                        ticks: {
                            beginAtZero: false,
                            autoSkip: true,
                            maxTicksLimit: 7,
                            minTicksLimit: 5,
                            callback: function (value, index, values) {
                                if (value === Math.round(value)) {
                                    return '€ ' + value.toString().replace(/\B(?=(\d{3})+(?!\d))/g, " ");
                                } else {
                                    return null
                                }
                            }
                        }
                    }]
                }
            },
            elements:
                {
                    point: {
                        radius: 0
                    }
                    ,
                    arc: {
                        borderWidth: 0
                    }
                },
            tooltips:
                {
                    intersect: false
                },
            title:
                {
                    display: true,
                    text: 'Outstanding Investments',
                    fontFamily: 'Nunito Sans',
                    fontStyle: 'normal',
                    fontSize: '14',
                    fontColor: '#000',
                    padding: '15'
                },
            scales:
                {
                    xAxes: [{
                        gridLines: {
                            display: false,
                            drawBorder: false
                        },
                        type: 'time',
                        position: 'bottom',
                        time: {
                            displayFormats: {'day': 'DD-MM-YY'},
                            tooltipFormat: 'DD-MM-YY',
                            unit: 'day',
                            isoWeekday: true,
                        },
                        ticks: {
                            beginAtZero: false,
                            autoSkip: false,
                            maxRotation: 45,
                            minRotation: 45,
                            maxTicksLimit: 7,
                            minTicksLimit: 7,
                            userCallback: function (item, index) {
                                if (!(index === 0)) return item;
                            },
                        }
                    }],
                    yAxes: [{
                        gridLines: {
                            display: false,
                            drawBorder: false
                        },
                        ticks: {
                            beginAtZero: false,
                            autoSkip: true,
                            maxTicksLimit: 7,
                            minTicksLimit: 5,
                            callback: function (value, index, values) {
                                return '€ ' + value.toString().replace(/\B(?=(\d{3})+(?!\d))/g, " ");
                            }
                        }
                    }]
                }
        });

        let ctx2 = document.getElementById('earnedIncomeChart').getContext('2d');
        let chart2 = new Chart(ctx2, {
            // The type of chart we want to create
            type: 'bar', // also try bar or other graph types

            // The data for our dataset
            data: {
                labels: [],
                // Information about the dataset
                datasets: [{
                    backgroundColor: '#59b7b9',
                    borderColor: '#009193',
                    data: [],
                    categoryPercentage: 0.95,
                    barPercentage: 1,
                }]
            },

            // Configuration options
            options:
                {
                    layout: {
                        padding: 0,
                    },
                    responsive: true,
                    maintainAspectRatio: true,
                    tooltips: {
                        displayColors: false,
                        callbacks: {
                            label: function (tooltipItem, data) {
                                let dataset = data.datasets[tooltipItem.datasetIndex];
                                let currentValue = dataset.data[tooltipItem.index];
                                return '€ ' + currentValue.toString().replace(/\B(?=(\d{3})+(?!\d))/g, " ");
                            }
                        }
                    },
                    legend: {
                        display: false,
                    }
                    ,
                    title: {
                        display: true,
                        text: 'Earned Income',
                        fontFamily: 'Nunito Sans',
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
                                        return '€ ' + value.toString().replace(/\B(?=(\d{3})+(?!\d))/g, " ");
                                    } else {
                                        return null
                                    }
                                },

                            }
                        }]
                    }
                }
        });

        statisticDay({{$investor->statistic_days}});

        $("#chart_by_amount").click(function () {
            drawLoanStatusChartByAmount();
            drawRemainingTermChartByAmount();
        });

        $("#chart_by_number").click(function () {
            drawLoanStatusChartByAmount();
            drawRemainingTermChartByAmount();
        });

        let canvasDoughnutLoanStatusChart = '<canvas id="LoanStatusChart" height="225px"></canvas>';
        let canvasDoughnutRemainingTermChart = '<canvas id="RemainingTermChart" height="225px"></canvas>';

        let drawLoanStatusChartByAmount = function () {
            // reinit canvas
            $('#canvasLoanStatusContainer').html(canvasDoughnutLoanStatusChart);

            // redraw chart
            let ctx3 = document.getElementById("LoanStatusChart").getContext("2d");
            let chart3 = new Chart(ctx3, {
                // The type of chart we want to create
                type: 'doughnut', // also try bar or other graph types

                // The data for our dataset
                data: {
                    labels: ["Current", "1 - 15 Days", "13 - 30 Days", "31 - 60 Days", "60+ Days"],
                    // Information about the dataset
                    datasets: [{
                        data: [],
                        "backgroundColor": ["#009193", "#59B7B9", "#F2F2F2", "#D9D9D9", "#7F7F7F"]
                    }]
                },

                options: {
                    elements: {
                        arc: {
                            borderWidth: 0,
                        },
                        point: {
                            radius: 0
                        }
                    },
                    cutoutPercentage: 45,
                    responsive: true,
                    maintainAspectRatio: true,
                    tooltips: {
                        displayColors: false,
                        callbacks: {
                            label: function (tooltipItem, data) {

                                let callbackData;
                                let percentage;
                                let currentValue;

                                let dataset = data.datasets[tooltipItem.datasetIndex];
                                let total = data.total;

                                if (data.chartType === "by_amount") {
                                    percentage = dataset.data[tooltipItem.index];
                                    currentValue = parseFloat(((percentage * total) / 100));
                                    callbackData = percentage.toFixed(1) + ' % / € ' + currentValue.toFixed(2).toString().replace(/\B(?=(\d{3})+(?!\d))/g, " ");
                                }

                                if (data.chartType === "by_number") {
                                    percentage = dataset.data[tooltipItem.index];
                                    currentValue = total * (percentage / 100);
                                    callbackData = Number(percentage).toFixed(1)
                                        + ' % / ' + Math.round(currentValue) + ' Loans';

                                }

                                return callbackData;
                            },
                            title: function (tooltipItem, data) {
                                return data.labels[tooltipItem[0].index];
                            }
                        }
                    },
                    layout: {
                        padding: 0
                    },
                    legend: {
                        display: true,
                        position: 'bottom',
                        labels: {
                            boxWidth: 12,
                            padding: 10,
                            fontSize: 10,
                        },
                        align: 'start'
                    },
                    title: {
                        display: true,
                        text: 'Loan Status',
                        fontFamily: 'Nunito Sans',
                        fontStyle: 'normal',
                        fontSize: '14',
                        fontColor: '#000',
                        padding: '15'
                    }
                }
            });
            chartsDonatChart3(chart3);
        };
        let drawRemainingTermChartByAmount = function () {
            // reinit canvas
            $('#canvasRemainingTermContainer').html(canvasDoughnutRemainingTermChart);

            // redraw chart
            let ctx4 = document.getElementById('RemainingTermChart').getContext('2d');
            let chart4 = new Chart(ctx4, {
                    // The type of chart we want to create
                    type: 'doughnut', // also try bar or other graph types

                    // The data for our dataset
                    data: {
                        labels: ["1 - 3 mos.", "4 - 6 mos.", "7 - 12 mos.", "12 - 24 mos.", "24+ mos."],
                        // Information about the dataset
                        datasets: [{
                            data: [],
                            "backgroundColor": ["#009193", "#59B7B9", "#F2F2F2", "#D9D9D9", "#7F7F7F"]
                        }]
                    },
                    // Configuration options
                    options: {
                        elements: {
                            arc: {
                                borderWidth: 0
                            }
                        },
                        cutoutPercentage: 45,
                        showAllTooltips: true,
                        tooltips: {
                            displayColors: false,
                            callbacks: {
                                label: function (tooltipItem, data) {
                                    let callbackData;
                                    let percentage;
                                    let currentValue;
                                    let numberOfLoan;

                                    let dataset = data.datasets[tooltipItem.datasetIndex];
                                    let total = data.total;
                                    if (data.chartType === "by_amount") {
                                        percentage = dataset.data[tooltipItem.index];
                                        currentValue = parseFloat(((percentage * total) / 100));
                                        callbackData = percentage + ' % / € ' + currentValue.toFixed(2).toString().replace(/\B(?=(\d{3})+(?!\d))/g, " ");
                                    }

                                    if (data.chartType === "by_number") {
                                        numberOfLoan = dataset.data[tooltipItem.index];

                                        callbackData = Number((numberOfLoan / total) * 100).toFixed(1)
                                            + ' % / ' + Math.round(numberOfLoan) + ' Loans';

                                    }
                                    return callbackData;
                                },
                                title: function (tooltipItem, data) {
                                    return data.labels[tooltipItem[0].index];
                                }
                            }
                        },
                        responsive: true,
                        maintainAspectRatio: true,
                        layout: {
                            padding: 0
                        },
                        legend: {
                            display: true,
                            position: 'bottom',
                            labels: {
                                boxWidth: 12,
                                padding: 10,
                                fontSize: 10,
                            }
                        },
                        title: {
                            display: true,
                            text: 'Remaining Term',
                            fontFamily: 'Nunito Sans',
                            fontStyle: 'normal',
                            fontSize: '14',
                            fontColor: '#000',
                            padding: '15'
                        }
                    }

                })
            ;
            chartsDonatChart4(chart4);
        };


        drawLoanStatusChartByAmount();
        drawRemainingTermChartByAmount();

        function statisticDay(days) {
            ajax_chart(chart, '{{ route('profile.dashboard.outstandingBalanceChart')}}', days);
            ajax_chart(chart2, '{{ route('profile.dashboard.earnedIncomeChart')}}', days);
        }

        // function to update our chart
        function ajax_chart(chart, url, days) {
            let data =
                {
                    'days':
                    days
                };
            $.getJSON(url, data).done(function (response) {
                chart.data.labels = response.labels[0];
                chart.data.datasets[0].data = response.data.income[0]; // or you can iterate for multiple datasets
                chart.options.scales.xAxes[0].time.unit = response.format;
                chart.update(); // finally update our chart
            });
        }


        function chartsDonatChart3(chart) {
            let type = $("#checkbox-parent input[type='radio']:checked").val();
            ajax_chartsDonat(chart, '{{ route('profile.dashboard.loanByAmount')}}', type);
        }

        function chartsDonatChart4(chart) {
            let type = $("#checkbox-parent input[type='radio']:checked").val();
            ajax_chartsDonat(chart, '{{ route('profile.dashboard.loanByAmountTerm')}}', type);
        }

        // function to update our chart
        function ajax_chartsDonat(chart, url, type) {
            let data =
                {
                    'type':
                    type
                };
            $.getJSON(url, data).done(function (response) {
                chart.data.labels = response.labels[0];
                chart.data.total = response.total;
                chart.data.chartType = response.chartType;
                chart.data.datasets[0].data = response.data.income[0]; // or you can iterate for multiple datasets
                chart.update(); // finally update our chart
            });
        }
    </script>
@endpush

