<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{@$general->site_name}} - {{@$title}}</title>
    @php
      $fav_icon = $general->favicon ?? "site_favicon.png"
    @endphp
    <link rel="shortcut icon" href="{{showImage(filePath()['site_logo']['path'].'/'.$fav_icon)}}" type="image/x-icon">
    <link rel="stylesheet" type="text/css" href="{{asset('assets/theme/admin/auth/css/style.css')}}">
    <link rel="stylesheet" href="{{asset('assets/theme/global/css/toastr.css')}}">
</head>
@stack('style-push')
<body>

    <section class="admin-form">
        <div class="form-container">
            @yield('content')
        </div>
    </section>
    <div class="squire-container">
        <ul class="squares"></ul>
    </div>
    <script src="{{asset('assets/theme/global/js/jquery-3.6.0.min.js')}}"></script>
    <script src="{{asset('assets/theme/admin/auth/js/script.js')}}"></script>
    <script src="{{asset('assets/theme/admin/auth/js/fontAwesome.js')}}"></script>
    <script src="{{asset('assets/theme/global/js/toastr.js')}}"></script>
    @include('partials.notify')
</body>
</html>
