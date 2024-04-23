@extends('admin.layouts.app')
@section('panel')
<section>
    <div class="container-fluid p-0">
		<div class="card ">
			<div class="card-header">
				<h4 class="card-title">{{ translate('Message List')}}</h4>
			</div>

			<div class="card-filter">
				<form action="{{route('admin.sms.search',$scope ?? str_replace('admin.sms.', '', request()->route()->getName()))}}" method="GET">
					<div class="filter-form">
						<div class="filter-item">
							<select name="status" class="form-select">
								<option value="all" @if(@$status == "all") selected @endif>{{translate('All')}}</option>
								<option value="pending" @if(@$status == "pending") selected @endif>{{translate('Pending')}}</option>
								<option value="schedule" @if(@$status == "schedule") selected @endif>{{translate('Schedule')}}</option>
								<option value="fail" @if(@$status == "fail") selected @endif>{{translate('Fail')}}</option>
								<option value="delivered" @if(@$status == "delivered") selected @endif>{{translate('Delivered')}}</option>
								<option value="processing" @if(@$status == "processing") selected @endif>{{translate('Processing')}}</option>
							</select>
						</div>

						<div class="filter-item">
							<input type="text" autocomplete="off" name="search" placeholder="{{ translate('Search with User, Email or To Recipient number')}}" class="form-control" id="search" value="{{@$search}}">
						</div>

						<div class="filter-item">
							<input type="text" class="form-control datepicker-here" name="date" value="{{@$searchDate}}" data-range="true" data-multiple-dates-separator=" - " data-language="en" data-position="bottom right" autocomplete="off" placeholder="{{ translate('From Date-To Date')}}" id="date">
						</div>

						<div class="filter-action">
							<button class="i-btn info--btn btn--md" type="submit">
								<i class="fas fa-search"></i> {{ translate('Search')}}
							</button>
							<button class="i-btn danger--btn btn--md">
								<a class="text-white" href="{{ route('admin.sms.index') }}">
									<i class="las la-sync"></i>  {{translate('reset')}}
								</a>
							</button>

							<div class="statusUpdateBtn d-none">
								<a class="i-btn success--btn btn--md statusupdate"
									data-bs-toggle="tooltip"
									data-bs-placement="top" title="Status Update"
									data-bs-toggle="tooltip"
									data-bs-target="#smsstatusupdate">
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
								<th>{{ translate('User')}}</th>
								<th>{{ translate('Gateway')}}</th>
								<th>{{ translate('To')}}</th>
								<th>{{ translate('Date & Time')}}</th>
								<th>{{ translate('Status')}}</th>
								<th class="text-center">{{ translate('Action')}}</th>
							</tr>
						</thead>

                        <tbody>
                            @forelse($smslogs as $smsLog)
                                <tr class="@if($loop->even)@endif">
                                    <td class="lh-1" data-label="{{ translate('Id')}}">
                                        <input class="form-check-input mt-0 me-2" type="checkbox" name="smslogid" value="{{$smsLog->id}}" aria-label="Checkbox for following text input">
                                        {{$loop->iteration}}
                                    </td>

                                    <td data-label="{{ translate('User')}}">
                                        @if($smsLog->user_id)
                                            <a href="{{route('admin.user.details', $smsLog->user_id)}}" class="fw-bold text-dark">{{$smsLog->user?->name}}</a>
                                        @else
                                            <span>{{ translate('Admin')}}</span>
                                        @endif
                                    </td>

                                    <td data-label="{{ translate('Gateway')}}">
                                        @if($smsLog->api_gateway_id)
                                            <p class="mb-1">{{ translate('Api')}}</p>

                                            <span class="bg--lite--info text--info rounded px-2 py-1 d-inline-block fs--12">
                                               {{ucfirst($smsLog->smsGateway?->name)}}
                                            </span>
                                        @else
                                            <p class="mb-1">
                                                {{ translate('Android')}}
                                            </p>
                                            @if(@$smsLog->androidGateway->sim_number!="")
                                                <span class="bg--lite--info text--info rounded px-2 py-1 d-inline-block fs--12">
                                                    {{"Sim Number: ".@$smsLog->androidGateway->sim_number}}
                                                </span>
                                            @endif
                                        @endif
                                    </td>

                                    <td data-label="{{ translate('To')}}">
                                        {{$smsLog->to}}

                                        <p>
                                            @php
                                                $getMessageCountWord = $smsLog->sms_type==1?$general->sms_word_text_count:$general->sms_word_unicode_count;
                                                $messages = str_split($smsLog->message,$getMessageCountWord);
                                                $totalMessage = count($messages);
                                            @endphp

                                            <span class="badge badge--success">
                                             <i class="las la-coins"></i>
                                             {{$totalMessage}}
                                             {{ translate('Credit')}}
                                            </span>
                                        </p>
                                    </td>

                                    <td data-label="{{ translate('Date & Time')}}">
                                        <p class="mb-1">
                                            {{translate("Initiated: ")}}<span class="text-muted">{{getDateTime($smsLog->created_at)}}</span>
                                        </p>

                                        @if(!is_null($smsLog->initiated_time))
                                            <p class="mb-1">
                                                {{translate("Schedule: ")}}<span class="text-muted">{{getDateTime($smsLog->initiated_time)}}</span>
                                            </p>
                                        @else
                                            <p>{{translate("Schedule: ")}}{{translate('N/A')}}</p>
                                        @endif
                                        @if(!is_null($smsLog?->delivered_at))
                                            <p class="mb-1">
                                                {{translate("Delivered At: ")}}<span class="text-muted">{{getDateTime($smsLog?->delivered_at)}}</span>
                                            </p>
                                        @else
                                            <p>{{translate("Delivered At: ")}}{{translate('N/A')}}</p>
                                        @endif
                                    </td>

                                    <td data-label="{{ translate('Status')}}">
                                        <div class="d-flex align-items-center gap-2">
                                            @if($smsLog->status == 1)
                                                <span class="badge badge--primary">{{ translate('Pending')}}</span>
                                            @elseif($smsLog->status == 2)
                                                <span class="badge badge--info">{{ translate('Schedule')}}</span>
                                            @elseif($smsLog->status == 3)
                                                <span class="badge badge--danger">{{ translate('Fail')}}</span>
                                            @elseif($smsLog->status == 4)
                                                <span class="badge badge--success">{{ translate('Delivered')}}</span>
                                            @elseif($smsLog->status == 5)
                                                <span class="badge badge--primary">{{ translate('Processing')}}</span>
                                            @endif
                                            <a class="s_btn--coral text--light statusupdate"
                                                data-id="{{$smsLog->id}}"
                                                data-android-sim-id="{{$smsLog->androidGateway?->id}}"
                                                data-bs-placement="top" title="Status Update"
                                                data-bs-toggle="modal"
                                                data-bs-target="#smsstatusupdate"
                                                ><i class="las la-info-circle text-light fs-10"></i>
                                            </a>
                                        </div>
                                    </td>

                                    <td data-label={{ translate('Action')}}  class="text-center">
                                        <div class="d-flex align-items-center justify-content-md-center justify-content-end gap-3">
                                            <a class="i-btn primary--btn btn--sm details"
                                            data-message="{{$smsLog->message}}"
                                            data-response_gateway="{{$smsLog->response_gateway}}"
                                            data-bs-placement="top" title="Details"
                                            data-bs-toggle="modal"
                                            data-bs-target="#smsdetails"
                                                ><i class="las la-desktop"></i>
                                            </a>

                                            <a href="javascript:void(0)" class="i-btn danger--btn btn--sm smsdelete"
                                                data-bs-toggle="modal"
                                                data-bs-target="#delete"
                                                data-delete_id="{{$smsLog->id}}"
                                                ><i class="las la-trash"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td class="text-muted text-center" colspan="100%">{{ translate('No Data Found')}}</td>
                                </tr>
                            @endforelse
                        </tbody>
					</table>
				</div>

				<div class="m-3">
					{{$smslogs->appends(request()->all())->onEachSide(1)->links()}}
				</div>
			</div>
		</div>
	</div>
</section>


<div class="modal fade" id="smsstatusupdate" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog nafiz">
        <div class="modal-content">
            <form action="{{route('admin.sms.status.update')}}" method="POST">
                @csrf
                <input type="hidden" name="id">
                <input type="hidden" name="smslogid">
                <div class="modal-body">
                    <div class="card">
                        <div class="card-header bg--lite--violet">
                            <div class="card-title text-center text--light">{{ translate('SMS Status Update')}}</div>
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <label for="status" class="form-label">{{translate('Status')}}<sup class="text--danger">*</sup></label>
                                <select class="form-select" name="status" id="status" required>
                                    <option value="" selected="" disabled="">{{ translate('Select Status')}}</option>
                                    <option value="1">{{ translate('Pending')}}</option>
                                    <option value="4">{{ translate('Success')}}</option>
                                    <option value="3">{{ translate('Fail')}}</option>
                                </select>
                            </div>
							<div class="android-sim-select d-none">

								<input type="text" name="android_sim_update" value="true" hidden>
								<div class="android-gateway">
									<label for="android_gateways_id" class="form-label">{{translate('Android Gateway')}} </label>
									<select class="form-select repeat-scale android_gateways" name="android_gateways_id" id="android_gateways_id">
										<option selected value="auto">{{ translate('Automatic') }}</option>
										@foreach($android_gateways as $gateway)
											<option value="{{$gateway->id}}">{{strtoupper($gateway->name)}}</option>
										@endforeach
									</select>
								</div>
								<div class="android-sim mt-3 d-none">
									<label for="sim_id" class="form-label">{{translate('Choose A SIM Number')}} <sup class="text-danger">*</sup></label>
									<select class="form-select sim-list " name="sim_id" id="sim_id"></select>
								</div>
							</div>
                        </div>
                    </div>
                </div>

                <div class="modal_button2 modal-footer">
					<div class="d-flex align-items-center justify-content-center gap-3">
						<button type="button" class="i-btn primary--btn btn--md" data-bs-dismiss="modal">{{ translate('Cancel')}}</button>
						<button type="submit" class="i-btn success--btn btn--md">{{ translate('Submit')}}</button>
					</div>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="smsdetails" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title">{{ translate('Message')}}</h5>
				 <button type="button" class="i-btn bg--lite--danger text--danger btn--sm" data-bs-dismiss="modal"> <i class="las la-times"></i></button>
			</div>
            <div class="modal-body">
            	<div class="card">
        			<div class="card-body mb-3">
        				<p id="message--text"></p>
        			</div>
        		</div>
        	</div>

            <div class="modal-footer">
                <button type="button" class="i-btn danger--btn btn--md" data-bs-dismiss="modal">{{ translate('Cancel')}}</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="delete" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
        	<form action="{{route('admin.sms.delete')}}" method="POST">
        		@csrf
        		<input type="hidden" name="id" value="">
	            <div class="modal_body2">
	                <div class="modal_icon2">
	                    <i class="las la-trash"></i>
	                </div>
	                <div class="modal_text2 mt-4">
	                    <h5>{{ translate('Are you sure to delete this plan')}}</h5>
	                </div>
	            </div>

				<div class="modal_button2 modal-footer">
					<div class="d-flex align-items-center justify-content-center gap-3">
						<button type="button" class="i-btn danger--btn btn--md" data-bs-dismiss="modal">{{ translate('Cancel')}}</button>
						<button type="submit" class="i-btn primary--btn btn--md">{{ translate('Delete')}}</button>
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

				$(".android-sim-select").addClass("d-none");
				$('#status').prop("selectedIndex", 0);
                var modal = $('#smsstatusupdate');
                modal.find('input[name=id]').val($(this).data('id'));
				var sim_id = $(this).data('android-sim-id');

				$("#status").on("change", function() {

					if(!isEmpty(sim_id)) {

						if($(this).val() == 1) {

							$(".android-sim-select").removeClass("d-none");
							$('.android-sim-select').find('.android_gateways').prop("selectedIndex", 0);
						}

					} else {

						$('.android-sim-select').find('.sim-list').prop("selectedIndex", -1);
						$('.android-sim-select').find('.android_gateways').prop("selectedIndex", -1);
						$(".android-sim-select").addClass("d-none");
					}
				})



				function isEmpty(value) {
					return (value == null || (typeof value === "string" && value.trim().length === 0));
				}

                modal.modal('show');
            });

            $('.details').on('click', function(){
                var modal = $('#smsdetails');
                var message = $(this).data('message');
                var response_gateway = $(this).data('response_gateway');
                $("#message--text").html(`${message} :: <span class="text-danger"> ${response_gateway} </span>`);
                modal.modal('show');
            });

            $('.smsdelete').on('click', function(){
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
                $("input:checkbox[name=smslogid]:checked").each(function(){
                    newArray.push($(this).val());
                });
                modal.find('input[name=smslogid]').val(newArray.join(','));
                modal.modal('show');
            });



			$('.android_gateways').change(function () {

                var selectedType = $(this).val();
                if(selectedType != "auto") {
                    $('.android-sim').find('sim-list').prop("selectedIndex", -1);
                    $('.android-sim').removeClass('d-none');
                    $('.android-sim').addClass('d-block');

                    if(selectedType == ''){
                        $('.android-sim').addClass('d-none');
                    }
                    $.ajax({
                        type: 'GET',
                        url: "{{route('admin.gateway.select2', 'android')}}",
                        data:{
                            'type' : selectedType,
                        },
                        dataType: 'json',
                        success: function (data) {

                            $('.sim-list').empty();

                            $.each(data, function (key, value) {
                                var select   = $('<option value="' + value.id + '">' + value.sim_number + '</option>');
                                $('.sim-list').append(select);
                            });
                        },
                        error: function (xhr, status, error) {

                            console.log(error);
                        }
                    });

                } else {

                    $('.android-sim').find('sim-list').prop("selectedIndex", -1);
                    $('.android-sim').addClass('d-none');
                }

            });


        })(jQuery);
    </script>
@endpush
