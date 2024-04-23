@extends('user.layouts.app')
@section('panel')
<section>
	<div class="table_heading d-flex align--center justify--between gap-3 mb-3">
		<nav  aria-label="breadcrumb">
			<ol class="breadcrumb mb-0">
				<li class="breadcrumb-item"><a href="{{route('user.ticket.index')}}">{{ translate('Support')}}</a></li>
				<li class="breadcrumb-item" aria-current="page">{{ translate('New Ticket')}}</li>
			</ol>
		</nav>
		<a href="{{route('user.ticket.index')}}" class="i-btn info--btn btn--sm border-0 px-2 py-1 rounded ms-3"><i class="la la-fw la-backward"></i> {{ translate('Go Back')}}</a>
	</div>

	<div class="card mb-4">
		<div class="card-header">
			<h4 class="card-title">{{translate('Support Ticket Form')}}</h4>
		</div>
		<div class="card-body">
			<form action="{{route('user.ticket.store')}}" method="POST" enctype="multipart/form-data">
				@csrf
				<div class="col-lg-12">
					<div class="row my-3">
						<div class="col-lg-6 mb-3">
							<input type="text" name="subject" class="form-control" placeholder="{{ translate('Enter Subject')}}" required>
						</div>
						<div class="col-lg-6 mb-3">
							<select class="form-select" name="priority" required>
								<option value="" disabled selected>{{ translate('Select Priority')}}</option>
								<option value="1">{{ translate('Low')}}</option>
								<option value="2">{{ translate('Medium') }}</option>
								<option value="3">{{ translate('High') }}</option>
							</select>
						</div>

						<div class="col-lg-12 mb-3">
							<textarea class="form-control" rows="5" name="message" placeholder="{{ translate('Enter Message')}}" required></textarea>
						</div>
						<div class="col-lg-8 mb-3">
							<input type="file" name="file[]" class="form-control">
							<div class="addnewdata mt-4">
							</div>
							<div class="form-text">{{ translate('Allowed File Extensions: .jpg, .jpeg, .png, .pdf, .doc, .docx') }}</div>
						</div>
						<div class="col-lg-2 mb-3">
							<button type="button" class="i-btn primary--btn btn--md addnewfile">{{ translate('Add More')}}</button>
						</div>
						<div class="col-lg-2 mb-3">
							<button type="submit" class="i-btn primary--btn btn--md w-100" >{{ translate('Submit')}}</button>
						</div>
					</div>
				</div>
			</form>
		</div>
	</div>
</section>
@endsection


@push('script-push')
<script>
	$('.addnewfile').on('click', function () {
        var html = `
        <div class="row newdata my-2">
    		<div class="mb-3 col-lg-10">
    			<input type="file" name="file[]" class="form-control" required>
			</div>

    		<div class="col-lg-2 col-md-12 mt-md-0 mt-2 text-right">
                <span class="input-group-btn">
                    <button class="i-btn danger--btn btn--md removeBtn w-100" type="button">
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
</script>
@endpush
