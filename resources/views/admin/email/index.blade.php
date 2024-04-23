@extends('admin.layouts.app')
@section('panel')
    <section>
        <div class="card mb-4">
            <div class="card-header">
                <h4 class="card-title">{{translate('Email Logs')}}</h4>
            </div>

            <div class="card-filter">
                <form action="{{route('admin.email.search')}}" method="GET">
                    <div class="filter-form">
                        <div class="filter-item">
                            <select name="status" class="form-control">
                                <option value="all" @if(@$status == "all") selected @endif>{{translate('All')}}</option>
                                <option value="pending" @if(@$status == "pending") selected @endif>{{translate('Pending')}}</option>
                                <option value="schedule" @if(@$status == "schedule") selected @endif>{{translate('Schedule')}}</option>
                                <option value="fail" @if(@$status == "fail") selected @endif>{{translate('Fail')}}</option>
                                <option value="delivered" @if(@$status == "delivered") selected @endif>{{translate('Delivered')}}</option>
                            </select>
                        </div>

                        <div class="filter-item">
                            <input type="text" autocomplete="off" name="search" placeholder="{{translate('Search with User, Email or To Recipient Number')}}" class="form-control" id="search" value="{{@$search}}">
                        </div>

                        <div class="filter-item">
                            <input type="text" class="form-control datepicker-here" name="date" value="{{@$searchDate}}" data-range="true" data-multiple-dates-separator=" - " data-language="en" data-position="bottom right" autocomplete="off" placeholder="{{translate('From Date-To Date')}}" id="date">
                        </div>

                        <div class="filter-action">
                            <button class="i-btn info--btn btn--md" type="submit">
                                <i class="fas fa-search"></i> {{ translate('Search')}}
                            </button>
                            <button class="i-btn danger--btn btn--md">
                                <a class="text-white" href="{{ route('admin.email.index') }}">
                                    <i class="las la-sync"></i>  {{translate('reset')}}
                                </a>
                            </button>

                            <div class="statusUpdateBtn d-none">
                                <a class="i-btn success--btn btn--md statusupdate"
                                   data-bs-toggle="tooltip"
                                   data-bs-placement="top" title="Status Update"
                                   data-bs-toggle="modal"
                                   data-bs-target="#smsstatusupdate"
                                   type="submit">
                                    <i class="fas fa-gear"></i> {{translate('Action')}}
                                </a>
                            </div>
                        </div>


                    </div>
                </form>
            </div>

            <div class="card-body px-0">
                <div class="responsive-table">
                    <table>
                        <thead>
                            <tr>
                                <th>
                                    <div class="d-flex align-items-center">
                                        <input class="form-check-input mt-0 me-2 checkAll"
                                           type="checkbox"
                                           value=""
                                           aria-label="Checkbox for following text input"> <span>#</span>
                                    </div>
                                </th>

                                <th> {{ translate('User')}}</th>
                                <th> {{ translate('Gateway')}}</th>
                                <th> {{ translate('To')}}</th>
                                <th> {{ translate('Subject')}}</th>
                                <th> {{ translate('Date & Time')}}</th>
                                <th> {{ translate('Status')}}</th>
                                <th class="text-center"> {{ translate('Action')}}</th>
                            </tr>
                        </thead>
                        @forelse($emailLogs as $emailLog)
                            <tr class="@if($loop->even)@endif">
                                <td class="lh-1" data-label="{{ translate('Id')}}">
                                    <input class="form-check-input mt-0 me-2" type="checkbox" name="emaillog" value="{{$emailLog->id}}" aria-label="Checkbox for following text input">
                                    {{$loop->iteration}}
                                </td>

                                 <td data-label=" {{ translate('User')}}">
                                    @if($emailLog->user_id)
                                        <a href="{{route('admin.user.details', $emailLog->user_id)}}" class="fw-bold text-dark">{{$emailLog->user?->name}}</a>
                                    @else
                                        <span> {{translate('Admin')}}</span>
                                    @endif
                                </td>

                                <td data-label=" {{ translate('Gateway')}}">
                                    <span class="bg--lite--info text--info rounded px-2 py-1 d-inline-block fs--12">
                                        {{ucfirst(@$emailLog->sender->name)}}
                                    </span>
                                </td>

                                <td data-label=" {{ translate('To')}}">
                                    {{$emailLog->to}}
                                </td>

                                <td data-label=" {{ translate('Subject')}}">
                                    {{$emailLog->subject}}
                                </td>

                                <td data-label="{{ translate('Date & Time')}}">
                                    <p class="mb-1">
                                       {{translate("Initiated:")}}
                                        <span class="text-muted">{{getDateTime($emailLog->created_at)}}
                                        </span>
                                    </p>

                                    @if(!is_null($emailLog->initiated_time))
                                        <p class="mb-1">
                                            {{translate("Schedule:")}}
                                            <span class="text-muted">
                                                {{getDateTime($emailLog->initiated_time)}}
                                            </span>
                                        </p>
                                    @else
                                        <p>{{transalte("Schedule: ")}}{{translate('N/A')}}</p>
                                    @endif
                                    @if(!is_null($emailLog->delivered_at))
                                        <p class="mb-1">
                                            {{translate("Delivered At:")}}
                                            <span class="text-muted">
                                                {{getDateTime($emailLog->delivered_at)}}
                                            </span>
                                        </p>
                                    @else
                                        <p>{{translate("Delivered At: ")}}{{translate('N/A')}}</p>
                                    @endif
                                </td>
                                <td data-label=" {{ translate('Status')}}">
                                    <div class="d-flex align-items-center gap-2">
                                        @if($emailLog->status == 1)
                                            <span class="badge badge--primary"> {{ translate('Pending ')}}</span>
                                        @elseif($emailLog->status == 2)
                                            <span class="badge badge--info"> {{ translate('Schedule')}}</span>
                                        @elseif($emailLog->status == 3)
                                            <span class="badge badge--danger"> {{ translate('Fail')}}</span>
                                        @else
                                            <span class="badge badge--success"> {{ translate('Delivered')}}</span>
                                        @endif

                                        <a class="s_btn--coral text--light statusupdate"
                                            data-id="{{$emailLog->id}}"
                                            data-bs-toggle="tooltip"
                                            data-bs-placement="top" title="Status Update"
                                            data-bs-target="#smsstatusupdate"
                                            ><i class="las la-info-circle"></i>
                                        </a>
                                    </div>
                                </td>

                                <td data-label=" {{ translate('Action')}}">
                                    <div class="d-flex align-items-center justify-content-md-center justify-content-end gap-3">
                                        @if($emailLog->status == 1 ||$emailLog->status == 3)
                                            <a href="{{route('admin.email.single.mail.send', $emailLog->id)}}" class="i-btn warning--btn btn--sm" data-bs-toggle="tooltip" data-bs-placement="top" title="Resend" ><i class="las la-paper-plane"></i></a>
                                        @endif
                                        <a class="i-btn primary--btn btn--sm" href="{{route('admin.email.view',$emailLog->id)}}" target="_blank"
                                            ><i class="las la-desktop"></i></a>

                                        <a href="javascript:void(0)" class="i-btn danger--btn btn--sm emaildelete"
                                            data-bs-toggle="modal"
                                            data-bs-target="#delete"
                                            data-delete_id="{{$emailLog->id}}"
                                            ><i class="las la-trash"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td class="text-muted text-center" colspan="100%"> {{ translate('No Data Found')}}</td>
                            </tr>
                        @endforelse
                    </table>
                </div>
                <div class="m-3">
                    {{$emailLogs->appends(request()->all())->onEachSide(1)->links()}}
                </div>
            </div>
        </div>
    </section>

    <div class="modal fade" id="smsstatusupdate" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="{{route('admin.email.status.update')}}" method="POST">
                    @csrf
                    <input type="hidden" name="id">
                    <input type="hidden" name="email_log_id">
                    <div class="modal-body">
                        <div class="card">
                            <div class="card-header bg--lite--violet">
                                <div class="card-title text-center text--light"> {{ translate('Email Status Update')}}</div>
                            </div>
                            <div class="card-body">
                                <div class="mb-3">
                                    <label for="status" class="form-label"> {{ translate('Status')}} <sup class="text--danger">*</sup></label>
                                    <select class="form-control" name="status" id="status" required>
                                        <option value="" selected disabled> {{ translate('Select Status')}}</option>
                                        <option value="1"> {{ translate('Pending')}}</option>
                                        <option value="4"> {{ translate('Success')}}</option>
                                        <option value="3"> {{ translate('Fail')}}</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="modal_button2 modal-footer">
                        <div class="d-flex align-items-center justify-content-center gap-3">
                            <button type="button" class="i-btn primary--btn btn--md" data-bs-dismiss="modal"> {{ translate('Cancel')}}</button>
                            <button type="submit" class="i-btn success--btn btn--md"> {{ translate('Submit')}}</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>


    <div class="modal fade" id="smsdetails" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-body">
                    <div class="card">
                        <div class="card-header bg--lite--violet">
                            <div class="card-title text-center text--light"> {{ translate('Message')}}</div>
                        </div>
                        <div class="card-body mb-3">
                            <p id="message--text"></p>
                        </div>
                    </div>
                </div>

                <div class="modal_button2 modal-footer">
                    <div class="d-flex align-items-center justify-content-center gap-3">
                        <button type="button" class="i-btn primary--btn btn--md" data-bs-dismiss="modal"> {{ translate('Cancel')}}</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="delete" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="{{route('admin.email.delete')}}" method="POST">
                    @csrf
                    <input type="hidden" name="id" value="">
                    <div class="modal_body2">
                        <div class="modal_icon2">
                            <i class="las la-trash"></i>
                        </div>
                        <div class="modal_text2 mt-3">
                            <h6> {{ translate('Are you sure to delete this email from log')}}</h6>
                        </div>
                    </div>
                    <div class="modal_button2 modal-footer">
                        <div class="d-flex align-items-center justify-content-center gap-3">
                            <button type="button" class="i-btn primary--btn btn--md" data-bs-dismiss="modal"> {{ translate('Cancel')}}</button>
                            <button type="submit" class="i-btn danger--btn btn--md"> {{ translate('Delete')}}</button>
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
		$('.statusupdate').on('click', function(){
			var modal = $('#smsstatusupdate');
			modal.find('input[name=id]').val($(this).data('id'));
			modal.modal('show');
		});

		$('.emaildelete').on('click', function(){
			var modal = $('#delete');
			modal.find('input[name=id]').val($(this).data('delete_id'));
			modal.modal('show');
		});

        $('.checkAll').click(function(){
            $('input:checkbox').not(this).prop('checked', this.checked);
        });

        $('.statusupdate').on('click', function(){
            var modal = $('#smsstatusupdate');
            var newArray = [];
            $("input:checkbox[name=emaillog]:checked").each(function(){
                newArray.push($(this).val());
            });
            modal.find('input[name=email_log_id]').val(newArray.join(','));
            modal.modal('show');
        });
	})(jQuery);
</script>
@endpush
