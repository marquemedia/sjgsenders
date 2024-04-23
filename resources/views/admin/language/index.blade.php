@extends('admin.layouts.app')
@section('panel')
    <section>
        <div class="card">
            <div class="card-header">
                <h4 class="card-title"> {{ translate('Manage Language')}}</h4>
                <div class="d-flex justify-content-end">
                    <form action="{{route('admin.language.default')}}" method="POST" class="form-inline float-sm-right text-end">
                        @csrf
                        <div class="input-group">
                            <select class="form-select" name="id" required="">
                                @foreach($languages as $language)
                                    <option value="{{$language->id}}" @if($language->is_default == 1) selected @endif>{{strtoupper($language->name)}}</option>
                                @endforeach
                            </select>
                            <button class="i-btn info--btn btn--md" id="basic-addon2" type="submit">{{translate('Set Default Language')}}</button>
                        </div>
                    </form>
                    <a href="javascript:void(0);" class="ms-3 i-btn primary--btn btn--md text-white" data-bs-toggle="modal" data-bs-target="#create-language" title=" {{ translate('Create New Language')}}">
                        <i class="fa-solid fa-plus"></i> {{translate('Add New')}}
                    </a>
                    
                </div>
            </div>

            <div class="card-body px-0">
                <div class="responsive-table">
                    <table>
                        <thead>
                            <tr>
                                <th> {{ translate('Name')}}</th>
                                <th> {{ translate('Code')}}</th>
                                <th> {{ translate('Status')}}</th>
                                <th> {{ translate('Action')}}</th>
                            </tr>
                        </thead>
                        @forelse($languages as $language)
                            <tr class="@if($loop->even)@endif">
                                <td data-label=" {{ translate('Name')}}">
                                    <i class="flag-icon flag-icon-{{$language->flag}} flag-icon-squared rounded-circle fs-4 me-1"></i>{{$language->name}}
                                </td>
                                <td data-label=" {{ translate('Code')}}">
                                    {{$language->code}}
                                </td>

                                <td data-label=" {{ translate('Status')}}">
                                    @if($language->is_default == 1)
                                        <span class="badge badge--success"> {{ translate('Default')}}</span>
                                    @else
                                        <span> {{ translate('N/A')}}</span>
                                    @endif
                                </td>
                                <td data-label= {{ translate('Action')}}>
                                    <div class="d-flex align-items-center justify-content-md-start justify-content-end gap-3">
                                        <a class="i-btn primary--btn btn--sm language" data-bs-toggle="modal" data-bs-target="#updatebrand" href="javascript:void(0)" data-id="{{$language->id}}" data-name="{{$language->name}}" data-code="{{$language->code}}"><i class="las la-pen"></i></a>
                                        <a class="i-btn info--btn btn--sm brand" href="{{route('admin.language.translate', $language->code)}}"><i class="las la-language"></i></a>
                                        @if($language->is_default != 1 && $language->id != 1)
                                            <a href="javascript:void(0)" class="i-btn danger--btn btn--sm language-delete"
                                            data-bs-toggle="modal"
                                            data-bs-target="#delete"
                                            data-delete_id="{{$language->id}}"
                                            ><i class="las la-trash"></i></a>
                                        @endif
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
            </div>
        </div>

        
    </section>


<div class="modal fade" id="create-language" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
			<form action="{{route('admin.language.store')}}" method="POST" enctype="multipart/form-data">
				@csrf
	            <div class="modal-body">
	            	<div class="card">
	            		<div class="card-header bg--lite--violet">
	            			<div class="card-title text-center text--light"> {{ translate('Add New Language')}}</div>
	            		</div>
		                <div class="card-body">
		                	<div class="mb-3">
								<label for="name" class="form-label"> {{ translate('Country Flag')}} <sup class="text--danger">*</sup></label>
								<span id="flag-icon"></span>
								<select name="flag" class="form-select flag" id="flag">
										<option value=""> {{ translate('Select Country Flag')}}</option>
								    @foreach($countries as $key=>$countryData)
										<option value="{{$key}}" @if(session('flag') == $key) selected="" @endif>{{$countryData->country}}</option>
									@endforeach
								</select>
							</div>

							<div class="mb-3">
								<label for="name" class="form-label"> {{ translate('Language Name')}} <sup class="text--danger">*</sup></label>
								<input type="text" class="form-control" id="name" name="name" placeholder=" {{ translate('Language Name Here')}}" required>
							</div>

							<div class="mb-3">
								<label for="code" class="form-label"> {{ translate('Code')}} <sup class="text--danger">*</sup></label>
								<input type="text" class="form-control" id="code" name="code" placeholder=" {{ translate('Enter Language Code [i.g: en, bn, in]')}}" required>
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


<div class="modal fade" id="update-language" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
				<h5 class="modal-title"> {{ translate('Update Language')}}</h5>

                <button type="button" class="i-btn bg--lite--danger text--danger btn--sm" data-bs-dismiss="modal"> <i class="las la-times"></i></button>
			</div>
			<form action="{{route('admin.language.update')}}" method="POST" enctype="multipart/form-data">
				@csrf
				<input type="hidden" name="id">
	            <div class="modal-body">
                    <div>
                        <label for="name" class="form-label"> {{ translate('Name')}} <sup class="text--danger">*</sup></label>
                        <input type="text" class="form-control" id="name" name="name" placeholder=" {{ translate('Enter Name')}}" required>
                    </div>
	            </div>

	            <div class="modal_button2 modal-footer">
                    <div class="d-flex align-items-center gap-3">
                        <button type="button" class="i-btn primary--btn btn--md" data-bs-dismiss="modal"> {{ translate('Cancel')}}</button>
                        <button type="submit" class="i-btn success--btn btn--md"> {{ translate('Submit')}}</button>
                    </div>
	            </div>
	        </form>
        </div>
    </div>
</div>

<div class="modal fade" id="delete" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
        	<form action="{{route('admin.language.delete')}}" method="POST">
        		@csrf
        		<input type="hidden" name="id" value="">
	            <div class="modal_body2">
	                <div class="modal_icon2">
	                    <i class="las la-trash"></i>
	                </div>
	                <div class="modal_text2 mt-3">
	                    <h6> {{ translate('Are you sure to delete this language')}}</h6>
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
		$('.language').on('click', function(){
            const modal = $('#update-language');
            modal.find('input[name=id]').val($(this).data('id'));
			modal.find('input[name=name]').val($(this).data('name'));
			modal.modal('show');
		});

		$('.language-delete').on('click', function(){
            const modal = $('#delete');
            modal.find('input[name=id]').val($(this).data('delete_id'));
			modal.modal('show');
		});

		$('#flag').on('change', function() {
            const countryCode = this.value.toLowerCase();
            $('#flag-icon').html('').html('<i class="flag-icon flag-icon-squared rounded-circle fs-4 me-1 flag-icon-'+countryCode+'"></i>');
		});
	})(jQuery);
</script>
@endpush
