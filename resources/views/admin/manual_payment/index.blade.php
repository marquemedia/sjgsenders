@extends('admin.layouts.app')
@section('panel')
<section>
    <div class="container-fluid p-0">
		<div class="card">
			<div class="card-header">
				<h4 class="card-title">{{translate('Manual Payment Method')}}</h4>
				
				<a href="{{route('admin.manual.payment.create')}}" class="i-btn primary--btn btn--md text-white" title="{{translate('Create New Manual Payment Option')}}">
					<i class="fa-solid fa-plus"></i> {{translate('Add New')}}
				</a>
			</div>

			<div class="card-body px-0">
				<div class="responsive-table">
					<table class="m-0 text-center table--light">
						<thead>
							<tr>
								<th> {{ translate('Name')}}</th>
								<th> {{ translate('Image')}}</th>
								<th> {{ translate('Method Currency')}}</th>
								<th> {{ translate('Status')}}</th>
								<th> {{ translate('Action')}}</th>
							</tr>
						</thead>
						@forelse($manualPayments as $manualPayment)
							<tr class="@if($loop->even)@endif">
								<td data-label=" {{ translate('Name')}}">
									{{$manualPayment->name}}
								</td>

								<td data-label=" {{ translate('Logo')}}">
									<img src="{{showImage(filePath()['payment_method']['path'].'/'.$manualPayment->image)}}" class="brandlogo">
								</td>

								<td data-label=" {{ translate('Currency')}}">
									1 {{$general->currency_name}} = {{shortAmount($manualPayment->rate)}} {{$manualPayment->currency->name}}
								</td>
								<td data-label=" {{ translate('Status')}}">
									@if($manualPayment->status == 1)
										<span class="badge badge--success"> {{ translate('Active')}}</span>
									@else
										<span class="badge badge--danger"> {{ translate('Inactive')}}</span>
									@endif
								</td>
								<td data-label= {{ translate('Action')}}>
									<div class="d-flex align-items-center justify-content-center gap-3">
										<a href="{{route('admin.manual.payment.edit',$manualPayment->id)}}" class="i-btn primary--btn btn--sm"><i class="las la-pen"></i></a>

										<a href="javascript:void(0)" class="i-btn danger--btn btn--sm gwdelete"
											data-bs-toggle="modal"
											data-bs-target="#delete"
											data-delete_id="{{$manualPayment->id}}"
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
					{{$manualPayments->appends(request()->all())->onEachSide(1)->links()}}
				</div>
			</div>
		</div>
	</div>

	
</section>

<div class="modal fade" id="delete" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
        	<form action="{{route('admin.manual.payment.delete')}}" method="POST">
        		@csrf
        		<input type="hidden" name="id" value="">
	            <div class="modal_body2">
	                <div class="modal_icon2">
	                    <i class="las la-trash"></i>
	                </div>
	                <div class="modal_text2 mt-3">
	                    <h6>{{translate('Are you sure to delete this payment method')}}</h6>
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


@push('style-push')
	<style>
		.brandlogo{
			width: 50px;
		}
	</style>
@endpush

@push('script-push')
<script>
	(function($){
       	"use strict";
		$('.gwdelete').on('click', function(){
			var modal = $('#delete');
			modal.find('input[name=id]').val($(this).data('delete_id'));
			modal.modal('show');
		});
	})(jQuery);
</script>
@endpush


