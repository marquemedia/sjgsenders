@extends('admin.layouts.auth')
@php
   $panel_logo =  $general->panel_logo ?? "panel_logo.png";
   $admin_card =  $general->admin_card ?? "admin_card.png";
   $admin_bg   =  $general->admin_bg ?? "admin_bg.png";
@endphp


@push('style-push')
    <style>
        section:after {
            background-image: url("{{showImage(filePath()['admin_bg']['path'].'/'.$admin_bg)}}");
        }
    </style>
@endpush

@section('content')

<div class="login-content">
    <div class="login-left">
        <img src="{{showImage(filePath()['admin_card']['path'].'/'.$admin_card)}}" alt="">
    </div>

    <form action="{{route('admin.password.reset.update')}}" method="POST">
        @csrf
         <input type="hidden" name="token" value="{{$passwordToken}}">
        <div class="logo">
            <img src="{{showImage(filePath()['panel_logo']['path'].'/'.$panel_logo)}}" alt="logo">
            <h3>{{ translate('Admin Password Reset')}}</h3>
        </div>
        <div class="input-field password">
             <i class="fas fa-lock"></i>
            <input type="password"  name="password" id="login-email" placeholder="{{ translate('Enter Password')}}">
        </div>
        <div class="input-field password">
             <i class="fas fa-lock"></i>
            <input type="password"  name="password_confirmation" id="login-email" placeholder="{{ translate('Enter Confirm Password')}}">
        </div>
        <div class="forgot-pass">
            <a href="{{route('admin.login')}}">{{ translate('Login')}}?</a>
        </div>
        <button type="submit" class="btn-login">{{ translate('Reset Password')}}</button>
    </form>
</div>
@endsection
