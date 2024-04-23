@extends('admin.layouts.app')
@section('panel')
    <section>
        <div class="card mb-4">
            <div class="card-header">
                <h4 class="card-title">{{translate('Payment Logs')}}</h4>
            </div>

            <div class="card-filter">
                <form action="{{route('admin.report.payment.search')}}" method="GET">
                    <div class="filter-form">
                        <div class="filter-item">
                            <select class="form-select" name="paymentMethod" id="paymentMethod">
                                <option value="" selected="" disabled="">{{ translate('Select One')}}</option>
                                @foreach($paymentMethods as $paymentMethod)
                                    <option value="{{$paymentMethod->id}}">{{$paymentMethod->name}}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="filter-item">
                            <input type="text" autocomplete="off" name="search" placeholder="{{ translate('Search by trx id')}}"  class="form-control" id="search" value="{{@$search}}">
                        </div>

                        <div class="filter-item">
                            <input type="text" class="form-control datepicker-here" name="date" value="{{@$searchDate}}" data-range="true" data-multiple-dates-separator=" - " data-language="en" data-position="bottom right" autocomplete="off" placeholder="{{ translate('From Date-To Date')}}" id="date">
                        </div>
                        <div class="filter-action">
                            <button class="i-btn info--btn btn--md" type="submit">
                                <i class="fas fa-search"></i> {{ translate('Search')}}
                            </button>
                        </div>
                    </div>
                </form>
            </div>

            <div class="card-body px-0">
                <div class="responsive-table">
                    <table>
                        <thead>
                            <tr>
                                <th>{{ translate('Time')}}</th>
                                <th>{{ translate('User')}}</th>
                                <th>{{ translate('Method')}}</th>
                                <th>{{ translate('Amount')}}</th>
                                <th>{{ translate('Final Amount')}}</th>
                                <th>{{ translate('Transaction Number')}}</th>
                                <th>{{ translate('Status')}}</th>
                                <th>{{ translate('Action')}}</th>
                            </tr>
                        </thead>
                        @forelse($paymentLogs as $paymentLog)
                            <tr class="@if($loop->even)@endif">
                                <td data-label="{{ translate('Time')}}">
                                    <span>{{diffForHumans($paymentLog->created_at)}}</span><br>
                                    {{getDateTime($paymentLog->created_at)}}
                                </td>

                                <td data-label="{{ translate('User')}}">
                                    <a href="{{route('admin.user.details', $paymentLog->user_id)}}" class="fw-bold text-dark">{{@$paymentLog->user->email}}</a>
                                </td>

                                <td data-label="{{ translate('Method')}}">
                                    {{$paymentLog->paymentGateway->name}}
                                </td>

                                <td data-label="{{ translate('Amount')}}">
                                    {{shortAmount($paymentLog->amount)}} {{$general->currency_name}}
                                </td>

                                <td data-label="{{ translate('Final Amount')}}">
                                    <span class="text--success fw-bold">{{shortAmount($paymentLog->final_amount)}} {{$paymentLog->paymentGateway->currency->name}}</span>
                                </td>

                                 <td data-label="{{ translate('Trx Number')}}">
                                    {{$paymentLog->trx_number}}
                                </td>

                                <td data-label="{{ translate('Status')}}">
                                    @if($paymentLog->status == 1)
                                        <span class="badge badge--primary">{{ translate('Pending')}}</span>
                                    @elseif($paymentLog->status == 2)
                                        <span class="badge badge--success">{{ translate('Received')}}</span>
                                    @elseif($paymentLog->status == 3)
                                        <span class="badge badge--danger">{{ translate('Rejected')}}</span>
                                    @endif
                                </td>

                                <td data-label="{{ translate('Action')}}">
                                    <a href="{{route('admin.report.payment.detail', $paymentLog->id)}}" class="i-btn primary--btn btn--sm text-light"><i class="las la-desktop"></i></a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td class="text-muted text-center" colspan="100%">{{ translate('No Data Found')}}</td>
                            </tr>
                        @endforelse
                    </table>
                </div>

                <div class="m-3">
                    {{$paymentLogs->appends(request()->all())->onEachSide(1)->links()}}
                </div>
            </div>
        </div>
    </section>
@endsection



