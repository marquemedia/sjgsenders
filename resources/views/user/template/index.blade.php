@extends('user.layouts.app')
@section('panel')
<section class="mt-3">
		<div class="card">
			<div class="card-header">
				<h4 class="card-title">
					{{translate("SMS Templates")}}
				</h4>
			</div>
			<div class="card-body px-0">
				<div class="responsive-table">
					<table>
						<thead>
							<tr>
								<th>{{translate('Name')}}</th>
								<th>{{translate('Message')}}</th>
								<th>{{translate('Status')}}</th>
								<th>{{translate('Action')}}</th>
							</tr>
						</thead>
						@forelse($templates as $template)
							<tr class="@if($loop->even)@endif">
								<td data-label="{{translate('Name')}}">
									{{$template->name}}
								</td>

								<td data-label="{{translate('Message')}}">
									{{$template->message}}
								</td>
								<td data-label="{{translate('Status')}}">
									@if($template->status == 1)
										<div class="badge badge--primary">{{translate('Pending')}}</div>
									@elseif($template->status == 2)
										<div class="badge badge--success">{{translate('Approved')}}</div>
									@elseif($template->status == 3)
										<div class="badge badge--danger">{{translate('Rejected')}}</div>
									@endif
								</td>

								<td data-label={{translate('Action')}}>
									<div class="d-flex align-items-center justify-content-md-start justify-content-end gap-3">
										<a class="i-btn primary--btn btn--sm template text-white" data-bs-toggle="modal" data-bs-target="#updatebrand" href="javascript:void(0)"
											data-id="{{$template->id}}"
											data-name="{{$template->name}}"
											data-message="{{$template->message}}"><i class="las la-pen"></i></a>
										<a class="i-btn danger--btn btn--sm delete" data-bs-toggle="modal" data-bs-target="#deletetemplate" href="javascript:void(0)"data-id="{{$template->id}}"><i class="las la-trash"></i></a>
									</div>
								</td>
							</tr>
						@empty
							<tr>
								<td class="text-muted text-center" colspan="100%">{{translate('No Data Found')}}</td>
							</tr>
						@endforelse
					</table>
				</div>
				<div class="m-3">
					{{$templates->appends(request()->all())->onEachSide(1)->links()}}
				</div>
			</div>
		</div>

	<a href="javascript:void(0);" class="support-ticket-float-btn" data-bs-toggle="modal" data-bs-target="#createTemplate" title="{{translate('Create New Template')}}">
		<i class="fa fa-plus ticket-float"></i>
	</a>
</section>


<div class="modal fade" id="createTemplate" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
			<form action="{{route('user.phone.book.template.store')}}" method="POST">
				@csrf
	            <div class="modal-body">
	            	<div class="card">
	            		<div class="card-header bg--lite--violet">
	            			<div class="card-title text-center text--light">{{translate('Add New Template')}}</div>
	            		</div>
		                <div class="card-body">
							<div class="mb-3">
								<label for="name" class="form-label">{{translate('Name')}} <sup class="text--danger">*</sup></label>
								<input type="text" class="form-control" id="name" name="name" placeholder="{{translate('Enter Name')}}" required>
							</div>

							<div class="mb-3">
								<label for="message" class="form-label">{{translate('Message') }}<sup class="text--danger">*</sup></label>
								<textarea rows="5"  class="form-control" id="message" name="message" placeholder="{{translate('Enter Message')}}" required=""></textarea>
							</div>
						</div>
	            	</div>
	            </div>

	            <div class="modal_button2 modal-footer">
					<div class="d-flex align-items-center justify-content-center gap-3">
						<button type="button" class="i-btn primary--btn btn--md" data-bs-dismiss="modal">{{translate('Cancel')}}</button>
						<button type="submit" class="i-btn success--btn btn--md">{{translate('Submit')}}</button>
					</div>
	            </div>
	        </form>
        </div>
    </div>
</div>


<div class="modal fade" id="updatetemplate" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
			<form action="{{route('user.phone.book.template.update')}}" method="POST">
				@csrf
				<input type="hidden" name="id">
	            <div class="modal-body">
	            	<div class="card">
	            		<div class="card-header bg--lite--violet">
	            			<div class="card-title text-center text--light">{{translate('Update Group')}}</div>
	            		</div>
		                <div class="card-body">
							<div class="mb-3">
								<label for="name" class="form-label">{{translate('Name')}} <sup class="text--danger">*</sup></label>
								<input type="text" class="form-control" id="name" name="name" placeholder="{{translate('Enter Name')}}" required>
							</div>

							<div class="mb-3">
								<label for="message" class="form-label">{{translate('Message')}} <sup class="text--danger">*</sup></label>
								<textarea rows="5"  class="form-control" id="message" name="message" placeholder="{{translate('Enter Message')}}" required=""></textarea>
							</div>
						</div>
	            	</div>
	            </div>

	            <div class="modal_button2 modal-footer">\
					<div class="d-flex align-items-center justify-content-center gap-3">
						<button type="button" class="i-btn primary--btn btn--md" data-bs-dismiss="modal">{{translate('Cancel')}}</button>
						<button type="submit" class="i-btn success--btn btn--md">{{translate('Submit')}}</button>
					</div>
	            </div>
	        </form>
        </div>
    </div>
</div>



<div class="modal fade" id="deletetemplate" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<form action="{{route('user.phone.book.template.delete')}}" method="POST">
				@csrf
				<input type="hidden" name="id">
				<div class="modal_body2">
					<div class="modal_icon2">
						<i class="las la-trash"></i>
					</div>
					<div class="modal_text2 mt-3">
						<h6>{{translate('Are you sure to want delete this group?')}}</h6>
					</div>
				</div>
				<div class="modal_button2 modal-footer">
					<div class="d-flex align-items-center justify-content-center gap-3">
						<button type="button" class="i-btn primary--btn btn--md" data-bs-dismiss="modal">{{translate('Cancel')}}</button>
						<button type="submit" class="i-btn danger--btn btn--md">{{translate('Delete')}}</button>
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
		$('.template').on('click', function(){
			var modal = $('#updatetemplate');
			modal.find('input[name=id]').val($(this).data('id'));
			modal.find('input[name=name]').val($(this).data('name'));
			modal.find('textarea[name=message]').val($(this).data('message'));
			modal.modal('show');
		});

		$('.delete').on('click', function(){
			var modal = $('#deletetemplate');
			modal.find('input[name=id]').val($(this).data('id'));
			modal.modal('show');
		});
	})(jQuery);
</script>
@endpush
