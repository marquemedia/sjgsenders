@extends('layouts.frontend')
@section('content')
<div class="col-xl-5 col-lg-6">
    <div class="login-left-section d-flex align-items-center justify-content-center">
        <div class="form-container">
            <div class="mb-5">
                <h4 class="text-white">{{ translate('Password Reset')}}</h4>
            </div>

            <form action="{{route('password.update')}}" method="POST">
                @csrf
                <input type="hidden" name="token" value="{{$passwordToken}}">
                <div class="mb-4">
                    <label for="exampleInputPassword1" class="form-label d-block">{{ translate('Password')}}</label>
                    <div class="input-field">
                        <span><i class="las la-lock"></i></span>
                        <input type="password" name="password" placeholder="{{ translate('Enter Password')}}" id="exampleInputPassword1"/>
                    </div>
                </div>

                <div class="mb-5">
                    <label for="exampleInputPassword1" class="form-label d-block">{{ translate('Confirm Password')}}</label>
                    <div class="input-field">
                        <span><i class="las la-lock"></i></span>
                        <input type="password" name="password_confirmation" placeholder="{{ translate('Enter Confirm Password')}}" id="exampleInputPassword1"/>
                    </div>
                </div>
                <button type="submit" class=" btn btn-md btn--primary w-100">{{ translate('Reset Password')}}</button>
            </form>
        </div>
    </div>
</div>
@endsection
