@extends('admin.layouts.app')
@section('panel')
    <section>
        <div class="card mb-4">
            <div class="card-header">
                <h4 class="card-title">{{translate('Subscription Logs')}}</h4>
            </div>

            <div class="card-filter">
                <form action="{{route('admin.report.subscription.search')}}" method="GET">
                    <div class="filter-form">
                        <div class="filter-item">
                            <select class="form-select" name="subs_plan" id="subs_plan">
                                <option value="" selected="" disabled="">{{ translate('Select One')}}</option>
                                @foreach($pricingPlan as $plan)
                                    <option value="{{$plan->id}}">{{$plan->name}}</option>
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
                                <th>{{ translate('Date')}}</th>
                                <th>{{ translate('User')}}</th>
                                <th>{{ translate('Plan')}}</th>
                                <th>{{ translate('Amount')}}</th>
                                <th>{{ translate('Trx Number')}}</th>
                                <th>{{ translate('Expired Date')}}</th>
                                <th>{{ translate('Status')}}</th>
                            </tr>
                        </thead>
                        @forelse($subscriptions as $subscription)
                            <tr class="@if($loop->even)@endif">
                                <td data-label="{{ translate('Date')}}">
                                    <span class="fw-bold">{{diffForHumans($subscription->created_at)}}</span><br>
                                    {{getDateTime($subscription->created_at)}}
                                </td>

                                <td data-label="{{ translate('User')}}">
                                    <a href="{{route('admin.user.details', $subscription->user_id)}}" class="fw-bold text-dark">{{@$subscription->user->email}}</a>
                                </td>

                                <td data-label="{{ translate('Plan')}}">
                                    {{$subscription->plan->name}}
                                </td>

                                <td data-label="{{ translate('Amount')}}">
                                    <span>{{shortAmount($subscription->amount)}} {{$general->currency_name}}</span>
                                </td>

                                <td data-label="{{ translate('Trx Number')}}">
                                    {{$subscription->trx_number}}
                                </td>

                                 <td data-label="{{ translate('Expired Date')}}">
                                    {{getDateTime($subscription->expired_date)}}
                                </td>

                                <td data-label="{{ translate('Status')}}">
                                    @if($subscription->status == 1)
                                        <span class="badge badge--success">{{ translate('Active')}}</span>
                                    @elseif($subscription->status == 2)
                                        <span class="badge badge--warning">{{ translate('Expired')}}</span>
                                    @elseif($subscription->status == 3)
                                        <span class="badge badge--danger">{{ translate('Inactive')}}</span>
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
                <div class="m-3">
                    {{$subscriptions->appends(request()->all())->onEachSide(1)->links()}}
                </div>
            </div>
        </div>
    </section>
@endsection
