@extends('admin.layouts.app')
@section('panel')
<section>
	<div class="container-fluid p-0">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">{{$title}}</h4>
            </div>

            <div class="card-body">
                <form action="{{route('admin.general.setting.recaptcha.update')}}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="form-wrapper">
                        <h6 class="form-wrapper-title">{{translate("Applicability")}}</h6>
                        <div class="row g-4">
                            {{-- <div class="col-xl-4 col-md-6">
                                <div class="switch-container">
                                    <label class="form-check-label" for="default_recaptcha">{{translate('Use Default Captcha')}}</label>
                                    <label class="switch">
                                        <input type="checkbox" value="true" name="default_recaptcha" type="checkbox" id="default_recaptcha" {{$general->default_recaptcha=="true" ? "checked" : ""}}>
                                        <span class="slider"></span>
                                    </label>
                                </div>
                            </div> --}}
                            <div class="col-xl-6 col-md-6">
                                <div class="switch-container">
                                    <label class="form-check-label" for="captcha_with_registration">{{translate('Captcha With Registration')}}</label>
                                    <label class="switch">
                                        <input type="checkbox" value="true" name="captcha_with_registration" type="checkbox" id="captcha_with_registration" {{$general->captcha_with_registration=="true" ? "checked" : ""}}>
                                        <span class="slider"></span>
                                    </label>
                                </div>
                            </div>
                            <div class="col-xl-6 col-md-6">
                                <div class="switch-container">
                                    <label class="form-check-label" for="captcha_with_login">{{translate('Captcha With Login')}}</label>
                                    <label class="switch">
                                        <input type="checkbox" value="true" name="captcha_with_login" type="checkbox" id="captcha_with_login" {{$general->captcha_with_login=="true" ? "checked" : ""}}>
                                        <span class="slider"></span>
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="form-wrapper">
                        <h6 class="form-wrapper-title">{{translate("Credentials")}}</h6>
                        <div class="row g-4">
                            <div class="col-md-6">
                                
                                <div class="form-item">
                                    <label for="recaptcha_key" class="form-label">{{translate('Key')}} <sup class="text--danger">*</sup></label>
                                    <input type="text" name="recaptcha_key" id="recaptcha_key" class="form-control" value="{{\Illuminate\Support\Arr::get($credentials, 'recaptcha_key', '')}}" placeholder="{{translate('Enter reCaptcha Key')}}" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-item">
                                    <label for="recaptcha_secret" class="form-label">{{translate('Secret Key')}} <sup class="text--danger">*</sup></label>
                                    <input type="text" name="recaptcha_secret" id="recaptcha_secret" class="form-control" value="{{\Illuminate\Support\Arr::get($credentials, 'recaptcha_secret', '')}}" placeholder="{{translate('Enter reCaptcha Secret Key')}}" required>
                                </div>
                            </div>
                            <div class="col">
                                <div class="form-item">
                                    <label for="recaptcha_status" class="form-label">{{translate('Status')}} <sup class="text--danger">*</sup></label>
                                    <select class="form-select" id="recaptcha_status" name="recaptcha_status" required="">
                                        <option value="1" @if(\Illuminate\Support\Arr::get($credentials, 'recaptcha_status', '1') == 1) selected @endif>{{translate('Active')}}</option>
                                        <option value="2" @if(\Illuminate\Support\Arr::get($credentials, 'recaptcha_status', '2') == 2) selected @endif>{{translate('Inactive')}}</option>
                                    </select>
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

@push('style-include')
    <link rel="stylesheet" href="{{ asset('assets/theme/admin/css/spectrum.css') }}">
@endpush

@push('script-include')
    <script src="{{ asset('assets/theme/admin/js/spectrum.js') }}"></script>
@endpush

@push('script-push')
    <script>
        "use strict";
    </script>
@endpush
