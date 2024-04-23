@extends('layouts.frontend')
@section('content')
@php 
    $credentials = array_replace_recursive(config('setting.recaptcha'), (array)$general->recaptcha);
@endphp
 <div class="col-xl-5 col-lg-6">
    <div class="login-left-section d-flex align-items-center justify-content-center">
        <div class="form-container">
            <div class="mb-5">
                <a href="{{route('home')}}" class="site-logo">
                    <img src="{{showImage(filePath()['site_logo']['path'].'/'.$general->site_logo)}}" class="logo-sm" alt="">
                </a>

                <h4>{{ translate('Sign In with')}} <span class="site--title">{{ucfirst($general->site_name)}}</span></h4>
            </div>

            <form action="{{route('login.store')}}" method="POST" id="login-form">
                @csrf
                <div class="my-3">
                    <label for="user" class="form-label d-block">{{ translate('Email address')}}</label>
                    <div class="input-field">
                        <span><i class="las la-envelope"></i></span>
                        <input type="email" name="email" value="{{old('email')}}" placeholder="{{ translate('Give your login mail')}}" id="user"aria-describedby="emailHelp"/>
                    </div>
                </div>

                <div class="mb-4">
                    <label for="password" class="form-label d-block">{{ translate('Password')}}</label>
                    <div class="input-field">
                        <span><i class="las la-lock"></i></span>
                        <input type="password" name="password" placeholder="{{ translate('Give Valid password')}}" id="password"/>
                    </div>
                </div>

                <div>
                    
                    <button 
                        @if(\Illuminate\Support\Arr::get($credentials, 'recaptcha_status', '1') == 1 && $general->captcha_with_login == "true") 
                            class="g-recaptcha btn btn-md btn--primary w-100"
                            data-sitekey="{{$credentials["recaptcha_key"]}}"
                            data-callback='onSubmit'
                            data-action='register'
                        @else
                            class="btn btn-md btn--primary w-100"
                        @endif
                        type="submit">{{ translate('Submit')}}

                    </button>
                </div>

                <div class="mb-4">
                    <a href="{{route('password.request')}}">{{ translate('Forget password')}}?</a>
                </div>
            </form>

            <p class="text-center mt-4">
                {{ translate('New To')}} {{ucfirst($general->site_name)}}? <a href="{{route('register')}}">{{ translate('Sign Up!')}}</a>
            </p>

            <div class="mt-5">
                @if(\Illuminate\Support\Arr::get($general->social_login, 'g_client_status', 1) == 1)
                    <div class="or text-center"><p class="m-0">{{ translate('Or')}}</p></div>
                    <div class="mt-4">
                        <a href="{{url('auth/google')}}" class="btn btn-md btn--danger d-flex align-items-center justify-content-center google--login--text google--login gap-3">
                            <span>
                                <i class="fa-brands fa-google fs-5"></i>
                            </span>
                            {{ translate('Continue with google')}}
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

@push('script-include')
    @if($credentials && \Illuminate\Support\Arr::get($credentials, 'recaptcha_status', '1') == 1)
        <script src="https://www.google.com/recaptcha/api.js"></script>
        <script>
            'use strict'
            function onSubmit(token) {
                console.log(token);
                document.getElementById("login-form").submit();
            }
        </script>
    @endif
@endpush
