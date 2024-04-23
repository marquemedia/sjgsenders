@extends('admin.layouts.app')
@section('panel')
<section>
	<div class="card">
		<div class="card-header">
			<h4 class="card-title">{{ translate('Reply to Customer')}}</h4>
			@if($supportTicket->status != 4)
				<button class="i-btn danger--btn btn--sm" data-bs-toggle="modal" data-bs-target="#close">{{ translate('Close Ticket')}}</button>
			@endif
		</div>

		<div class="card-body">
			@if($supportTicket->status != 4)
                <form action="{{route('admin.support.ticket.reply', $supportTicket->id)}}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="row mb-3">
                        <div class="col-lg-12 mb-3">
                            <textarea class="form-control" rows="5" name="message" placeholder="{{ translate('Enter Message')}}" required></textarea>
                        </div>
                        <div class="col-lg-8 mb-3">
                            <input type="file" name="file[]" class="form-control">
                            <div class="addnewdata"></div>
                            <div class="form-text">"{{ translate('Allowed File Extensions: .jpg, .jpeg, .png, .pdf, .doc, .docx')}}</div>
                        </div>
                        <div class="col-lg-2 mb-3">
                            <button type="button" class="i-btn primary--btn btn--md addnewfile">{{ translate('Add New')}}</button>
                        </div>
                        <div class="col-lg-2 mb-3">
                            <button type="submit" class="i-btn primary--btn btn--md text--light w-100">{{ translate('Reply')}}</button>
                        </div>
                    </div>
                </form>
			@endif

			@foreach($supportTicket->messages as $meg)
				@if($meg->admin_id == 0)
					<div class="ticket-single bg-white shadow-sm rounded mb-3">
						<div class="row p-2 mb-3">
							<div class="col-lg-3">
								<h6 class="ticket-user">{{translate("Created By: ")}}<a href="{{route('admin.user.details',$supportTicket->user_id)}}" class="text-dark">{{$supportTicket->user?->name}}</a></h6>
								<p class="ticket-time">{{ translate('Created at')}} {{getDateTime($meg->created_at) }}</p>
							</div>

							<div class="col-lg-9">
								<p>{{$meg->message}}</p>
								@if($meg->supportfiles()->count() > 0)
									<div class="my-3">
										@foreach($meg->supportfiles as $key=> $file)
											<span class="me-2">{{ translate("Attachments:") }}</span><a href="{{route('admin.support.ticket.download',encrypt($file->id))}}" class="mr-3 text-dark"><i class="text-primary fa fa-file me-1"></i>{{ translate('File')}} {{++$key}}</a>
										@endforeach
									</div>
								@endif
							</div>
						</div>
					</div>
				@else
					<div class="ticket-single row mb-3 shadow-sm bg-white rounded">
						<div class="col-lg-3">
							<p >{{ translate('Created at')}} {{getDateTime($meg->created_at)}}</p>
							<h6 class="mt-2">{{ translate('Admin')}}</h6>
						</div>

						<div class="col-lg-9">
							<p >{{$meg->message}}</p>
							@if($meg->supportfiles()->count() > 0)
								<div class="my-3">
									@foreach($meg->supportfiles as $key=> $file)
										<a href="{{route('admin.support.ticket.download',encrypt($file->id))}}" class="mr-3"><i class="fa fa-file"></i> {{ translate('File')}} {{++$key}} </a>
									@endforeach
								</div>
							@endif
						</div>
					</div>
				@endif
			@endforeach
		</div>
	</div>
</section>

<div class="modal fade" id="close" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
        	<form action="{{route('admin.support.ticket.closeds', $supportTicket->id)}}" method="POST">
        		@csrf
        		<input type="hidden" name="id">
	            <div class="modal_body2">
	                <div class="modal_icon2">
	                    <i class="las la-trash"></i>
	                </div>
	                <div class="modal_text2 mt-3">
	                    <h6>{{ translate('Are you sure to want close this ticket?')}}</h6>
	                </div>
	            </div>
	            <div class="modal_button2 modal-footer">
					<div class="d-flex align-items-center justify-content-center gap-3">
						<button type="button" class="i-btn primary--btn btn--md" data-bs-dismiss="modal">{{ translate('Cancel')}}</button>
						<button type="submit" class="i-btn danger--btn btn--md">{{ translate('Closed')}}</button>
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
		"use strict"
		$('.addnewfile').on('click', function () {
	        var html = `
	        <div class="row newdata my-2">
	    		<div class="mb-3 col-lg-10">
	    			<input type="file" name="file[]" class="form-control" required>
				</div>

	    		<div class="col-lg-2 col-md-12 mt-md-0 mt-2 text-right">
	                <span class="input-group-btn">
	                    <button class="btn btn-danger btn-sm removeBtn w-100" type="button">
	                        <i class="fa fa-times"></i>
	                    </button>
	                </span>
	            </div>
	        </div>`;
	        $('.addnewdata').append(html);
		    $(".removeBtn").on('click', function(){
		        $(this).closest('.newdata').remove();
		    });
	    });
    })(jQuery);
</script>
@endpush
