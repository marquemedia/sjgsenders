<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta http-equiv="Content-Security-Policy" content="upgrade-insecure-requests" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>{{$general->site_name}} - {{@$title}}</title>
    @php
        $fav_icon = $general->favicon ?  $general->favicon : "site_favicon.png"
    @endphp
    <link rel="shortcut icon" href="{{showImage(filePath()['site_logo']['path'].'/'.$fav_icon)}}" type="image/x-icon">
    <link rel="stylesheet" href="{{asset('assets/theme/global/css/bootstrap.min.css')}}">
    <link rel="stylesheet" href="{{asset('assets/theme/global/css/line-awesome.min.css')}}">
    <link rel="stylesheet" href="{{asset('assets/theme/global/css/toastr.css')}}">
    <link rel="stylesheet" href="{{asset('assets/theme/auth/css/style.css')}}">
    <link rel="stylesheet" href="{{asset('assets/theme/auth/css/responsive.css')}}">
    @stack('script-include')
</head>
<body>

<div class="login-page-container">
    <div class="container-fluid p-0">
        <div class="row g-0 overflow-hidden">
            @yield('content')
            <div class="col-xl-7 col-lg-6">
                <div class="login-right-section responsive-padding bg-purple d-flex align-items-center justify-content-center">
                    <div class="login-right-content">
                        <h1>{{translate('Welcome to')}} {{$general->site_name}}</h1>
                        <p>{{@$general->frontend_section->sub_heading}}</p>
                        @if(count($users)>5)
                            <div class="users">
                                @foreach($users as $user)
                                    <div class="user">
                                        <img src="{{showImage('assets/file/images/user/profile/'.$user->image)}}" alt="{{$user->name}}" class="w-100 h-100"/>
                                    </div>
                                @endforeach
                                <i class="fas fa-arrow-right fs-3 ms-3"></i>
                            </div>
                            <span>{{@$general->frontend_section->heading}}</span>
                        @endif
                        <div class="text-start mt-5">
                            <a href="{{route('home')}}" class="btn btn-sm btn--primary d-flex align-items-center justify-content-center gap-2 lh-1 back-to-home"><i class="las la-long-arrow-alt-left fs-3"></i> {{translate('Back To Home')}} </a>
                        </div>
                    </div>

                    <div class="user-login-bg">
                        <img src="https://img.freepik.com/free-vector/watercolor-stains-abstract-background_23-2149107181.jpg?w=1380&t=st=1697876324~exp=1697876924~hmac=e56ceb0be0b7f2e52411a3b44051683e739d9a769e5a39321506a064ef9b7a4b" alt="">
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="{{asset('assets/theme/global/js/jquery-3.6.0.min.js')}}"></script>
<script src="{{asset('assets/theme/global/js/bootstrap.bundle.min.js')}}"></script>
<script src="{{asset('assets/theme/global/js/all.min.js')}}"></script>
<script src="{{asset('assets/theme/global/js/toastr.js')}}"></script>


@include('partials.notify')
</body>
</html>
