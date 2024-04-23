@extends('layouts.frontend')
@section('content')
 <div class="col-xl-5 col-lg-6">
    <div class="login-left-section d-flex align-items-center justify-content-center">
        <div class="form-container">
            <div class="mb-5">
                <h4> {{ translate('Forgot your password')}}</h4>
            </div>

            <form action="{{route('password.email')}}" method="POST">
                @csrf
                <div class="mb-5">
                    <label for="exampleInputEmail1" class="form-label d-block">{{ translate('Email address')}}</label>
                    <div class="input-field">
                        <span><i class="las la-envelope"></i></span>
                        <input type="email" name="email" value="{{old('email')}}" placeholder="{{ translate('example@gmail.com')}}" id="exampleInputEmail1"aria-describedby="emailHelp"/>
                    </div>
                </div>
                
                <button type="submit" class=" btn btn-md btn--primary w-100">{{ translate('Submit')}}</button>
            </form>
        </div>
    </div>
</div>
@endsection
