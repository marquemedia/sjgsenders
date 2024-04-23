@extends('admin.layouts.app')
@push('style-push')
    <style>
        .plan-card::after {
            background-image: url("{{asset('assets/file/default/left.png')}}");
        }
        .email-card::after {
            background-image: url("{{asset('assets/file/default/right.png')}}");
        }
        .info-card-top::after {
            background-image: url("{{asset('assets/file/default/info-card.png')}}");
        }
    </style>
@endpush
@section('panel')
<div class="row g-4 mb-4">
    <div class="col-md-6">
        <div class="card banner-card plan-card">
            <h3>{{translate('Welcome Back')}}, {{auth()->guard('admin')->user()->name}}!!</h3>
            <p>{{translate('To change any of plans for user subscription, simply click here to explore your options.')}}</p>
            <a href="{{route('admin.plan.index')}}" class="i-btn primary--btn btn--lg">{{translate('Membership Plan')}}</a>
        </div>
    </div>

    <div class="col-md-6">
           <div class="card banner-card email-card">
            <h3>{{translate('Send Email for Production')}}</h3>
            <p>{{translate('Efficient Production Email Notification: Streamlining Communications Seamlessly and Effectively')}}</p>
           <a href="{{route('admin.email.send')}}" class="i-btn primary--btn btn--lg">{{translate('Send Mail')}}</a>
        </div>
    </div>
</div>

<section>
    <div class="row g-4">
        <div class="col-12">
            <div class="row g-4 dash-cards">
                <div class="col-md-6 col-xl-4">
                    <div class="card">
                        <div class="info-card">
                            <div class="info-card-top">
                                <div class="info-card-header">
                                    <h4>{{ translate('SMS Statistics')}}</h4>
                                    <span>
                                        <i class="las la-comment"></i>
                                    </span>
                                </div>
                            </div>

                            <div class="info-card-content">
                                <div class="info-inner-card">
                                    <div class="info-inner-content">
                                        <span class="bg--lite--info text--info">
                                            <i class="las la-address-book"></i>
                                        </span>

                                        <div>
                                            <h4>{{$logs['sms']['all']}}</h4>
                                            <p >{{ translate('Total')}}</p>
                                        </div>

                                    </div>
                                </div>

                                <div class="info-inner-card">
                                    <div class="info-inner-content">
                                        <span class="bg--lite--success text--success">
                                            <i class="las la-check-double"></i>
                                        </span>

                                        <div>
                                            <h4>{{$logs['sms']['success']}}</h4>
                                            <p >{{ translate('Success')}}</p>
                                        </div>
                                    </div>
                                </div>

                                <div class="info-inner-card">
                                    <div class="info-inner-content">
                                        <span class="bg--lite--warning text--warning ">
                                            <i class="las la-hourglass-half"></i>
                                        </span>
                                        <div>
                                            <h4>{{$logs['sms']['pending']}}</h4>
                                            <p >{{ translate('Pending')}}</p>
                                        </div>
                                    </div>
                                </div>

                                <div class="info-inner-card">
                                    <div class="info-inner-content">
                                        <span class="bg--lite--danger text--danger">
                                            <i class="las la-times-circle"></i>
                                        </span>
                                        <div>
                                            <h4>{{$logs['sms']['failed']}}</h4>
                                            <p >{{ translate('Failed')}}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-6 col-xl-4">
                    <div class="card">
                        <div class="info-card">
                            <div class="info-card-top">
                                <div class="info-card-header">
                                    <h4>{{ translate('Email Statistics')}}</h4>

                                    <span>
                                        <i class="las la-envelope-open"></i>
                                    </span>
                                </div>
                            </div>

                            <div class="info-card-content">
                                <div class="info-inner-card">
                                    <div class="info-inner-content">
                                        <span class="bg--lite--info text--info">
                                            <i class="las la-address-book"></i>
                                        </span>

                                        <div>
                                            <h4>{{$logs['email']['all']}}</h4>
                                            <p >{{ translate('Total')}}</p>
                                        </div>

                                    </div>
                                </div>

                                <div class="info-inner-card">
                                    <div class="info-inner-content">
                                        <span class="bg--lite--success text--success">
                                            <i class="las la-check-double"></i>
                                        </span>

                                        <div>
                                            <h4>{{$logs['email']['success']}}</h4>
                                            <p >{{ translate('Success')}}</p>
                                        </div>
                                    </div>
                                </div>

                                <div class="info-inner-card">
                                    <div class="info-inner-content">
                                        <span class="bg--lite--warning text--warning ">
                                            <i class="las la-hourglass-half"></i>
                                        </span>
                                        <div>
                                            <h4>{{$logs['email']['pending']}}</h4>
                                            <p >{{ translate('Pending')}}</p>
                                        </div>
                                    </div>
                                </div>

                                <div class="info-inner-card">
                                    <div class="info-inner-content">
                                        <span class="bg--lite--danger text--danger">
                                            <i class="las la-times-circle"></i>
                                        </span>
                                        <div>
                                            <h4>{{$logs['email']['failed']}}</h4>
                                            <p >{{ translate('Failed')}}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-6 col-xl-4">
                    <div class="card">
                        <div class="info-card">
                            <div class="info-card-top">
                                <div class="info-card-header">
                                    <h4>{{ translate('Whatsapp Statistics')}}</h4>
                                    <span>
                                        <i class="lab la-whatsapp"></i>
                                    </span>
                                </div>
                            </div>

                             <div class="info-card-content">
                                <div class="info-inner-card">
                                    <div class="info-inner-content">
                                        <span class="bg--lite--info text--info">
                                            <i class="las la-address-book"></i>
                                        </span>

                                        <div>
                                            <h4>{{$logs['whats_app']['all']}}</h4>
                                            <p >{{ translate('Total')}}</p>
                                        </div>
                                    </div>
                                </div>

                                <div class="info-inner-card">
                                    <div class="info-inner-content">
                                        <span class="bg--lite--success text--success">
                                            <i class="las la-check-double"></i>
                                        </span>

                                        <div>
                                            <h4>{{$logs['whats_app']['success']}}</h4>
                                            <p >{{ translate('Success')}}</p>
                                        </div>
                                    </div>
                                </div>

                                <div class="info-inner-card">
                                    <div class="info-inner-content">
                                        <span class="bg--lite--warning text--warning ">
                                            <i class="las la-hourglass-half"></i>
                                        </span>
                                        <div>
                                            <h4>{{$logs['whats_app']['pending']}}</h4>
                                            <p >{{ translate('Pending')}}</p>
                                        </div>
                                    </div>
                                </div>

                                <div class="info-inner-card">
                                    <div class="info-inner-content">
                                        <span class="bg--lite--danger text--danger">
                                            <i class="las la-times-circle"></i>
                                        </span>
                                        <div>
                                            <h4>{{$logs['whats_app']['failed']}}</h4>
                                            <p >{{ translate('Failed')}}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-4 col-lg-5">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">{{ translate('All User')}}</h4>
                </div>
                <div class="card-body">
                    <div>
                        <div id="userInfo"></div>
                        <ul class="list-group list-group-flush border-dashed mb-0 mt-3 pt-2">
                            <li class="list-group-item px-0">
                                <div class="d-flex align-items-center">
                                    <div class="flex-shrink-0 avatar-sm">
                                        <span class="avatar-title bg-light rounded-circle fs-3">
                                        <i class="las la-users"></i>
                                        </span>
                                    </div>
                                    <div class="flex-grow-1 ms-3">
                                        <h6 class="mb-0">{{ translate('Users')}}</h6>
                                    </div>
                                    <div class="flex-shrink-0 text-end">
                                        <h6 class="mb-1">{{$totalUser['total_user']}}</h6>
                                    </div>
                                </div>
                            </li>

                            <li class="list-group-item px-0">
                                <div class="d-flex align-items-center">
                                    <div class="flex-shrink-0 avatar-sm">
                                        <span class="avatar-title bg-light rounded-circle fs-3">
                                        <i class="las la-user-shield"></i>
                                        </span>
                                    </div>
                                    <div class="flex-grow-1 ms-3">
                                        <h6 class="mb-0">{{ translate('SUBSCRIBER')}}</h6>
                                    </div>
                                    <div class="flex-shrink-0 text-end">
                                        <h6 class="mb-1">{{$totalUser['subscriber']}}</h6>
                                    </div>
                                </div>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-8 col-lg-7">
            <div class="card amount">
                <div class="card-header">
                    <h4 class="card-title">{{ translate('Amount')}}</h4>
                    <div class="flex-shrink-0 avatar-xs">
                        <span class="avatar-title text-primary rounded-circle fs-4"><i class="las la-wallet"></i></span>
                    </div>
                </div>

                <div class="amount-card-container">
                    <div class="row g-0">
                        <div class="col-6">
                            <div class="amount-card">
                                <p>{{ translate('Subscription')}}</p>
                                <h6>{{$general->currency_symbol}}{{array_sum($paymentReport['amount'])}}</h6>
                            </div>
                        </div>

                        <div class="col-6">
                            <div class="amount-card">
                                <p>{{ translate('Payment Charge')}}</p>
                                <h6>{{$general->currency_symbol}}{{array_sum($paymentReport['charge'])}}</h6>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card-body px-0 pt-0">
                    <div id="revenueChart" class="charts-height"></div>
                </div>
            </div>
        </div>

        <div class="col-xl-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">{{ translate('SMS Whatsapp & Emails Details Report')}}</h4>
                </div>

                <div class="card-body px-0">
                    <div id="emailDetails" class="charts-height"></div>
                </div>
            </div>
        </div>

        <div class="col-xl-6">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">{{ translate('New User')}}</h4>
                </div>
                <div class="card-body px-0">
                    <div class="responsive-table">
                        <table>
                            <thead>
                                <tr>
                                    <th>{{ translate('Customer')}}</th>
                                    <th>{{ translate('Email - Phone')}}</th>
                                    <th>{{ translate('Status')}}</th>
                                    <th>{{ translate('Joined At')}}</th>
                                </tr>
                            </thead>
                            @forelse($customers as $customer)
                                <tr class="@if($loop->even)@endif">
                                    <td data-label="{{ translate('Customer')}}">
                                        <a href="{{route('admin.user.details', $customer->id)}}" class="brand" data-bs-toggle="tooltip" data-bs-placement="top" title="{{ translate('Click For Details')}}">
                                            {{$customer->name}}<br>
                                        </a>
                                    </td>
                                    <td data-label="{{ translate('Email')}}">
                                        {{$customer->email}}<br>
                                        {{$customer->phone}}
                                    </td>

                                    <td data-label="{{ translate('Status')}}">
                                        @if($customer->status == 1)
                                            <span class="badge badge--success">{{ translate('Active')}}</span>
                                        @else
                                            <span class="badge badge--danger">{{ translate('Banned')}}</span>
                                        @endif
                                    </td>

                                    <td data-label="{{ translate('Joined At')}}">
                                        {{diffForHumans($customer->created_at)}}<br>
                                        {{getDateTime($customer->created_at)}}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td class="text-muted text-center" colspan="100%">{{ translate('No Data Found')}}</td>
                                </tr>
                            @endforelse
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-6">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">{{ translate('Latest Payment Log')}}</h4>
                </div>

                <div class="card-body px-0">
                    <div class="responsive-table">
                        <table>
                            <thead>
                                <tr>
                                    <th>{{ translate('Time')}}</th>
                                    <th>{{ translate('User')}}</th>
                                    <th>{{ translate('Amount')}}</th>
                                    <th>{{ translate('Final Amount')}}</th>
                                    <th>{{ translate('TrxID')}}</th>
                                </tr>
                            </thead>
                            @forelse($paymentLogs as $paymentLog)
                                <tr class="@if($loop->even)@endif">
                                    <td data-label="{{ translate('Time')}}">
                                        <span>{{diffForHumans(@$paymentLog->created_at)}}</span><br>
                                        {{getDateTime(@$paymentLog->created_at)}}
                                    </td>

                                    <td data-label="{{ translate('User')}}">
                                        <a href="{{route('admin.user.details', $paymentLog->user_id)}}" class="fw-bold text-dark">{{@$paymentLog->user->name}}</a>
                                    </td>

                                    <td data-label="{{ translate('Amount')}}">
                                        {{shortAmount(@$paymentLog->amount)}} {{@$general->currency_name}}
                                        <br>
                                        {{@$paymentLog->paymentGateway->name}}
                                    </td>

                                    <td data-label="{{ translate('Final Amount')}}">
                                        <span class="text--success fw-bold">{{shortAmount($paymentLog->final_amount)}} {{@$paymentLog->paymentGateway->currency->name}}</span>
                                    </td>

                                        <td data-label="{{ translate('TrxID')}}">
                                        {{$paymentLog->trx_number}} <br>
                                        @if($paymentLog->status == 1)
                                            <span class="badge badge--primary">{{ translate('Pending')}}</span>
                                        @elseif($paymentLog->status == 2)
                                            <span class="badge badge--success">{{ translate('Received')}}</span>
                                        @elseif($paymentLog->status == 3)
                                            <span class="badge badge--danger">{{ translate('Rejected')}}</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td class="text-muted text-center" colspan="100%">{{ translate('No Data Found')}}</td>
                                </tr>
                            @endforelse
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>


@if($general->cron_pop_up=='true')
    <div class="modal fade" id="cronjob" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog  modal-lg modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">{{translate('Basic Settings & Cron Job Setting Alert')}}</h5>
                    <button type="button" class="i-btn btn--sm text--danger bg--lite--danger" data-bs-dismiss="modal"><i class="las la-times"></i></button>
                </div>
                <div class="modal-body">
                    <div class="card">
                        <div class="card-body">
                            <span class="badge badge--success mb-1">{{translate('Configure following area for SMS, EMAIL, WhatsApp')}}</span>
                            <p class="text-muted">
                                <i class="fas fa-hand-point-right"></i> {{translate('For SMS: Please setup SMS API From Those Provider or use SMS App.')}}<br>
                                <i class="fas fa-hand-point-right"></i> {{translate('For Email: Please setup SMTP Configuration for Sending Email and users notification.')}}<br>
                                <i class="fas fa-hand-point-right"></i> {{translate('For WhatsApp: Please setup node server, then scan your device for sending WhatsApp messages')}}
                            </p>
                            <div class="d-flex align-items-center justify-content-center flex-wrap gap-3 my-3">
                                <a class="i-btn primary--btn btn--sm" href="{{route('admin.sms.gateway.sms.api')}}">
                                    <i class="fas fa-sms"></i> {{translate('SMS')}}
                                </a>
                                <a class="i-btn info--btn btn--sm" href="{{route('admin.mail.list')}}">
                                    <i class="fas fa-envelope"></i> {{translate('Email')}}
                                </a>
                                <a class="i-btn success--btn btn--sm" href="{{route('admin.gateway.whatsapp.device')}}">
                                    <i class="fab fa-whatsapp"></i> {{translate('WhatsApp')}}
                                </a>
                            </div>
                            <span class="badge badge--success mb-1">{{translate('To set cron job for the following tasks automation:')}}</span>
                            <p class="text-muted">
                                <i class="fas fa-hand-point-right"></i> {{translate('Bulk SMS, Email, and WhatsApp message sending.')}}<br>
                                <i class="fas fa-hand-point-right"></i> {{translate('Background process for contact import to reduce server usage.')}}<br>
                                <i class="fas fa-hand-point-right"></i> {{translate('Implementing delays and strategies to minimize blocking issues with WhatsApp.')}}
                            </p>
                            <hr>
                            <div class="mt-3 mb-3">
                                <label for="queue_url" class="form-label">{{translate('Cron Job i')}} <sup class="text--danger">* {{translate('Set time for 1 minute')}}</sup></label>
                                <div class="input-group mb-3">
                                    <input type="text" class="form-control" value="curl -s {{route('queue.work')}}" id="queue_url" readonly="">
                                    <span class="input-group-text btn btn--success" onclick="queue()">
                                        <i class="fas fa-copy"></i>
                                    </span>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label for="cron--run" class="form-label">{{translate('Cron Job ii')}} <sup class="text--danger">* {{translate('Set time for 2 minutes')}}</sup></label>
                                <div class="input-group mb-3">
                                    <input type="text" class="form-control" value="curl -s {{route('cron.run')}}" id="cron--run" readonly="">
                                    <span class="input-group-text btn btn--success" onclick="cronJobRun()">
                                        <i class="fas fa-copy"></i>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="my-3 px-3">
                    <button type="button" class="w-100 bg--lite--danger text--danger i-btn btn--lg bor" data-bs-dismiss="modal">{{translate('Cancel')}}</button>
                </div>
                <div class="col-md-12 text-center">
                    <p class="bg-dark text-white p-2">
                        {{translate('To disable the CronJob setup pop-up, simply')}}
                        <a href="{{route('admin.general.setting.index')}}" class="badge badge--info text--white">
                        {{translate('Click Here')}}</a>
                    </p>
                </div>
            </div>
        </div>
    </div>
@endif


@endsection


@push('script-push')
<script>
     window.addEventListener('DOMContentLoaded', function() {
        const cronJob =document.getElementById('cronjob');
        if (cronJob) {
            var cronjob = new bootstrap.Modal(cronJob);
            if (cronjob) {cronjob.show()}
        }
     });

     var chartColor = ["var(--primary-color)", "var(--secondary-color)"]
    // User Chart
        const userInfo = document.querySelector('#userInfo');
        if (userInfo !== null) {
            var options = {
                    series: [{{implode(',',array_values($totalUser))}}],
                    labels: ["Users", "Subscribers"],
                    chart: { type: "donut", height: 242 },
                    plotOptions: {
                    pie: {
                        size: 100,
                        offsetX: 0,
                        offsetY: 0,
                        donut: {
                        size: "65%",
                        labels: {
                            show: !0,
                            name: { show: !0, fontSize: "18px", offsetY: -5 },
                            value: {
                            show: !0,
                            fontSize: "20px",
                            color: "#343a40",
                            fontWeight: 500,
                            offsetY: 5,
                            },
                            total: {
                            show: !0,
                            fontSize: "13px",
                            label: "Total Users",
                            color: "#9599ad",
                            fontWeight: 500,
                            },
                        },
                        },
                    },
                    },
                    dataLabels: { enabled: !1 },
                    legend: { show: !1 },
                    yaxis: {
                    labels: {
                        formatter: function (e) {
                        return e;
                        },
                    },
                    },
                    stroke: { lineCap: "round", width: 2 },
                    colors: chartColor,
                }

            var userChart = new ApexCharts(userInfo, options);
            userChart.render();
        }

    //Email Chart
    const emailDetails = document.querySelector('#emailDetails');
    if(emailDetails !== null){
        var options = {
            series: [
                {
                    name: 'SMS',
                    type: 'column',
                    data: [{{implode(',', $smsWhatsAppReport['sms'])}}]
                },
                {
                    name: 'Whatsapp',
                    type: 'line',
                    data: [{{implode(',', $smsWhatsAppReport['whatsapp'])}}]
                },
                {
                    name: 'Email',
                    type: 'line',
                    data: [{{implode(',', $smsWhatsAppReport['email'])}}]
                }
            ],
            chart: {
                height: 350,
                type: 'line',
            },
            stroke: {
                width: [0, 4]
            },
            colors: chartColor,
                dataLabels: {
                enabled: true,
                enabledOnSeries: [1]
            },
            labels: [@php echo $paymentReportMonths @endphp],
                xaxis: {
                type: 'month'
            },
            yaxis: [{
                title: {
                    text: 'SMS & Whatsapp Details',
                },
            },
            {
            opposite: true,
            title: {
                text: 'Emails Details'
            }
        }],
        plotOptions: {
            bar: {
                columnWidth: "30%",
                barHeight: "80%",
            },
        }
        };
        var chart = new ApexCharts(emailDetails, options);
        chart.render();
    }

    // Revenue Chart

   const revenueChart = document.querySelector('#revenueChart');
   if (revenueChart) {
      var options = {
        series: [{
                name: "Subscription",
                type: "area",
                data: [{{implode(',', $paymentReport['amount'])}}],
            },
            {
                name: "Payment Charge",
                type: "line",
                data: [{{implode(',', $paymentReport['charge'])}}],
            },
        ],
        chart: {
            height: 285,
            type: "line",
            toolbar: {
                show: false,
            },
        },
        stroke: {
            curve: "straight",
            dashArray: [0, 0, 8],
            width: [2, 0, 2.2],
        },
        fill: {
            opacity: [0.1, 0.9, 1],
        },
        markers: {
            size: [0, 0, 0],
            strokeWidth: 2,
            hover: {
                size: 4,
            },
        },
        xaxis: {
            categories: [@php echo $paymentReportMonths @endphp],
            axisTicks: {
                show: false,
            },
            axisBorder: {
                show: false,
            },
        },
        grid: {
            show: true,
            xaxis: {
                lines: {
                    show: true,
                },
            },
            yaxis: {
                lines: {
                    show: false,
                },
            },
            padding: {
                top: 0,
                right: -2,
                bottom: 15,
                left: 10,
            },
        },
        legend: {
            show: true,
            horizontalAlign: "center",
            offsetX: 0,
            offsetY: -5,
            markers: {
                width: 9,
                height: 9,
                radius: 6,
            },
            itemMargin: {
                horizontal: 10,
                vertical: 0,
            },
        },
        plotOptions: {
            bar: {
                columnWidth: "20%",
                barHeight: "80%",
            },
        },
        // colors: chartColor,
        tooltip: {
            shared: true,
            y: [{
                    formatter: function (y) {
                        if (typeof y !== "undefined") {
                            return "{{$general->currency_symbol}}" + y.toFixed(0);
                        }
                        return y;
                    },
                },
                {
                    formatter: function (y) {
                        if (typeof y !== "undefined") {
                            return "{{$general->currency_symbol}}" + y.toFixed(2);
                        }
                        return y;
                    },
                },
                {
                    formatter: function (y) {
                        if (typeof y !== "undefined") {
                            return "{{$general->currency_symbol}}" + y.toFixed(2);
                        }
                        return y;
                    },
                },
            ],
        },
      };

   const chart = new ApexCharts(
       revenueChart,
       options
   );
   chart.render();
}

function cronJobRun() {
    const copyText = document.getElementById("cron--run");
    copyText.select();
    copyText.setSelectionRange(0, 99999)
    document.execCommand("copy");
    notify('success', 'Copied the text : ' + copyText.value);
}

function queue() {
    const copyText = document.getElementById("queue_url");
    copyText.select();
    copyText.setSelectionRange(0, 99999)
    document.execCommand("copy");
    notify('success', 'Copied the text : ' + copyText.value);
}
</script>
@endpush
