@extends('admin.layouts.app')
@section('panel')
<section>
    <div class="container-fluid p-0">
	    <div class="card">
            <div class="card-header">
                <h4 class="card-title">{{translate('Spam Words')}}</h4>
                
                <a href="javascript:void(0);" class="i-btn primary--btn btn--md text-white" data-bs-toggle="modal" data-bs-target="#createWord" title="{{ translate('Add New Word')}}">
                    <i class="fa-solid fa-plus"></i> {{translate('Add New')}}
                </a>
            </div>
            <div class="card-body px-0">
                <div class="responsive-table">
                    <table>
                        <thead>
                            <tr>
                                <th>{{ translate('Name') }}</th>
                                <th>{{ translate('Value')}}</th>
                                <th>{{ translate('Action')}}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($offensiveData as $key => $data)
                                <tr>
                                    <td data-label="{{ translate('Name')}}">{{$key}}</td>
                                    <td data-label="{{ translate('Value')}}">
                                        <form action="{{route('admin.spam.word.update')}}" method="POST">
                                            @csrf
                                            <div class="d-flex align-items-center justify-content-md-start justify-content-end gap-3">
                                                <input type="hidden" name="key" value="{{$key}}" class="form-control">
                                                <input type="text" name="value" value="{{$data}}" class="form-control">

                                                <button type="submit" class="i-btn success--btn btn--sm btn-sm text--light">
                                                    <i class="las la-save"></i>
                                                </button>
                                            </div>
                                        </form>
                                    </td>

                                    <td data-label="{{ translate('Action')}}">
                                        <div class="d-flex align-items-center justify-content-md-start justify-content-end gap-3">
                                            <button class="i-btn danger--btn btn--sm text--light worddelete" data-bs-toggle="modal" data-bs-target="#worddelete" data-id="{{$key}}"><i class="las la-trash"></i></button>
                                        </div>
                                    </td>
                                </tr>
                            @empty

                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
	    </div>

        

    {{-- add word --}}
        <div class="modal fade" id="createWord" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <form action="{{route('admin.spam.word.store')}}" method="POST">
                        @csrf
                        <div class="modal-body">
                            <div class="card">
                                <div class="card-header bg--lite--violet">
                                        <div class="card-title text-center text--light">{{ translate('Add New Word')}}</div>
                                </div>
                                <div class="card-body">
                                    <div class="mb-3">
                                        <label for="key" class="form-label">{{ translate('Name')}} <sup class="text--danger">*</sup></label>
                                        <input type="text" class="form-control" id="key" name="key" placeholder="{{ translate('Enter Name')}}" required>
                                    </div>
                                    <div class="mb-3">
                                        <label for="value" class="form-label">{{ translate('Value')}} <sup class="text--danger">*</sup></label>
                                        <input type="text" class="form-control" id="value" name="value" placeholder="{{ translate('Enter value')}}" required>
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

        <div class="modal fade" id="worddelete" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <form action="{{route('admin.spam.word.delete')}}" method="POST">
                        @csrf
                        <input type="hidden" name="id">
                        <div class="modal_body2">
                            <div class="modal_icon2">
                                <i class="las la-trash"></i>
                            </div>
                            <div class="modal_text2 mt-3">
                                    <h6>{{ translate('Are you sure to want delete this?')}}</h6>
                            </div>
                        </div>
                        <div class="modal_button2 modal-footer">
                            <div class="d-flex align-items-center justify-content-center gap-3">
                                <button type="button" class="i-btn primary--btn btn--md" data-bs-dismiss="modal">{{ translate('Cancel')}}</button>
                                <button type="submit" class="i-btn danger--btn btn--md">{{ translate('Delete')}}</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection


@push('script-push')
<script>
	(function($){
		"use strict";
		$('.worddelete').on('click', function(){
			var modal = $('#worddelete');
			modal.find('input[name=id]').val($(this).data('id'));
			modal.modal('show');
		});

	})(jQuery);
</script>
@endpush
