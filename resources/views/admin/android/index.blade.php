@extends('admin.layouts.app')
@section('panel')
<section class="mt-3">
    <div class="container-fluid p-0">
	    <div class="row">
	 		<div class="col-lg-12">
	            <div class="card mb-4">
	                <div class="responsive-table">
		                <table>
		                    <thead>
		                        <tr>
		                            <th>{{ translate('Name') }}</th>
		                            <th>{{ translate('Password') }}</th>
		                            <th>{{ translate('Status') }}</th>
		                            <th>{{ translate('SIM List') }}</th>
		                            <th>{{ translate('Action') }}</th>
		                        </tr>
		                    </thead>
		                    @forelse($androids as $android)
			                    <tr class="@if($loop->even)@endif">
				                    <td data-label="{{ translate('Name') }}">
				                    	{{$android->name}}
				                    </td>

				                     <td data-label="{{ translate('Password') }}">
				                    	{{$android->show_password}}
				                    </td>

				                    <td data-label="{{ translate('Status') }}">
				                    	@if($android->status == 1)
				                    		<span class="badge badge--success">{{ translate('Active') }}</span>
				                    	@else
				                    		<span class="badge badge--danger">{{ translate('Inactive') }}</span>
				                    	@endif
				                    </td>

				                    <td data-label="{{ translate('list')}}">
				                    	<a href="{{route('admin.sms.gateway.android.sim.index', $android->id)}}" class="badge badge--primary p-2">{{ translate('View All') }}</a>
				                    </td>

				                    <td data-label={{ translate('Action') }}>
			                    		<div class="d-flex align-items-center justify-content-center gap-3">
											<a class="i-btn primary--btn btn--sm android" data-bs-toggle="modal" data-bs-target="#updateandroid" href="javascript:void(0)"
			                    			data-id="{{$android->id}}"
			                    			data-name="{{$android->name}}"
			                    			data-password="{{$android->show_password}}"
			                    			data-status="{{$android->status}}"><i class="las la-pen"></i></a>
			                    		<a class="i-btn danger--btn btn--sm delete" data-bs-toggle="modal" data-bs-target="#deleteandroidApi" href="javascript:void(0)" data-id="{{$android->id}}"><i class="las la-trash"></i></a>
										</div>
				                    </td>
			                    </tr>
			                @empty
			                	<tr>
			                		<td class="text-muted text-center" colspan="100%">{{ translate('No Data Found') }}</td>
			                	</tr>
			                @endforelse
		                </table>
	            	</div>
	                <div class="m-3">
	                	{{$androids->appends(request()->all())->onEachSide(1)->links()}}
					</div>
	            </div>
	        </div>
	    </div>
	</div>
	<a href="javascript:void(0);" class="support-ticket-float-btn" data-bs-toggle="modal" data-bs-target="#createandroid" title="{{ translate('Create New Android GW') }}">
		<i class="fa fa-plus ticket-float"></i>
	</a>
</section>


@endsection


@push('script-push')
<script>
	(function($){
		"use strict";
		$('.android').on('click', function(){
            const modal = $('#updateandroid');
            modal.find('input[name=id]').val($(this).data('id'));
			modal.find('input[name=name]').val($(this).data('name'));
			modal.find('input[name=password]').val($(this).data('password'));
			modal.find('select[name=status]').val($(this).data('status'));
			modal.modal('show');
		});

		$('.delete').on('click', function(){
			var modal = $('#deleteandroidApi');
			modal.find('input[name=id]').val($(this).data('id'));
			modal.modal('show');
		});
	})(jQuery);
</script>
@endpush
