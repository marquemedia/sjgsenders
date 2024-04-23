@extends('admin.layouts.app')
@section('panel')
<section class="mt-3 rounded_box">
	<div class="container-fluid p-0 mb-3 pb-2">
		<div class="row d-flex rounded">
			@php
				$col =  $paymentLog->user_data != null ? 4 :12;
			@endphp
			
			<div class="col-lg-{{$col}}">
            	<div class="card shadow border-0 p-3 bg-body rounded">
	                <div class="card-body">
	                    <h6 class="mb-3">{{ translate('Customer information')}}</h6>
	                    <ul class="list-group">
	                        <li class="list-group-item d-flex justify-content-between align-items-center">
                                {{ translate('Seller')}}
	                            <span class="font-weight-bold">{{$paymentLog->user?->name}}</span>
	                        </li>
	                        <li class="list-group-item d-flex justify-content-between align-items-center">
	                            {{ translate('Method')}}
	                            <span class="font-weight-bold">{{$paymentLog->paymentGateway->name}}</span>
	                        </li>
	                         <li class="list-group-item d-flex justify-content-between align-items-center">
	                            {{ translate('Time')}}
	                            <span class="font-weight-bold">{{getDateTime($paymentLog->created_at)}}</span>
	                        </li>

	                         <li class="list-group-item d-flex justify-content-between align-items-center">
                                {{ translate('Amount')}}
	                            <span class="font-weight-bold">{{shortAmount($paymentLog->amount)}} {{$general->currency_name}}</span>
	                        </li>
	                         <li class="list-group-item d-flex justify-content-between align-items-center">
	                            {{ translate('Charge')}}
	                            <span class="font-weight-bold">{{shortAmount($paymentLog->charge)}} {{$general->currency_name}}</span>
	                        </li>

	                        <li class="list-group-item d-flex justify-content-between align-items-center">
                                {{ translate('Receivable')}}
	                            <span class="font-weight-bold">{{shortAmount($paymentLog->final_amount)}} {{$paymentLog->paymentGateway->currency->name}}</span>
	                        </li>

	                        <li class="list-group-item d-flex justify-content-between align-items-center">
	                            {{ translate('Status')}}
	                            <span class="font-weight-bold">
                            		@if($paymentLog->status == 1)
			                    		<span class="badge badge--primary">{{ translate('Pending')}}</span>
			                    	@elseif($paymentLog->status == 2)
			                    		<span class="badge badge--success">{{ translate('Received')}}</span>
			                    	@elseif($paymentLog->status == 3)
			                    		<span class="badge badge--danger">{{ translate('Rejected')}}</span>
			                    	@endif
			                    </span>
	                        </li>
	                    </ul>
	                </div>
	            </div>
	        </div>
			@if($paymentLog->user_data != null)
				<div class="col-lg-8 col-md-8 mb-30">
					<div class="card b-radius--10 overflow-hidden box-shadow1">
						<div class="card-body">
						
							<h6 class="card-title border-bottom pb-2">{{ translate('User Data')}}</h6>
							@foreach($paymentLog->user_data as $k => $val)
							
								@if($val->field_type == 'file')
									<div class="row mt-4">
										<div class="col-md-6">
											<h6>{{labelName($k)}}</h6>
											<img src="{{showImage('assets/file/payment/data/'.$val->field_name)}}" class="mt-1" alt="{{ translate('Image')}}">
										</div>
									</div>
								@else
									<div class="row mt-4">
										<div class="col-md-12">
											<h6>{{labelName($k)}}</h6>
											<p>{{$val->field_name}}</p>
										</div>
									</div>
								@endif
							@endforeach

			
							@if($paymentLog->status == 1)
								<div class="row mt-4">
									<div class="col-md-12">
										<div class="d-flex align-items-center gap-3">
											<button class="i-btn success--btn btn--md text-light ml-1 approveBtn" data-bs-toggle="modal" data-bs-target="#approveModal">
												<i class="las la-check-double"></i> {{ translate('Approve')}}
											</button>

											<button class="i-btn danger--btn btn--md text-light ml-1 rejectBtn" data-bs-toggle="modal" data-bs-target="#rejectModal">
												<i class="las la-times-circle"></i> {{ translate('Reject')}}
											</button>
										</div>
									</div>
								</div>
							@else
								<h6 class="card-title border-bottom pb-2 mt-4">{{ translate('Feedback')}}</h6>
								<div class="row">
									<div class="col-md-12">
										<p>{{$paymentLog->feedback}}</p>
									</div>
								</div>
							@endif
						</div>
					</div>
				</div>
			@endif
	    </div>
	</div>
</section>


<div class="modal fade" id="approveModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<form action="{{route('admin.report.payment.approve')}}" method="POST">
				@csrf
				<input type="hidden" name="id" value="{{$paymentLog->id}}">
				<div class="modal_body2 text-start">
					
					<div class="modal_text2">
						<h6 class="text-center">{{ translate('Are you sure you want to approved this application?')}}</h6>
					</div>
					<div class="mt-4 mx-5">
						<label for="feedback" class="form-label text-start">{{ translate('Feedback')}}<sup class="text-danger">*</sup></label>
						<div class="input-group">
							<textarea required class="form-control mt-1" name="feedback" id="feedback" rows="2"></textarea>
						</div>
					</div>
					
				</div>
				<div class="modal_button2 modal-footer">
					<div class="d-flex align-items-center justify-content-center gap-3">
						<button type="button" class="i-btn primary--btn btn--md" data-bs-dismiss="modal">{{ translate('Cancel')}}</button>
						<button type="submit" class="i-btn success--btn btn--md">{{ translate('Approved')}}</button>
					</div>
				</div>
			</form>
		</div>
	</div>
</div>


<div class="modal fade" id="rejectModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<form action="{{route('admin.report.payment.reject')}}" method="POST">
				@csrf
				<input type="hidden" name="id" value="{{$paymentLog->id}}">
				<div class="modal_body2 text-start">
		
					<div class="modal_text2">
						<h6 class="text-center">{{ translate('Are you sure you want to rejected this Payment?')}}</h6>
					</div>
					<div class="mt-4 mx-5">
						<label for="feedback" class="form-label">{{ translate('Feedback')}}<sup class="text-danger">*</sup></label>
						<div class="input-group">
							<textarea required class="form-control mt-1" name="feedback" id="feedback" rows="2"></textarea>
						</div>
					</div>
				</div>
				<div class="modal_button2 modal-footer">
					<div class="d-flex align-items-center justify-content-center gap-3">
						<button type="button" class="i-btn primary--btn btn--md" data-bs-dismiss="modal">{{ translate('Cancel')}}</button>
						<button type="submit" class="i-btn danger--btn btn--md">{{ translate('Rejected')}}</button>
					</div>
				</div>
			</form>
		</div>
	</div>
</div>
@endsection
