@extends('admin.layouts.app')
@section('panel')
<section>
    <div class="container-fluid p-0">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">
                    {{ translate('Google Auth Credentials Setup')}}
                    <sup class="pointer" title="{{ translate('To setup google auth')}}">
                        <a href="https://console.cloud.google.com/">
                            <i class="fa fa-info-circle"></i>
                        </a>
                    </sup>
                </h4>
            </div>

            <div class="card-body">
                <form action="{{route('admin.general.setting.social.login.update')}}" method="POST">
                    @csrf
                    <div class="form-wrapper">
                        <div class="row g-4">
                            <div class="mb-3 col-lg-6">
                                <label for="g_client_id" class="form-label">{{ translate('Client Id')}} <sup class="text--danger">*</sup></label>
                                <input type="text" name="g_client_id" id="g_client_id" class="form-control" value="{{\Illuminate\Support\Arr::get($credentials, 'g_client_id', '')}}" placeholder="{{ translate('Enter Google Client Id')}}" required>
                            </div>

                            <div class="mb-3 col-lg-6">
                                <label for="g_client_secret" class="form-label">{{ translate('Client Secret')}}<sup class="text--danger">*</sup></label>
                                <input type="text" name="g_client_secret" id="g_client_secret" class="form-control" value="{{\Illuminate\Support\Arr::get($credentials, 'g_client_secret', '')}}" placeholder="{{ translate('Enter Google Secret Key')}}" required>
                            </div>


                            <div class="mb-3 col-lg-6">
                                <label for="g_client_status" class="form-label">{{ translate('Status')}}<sup class="text--danger">*</sup></label>
                               <select class="form-control" id="g_client_status" name="g_client_status" required>
                                   <option value="1" @if(\Illuminate\Support\Arr::get($credentials, 'g_client_status', '1') == 1) selected  @endif>ON</option>
                                   <option value="2" @if(\Illuminate\Support\Arr::get($credentials, 'g_client_status', '1') == 2) selected  @endif>OFF</option>
                               </select>
                            </div>


                            <div class="mb-3 col-lg-6">
                                <label for="callback_google_url" class="form-label">{{ translate('Authorized redirect URIs')}} </label>
                                <div class="input-group">
                                    <input type="text" id="callback_google_url" class="form-control" value="{{url('auth/google/callback')}}" readonly="" aria-label="Recipient's username" aria-describedby="basic-addon2">
                                    <span class="input-group-text bg--success pointer" onclick="myFunction1()" id="basic-addon2">{{ translate('Copy URL')}}</span>
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


@push('script-push')
<script>
	"use strict";
	function myFunction() {
        var copyText = document.getElementById("callback_facebook_url");
        copyText.select();
        copyText.setSelectionRange(0, 99999)
        document.execCommand("copy");
        notify('success', 'Copied the text : ' + copyText.value);
    }

    function myFunction1() {
        var copyText = document.getElementById("callback_google_url");
        copyText.select();
        copyText.setSelectionRange(0, 99999)
        document.execCommand("copy");
        notify('success', 'Copied the text : ' + copyText.value);
    }
</script>
@endpush
