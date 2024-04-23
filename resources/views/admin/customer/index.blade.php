@extends('admin.layouts.app')
@section('panel')
<section>
    <div class="card mb-4">
        <div class="card-header">
            <h4 class="card-title">{{translate('Users List')}}</h4>
        </div>

        <div class="card-filter">
            <form action="{{route('admin.user.search')}}" method="GET">
                <div class="filter-form">
                    <div class="filter-item">
                        <select name="status" class="form-select">
                            <option value="all" @if(@$status == "all") selected @endif>{{translate('All')}}</option>
                            <option value="active" @if(@$status == "active") selected @endif>{{translate('Active')}}</option>
                            <option value="banned" @if(@$status == "banned") selected @endif>{{translate('Banned')}}</option>
                        </select>
                    </div>

                    <div class="filter-item">
                        <input type="text" autocomplete="off" name="search" placeholder="{{translate('Search with User, Email')}}" class="form-control" id="search" value="{{@$search}}">
                    </div>

                    <div class="filter-item">
                        <input type="text" class="form-control datepicker-here" name="date" value="{{@$searchDate}}" data-range="true" data-multiple-dates-separator=" - " data-language="en" data-position="bottom right" autocomplete="off" placeholder="{{translate('From Date-To Date')}}" id="date">
                    </div>
                    <div class="filter-action">
                        <button class="i-btn info--btn btn--md" type="submit">
                            <i class="fas fa-search"></i> {{ translate('Search')}}
                        </button>
                        <button class="i-btn danger--btn btn--md">
                            <a class="text-white" href="{{ route('admin.user.index') }}">
                                <i class="las la-sync"></i>  {{translate('reset')}}
                            </a>
                        </button>
                        <button class="i-btn primary--btn btn--md" type="button" data-bs-toggle="modal" data-bs-target="#addUser">
                            <i class="fas fa-users"></i> {{ translate('Add New')}}
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
                            <th>{{ translate('Customer')}}</th>
                            <th>{{ translate('Email')}}</th>
                            <th>{{ translate('Status')}}</th>
                            <th>{{ translate('Add / Returned Credit')}}</th>
                            <th>{{ translate('Joined At')}}</th>
                            <th>{{ translate('Action')}}</th>
                        </tr>
                    </thead>
                    @forelse($customers as $customer)
                        <tr class="@if($loop->even)@endif">
                            <td data-label="{{ translate('Customer')}}">
                                {{$customer->name ?? 'N/A'}}
                            </td>

                            <td data-label="{{ translate('Email')}}">
                                {{$customer->email}}
                            </td>

                            <td data-label="{{ translate('Status')}}">
                                @if($customer->status == 1)
                                    <span class="badge badge--success">{{ translate('Active')}}</span>
                                @else
                                    <span class="badge badge--danger">{{ translate('Banned')}}</span>
                                @endif
                            </td>

                            <td data-label="{{ translate('Add / Returned Credit')}}">
                                <button type="button"
                                    class="badge btn bg--success text-white createdupdate"
                                    data-bs-toggle="modal" data-id="{{$customer->id}}"
                                    data-bs-target="#creditaddreturn">{{translate('Add / Returned')}}
                                </button>
                            </td>

                            <td data-label="{{ translate('Joined At')}}">
                                {{diffForHumans($customer->created_at)}}<br>
                                {{getDateTime($customer->created_at)}}
                            </td>

                            <td data-label={{ translate('Action')}}>
                                <div class="d-flex align-items-center justify-content-md-start justify-content-end gap-3">
                                    <a target="_blank" href="{{route('admin.user.login', $customer->id)}}" class="i-btn btn--sm success--btn"><i class="las la-sign-in-alt"></i></a>
                                    <a href="{{route('admin.user.details', $customer->id)}}" class="i-btn btn--sm primary--btn brand" data-bs-toggle="tooltip" data-bs-placement="top" title="Details"><i class="las la-desktop"></i></a>
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
                {{$customers->appends(request()->all())->onEachSide(1)->links()}}
            </div>
        </div>
    </div>
</section>


<div class="modal fade" id="addUser" tabindex="-1" aria-labelledby="addUserLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
				<h5 class="modal-title">{{ translate('Add New User')}}</h5>

                <button type="button" class="i-btn bg--lite--danger text--danger btn--sm" data-bs-dismiss="modal"> <i class="las la-times"></i></button>
			</div>
            <form action="{{route('admin.user.store')}}" method="POST">
                @csrf
                <div class="modal-body">
                    <div>
                        <div class="mb-3">
                            <label for="name" class="form-label"> {{ translate('Name')}} <sup class="text--danger">*</sup></label>
                            <input type="text" class="form-control" name="name" id="name" placeholder="{{translate('Enter Name')}}" required>
                        </div>

                        <div class="mb-3">
                            <label for="email" class="form-label"> {{ translate('Email-Address')}} <sup class="text--danger">*</sup></label>
                            <input type="email" class="form-control" name="email" id="email" placeholder="{{translate('Enter Email-Address')}}" required>
                        </div>

                        <div class="mb-3">
                            <label for="password" class="form-label"> {{ translate('Password')}} <sup class="text--danger">*</sup></label>
                            <input type="password" class="form-control" name="password" id="password" placeholder="{{translate('Enter Password')}}" required>
                        </div>

                        <div class="mb-3">
                            <label for="password" class="form-label"> {{ translate('Confirm Password')}} <sup class="text--danger">*</sup></label>
                            <input type="password" class="form-control" name="password_confirmation" id="email" placeholder="{{translate('Enter Confirm Password')}}" required>
                        </div>

                    </div>
                </div>
                <div class="modal-footer">
                    <div class="d-flex align-items-center gap-3">
                        <button type="button" class="i-btn danger--btn btn--md" data-bs-dismiss="modal"> {{ translate('Cancel')}}</button>
                        <button type="submit" class="i-btn primary--btn btn--md"> {{ translate('Submit')}}</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>


<div class="modal fade" id="creditaddreturn" tabindex="-1" aria-labelledby="addUserCreditLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
				<h5 class="modal-title">{{ translate('Add / Returnted Credit')}}</h5>
                 <button type="button" class="i-btn bg--lite--danger text--danger btn--sm" data-bs-dismiss="modal"> <i class="las la-times"></i></button>
			</div>
            <form action="{{route('admin.user.add.return')}}" method="POST">
                @csrf
                <input type="hidden" name="id" value="">
                <div class="modal-body">
                        <div>
                            <div class="mb-3">
                                <label for="type" class="form-label"> {{ translate('Type')}} <sup class="text--danger">*</sup></label>
                                <select class="form-select" name="type" id="type" required>
                                    <option value="1">{{translate('Add Credit')}}</option>
                                    <option value="2">{{translate('Returned Credit')}}</option>
                                </select>
                            </div>

                            <div class="mb-3">
                                <label for="sms_credit" class="form-label"> {{ translate('SMS Credit')}} </label>
                                <input type="text" class="form-control" name="sms_credit" id="sms_credit" placeholder="{{translate('Enter SMS Credit')}}">
                            </div>

                            <div class="mb-3">
                                <label for="email_credit" class="form-label"> {{ translate('Email Credit')}}</label>
                                <input type="text" class="form-control" name="email_credit" id="email_credit" placeholder="{{translate('Enter Email Credit')}}">
                            </div>

                            <div class="mb-3">
                                <label for="whatsapp_credit" class="form-label"> {{ translate('WhatsApp Credit')}}</label>
                                <input type="text" class="form-control" name="whatsapp_credit" id="whatsapp_credit" placeholder="{{translate('Enter WhatsApp Credit')}}">
                            </div>
                        </div>
                </div>

                <div class="modal-footer">
                    <div class="d-flex align-items-center gap-3">
                        <button type="button" class="i-btn danger--btn btn--md" data-bs-dismiss="modal"> {{ translate('Cancel')}}</button>
                        <button type="submit" class="i-btn primary--btn btn--md"> {{ translate('Submit')}}</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection


@push('script-push')
    <script>
        (function($){
            "use strict";
            $('.createdupdate').on('click', function(){
                var modal = $('#creditaddreturn');
                modal.find('input[name=id]').val($(this).data('id'));
                modal.modal('show');
            });
        })(jQuery);
    </script>
@endpush
