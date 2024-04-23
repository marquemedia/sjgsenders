<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta http-equiv="Content-Security-Policy" content="upgrade-insecure-requests" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{csrf_token()}}" />
    <meta name="bee-endpoint" content="https://auth.getbee.io/apiauth">
    <meta name="base-url" content="{{ url('') }}">
    <meta name="bee-client-id" content="{{ @json_decode($general->bee_plugin,true)['client_id'] }}">
    <meta name="bee-client-secret" content="{{ @json_decode($general->bee_plugin,true)['client_secret'] }}">
    <title>{{@$general->site_name}} - {{@$title}}</title>
    @php
      $fav_icon = $general->favicon ?? "site_favicon.png"
    @endphp

    <link rel="shortcut icon" href="{{showImage(filePath()['site_logo']['path'].'/'.$fav_icon)}}" type="image/x-icon">
    <link rel="stylesheet" href="{{asset('assets/theme/global/css/bootstrap.min.css')}}">
    <link rel="stylesheet" href="{{asset('assets/theme/global/css/all.min.css')}}">
    <link rel="stylesheet" href="{{asset('assets/theme/global/css/line-awesome.min.css')}}">
    <link rel="stylesheet" href="{{asset('assets/theme/global/css/font_bootstrap-icons.css')}}">
    <link rel="stylesheet" href="{{asset('assets/theme/global/css/plugin.css')}}">
    <link rel="stylesheet" href="{{asset('assets/theme/global/css/toastr.css')}}">
    <link rel="stylesheet" href="{{asset('assets/theme/admin/css/select2.min.css')}}">
    <link rel="stylesheet" href="{{asset('assets/theme/admin/css/apexcharts.css')}}">
    <link rel="stylesheet" href="{{asset('assets/theme/admin/css/datepicker/datepicker.min.css')}}">
    <link rel="stylesheet" href="{{asset('assets/theme/admin/css/simplebar.css')}}">
    <link rel="stylesheet" href="{{asset('assets/theme/admin/css/style.css')}}">
    <link rel="stylesheet" href="{{asset('assets/theme/admin/css/responsive.css')}}">
    <link rel="stylesheet" href="{{asset('assets/theme/admin/css/summernote-lite.min.css')}}">
    <link rel="stylesheet" href="{{asset('assets/theme/admin/flag-icons/flag-icons.css')}}">

    @stack('style-include')
    @stack('style-push')
</head>
<body>
    <div class="update-wrapper">
        <div class="container">
            @yield('content')
        </div>
        <div class="update-bg">
            <img class="" src="{{ asset('assets/file/default/update.jpg') }}" alt="">
        </div>
    </div>
    
   
    <script src="{{asset('assets/theme/global/js/jquery-3.6.0.min.js')}}"></script>
    <script src="{{asset('assets/theme/global/js/bootstrap.bundle.min.js')}}"></script>
    <script src="{{asset('assets/theme/global/js/all.min.js')}}"></script>
    <script src="{{asset('assets/theme/global/js/toastr.js')}}"></script>
    <script src="{{asset('assets/theme/admin/js/select2.min.js')}}"></script>
    <script src="{{asset('assets/theme/admin/js/apexcharts.js')}}"></script>
    <script src="{{asset('assets/theme/admin/js/ckd.js')}}"></script>
    <script src="{{asset('assets/theme/admin/js/simplebar.min.js')}}"></script>
    <script src="{{asset('assets/theme/admin/js/datepicker/datepicker.min.js')}}"></script>
    <script src="{{asset('assets/theme/admin/js/datepicker/datepicker.en.js')}}"></script>
    <script src="{{asset('assets/theme/admin/js/script.js')}}"></script>
    <script src="{{asset('assets/theme/admin/js/summernote-lite.min.js')}}"></script>

    @include('partials.notify')
    @stack('script-include')
    @stack('script-push')
</body>
</html>
