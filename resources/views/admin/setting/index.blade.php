@extends('admin.layouts.app')
@section('panel')
<section>
	<div class="container-fluid p-0">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">
                    {{translate('General Setting')}}
                </h4>
                <div class="d-flex align-items-center flex-wrap gap-3">
                    <div>
                        {{translate('Last time cron job run')}}<i class="las la-arrow-right"></i><span class="text--success"> {{getDateTime($general->cron_job_run)}}</span>
                    </div>
                    <a href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#cronjob" class="i-btn primary--btn btn--sm"><i class="las la-key"></i> {{translate('Setup Cron Jobs')}}</a>
                </div>
            </div>

            <div class="card-body">
                <form action="{{route('admin.general.setting.store')}}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="form-wrapper">
                        <h6 class="form-wrapper-title">{{translate('Site Setting')}}</h6>
                        <div class="row g-4">
                            <div class="col-xl-3  col-md-6">
                                <div class="form-item">
                                    <label for="site_name" class="form-label">{{translate('Site Name')}} <sup class="text--danger">*</sup></label>
                                    <input type="text" name="site_name" id="site_name" class="form-control" value="{{$general->site_name}}" placeholder="{{translate('Enter Site Name')}}" required>
                                </div>
                            </div>

                            <div class="col-xl-3 col-md-6">
                                <div class="form-item">
                                    <label for="copyright" class="form-label">{{translate('Copyright Text')}} <sup class="text--danger">*</sup></label>
                                    <div class="input-group">
                                        <input type="text" id="copyright" name="copyright" value="{{$general->copyright}}" class="form-control" placeholder="{{translate('Enter Copyright Text')}}" aria-label="Username" aria-describedby="basic-addon1">
                                    </div>
                                </div>
                            </div>

                            <div class="col-xl-3 col-md-6">
                                <div class="form-item">
                                    <label for="primary_color" class="form-label">
                                        {{translate('Primary Color')}}
                                        <sup class="text--danger">*</sup>
                                    </label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <input type='text' class="input-group-text color-picker" value="{{$general->primary_color}}"/>
                                        </div>
                                        <input type="text" class="form-control color-code" name="primary_color" id="primary_color" value="{{$general->primary_color}}"/>
                                    </div>
                                </div>
                            </div>

                            <div class="col-xl-3 col-md-6">
                                <div class="form-item">
                                    <label for="secondary_color" class="form-label">
                                        {{translate('Secondary Color')}}
                                        <sup class="text--danger">*</sup>
                                    </label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <input type='text' class="input-group-text color-picker" value="{{$general->secondary_color}}"/>
                                        </div>
                                        <input type="text" class="form-control color-code" name="secondary_color" id="secondary_color" value="{{$general->secondary_color}}"/>
                                    </div>
                                </div>
                            </div>
                            <div class="col-xl-3 col-md-6">
                                <div class="form-item">
                                    <label for="panel_logo" class="form-label">{{translate('Panel Logo')}}</label> <sup class="text--danger">{{translate('150 X 60')}}</sup>
                                    <input type="file" name="panel_logo" id="panel_logo" class="form-control">
                                </div>
                            </div>

                            <div class="col-xl-3 col-md-6">
                                <div class="form-item">
                                    <label for="site_logo" class="form-label">{{translate('Site Logo')}}</label> <sup class="text--danger">{{translate('150 X 60')}}</sup>
                                    <input type="file" name="site_logo" id="site_logo" class="form-control">
                                </div>
                            </div>

                            <div class="col-xl-3 col-md-6">
                                <div class="form-item">
                                    <label for="site_favicon" class="form-label">{{translate('Favicon')}}</label> <sup class="text--danger">{{translate('60 X 60')}}</sup>
                                    <input type="file" name="site_favicon" id="site_favicon" class="form-control">
                                </div>
                            </div>

                            <div class="col-xl-3 col-md-6">
                                <div class="form-item">
                                    <label for="site_icon" class="form-label">{{translate('Site Icon')}}</label> <sup class="text--danger">{{translate('80 X 80')}}</sup>
                                    <input type="file" name="site_icon" id="site_icon" class="form-control">
                                </div>
                            </div>

                            <div class="col-xl-3 col-md-6">
                                <div class="switch-container">
                                    <label class="form-check-label" for="cron_pop_up">{{translate('Turn On/Off In Dashboard Popup notification')}}</label>
                                    <label class="switch">
                                        <input type="checkbox" value="true" name="cron_pop_up" type="checkbox" id="cron_pop_up" {{$general->cron_pop_up=="true" ? "checked" : ""}}>
                                        <span class="slider"></span>
                                    </label>
                                </div>
                            </div>

                            <div class="col-xl-3 col-md-6">
                                <div class="switch-container">
                                    <label class="form-check-label" for="debug_mode">{{translate('Debug Mode For Developing Purpose')}}</label>
                                    <label class="switch">
                                        <input type="checkbox" value="true" name="debug_mode" type="checkbox" id="debug_mode" {{env('APP_DEBUG')=="true" ? "checked" : ""}}>
                                        <span class="slider"></span>
                                    </label>
                                </div>
                            </div>

                            <div class="col-xl-3 col-md-6">
                                <div class="switch-container">
                                    <label class="form-check-label" for="maintenance_mode">{{translate('Maintenance Mode For Site Maintenance')}}</label>
                                    <label class="switch">
                                        <input type="checkbox" value="true" name="maintenance_mode" type="checkbox" id="maintenance_mode" {{$general->maintenance_mode=="true" ? "checked" : ""}}>
                                        <span class="slider"></span>
                                    </label>
                                </div>
                            </div>

                            <div class="col-xl-3 col-md-6">
                                <div class="switch-container">
                                    <label class="form-check-label" for="landing_page">{{translate('Turn On/Off Landing Page')}}</label>
                                    <label class="switch">
                                        <input title="If turned off users will navigate to log in page directly" type="checkbox" value="true" name="landing_page" type="checkbox" id="landing_page" {{$general->landing_page=="true" ? "checked" : ""}}>
                                        <span class="slider"></span>
                                    </label>
                                </div>
                            </div>


                            <div class="col-12" id="maintenance_mode_div"
                                @if($general->maintenance_mode != "true") style="display:none" 	@endif>
                                <div class="form-item">
                                    <label for="maintenance_mode_message" class="form-label">{{translate('Maintenance Mode Message')}}
                                        <sup class="text--danger">*</sup></label>
                                    <input type="text" name="maintenance_mode_message" id="maintenance_mode_message" class="form-control" value="{{$general->maintenance_mode_message}}" placeholder="{{translate('Write some message for maintenance mode page')}}">
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="form-wrapper">
                        <h6 class="form-wrapper-title">{{translate('Others Setting')}}</h6>
                        <div class="row g-4">
                            <div class="col-xl-3  col-md-6">
                                <div class="form-item">
                                    <label for="timelocation" class="form-label">{{translate('Time Zone')}} <sup class="text--danger">*</sup></label>
                                    <select class="form-control" id="timelocation" name="timelocation" required="">
                                        @foreach($timeLocations as $timeLocation)
                                            <option value="'{{ @$timeLocation}}'" @if(config('app.timezone') == $timeLocation) selected @endif>{{$timeLocation}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="col-xl-3  col-md-6">
                                <div class="form-item">
                                    <label for="country_code" class="form-label"> {{translate('Country Code')}} <sup class="text--danger">*</sup></label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text" id="country--dial--code">
                                            {{$general->country_code}}
                                            </span>
                                        </div>
                                        <select name="country_code" class="form-select" id="country_code">
                                        <@foreach($countries as $countryData)
                                            <option value="{{$countryData->dial_code}}" @if($general->country_code == $countryData->dial_code) selected="" @endif>{{$countryData->country}}</option>
                                        @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="col-xl-3  col-md-6">
                                <div class="form-item">
                                    <label for="currency_name" class="form-label">{{translate('Currency')}} <sup class="text--danger">*</sup></label>
                                    <input type="text" name="currency_name" id="currency_name" class="form-control" value="{{$general->currency_name}}" placeholder="{{translate('Enter Currency Name')}}" required>
                                </div>
                            </div>

                            <div class="col-xl-3  col-md-6">
                                <div class="form-item">
                                    <label for="currency_symbol" class="form-label">{{translate('Currency Symbol')}} <sup class="text--danger">*</sup></label>
                                    <input type="text" name="currency_symbol" id="currency_symbol" class="form-control" value="{{$general->currency_symbol}}" placeholder="{{translate('Enter Currency Symbol')}}" required>
                                </div>
                            </div>


                            <div class="col-xl-3  col-md-6">
                                <div class="form-item">
                                    <label for="registration_status" class="form-label">{{translate('User Registration')}} <sup class="text--danger">*</sup></label>
                                    <select class="form-select" id="registration_status" name="registration_status" required="">
                                        <option value="1" @if($general->registration_status == 1) selected @endif>{{translate('ON')}}</option>
                                        <option value="2" @if($general->registration_status == 2) selected @endif>{{translate('OFF')}}</option>
                                    </select>
                                </div>
                            </div>

                            <div class="col-xl-3  col-md-6">
                                <div class="form-item">
                                    <label for="email_verification_status" class="form-label">{{translate('Email Verification Status')}} <sup class="text--danger">*</sup></label>
                                    <select class="form-select" id="email_verification_status" name="email_verification_status" required="">
                                        <option value="1" @if($general->email_verification_status == 1) selected @endif>{{translate('ON')}}</option>
                                        <option value="2" @if($general->email_verification_status == 2) selected @endif>{{translate('OFF')}}</option>
                                    </select>
                                </div>
                            </div>

                            <div class="col-xl-3  col-md-6">
                                <div class="form-item">
                                    <label for="sign_up_bonus" class="form-label">{{translate('Signup Bonus Status')}} <sup class="text--danger">*</sup></label>
                                    <select class="form-select" id="sign_up_bonus" name="sign_up_bonus" required="">
                                        <option value="1" @if($general->sign_up_bonus == 1) selected @endif>{{translate('ON')}}</option>
                                        <option value="2" @if($general->sign_up_bonus == 2) selected @endif>{{translate('OFF')}}</option>
                                    </select>
                                </div>
                            </div>

                            <div class="col-xl-3  col-md-6">
                                <div class="form-item">
                                    <label for="plan_id" class="form-label">{{translate('Signup Plan')}} <sup class="text--danger">*</sup></label>
                                    <select class="form-select" id="plan_id" name="plan_id" required="">
                                        @foreach($plans as $plan)
                                            <option value="{{$plan->id}}" @if($plan->id == $general->plan_id) selected @endif>{{$plan->name}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <!-- Group Four -->
                            <div class="col-xl-3  col-md-6">
                                <div class="form-item">
                                    <label for="sms_gateway" class="form-label">{{translate('SMS Gateway')}} <sup class="text--danger">*</sup></label>
                                    <select class="form-select" id="sms_gateway" name="sms_gateway" required="">
                                        <option value="1" @if($general->sms_gateway == 1) selected @endif>{{translate('Api Gateway')}}</option>
                                        <option value="2" @if($general->sms_gateway == 2) selected @endif>{{translate('Android Gateway')}}</option>
                                    </select>
                                </div>
                            </div>

                            <div class="col-xl-3  col-md-6">
                                <div class="form-item">
                                    <label for="whatsapp_credit_count" class="form-label">{{translate('WhatsApp Word Count')}} <sup class="text--danger">*</sup></label>
                                    <div class="input-group">
                                        <span class="input-group-text" id="basic-addon1">{{translate('1 Credit')}} </span>
                                        <input type="text" id="rate" name="whatsapp_word_count" value="{{$general->whatsapp_word_count}}" class="form-control" placeholder="{{translate('Enter number of words')}}" aria-label="Username" aria-describedby="basic-addon1">
                                    </div>
                                </div>
                            </div>

                            <div class="col-xl-3  col-md-6">
                                <div class="form-item">
                                    <label for="whatsapp_credit_count" class="form-label">{{translate('SMS Word Count Plain Text')}} <sup class="text--danger">*</sup></label>
                                    <div class="input-group">
                                        <span class="input-group-text" id="basic-addon1">{{translate('1 Credit')}} </span>
                                        <input type="text" id="rate" name="sms_word_text_count" value="{{$general->sms_word_text_count}}" class="form-control" placeholder="{{translate('Enter number of words')}}" aria-label="Username" aria-describedby="basic-addon1">
                                    </div>
                                </div>
                            </div>


                            <div class="col-xl-3  col-md-6">
                                <div class="form-item">
                                    <label for="whatsapp_credit_count" class="form-label">{{translate('SMS Word Count Unicode')}} <sup class="text--danger">*</sup></label>
                                    <div class="input-group">
                                        <span class="input-group-text" id="basic-addon1">{{translate('1 Credit')}} </span>
                                        <input type="text" id="rate" name="sms_word_unicode_count" value="{{$general->sms_word_unicode_count}}" class="form-control" placeholder="{{translate('Enter number of words')}}" aria-label="Username" aria-describedby="basic-addon1">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <button type="submit" class="i-btn primary--btn btn--lg">{{translate('Submit')}}</button>
                </form>
            </div>
        </div>
	</div>
</section>

<div class="modal fade" id="cronjob" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog  modal-md">
        <div class="modal-content">
            <div class="modal-body">
            	<div class="card">
        			<div class="card-header bg--lite--violet">
            			<div class="card-title text-center text--light">
            				<h6 class="text--light">{{translate('Cron Job Setting')}}</h6>
            				<p>{{translate('Set the cron once every minute this is the ideal time')}}</p>
            			</div>
            		</div>
	                <div class="card-body">
	            		<div class="mb-3">
	            			<label for="queue_url" class="form-label">{{translate('Cron Job ii')}} <sup class="text--danger">* {{translate('Set time for 1 minute')}}</sup></label>
	            			<div class="input-group mb-3">
							  	<input type="text" class="form-control" value="curl -s {{route('queue.work')}}" id="queue_url" aria-describedby="basic-addon2" readonly="">
							 	 <div class="input-group-append pointer">
							    	<span class="input-group-text bg--success text--light" id="basic-addon2" onclick="queue()">{{translate('Copy')}}</span>
							  	</div>
							</div>
	            		</div>
	            		<div class="mb-3">
	            			<label for="cron--run" class="form-label">{{translate('Cron Job i')}} <sup class="text--danger">* {{translate('Set time for 2 minutes')}}</sup></label>
	            			<div class="input-group mb-3">
							  	<input type="text" class="form-control" value="curl -s {{route('cron.run')}}" id="cron--run" aria-describedby="basic-addon2" readonly="">
							 	 <div class="input-group-append pointer">
							    	<span class="input-group-text bg--success text--light" id="basic-addon2" onclick="cronJobRun()">{{translate('Copy')}}</span>
							  	</div>
							</div>
	            		</div>
		            </div>
            	</div>
            </div>
            <div class="modal_button2 modal-footer">
                <div class="d-flex align-items-center justify-content-center">
                    <button type="button" class="i-btn primary--btn btn--md mx-3" data-bs-dismiss="modal">{{translate('Cancel')}}</button>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@push('style-include')
    <link rel="stylesheet" href="{{ asset('assets/theme/admin/css/spectrum.css') }}">
@endpush

@push('script-include')
    <script src="{{ asset('assets/theme/admin/js/spectrum.js') }}"></script>
@endpush

@push('script-push')
    <script>
        "use strict";


        $('select[name=country_code]').on('change', function(){
            var value = $(this).val();
            $("#country--dial--code").text(value);
        });

        $('#maintenance_mode').on('click',function (e) {
            var status = $(this).val();
            if($(this).prop("checked") === true){
                $("#maintenance_mode_div").fadeIn();
            }
            else if($(this).prop("checked") === false){
                $("#maintenance_mode_div").fadeOut();
            }
        })

        function cronJobRun() {
            var copyText = document.getElementById("cron--run");
            copyText.select();
            copyText.setSelectionRange(0, 99999)
            document.execCommand("copy");
            notify('success', 'Copied the text : ' + copyText.value);
        }

        function queue() {
            var copyText = document.getElementById("queue_url");
            copyText.select();
            copyText.setSelectionRange(0, 99999)
            document.execCommand("copy");
            notify('success', 'Copied the text : ' + copyText.value);
        }

        const initColorPicker = (color) => {
            $('.color-picker').spectrum({
                color,
                change: function (color) {
                    $(this).parent().siblings('.color-code').val(color.toHexString().replace(/^#?/, ''));
                }
            });
        };

        const initColorCodeInput = () => {
            $('.color-code').on('input', function () {
                const color_value = $(this).val();
                $(this).parents('.input-group').find('.color-picker').spectrum({
                    color: color_value,
                });
            });
        };

        // Initialize color picker and color code input
        const color = $(this).data('color');
        initColorPicker(color);
        initColorCodeInput();


        $(document).ready(function() {
        
            checkSignUpBonusStatus();
            $('#sign_up_bonus').on('change', function() {
                checkSignUpBonusStatus();
            });
            function checkSignUpBonusStatus() {
                
                var signUpBonusStatus = $('#sign_up_bonus').val();
                if (signUpBonusStatus == 2) {
                    
                    $('#plan_id').prop('disabled', true);
                } else {
                    
                    $('#plan_id').prop('disabled', false);
                }
            }
        });
    </script>
@endpush

@push('style-push')
    <style>
        .sp-preview-inner {
            width: 100px;
        }

        .sp-replacer {
            padding: 0;
            margin: 0;
            border-right: none;
            border: 2px solid rgba(0, 0, 0, .125);
            border-radius: 5px 0 0 5px;
            height: 39.1px;
        }

        .sp-preview {
            border: 1px;
            width: 90px;
            height: 45px;
        }

        .sp-dd {
            display: none;
        }
    </style>
@endpush
