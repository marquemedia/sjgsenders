@extends('user.layouts.app')
@section('panel')
<section>
    <div class="container-fluid p-0">
        <div class="row gy-4">
            @include('user.gateway.method')
            <div class="col">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">{{translate('Select Sending Method')}}</h4>
                    </div>

                    <div class="card-body">
                        <form method="post" action="{{route('user.default.sms.gateway')}}" >
                            @csrf
                            <div class="mb-3">
                                <label for="sms_gateway" class="form-label">{{translate('Send SMS By')}} <sup class="text--danger">*</sup></label>
                                <select class="form-select" id="sms_gateway" name="sms_gateway" required>
                                    <option value="1" @if(auth()->user()->sms_gateway == 1) selected @endif>{{translate('API Gateway')}}</option>
                                    <option value="2" @if(auth()->user()->sms_gateway == 2) selected @endif>{{translate('Android Gateway')}}</option>
                                </select>
                            </div>
                            <div>
                                <button type="submit" class="i-btn btn--primary btn--md">{{ translate('Submit')}}</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
