@extends('admin.layouts.app')
@section('panel')
<section>
    <div class="container-fluid p-0">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">
                    {{ translate('WhatsApp Webhook Parameter Setup')}}
                    <sup class="pointer" title="{{ translate('To setup google auth')}}">
                        <a href="#">
                            <i class="fa fa-info-circle"></i>
                        </a>
                    </sup>
                </h4>
            </div>

            <div class="card-body">
                <form action="{{route('admin.general.setting.webhook.update')}}" method="POST">
                    @csrf
                    <div class="form-wrapper">
                        <div class="row g-4">
                            <div class="col-md-6 mb-4">
                                <label for="verify_token">{{ translate('Add A Verify Token For Webhook')}} <span class="text-danger">*</span></label>

                                <div class="input-group mt-2">

                                    <input title="Make sure to copy this same verify token in your Business Account 'Webhook Configuration'" type="text" class="form-control" name="verify_token" id="verify_token" value="{{\Illuminate\Support\Arr::get($credentials, 'verify_token', '')}}" placeholder="{{ translate('Enter A Token For Webhook')}}">

                                    <span class="input-group-text generate-token cursor-pointer">
                                        <i class="bi bi-arrow-repeat fs-4 text--success"></i>
                                    </span>
                                    <span class="input-group-text copy-text cursor-pointer">
                                        <i class="fa-regular fa-copy fs-4 text--success"></i>
                                    </span>
                                </div>
                            </div>

                            <div class="col-md-6 mb-4">
                                <label for="callback_url">{{ translate('Add A CallBack URL For Webhook')}} <span class="text-danger">*</span></label>
                               <div class="input-group mt-2">
                                    <input readonly title="Make sure to copy this same call back url in your Business Account 'Webhook Configuration'" type="text" class="form-control" name="callback_url" id="callback_url" value="{{route('webhook')}}">
                                    <span class="input-group-text copy-text cursor-pointer">
                                        <i class="fa-regular fa-copy fs-4 text--success"></i>
                                    </span>
                               </div>
                            </div>
                        </div>
                    </div>  
                    <button type="submit" class="i-btn primary--btn btn--lg">{{translate('Submit')}}</button>
                </form>
            </div>
        </div>
    </div>
</section>
@endsection
