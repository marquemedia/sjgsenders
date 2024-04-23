@extends('user.layouts.app')
@section('panel')
<section class="mt-3 rounded_box">
	<div class="container-fluid p-0 mb-3 pb-2">
		<div class="row d-flex align--center rounded">
			<div class="col-xl-12">
				<div class="card">
					<div class="card-header">
						<h4 class="card-title">{{ translate('Reply to admin') }}</h4>
						@if($ticket->status != 4)
							<button class="i-btn danger--btn btn--sm" data-bs-toggle="modal" data-bs-target="#close">{{ translate('Close Ticket') }}</button>
						@endif
					</div>
					<div class="card-body">
						@if($ticket->status != 4)
							<form action="{{route('user.ticket.reply', $ticket->id)}}" method="POST" enctype="multipart/form-data">
								@csrf
								<div class="row my-3">
									<div class="col-lg-12 mb-3">
										<textarea class="form-control" rows="5" name="message" placeholder="{{ translate('Enter Message')}}" required></textarea>
									</div>
									<div class="col-lg-8 mb-3">
										<input type="file" name="file[]" class="form-control mb-4">
										<div class="addnewdata"></div>
										<div class="form-text">{{translate("Allowed File Extensions: .jpg, .jpeg, .png, .pdf, .doc, .docx")}}</div>
									</div>
									<div class="col-lg-2 mb-3">
										<button type="button" class="i-btn primary--btn btn--md addnewfile ">{{ translate('Add More')}}</button>
									</div>
									<div class="col-lg-2 mb-3">
										<button type="submit" class="i-btn primary--btn btn--md w-100" >{{ translate('Reply')}}</button>
									</div>
								</div>
							</form>
						@endif

						@foreach($ticket->messages as $meg)
	                        @if($meg->admin_id == 0)
	                            <div class="ticket-single shadow-sm p-2 mb-3 bg-white rounded">
									<div class="row p-2 mb-3">
										<div class="col-lg-3">
											<p>{{ translate('Ticket Created: ')}} {{getDateTime($meg->created_at)}}</p>
											<h5>{{$ticket->name}}</h5>
										</div>

										<div class="col-lg-9">
											<p>{{$meg->message }}</p>
											@if($meg->supportfiles()->count() > 0)
												<div class="my-3">
													<span class="me-2">{{ translate("Attachments:") }}</span>
													@foreach($meg->supportfiles as $key=> $file)
													<a href="{{route('user.ticket.file.download',encrypt($file->id))}}" class="mr-3 text-dark"><i class="text-primary fa fa-file me-1"></i>{{ translate('File')}} {{++$key}}</a>
													@endforeach
												</div>
											@endif
										</div>
									</div>
	                            </div>
	                        @else
	                            <div class="ticket-single row shadow-sm p-2 mb-3 bg-white rounded">
	                                <div class="col-lg-3 ">
	                                	<p>{{ translate('Admin Reply')}} {{ getDateTime($meg->created_at) }}</p>
	                                    <h6 class=" mt-2">{{ translate('Admin')}}</h6>
	                                </div>

	                                <div class="col-lg-9">
	                                    <p >{{$meg->message}}</p>
	                                    @if($meg->supportfiles()->count() > 0)
	                                        <div class="my-3">
	                                            @foreach($meg->supportfiles as $key=> $file)
	                                                <a href="{{route('user.ticket.file.download',encrypt($file->id))}}" class="mr-3 text-light"><i class="fa fa-file"></i>  {{ translate('File')}} {{++$key}} </a>
	                                            @endforeach
	                                        </div>
	                                    @endif
	                                </div>
	                            </div>
	                        @endif
	                    @endforeach
					</div>
				</div>
			</div>
		</div>
	</div>
</section>

@if($ticket->status != 4)
	<div class="modal fade" id="close" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
	    <div class="modal-dialog">
	        <div class="modal-content">
	        	<form action="{{route('user.ticket.closed', $ticket->id)}}" method="POST">
	        		@csrf
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
@endif
@endsection


@if($ticket->status != 4)
	@push('script-push')
		<script>
			(function($){
				"use strict";
				$('.addnewfile').on('click', function () {
			        var html = `
			        <div class="row newdata my-2">
			    		<div class="mb-3 col-lg-10">
			    			<input type="file" name="file[]" class="form-control" required>
						</div>

			    		<div class="col-lg-2 col-md-12 mt-md-0 mt-2 text-right">
			                <span class="input-group-btn">
			                    <button class="i-btn danger--btn btn--md text--light removeBtn w-100" type="button">
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
@endif
