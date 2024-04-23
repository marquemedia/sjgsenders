@extends('admin.layouts.app')
@section('panel')
<section>
    <div class="container-fluid p-0">
        <div class="row gy-4">
            @include('admin.template.index')
            <div class="col">
                <div class="card mb-4">
                    <div class="card-header">
                        <h6>{{translate('User Templates')}}</h6>
                    </div>
                    <div class="card-body px-0">
                        <div class="responsive-table">
                            <table>
                                <thead>
                                    <tr>
                                        <th>{{translate('Name')}}</th>
                                        <th>{{translate('User Name')}}</th>
                                        <th>{{translate('Message')}}</th>
                                        <th>{{translate('Status')}}</th>
                                        <th>{{translate('Action')}}</th>
                                    </tr>
                                </thead>
                                @forelse($userTemplates as $template)
                                    <tr class="@if($loop->even)@endif">
                                        <td data-label="{{translate('Name')}}">
                                            {{$template->name}}
                                        </td>

                                        <td data-label="{{translate('User Name')}}">
                                            {{$template->user->name ?? 'N/A'}}
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
                                                <a class="i-btn primary--btn btn--sm statusUpdate" data-bs-toggle="modal" data-bs-target="#updateStatus" href="javascript:void(0)"
                                                data-id="{{$template->id}}" data-status="{{$template->status}}">
                                                <i class="las la-pen"></i>
                                                </a>
                                                <a class="i-btn danger--btn btn--sm delete" data-bs-toggle="modal" data-bs-target="#deletetemplate" href="javascript:void(0)" data-delete_id="{{$template->id}}"><i class="las la-trash"></i></a>
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
                            {{$userTemplates->appends(request()->all())->onEachSide(1)->links()}}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>


<div class="modal fade" id="updateStatus" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
			<form action="{{route('admin.template.userStatus.update')}}" method="POST">
				@csrf
				<input type="hidden" name="id">
	            <div class="modal-body">
	            	<div class="card">
	            		<div class="card-header bg--lite--violet">
	            			<div class="card-title text-center text--light">{{translate('Status Update')}}</div>
	            		</div>
		                <div class="card-body">
							<div class="mb-3" id="statusAppend">

							</div>
						</div>
	            	</div>
	            </div>

	            <div class="modal_button2 modal-footer">
                    <div class="d-flex align-items-center justify-content-center gap-3">
                        <button type="button" class="i-btn primary--btn btn--md" data-bs-dismiss="modal">{{translate('Cancel')}}</button>
                        <button type="submit" class="i-btn success--btn btn--md">{{translate('Update')}}</button>
                    </div>
	            </div>
	        </form>
        </div>
    </div>
</div>



<div class="modal fade" id="deletetemplate" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<form action="{{route('admin.template.delete')}}" method="POST">
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


		$('.statusUpdate').on('click', function(){
			var modal = $('#updateStatus');
			modal.find('input[name=id]').val($(this).data('id'));
			var value = $(this).data('status');
			$('#statusAppend').html('')
			$('#statusAppend').html(`
				<label for="status" class="form-label">{{translate('Status')}} <sup class="text--danger">*</sup></label>
				<select name="status" id="status" class="form-control" >
					<option  ${value == 1 ? 'selected' : ''} value="1">{{translate('Pending')}}</option>
					<option  ${value == 2 ? 'selected' : ''} value="2">{{translate('Approved')}}</option>
					<option  ${value == 3 ? 'selected' : ''} value="3">{{translate('Reject')}}</option>
				</select>
			`)
			modal.modal('show');
		});

		$('.delete').on('click', function(){
			var modal = $('#deletetemplate');
			modal.find('input[name=id]').val($(this).data('delete_id'));
			modal.modal('show');
		});
	})(jQuery);
</script>
@endpush
