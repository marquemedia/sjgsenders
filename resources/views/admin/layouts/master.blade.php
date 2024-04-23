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
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">


    @stack('style-include')
    @stack('style-push')
    @include('partials.theme')
</head>
<body>

    @yield('content')
    <script src="{{asset('assets/theme/global/js/jquery-3.6.0.min.js')}}"></script>
    <script src="{{asset('assets/theme/global/js/bootstrap.bundle.min.js')}}"></script>
    <script src="{{asset('assets/theme/global/js/all.min.js')}}"></script>
    <script src="{{asset('assets/theme/global/js/helper.js')}}"></script>
    <script src="{{asset('assets/theme/global/js/whatsapp.js')}}"></script>
    <script src="{{asset('assets/theme/global/js/toastr.js')}}"></script>
    <script src="{{asset('assets/theme/admin/js/select2.min.js')}}"></script>
    <script src="{{asset('assets/theme/admin/js/apexcharts.js')}}"></script>
    <script src="{{asset('assets/theme/admin/js/ckd.js')}}"></script>
    <script src="{{asset('assets/theme/admin/js/simplebar.min.js')}}"></script>
    <script src="{{asset('assets/theme/admin/js/datepicker/datepicker.min.js')}}"></script>
    <script src="{{asset('assets/theme/admin/js/datepicker/datepicker.en.js')}}"></script>
    <script src="{{asset('assets/theme/admin/js/script.js')}}"></script>
    <script src="{{asset('assets/theme/admin/js/summernote-lite.min.js')}}"></script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    @include('partials.notify')
    @stack('script-include')
    @stack('script-push')

    <script type="text/javascript">
        'use strict';
        function changeLang(val){
            window.location.href = "{{route('home')}}/language/change/"+val;
        }
        $(".active").focus();

        var checkboxes = document.querySelectorAll(".form-check-input");


        if (checkboxes) {
          var statusUpdateBtn = document.querySelector(".statusUpdateBtn");

            for (var i = 0; i < checkboxes.length; i++) {

                checkboxes[i].addEventListener("click", function () {
                    var checked = false;
                      for (var j = 0; j < checkboxes.length; j++) {
                        if (checkboxes[j].checked) {
                          checked = true;
                          break;
                        }
                      }
                    if (checked) {
                        statusUpdateBtn.classList.remove('d-none');
                    } else {
                        statusUpdateBtn.classList.add('d-none');
                    }
                });
            }
        }


            $('.single-audience').on('click', function(){
                $(".note-message").hide();
                $(".single-audience-note").show();


            });
            $('.group-audience').on('click', function(){
                $(".note-message").hide();
                $(".group-audience-note").show();
                $(".group-audience-note").removeClass('d-none');


            });
            $('.import-file').on('click', function(){
                $(".note-message").hide();
                $(".import-file-note").show();
                $(".import-file-note").removeClass('d-none');


            });
            $('.schedule-date').on('click', function(){
                $(".note-message").hide();
                $(".schedule-date-note").show();
                $(".schedule-date-note").removeClass('d-none');

            });
            $('.message').on('click', function(){
                $(".note-message").hide();
                $(".message-note").show();
                $(".message-note").removeClass('d-none');

            });
            $('.message-type').on('click', function(){
                $(".note-message").hide();
                $(".message-type-note").show();
                $(".message-type-note").removeClass('d-none');

            });
            $('.message-media').on('click', function() {

                $(".note-message").hide();
                $(".message-media-note").show();
                $(".message-media-note").removeClass('d-none');
            });
            $('.mail-subject').on('click', function() {

                $(".note-message").hide();
                $(".mail-subject-note").show();
                $(".mail-subject-note").removeClass('d-none');
            });
            $('.mail-send-from').on('click', function() {

                $(".note-message").hide();
                $(".mail-send-from-note").show();
                $(".mail-send-from-note").removeClass('d-none');
            });
            $('.mail-send-email').on('click', function() {

                $(".note-message").hide();
                $(".mail-send-email-note").show();
                $(".mail-send-email-note").removeClass('d-none');
            });
            $('.campaign-name').on('click', function() {

                $(".note-message").hide();
                $(".campaign-name-note").show();
                $(".campaign-name-note").removeClass('d-none');
            });
            $('.repeat-unit').on('click', function() {

                $(".note-message").hide();
                $(".repeat-unit-note").show();
                $(".repeat-unit-note").removeClass('d-none');
            });
            $('.repeat-scale').on('click', function() {

                $(".note-message").hide();
                $(".repeat-scale-note").show();
                $(".repeat-scale-note").removeClass('d-none');
            });
            $('.campaign-status').on('click', function() {

                $(".note-message").hide();
                $(".campaign-status-note").show();
                $(".campaign-status-note").removeClass('d-none');
            });
            $('.select-mail-gateway').on('click', function() {

                $(".note-message").hide();
                $(".select-mail-gateway-note").show();
                $(".select-mail-gateway-note").removeClass('d-none');
            });

            $('.select-sms-gateway').on('click', function() {

                $(".note-message").hide();
                $(".select-sms-gateway-note").show();
                $(".select-sms-gateway-note").removeClass('d-none');
            });

            $('.select-android-gateway').on('click', function() {

                $(".note-message").hide();
                $(".select-sms-android-gateway-note").show();
                $(".select-sms-android-gateway-note").removeClass('d-none');
            });
    </script>
</body>
</html>
