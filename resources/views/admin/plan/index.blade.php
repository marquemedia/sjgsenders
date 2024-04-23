@extends('admin.layouts.app')
@section('panel')
<section class="mt-3">
    <div class="container-fluid p-0">
	    <div class="row">
	 		<div class="col-lg-12">
	            <div class="card">
					<div class="card-header">
						<h4 class="card-title">{{ translate('Membership Plan')}}</h4>
						<a href="{{ route('admin.plan.create') }}" class="i-btn primary--btn btn--md text-white" title="{{ translate('Create New Plan')}}">
							<i class="fa-solid fa-plus"></i> {{translate('Add New')}}
						</a>
					</div>
					<div class="card-body px-0">
						<div class="responsive-table">
							<table>
								<thead>
									<tr>
										<th>{{ translate('Name')}}</th>
										<th>{{ translate('Amount')}}</th>
										<th>{{ translate('SMS Credit')}}</th>
										<th>{{ translate('Email Credit')}}</th>
										<th>{{ translate('Whatsapp Credit')}}</th>
										<th>{{ translate('Duration')}}</th>
										<th>{{ translate('Status')}}</th>
										<th>{{ translate('Recommended Status')}}</th>
										<th>{{ translate('Action')}}</th>
									</tr>
								</thead>
								@forelse($plans as $plan)
									<tr class="@if($loop->even)@endif">
										<td data-label="{{ translate('Name')}}">
											{{$plan->name}}
										</td>

										<td data-label="{{ translate('Amount')}}">
											{{shortAmount($plan->amount)}} {{$general->currency_name}}
										</td>

										<td data-label="{{ translate('SMS Credit')}}">
											{{$plan->sms?->credits}} {{ translate('Sending Credit')}}
										</td>

										 <td data-label="{{ translate('Email Credit')}}">
											{{$plan->email?->credits}} {{ translate('Credit')}}
										</td>

										 <td data-label="{{ translate('Whatsapp Credit')}}">
											{{$plan->whatsapp?->credits?? 'N/A '}} {{ translate('Credit')}}
										</td>

										 <td data-label="{{ translate('Duration')}}">
											{{$plan->duration}} {{ translate('Days')}}
										</td>

										<td data-label="{{ translate('Status')}}">
											@if($plan->status == 1)
												<span class="badge badge--success">{{ translate('Active')}}</span>
											@else
												<span class="badge badge--danger">{{ translate('Inactive')}}</span>
											@endif
										</td>

										<td class="text-center" data-label="{{ translate('Recommended')}}">
											<div class="d-flex justify-content-md-start justify-content-end">
												@if($plan->recommended_status == 1)
													<span class="badge badge--success">{{ translate('ON')}}</span>
												@else

														<label class="switch">
															<input type="checkbox" class="recommended_status" data-id="{{$plan->id}}" value="1" name="recommended_status" id="recommended_status">
															<span class="slider"></span>
														</label>

												@endif
											</div>
										</td>

										<td data-label={{ translate('Action')}}>
											<div class="d-flex align-items-center justify-content-md-start justify-content-end gap-3">
												<a class="i-btn primary--btn btn--sm" href="{{ route('admin.plan.edit', ['id' => $plan->id]) }}"><i class="las la-pen"></i>
												</a>

												<a href="javascript:void(0)" class="i-btn danger--btn btn--sm planDelete"
													data-bs-toggle="modal"
													data-bs-target="#delete"
													data-delete_id="{{$plan->id}}"
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
							</table>
						</div>
						<div class="m-3">
							{{$plans->appends(request()->all())->onEachSide(1)->links()}}
						</div>
					</div>
	            </div>
	        </div>
	    </div>
	</div>
	
</section>


<div class="modal fade" id="createPlan" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">

			<div class="modal-header">
				<h5 class="modal-title">{{ translate('Add New Pricing Plan')}}</h5>

				 <button type="button" class="i-btn bg--lite--danger text--danger btn--sm" data-bs-dismiss="modal"> <i class="las la-times"></i></button>
			</div>

			<form action="{{route('admin.plan.store')}}" method="POST">
				@csrf
	            <div class="modal-body form-wrapper">
					<div class="row gx-4 gy-3">
						<div class="col-lg-12">
							<label for="name" class="form-label">{{ translate('Name')}} <sup class="text--danger">*</sup></label>
							<input type="text" class="form-control" id="name" name="name" placeholder="{{ translate('Enter Name')}}" required>
						</div>

						<div class="col-lg-12">
							<label for="description" class="form-label">{{ translate('Plan Description')}} <sup class="text--danger">*</sup></label>
							<textarea type="text" class="form-control" id="description" name="description" placeholder="{{ translate('Type plan description')}}"></textarea>
						</div>
						<div class="col-lg-6">
							<div class="switch-container">
								
								<label class="form-check-label text-capitalize" for="multi_gateway">{{translate('Allow users to make multiple email gateways')}}</label>
								<label class="switch">
									<input type="checkbox" value="true" name="multi_gateway" id="multi_gateway" class="multiple_gateway">
									<span class="slider"></span>
								</label>
								
							</div>
							<p class="text-danger info-email">({{ translate("If this option is disabled users will only be able to use Admin created gateways") }})</p>
						</div>
						<div class="col-lg-6">
							<div class="switch-container">
								
								<label class="form-check-label text-capitalize" for="multi_gateway">{{translate('Allow users to make multiple sms gateways')}}</label>
								<label class="switch">
									<input type="checkbox" value="true" name="sms_gateway" id="sms_gateway" class="sms_gateway">
									<span class="slider"></span>
								</label>
							</div>
							<p class="text-danger info-sms">({{ translate("If this option is disabled users will only be able to use Admin created gateways. But users can request for a gateway to admin") }})</p>
						</div>

						<div class="email_gateway_options d-none">
							<div class="row">
								<div class="col-lg-6">
									<label for="repeat-time" class="form-label">{{translate('Select Gateways')}} <sup class="text-danger">*</sup></label>
                                    <div class="form-item">
										<label for="email" class="form-label">{{ translate('Single Input') }}</label>
										<select class="form-control email-collect" name="email[]" id="email" multiple></select>
										<div class="form-text">{{ translate('Put single or search from save contact')}}</div>
									</div>
								</div>
                    
								<div class="col-lg-6">
									<label for="email_credit" class="form-label text--primary">{{translate("Total Gateways")}}<sup class="text--danger">*</sup></label>
									<div class="input-group">
										  <input type="text" class="form-control" id="email_credit" title="{{ translate("Choose the amount of gateways users can add") }}" name="email_credit" placeholder="{{ translate('Enter Total gateway')}}" aria-label="Recipient's username" aria-describedby="basic-addon2">
										  <span class="input-group-text" id="basic-addon2">{{ translate('Email')}}</span>
									</div>
								</div>
							</div>
						</div>

						<div class="sms_gateway_options d-none">
							<div class="row">
								<div class="col-lg-6">
									<label for="email_credit" class="form-label text--primary">{{translate("Gateway Types")}}<sup class="text--danger">*</sup></label>
									<div class="input-group">
										  <input type="text" class="form-control" id="email_credit" title="{{translate('Choose which gateways users can add')}}" name="email_credit" placeholder="{{ translate('Select Gateway types')}}" aria-label="Recipient's username" aria-describedby="basic-addon2">
										  <span class="input-group-text" id="basic-addon2">{{ translate('SMS')}}</span>
									</div>
								</div>
								<div class="col-lg-6">
									<label for="email_credit" class="form-label text--primary">{{translate("Total Gateways")}}<sup class="text--danger">*</sup></label>
									<div class="input-group">
										  <input type="text" class="form-control" id="email_credit" title="{{ translate("Choose the amount of gateways users can add") }}" name="email_credit" placeholder="{{ translate('Enter Total gateway')}}" aria-label="Recipient's username" aria-describedby="basic-addon2">
										  <span class="input-group-text" id="basic-addon2">{{ translate('SMS')}}</span>
									</div>
								</div>
							</div>
						</div>
						<div class="col-lg-6">
							<label for="amount" class="form-label">{{ translate('Amount')}} <sup class="text--danger">*</sup></label>
							<div class="input-group">
								<input type="text" class="form-control" id="amount" name="amount" placeholder="{{ translate('Enter Amount')}}" aria-label="Recipient's username" aria-describedby="basic-addon2">
								<span class="input-group-text" id="basic-addon2">{{$general->currency_name}}</span>
							</div>
						</div>

						<div class="col-lg-6">
							<label for="credit" class="form-label">{{ translate('SMS Limit')}} <sup class="text--danger">*</sup></label>
							<div class="input-group">
								<input type="text" class="form-control" id="credit" name="credit" placeholder="{{ translate('Enter Credit')}}" aria-label="Recipient's username" aria-describedby="basic-addon2">
								<span class="input-group-text" id="basic-addon2">{{ translate('Credit')}}</span>
							</div>
						</div>

						<div class="col-lg-6">
							<label for="email_credit" class="form-label">{{ translate('Email Limit')}} <sup class="text--danger">*</sup></label>
							<div class="input-group">
								<input type="text" class="form-control" id="email_credit" name="email_credit" placeholder="{{ translate('Enter Email Credit')}}" aria-label="Recipient's username" aria-describedby="basic-addon2">
								<span class="input-group-text" id="basic-addon2">{{ translate('Credit')}}</span>
							</div>
						</div>

						<div class="col-lg-6">
							<label for="whatsapp_credit" class="form-label">{{ translate('Whatsapp Limit')}} <sup class="text--danger">*</sup></label>
							<div class="input-group">
								<input type="text" class="form-control" id="whatsapp_credit" name="whatsapp_credit" placeholder="{{ translate('Enter Whatsapp Credit')}}" aria-label="Recipient's username" aria-describedby="basic-addon2">
								<span class="input-group-text" id="basic-addon2">{{ translate('Credit')}}</span>
							</div>
						</div>

						<div class="col-lg-6">
							<label for="duration" class="form-label">{{ translate('Duration')}} <sup class="text--danger">*</sup></label>
							<div class="input-group">
								<input type="text" class="form-control" id="duration" name="duration" placeholder="{{ translate('Enter Duration')}}" aria-label="Recipient's username" aria-describedby="basic-addon2">
								<span class="input-group-text" id="basic-addon2">{{ translate('Days')}}</span>
							</div>
						</div>

						<div class="col-lg-6">
							<label for="status" class="form-label">{{ translate('Status')}} <sup class="text--danger">*</sup></label>
							<select class="form-select" name="status" id="status" required>
								<option value="1">{{ translate('Active')}}</option>
								<option value="2">{{ translate('Inactive')}}</option>
							</select>
						</div>

					
					</div>
	            </div>

				<div class="modal-footer">
					<div class="d-flex align-items-center gap-3">
						<button type="button" class="i-btn danger--btn btn--md" data-bs-dismiss="modal">{{ translate('Cancel')}}</button>
						<button type="submit" class="i-btn primary--btn btn--md">{{ translate('Submit')}}</button>
					</div>
				</div>
	        </form>
        </div>
    </div>
</div>


<div class="modal fade" id="update-brand" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title">{{ translate('Update Pricing Plan')}}</h5>
				 <button type="button" class="i-btn bg--lite--danger text--danger btn--sm" data-bs-dismiss="modal"> <i class="las la-times"></i></button>
			</div>
			<form action="{{route('admin.plan.update')}}" method="POST">
				@csrf
				<input type="hidden" name="id">
	            <div class="modal-body">
		                <div class="row gx-4 gy-3">
							<div class="col-lg-12">
								<label for="name" class="form-label">{{ translate('Name')}} <sup class="text--danger">*</sup></label>
								<input type="text" class="form-control" id="name" name="name" placeholder="{{ translate('Enter Name')}}" required>
							</div>
							<div class="col-lg-12">
								<label for="description" class="form-label">{{ translate('Plan Description')}} <sup class="text--danger">*</sup></label>
								<textarea type="text" class="form-control" id="description" name="description" placeholder="{{ translate('Type plan description')}}" required></textarea>
							</div>
							<div class="col-lg-6">
								<label for="amount" class="form-label">{{ translate('Amount')}} <sup class="text--danger">*</sup></label>
								<div class="input-group">
								  	<input type="text" class="form-control" id="amount" name="amount" placeholder="{{ translate('Enter Amount')}}" aria-label="Recipient's username" aria-describedby="basic-addon2">
								  	<span class="input-group-text" id="basic-addon2">{{$general->currency_name}}</span>
								</div>
							</div>

							<div class="col-lg-6">
								<label for="credit" class="form-label">{{ translate('Credit')}} <sup class="text--danger">*</sup></label>
								<div class="input-group">
								  	<input type="text" class="form-control" id="credit" name="credit" placeholder="{{ translate('Enter Credit')}}" aria-label="Recipient's username" aria-describedby="basic-addon2">
								  	<span class="input-group-text" id="basic-addon2">{{ translate('SMS')}}</span>
								</div>
							</div>

							<div class="col-lg-6">
								<label for="email_credit" class="form-label">{{ translate('Email Credit')}} <sup class="text--danger">*</sup></label>
								<div class="input-group">
								  	<input type="text" class="form-control" id="email_credit" name="email_credit" placeholder="{{ translate('Enter Email Credit')}}" aria-label="Recipient's username" aria-describedby="basic-addon2">
								  	<span class="input-group-text" id="basic-addon2">{{ translate('Email')}}</span>
								</div>
							</div>

							<div class="col-lg-6">
								<label for="whatsapp_credit" class="form-label">{{ translate('Whatsapp Credit')}} <sup class="text--danger">*</sup></label>
								<div class="input-group">
								  	<input type="text" class="form-control" id="whatsapp_credit" name="whatsapp_credit" placeholder="{{ translate('Enter Whatsapp Credit')}}" aria-label="Recipient's username" aria-describedby="basic-addon2">
								  	<span class="input-group-text" id="basic-addon2">{{ translate('Whatsapp')}}</span>
								</div>
							</div>

							<div class="col-lg-6">
								<label for="duration" class="form-label">{{ translate('Duration')}} <sup class="text--danger">*</sup></label>
								<div class="input-group">
								  	<input type="text" class="form-control " id="duration" name="duration" placeholder="{{ translate('Enter Duration')}}" aria-label="Recipient's username" aria-describedby="basic-addon2">
								  	<span class="input-group-text" id="basic-addon2">{{ translate('Days')}}</span>
								</div>
							</div>

							<div class="col-lg-6">
								<label for="status" class="form-label">{{ translate('Status')}} <sup class="text--danger">*</sup></label>
								<select class="form-select" name="status" id="status" required>
									<option value="1">{{ translate('Active')}}</option>
									<option value="2">{{ translate('Inactive')}}</option>
								</select>
							</div>

						</div>
	            </div>

				<div class="modal-footer">
					<div class="d-flex align-items-center gap-3">
						<button type="button" class="i-btn danger--btn btn--md" data-bs-dismiss="modal">{{ translate('Cancel')}}</button>
						<button type="submit" class="i-btn primary--btn btn--md">{{ translate('Submit')}}</button>
					</div>
				</div>
	        </form>
        </div>
    </div>
</div>

<div class="modal fade" id="delete" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
        	<form action="{{route('admin.plan.delete')}}" method="POST">
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

				<div class="modal-footer justify-content-center">
					<div class="d-flex align-items-center justify-content-center gap-3">
						<button type="button" class="i-btn primary--btn btn--md" data-bs-dismiss="modal">{{ translate('Cancel')}}</button>
						<button type="submit" class="i-btn danger--btn btn--md">{{ translate('Delete')}}</button>
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

		$(document).ready(function() {
     
			$(".multiple_gateway").change(function() {

				$(this).is(":checked") ? $(".email_gateway_options").removeClass("d-none").addClass("d-block") : $(".email_gateway_options").removeClass("d-block").addClass("d-none");
				$(this).is(":checked") ? $(".info-email").removeClass("d-block").addClass("d-none") : $(".info-email").removeClass("d-none").addClass("d-block");
			});
			
			$(".sms_gateway").change(function() {
				
				$(this).is(":checked") ? $(".sms_gateway_options").removeClass("d-none").addClass("d-block") : $(".sms_gateway_options").removeClass("d-block").addClass("d-none");
				$(this).is(":checked") ? $(".info-sms").removeClass("d-block").addClass("d-none") : $(".info-sms").removeClass("d-none").addClass("d-block");
			});
		});
		$('.brand').on('click', function(){
            const modal = $('#update-brand');
            modal.find('input[name=id]').val($(this).data('id'));
			modal.find('input[name=name]').val($(this).data('name'));
			modal.find('textarea[name=description]').val($(this).data('description'));
			modal.find('input[name=amount]').val($(this).data('amount'));
			modal.find('input[name=credit]').val($(this).data('credit'));
			modal.find('input[name=email_credit]').val($(this).data('email_credit'));
			modal.find('input[name=whatsapp_credit]').val($(this).data('whatsapp_credit'));
			modal.find('input[name=duration]').val($(this).data('duration'));
			modal.find('select[name=status]').val($(this).data('status'));
			modal.find('input[name=recommended_status]').val($(this).data('recommended_status'));
            const recommendedstatus = $(this).data('recommended_status');
            if(recommendedstatus === 1){
				modal.find('input[name=recommended_status]').attr('checked', true);
			}else{
				modal.find('input[name=recommended_status]').attr('checked', false);
			}
			modal.modal('show');
		});

		$('.planDelete').on('click', function(){
            const modal = $('#delete');
            modal.find('input[name=id]').val($(this).data('delete_id'));
			modal.modal('show');
		});

		$('.recommended_status').on('change', function(){
            const status = $(this).val();
            const id = $(this).attr('data-id');
            $.ajax({
                method:'get',
                url: "{{ route('admin.plan.status') }}",
                headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                data:{
                  'status' :status,
                  'id' :id
                },
                dataType: 'json'
            }).then(response => {
                if(response.status){
					notify('success', 'Recommended Status Updated Successfully');
					window.location.reload()
                }

            })
		});
	})(jQuery);
</script>
@endpush
