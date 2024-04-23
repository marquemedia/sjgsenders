@extends('admin.layouts.app')
@section('panel')
    <section>
        <div class="card mb-4">
            <div class="card-header">
                <h4 class="card-title">{{translate('All Transactions')}}</h4>
            </div>

            <div class="card-filter">
                <form action="{{route('admin.report.transaction.search')}}" method="GET">
                    <div class="filter-form">
                        <div class="filter-item">
                            <select class="form-select select2" name="paymentMethod" id="paymentMethod">
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
                            <a class="i-btn danger--btn btn--md" href="{{route('admin.report.transaction.index')}}">
                                <i class="las la-sync"></i>  {{translate('reset')}}
                            </a>
                        </div>
                    </div>
                </form>
            </div>

            <div class="card-body px-0">
                <div class="responsive-table">
                    <table>
                        <thead>
                            <tr>
                                <th>{{ translate('Date')}}</th>
                                <th>{{ translate('User')}}</th>
                                <th>{{ translate('Trx Number')}}</th>
                                <th>{{ translate('Amount')}}</th>
                                <th>{{ translate('Details')}}</th>
                            </tr>
                        </thead>
                        @forelse($transactions as $transaction)
                            <tr class="@if($loop->even)@endif">
                                <td data-label="{{ translate('Date')}}">
                                    <span class="fw-bold">{{diffForHumans($transaction->created_at)}}</span><br>
                                    {{getDateTime($transaction->created_at)}}
                                </td>

                                <td data-label="{{ translate('User')}}">
                                    <a href="{{route('admin.user.details', $transaction->user_id)}}" class="fw-bold text-dark">{{$transaction->user?->email}}</a>
                                </td>

                                <td data-label="{{ translate('Trx Number')}}">
                                    {{$transaction->transaction_number}}
                                </td>
                                <td data-label="{{ translate('Amount')}}">
                                    <span class="@if($transaction->transaction_type == "+") text--success @else text--danger @endif">
                                        {{$transaction->transaction_type == "+" ? '+' : '-'}}{{shortAmount($transaction->amount)}} {{$general->currency_name}}</span>
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
                <div class="m-3">
                    {{$transactions->appends(request()->all())->onEachSide(1)->links()}}
                </div>
	        </div>
        </div>
    </section>
@endsection


@push('script-push')
<script>
	(function($){
		"use strict";

		$('.select2').select2({
			tags: true,
			tokenSeparators: [',']
		});
	})(jQuery);
</script>
@endpush

