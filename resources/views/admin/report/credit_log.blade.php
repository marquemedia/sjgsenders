@extends('admin.layouts.app')
@section('panel')
    <section>
        <div class="card mb-4">
            <div class="card-header">
                <h4 class="card-title">{{translate('SMS Credit Logs')}}</h4>
            </div>

            <div class="card-filter">
                <form action="{{route('admin.report.credit.search')}}" method="GET">
                    <div class="filter-form">
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
                            <button class="i-btn danger--btn btn--md">
                                <a class="text-white" href="{{ route('admin.report.credit.index') }}">
                                    <i class="las la-sync"></i>  {{translate('reset')}}
                                </a>
                            </button>
                        </div>
                    </div>
                </form>
            </div>

            <div class="card-body px-0">
                <div class="responsive-table">
                    <table class="w-100 m-0 text-center table--light">
                        <thead>
                            <tr>
                                <th>{{ translate('Date')}}</th>
                                <th>{{ translate('User')}}</th>
                                <th>{{ translate('Trx ID')}}</th>
                                <th>{{ translate('Credit')}}</th>
                                <th>{{ translate('Post Credit')}}</th>
                                <th>{{ translate('Details')}}</th>
                            </tr>
                        </thead>
                        @forelse($creditLogs as $creditLog)
                            <tr class="@if($loop->even)@endif">
                                <td data-label="{{ translate('Date')}}">
                                    <span class="fw-bold">{{diffForHumans($creditLog->created_at)}}</span><br>
                                    {{getDateTime($creditLog->created_at)}}
                                </td>

                                <td data-label="{{ translate('User')}}">
                                    <a href="{{route('admin.user.details', $creditLog->user_id)}}" class="fw-bold text-dark">{{$creditLog->user?->email}}</a>
                                </td>

                                <td data-label="{{ translate('Trx ID')}}">
                                    {{$creditLog->trx_number}}
                                </td>

                                <td data-label="{{ translate('Credit')}}">
                                    <span class="@if($creditLog->credit_type == "+") text--success @else text--danger @endif">
                                        {{$creditLog->credit_type == "+" ? '+' : '-'}} {{$creditLog->credit}}</span> {{ translate('credit')}}
                                </td>

                                <td data-label="{{ translate('Post Credit')}}">
                                    {{$creditLog->post_credit}} {{ translate('credit')}}
                                </td>

                                <td data-label="{{ translate('Details')}}">
                                    {{$creditLog->details}}
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
                    {{$creditLogs->appends(request()->all())->onEachSide(1)->links()}}
                </div>
            </div>
        </div>
    </section>
@endsection
