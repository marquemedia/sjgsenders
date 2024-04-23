@extends('layouts.frontend')
@section('content')
    <div class="col-xl-5 col-lg-6">
        <div class="login-left-section d-flex align-items-center justify-content-center">
            <div class="form-container">
                <div class="mb-5">
                    <h4>{{ translate('Verify your Email')}}</h4>
                </div>

                <form action="{{route($route)}}" method="POST">
                    @csrf
                    <div class="my-3">
                        <label for="exampleInputEmail1" class="form-label d-block">{{ translate('Verify Code')}}</label>

                        <div class="input-field">
                            <span><i class="las la-lock"></i></span>
                            <input type="text" name="code" placeholder="{{ translate('Enter Verify Code')}}" id="exampleInputEmail1"aria-describedby="emailHelp"/>
                        </div>
                    </div>

                    <button type="submit" class=" btn btn-md btn--primary w-100">
                        {{ translate('Submit')}}
                    </button>

                    <div class="mt-4">
                        <label>{{translate('Please check your mail inbox for the email containing the verification code. If not found, you can')}} <a href="{{route('password.resend.code')}}" class="text--base">{{translate('Resend code')}}</a></label>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
