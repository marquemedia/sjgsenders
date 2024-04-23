@extends('admin.layouts.app')
@section('panel')
@push('style-push')
<style type="text/css">
    .header-with-btn{
        display: flex;
        justify-content: space-between;
    }
</style>
@endpush()
<section class="mt-3 rounded_box">
	<div class="container-fluid p-0 mb-3 pb-2">
		<div class="row">
			<div class="col-12">
                <div class="card mb-2">
                    <div class="card-header header-with-btn">
                        <span>{{ translate('Error Notice ')}}</span>
                        <span>
                            <a href="" class="badge badge--primary"> <i class="fas fa-refresh"></i>  {{ translate('Try Again') }}</a>
                            <a href="https://support.igensolutionsltd.com/help-center/categories/2/xsender" target="_blank" class="badge badge--success"> <i class="fas fa-info"></i>  {{ translate('Setup Guide') }}</a>
                        </span>
                    </div>

                    <div class="card-body">
                        <div class="row">
                            <h6 class="text--danger">{{ translate($message) }}</h6>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
