@extends('admin.layouts.app')
@section('panel')
    <section>
        <div class="card mb-4">
            <div class="card-header">
                <h4 class="card-title">{{translate('Support Tickets')}}</h4>
            </div>
          
            <div class="card-filter">
                <form action="{{route('admin.support.ticket.search',$scope ?? str_replace('admin.support.ticket.', '', request()->route()->getName()))}}" method="GET">
                    <div class="filter-form">
                        <div class="filter-item">
                            <select class="form-select" name="status" id="status">
                                <option value="" selected disabled>{{ translate('Select Status')}}</option>
                                <option {{ @$status == '1' ? 'selected' : '' }} value="1" >{{ translate('Running')}}</option>
                                <option {{ @$status == '2' ? 'selected' : '' }} value="2" >{{ translate('Answered')}}</option>
                                <option {{ @$status == '3' ? 'selected' : '' }} value="3" >{{ translate('Replied')}}</option>
                                <option {{ @$status == '4' ? 'selected' : '' }} value="4" >{{ translate('Closed')}}</option>
                            </select>
                        </div>

                        <div class="filter-item">
                            <select class="form-select" name="priority" id="priority">
                                <option value="" selected disabled>{{ translate('Select Priority')}}</option>
                                <option {{ @$priority == '1' ? 'selected' : '' }} value="1" >{{ translate('Low')}}</option>
                                <option {{ @$priority == '2' ? 'selected' : '' }} value="2" >{{ translate('Medium')}}</option>
                                <option {{ @$priority == '3' ? 'selected' : '' }} value="3" >{{ translate('High')}}</option>
                            </select>
                        </div>

                        <div class="filter-item">
                            <input type="text" autocomplete="off" name="search" placeholder="{{translate('Search with subject')}}" class="form-control" id="search" value="{{@$search}}">
                        </div>

                        <div class="filter-item">
                            <input type="text" class="form-control datepicker-here" name="date" value="{{@$searchDate}}" data-range="true" data-multiple-dates-separator=" - " data-language="en" data-position="bottom right" autocomplete="off" placeholder="{{translate('From Date-To Date')}}" id="date">
                        </div>
                        <div class="filter-action">
                            <button class="i-btn info--btn btn--md" type="submit">
                                <i class="fas fa-search"></i> {{ translate('Search')}}
                            </button>
                            
                            <a class="i-btn danger--btn btn--md" href="{{ route('admin.support.ticket.index') }}">
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
                                <th>{{ translate('Subject')}}</th>
                                <th>{{ translate('Submitted By')}}</th>
                                <th>{{ translate('Priority')}}</th>
                                <th>{{ translate('Status')}}</th>
                                <th>{{ translate('Action')}}</th>
                            </tr>
                        </thead>
                        @forelse($supportTickets as $supportTicket)
                            <tr class="@if($loop->even)@endif">
                                <td data-label="{{ translate('Date')}}">
                                    <span>{{diffForHumans($supportTicket->created_at)}}</span><br>
                                    {{getDateTime($supportTicket->created_at)}}
                                </td>

                                <td data-label="{{ translate('Subject')}}">
                                    <span class="fw-bold"><a href="{{route('admin.support.ticket.details', $supportTicket->id)}}">{{$supportTicket->subject}}</a></span>
                                </td>

                                <td data-label="{{ translate('Submitted By')}}">
                                    <a href="{{route('admin.user.details',$supportTicket->user_id)}}" class="fw-bold text-dark">{{$supportTicket->user?->email}}</a>
                                </td>

                                <td data-label="{{ translate('Priority')}}">
                                    @if($supportTicket->priority == 1)
                                        <span class="badge badge--info">{{ translate('Low')}}</span>
                                    @elseif($supportTicket->priority == 2)
                                        <span class="badge badge--primary">{{ translate('Medium ')}}</span>
                                    @elseif($supportTicket->priority == 3)
                                        <span class="badge badge--success">{{ translate('High')}}</span>
                                    @endif
                                </td>

                                <td data-label="{{ translate('Status')}}">
                                    @if($supportTicket->status == 1)
                                        <span class="badge badge--info">{{ translate('Running')}}</span>
                                    @elseif($supportTicket->status == 2)
                                        <span class="badge badge--primary">{{ translate('Answered')}}</span>
                                    @elseif($supportTicket->status == 3)
                                        <span class="badge badge--warning">{{ translate('Replied')}}</span>
                                    @elseif($supportTicket->status == 4)
                                        <span class="badge badge--danger">{{ translate('Closed')}}</span>
                                    @endif
                                </td>

                                <td data-label="{{ translate("Action")}}">
                                    <div class="d-flex align-items-center justify-content-md-start justify-content-end gap-3">
                                        <a href="{{route('admin.support.ticket.details', $supportTicket->id)}}" class="i-btn primary--btn btn--sm"><i class="las la-desktop"></i></a>
                                    </div>
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
                    {{$supportTickets->appends(request()->all())->onEachSide(1)->links()}}
                </div>
	        </div>
	    </div>
    </section>
@endsection




