@extends('user.layouts.app')
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

<section>
    <div class="row g-4">
        <div class="col-md-6">
            <div class="card banner-card plan-card">
                <h3>{{translate("Welcome Back ").auth()->user()->name}}</h3>
                <p>{{translate('If you ever feel the need to upgrade any of your plan, simply click here to explore your options.')}}</p>
                <a href="{{route('user.plan.create')}}" class="i-btn primary--btn btn--lg">{{translate('Upgrade Plan')}}</a>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card banner-card email-card">
                <h3>{{translate('Send Email for Production')}}</h3>
                <p>{{translate('Efficient Production Email Notification: Streamlining Communications Seamlessly and Effectively')}}</p>
            <a href="{{route('user.manage.email.send')}}" class="i-btn primary--btn btn--lg">{{translate('Send Mail')}}</a>
            </div>
        </div>

        <div class="col-md-6 col-xl-4">
            <div class="card user-credit-card">
                <div class="card-body">
                     <div class="user-credit-content">
                        <div class="remaining-credit">
                            <h4>{{auth()->user()->credit}}</h4>
                            <p class="pb-3 pt-2">{{ translate('Remaining SMS Credit')}}</p>
                            <a href="{{route('user.plan.create')}}" class="i-btn btn--sm info--btn">{{ translate('Buy Credit')}}</a>
                        </div>

                        <div class="icon text--secondary">
                             <i class="las la-sms"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-6 col-xl-4">
            <div class="card user-credit-card">
                <div class="card-body">
                     <div class="user-credit-content">
                        <div class="remaining-credit">
                            <h4>{{auth()->user()->email_credit}}</h4>
                            <p class="pb-3 pt-2">{{ translate('Remaining Email Credit')}}</p>
                            <a href="{{route('user.plan.create')}}" class="i-btn btn--sm info--btn">{{ translate('Buy Credit')}}</a>
                        </div>

                        <div class="icon text--danger">
                            <i class="las la-envelope"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-6 col-xl-4">
            <div class="card user-credit-card">
                <div class="card-body">
                    <div class="user-credit-content">
                        <div class="remaining-credit">
                            <h4>{{auth()->user()->whatsapp_credit}}</h4>
                            <p class="pb-3 pt-2">{{ translate('Remaining Whatsapp Credit')}}</p>
                            <a href="{{route('user.plan.create')}}" class="i-btn btn--sm info--btn">{{ translate('Buy Credit')}}</a>
                        </div>

                        <div class="icon text--success">
                            <i class="lab la-whatsapp"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

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


        <div class="col-xl-6">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">{{ translate('Latest Credit Log')}}</h4>
                </div>

                <div class="card-body px-0">
                    <div class="responsive-table">
                        <table>
                            <thead>
                                <tr>
                                    <th>{{ translate('Date')}}</th>
                                    <th>{{ translate('Trx Number')}}</th>
                                    <th>{{ translate('Credit')}}</th>
                                    <th>{{ translate('Post Credit')}}</th>
                                </tr>
                            </thead>
                            @forelse($credits as $creditdata)
                                <tr class="@if($loop->even)@endif">
                                    <td data-label="{{ translate('Date')}}">
                                        <span>{{diffForHumans($creditdata->created_at)}}</span><br>
                                        {{getDateTime($creditdata->created_at)}}
                                    </td>

                                    <td data-label="{{ translate('Trx Number')}}">
                                        {{$creditdata->trx_number}}
                                    </td>

                                    <td data-label="{{ translate('Credit')}}">
                                        <span class="@if($creditdata->credit_type == '+')text--success @else text--danger @endif">{{ $creditdata->credit_type }} {{shortAmount($creditdata->credit)}}
                                        </span>{{ translate('Credit')}}
                                    </td>

                                    <td data-label="{{ translate('Post Credit')}}">
                                        {{$creditdata->post_credit}} {{ translate('Credit')}}
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
                    <h4 class="card-title">{{ translate('Latest Transactions Log')}}</h4>
                </div>
                <div class="card-body px-0">
                    <div class="responsive-table">
                        <table>
                            <thead>
                                <tr>
                                    <th>{{ translate('Date')}}</th>
                                    <th>{{ translate('Trx Number')}}</th>
                                    <th>{{ translate('Amount')}}</th>
                                    <th>{{ translate('Detail')}}</th>
                                </tr>
                            </thead>
                            @forelse($transactions as $transaction)
                                <tr class="@if($loop->even)@endif">
                                    <td data-label="{{ translate('Date')}}">
                                        <span>{{diffForHumans($transaction->created_at)}}</span><br>
                                        {{getDateTime($transaction->created_at)}}
                                    </td>

                                    <td data-label="{{ translate('Trx Number')}}">
                                        {{$transaction->transaction_number}}
                                    </td>

                                    <td data-label="{{ translate('Amount')}}">
                                        <span class="@if($transaction->transaction_type == '+')text--success @else text--danger @endif">{{ $transaction->transaction_type }} {{shortAmount($transaction->amount)}} {{$general->currency_name}}
                                        </span>
                                    </td>

                                    <td data-label="{{ translate('Details')}}">
                                        {{$transaction->details}}
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
@endsection

