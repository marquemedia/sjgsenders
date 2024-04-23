@extends('admin.layouts.app')
@section('panel')
<section>
	<div class="container-fluid p-0">
		<div class="card">
			<div class="card-body">
				<form action="{{route('admin.plan.update')}}" method="POST" enctype="multipart/form-data">
					@csrf
					<input hidden type="text" name="id" value="{{$plan->id}}">
					<div class="form-wrapper">
						<div class="form-wrapper-title">
							<h6>{{translate($title)}}</h6>
						</div>
						<div class="row">
							<div class="mb-3 col-sm-6">
								<label for="name" class="form-label"> {{ translate('Name')}} <sup class="text--danger">*</sup></label>
								<input type="text" name="name" id="name" value="{{ $plan->name }}" class="form-control" placeholder=" {{ translate('Enter Name')}}" required="">
							</div>
							<div class="mb-3 col-sm-6">
								<label for="duration" class="form-label">{{ translate('Duration')}} <sup class="text--danger">*</sup></label>
								<div class="input-group">
									<input type="text" class="form-control" id="duration" value="{{ $plan->duration }}" name="duration" placeholder="{{ translate('Enter Duration')}}" aria-label="Recipient's username" aria-describedby="basic-addon2">
									<span class="input-group-text" id="basic-addon2">{{ translate('Days')}}</span>
								</div>
							</div>

							<div class="mb-3 col-sm-12">
								<label for="description" class="form-label">{{ translate('Plan Description')}} <sup class="text--danger">*</sup></label>
								<textarea type="text" class="form-control" id="description" name="description" placeholder="{{ translate('Type plan description')}}">{{ $plan->description }}</textarea>
							</div>
							
							<div class="col-xl-4 col-sm-6 mb-3">
								<label for="amount" class="form-label">{{ translate('Amount')}} <sup class="text--danger">*</sup></label>
								<div class="input-group">
									<input type="text" class="form-control" value="{{ $plan->amount }}" id="amount" name="amount" placeholder="{{ translate('Enter Amount')}}" aria-label="Recipient's username" aria-describedby="basic-addon2">
									<span class="input-group-text" id="basic-addon2">{{$general->currency_name}}</span>
								</div>
							</div>
							<div class="col-xl-4 col-sm-6 mb-3">
								<label for="status" class="form-label">{{ translate('Status')}} <sup class="text--danger">*</sup></label>
								<select class="form-select" name="status" id="status" required>
									<option value="1" {{ $plan->status == 1 ? 'selected' : ' ' }}>{{ translate('Active')}}</option>
									<option value="2" {{ $plan->status == 2 ? 'selected' : ' ' }}>{{ translate('Inactive')}}</option>
								</select>
							</div>
							<div class="col-xl-4 col-sm-12 mb-3" title="{{ translate("If you turn on this option everytime user tries to renew this plan their remaining credits will be added") }}">
								<label for="allow_carry_forward" class="form-label">{{ translate('Carry Forward')}} <sup class="text--danger">*</sup></label>
								<div class="switch-container">
                                    <label class="form-check-label" for="allow_carry_forward">{{translate('Turn on/off ')}}<strong>{{translate(' Manual Renewals ')}}</strong>{{translate(' of this plan')}}</label>
                                    <label class="switch">
                                        <input type="checkbox" value="true" name="allow_carry_forward" type="checkbox" id="allow_carry_forward" {{ $plan->carry_forward == App\Models\PricingPlan::ENABLED ? "checked" : ""}}>
                                        <span class="slider"></span>
                                    </label>
                                </div>
							</div>
							<div class="col-sm-12 my-3">
								<div class="switch-container bg---light py-3">
                                    <label class="form-check-label" for="allow_admin_creds">{{translate('Allow users to use ')}}<strong class="text--primary">{{translate('Admin Gateways or Devices')}}</strong></label>
                                    <label class="switch">
                                        <input type="checkbox" value="true" name="allow_admin_creds" type="checkbox" id="allow_admin_creds" {{ $plan->type == App\Models\PricingPlan::ADMIN ? "checked" : "" }}>
                                        <span class="slider"></span>
                                    </label>
                                </div>
							</div>
							<div class="card-body admin-items d-none">
								<div class="row">
									<div class="col-lg-6">
										<div class="form-wrapper border--dark">
											<div class="form-wrapper-title mb-3">{{ translate("Admin's SMS Section") }}</div>
											<div class="row">
												<div class="mb-3 col-lg-12 col-xxl-6">
													<div class="switch-container">
														<label class="form-check-label" for="allow_admin_sms">{{translate("Admin's  ")}}<strong>{{translate("SMS Gateways")}}</strong>
															<br>
															<p class="relative-note"><sup class="text--danger">*</sup>{{ translate("Enable users to use Admin's SMS Gateways") }}</p>
														</label>
														<label class="switch">
															<input class="allow_admin_sms" type="checkbox" value="true" name="allow_admin_sms" type="checkbox" id="allow_admin_sms" {{$plan->sms->is_allowed == true ? "checked" : ""}}>
															<span class="slider"></span>
														</label>
													</div>
												</div>
												<div class="mb-3 col-lg-12 col-xxl-6">
													<div class="switch-container">
														<label class="form-check-label" for="allow_admin_android">{{translate("Admin's ")}}<strong>{{translate("Android Gateways")}}</strong>
															<br>
															<p class="relative-note"><sup class="text--danger">*</sup>{{ translate("Enable users to use Admin's Android Gateways") }}</p>
														</label>
														<label class="switch">
															<input class="allow_admin_sms" type="checkbox" value="true" name="allow_admin_android" type="checkbox" id="allow_admin_android" {{ $plan->sms->android->is_allowed == true ? "checked" : "" }}>
															<span class="slider"></span>
														</label>
													</div>
												</div>
											</div>
											<div class="row">
												<div class="col-lg-12 mb-3 admin-sms-credit">
													<label for="sms_credit_ga" class="form-label">{{ translate('SMS Credit Limit')}} <sup class="text--danger">*</sup></label>
													<div class="input-group">
														<input type="text" class="form-control" id="sms_credit_ga" name="sms_credit_ga" placeholder="{{ translate('Enter SMS Credit')}}" aria-label="Recipient's username" value="{{ $plan->sms->credits }}" aria-describedby="basic-addon2" >
														<span class="input-group-text" id="basic-addon2">{{ translate('Credit')}}</span>
													</div>
												</div>
											</div>
										</div>
									</div>
									<div class="col-lg-6">
										<div class="form-wrapper border--dark">
											<div class="form-wrapper-title mb-3">{{ translate("Admin's E-mail Section") }}</div>
											<div class="row">
												<div class="mb-3 col">
													<div class="switch-container">
														<label class="form-check-label" for="allow_admin_email">{{translate("Admin's ")}}<strong>{{translate("Email Gateways")}}</strong>
															<br>
															<p class="relative-note"><sup class="text--danger">*</sup>{{ translate("Enable users to use Admin's Email Gateways") }}</p>
														</label>
														<label class="switch">
															<input class="allow_admin_email" type="checkbox" value="true" name="allow_admin_email" type="checkbox" id="allow_admin_email" {{ $plan->email->is_allowed == true ? "checked" : "" }}>
															<span class="slider"></span>
														</label>
													</div>
												</div>
												
											</div>
											<div class="row">
												<div class="col mb-3 admin-email-credit">
													<label for="email_credit_ga" class="form-label">{{ translate('Email Credit Limit')}} <sup class="text--danger">*</sup></label>
													<div class="input-group">
														<input value="{{ $plan->email->credits }}" type="text" class="form-control" id="email_credit_ga" name="email_credit_ga" placeholder="{{ translate('Enter Email Credit')}}" aria-label="Recipient's username" aria-describedby="basic-addon2" >
														<span class="input-group-text" id="basic-addon2">{{ translate('Credit')}}</span>
													</div>
												</div>
											</div>
										</div>
									</div>
									<div class="col-lg-12">
										<div class="form-wrapper border--dark">
											<div class="form-wrapper-title mb-3">{{ translate("Admin's Whatsapp Section") }}</div>
											<div class="row">
												<div class="mb-3 col-lg-12 admin_whatsapp">
													<div class="switch-container">
														<label class="form-check-label" for="allow_admin_whatsapp">{{translate('Allow users to add ')}}<strong>{{translate('Whatsapp Devices')}}</strong>
															<br>
															<p class="relative-note"><sup class="text--danger">*</sup>{{ translate("Enable unlimited Devices if you set 'Whatsapp Device Limit' value to '0'")}}</p>
														</label>
														
														<label class="switch">
															<input class="allow_admin_whatsapp" type="checkbox" value="true" name="allow_admin_whatsapp" type="checkbox" id="allow_admin_whatsapp" {{ $plan->whatsapp->is_allowed == true ? "checked" : "" }}>
															<span class="slider"></span>
														</label>
													</div>
												</div>
												
											</div>
											<div class="row">
												<div class="col-lg-6 mb-3 admin-whatsapp-credit">
													<label for="whatsapp_credit_ga" class="form-label">{{ translate('Whatsapp Credit Limit')}} <sup class="text--danger">*</sup></label>
													<div class="input-group">
														<input value="{{ $plan->whatsapp->credits }}" type="text" class="form-control" id="whatsapp_credit_ga" name="whatsapp_credit_ga" placeholder="{{ translate('Enter Whatsapp Credit')}}" aria-label="Recipient's username" aria-describedby="basic-addon2" >
														<span class="input-group-text" id="basic-addon2">{{ translate('Credit')}}</span>
													</div>
												</div>
												<div class="col-lg-6 mb-3 d-none">
													<label for="whatsapp_credit" class="form-label">{{ translate('Whatsapp Device Limit')}} <sup class="text--danger">*</sup></label>
													<div class="input-group">
														<input value="{{ $plan->whatsapp->gateway_limit ?? "0" }}" type="number" class="form-control" id="whatsapp_device_limit" name="whatsapp_device_limit" placeholder="{{ translate('Users can Add upto')}}" aria-label="Whatsapp Device Limit" aria-describedby="basic-addon2">
														<span class="input-group-text" id="basic-addon2">{{ translate('Whatsapp Device Limit')}}</span>
													</div>
												</div>
											</div>
										</div>
									</div>
								</div>
							</div>
							<div class="card-body user-items">
								<div class="row">
									<div class="col-lg-12">
										<div class="form-wrapper border--dark">
											<div class="form-wrapper-title">{{ translate("User: SMS Section") }}</div>
											<div class="row">
												<div class="col-lg-12 col-xxl-6">
													<div class="row">
														<div class="mb-3 col-lg-12 user_android">
															<div class="switch-container">
																<label class="form-check-label" for="allow_user_android">{{translate('Allow users to add ')}}<strong>{{translate('Android Gateways')}}</strong>
																	<br>
																	<p class="relative-note"><sup class="text--danger">*</sup>{{ translate("Enable unlimited Android Gateways if you set the value to '0'") }}</p>
																</label>
																<label class="switch">
																	<input class="allow_user_sms" type="checkbox" value="true" name="allow_user_android" type="checkbox" id="allow_user_android" {{ $plan->sms->android->is_allowed == true ? "checked" : "" }}>
																	<span class="slider"></span>
																</label>
															</div>
														</div>
														<div class="col-lg-12 mb-3 d-none">
															<label for="credit" class="form-label">{{ translate('Android Gateway Limit')}} <sup class="text--danger">*</sup></label>
															<div class="input-group">
																<input value="{{array_key_exists("gateway_limit",(array)$plan->sms->android) && $plan->type == App\Models\PricingPlan::USER ? $plan->sms->android->gateway_limit : ""}}" type="number" class="form-control" id="user_android_gateway_limit" name="user_android_gateway_limit" placeholder="{{ translate('Users can Add upto')}}" aria-label="Android Gateway Limit" aria-describedby="basic-addon2">
																<span class="input-group-text" id="basic-addon2">{{ translate('Android Gateway Limit')}}</span>
															</div>
														</div>
													</div>
												</div>
												<div class="col-lg-12 col-xxl-6">
													<div class="row">
														<div class="mb-3 col-lg-12">
															<div class="switch-container">
																<label class="form-check-label text-capitalize" for="sms_gateway">{{translate('Allow users to make multiple ')}}<strong>{{translate('SMS Gateways')}}</strong>
																	<br>
																	<p class="relative-note"><sup class="text--danger">*</sup>{{translate("Choose the amount of gateways users can create from each SMS gateway type")}}</p>
																</label>
																<label class="switch">
																	<input {{ $plan->sms->is_allowed == true ? 'checked' : ' ' }} type="checkbox" value="true" name="sms_multi_gateway" id="sms_gateway" class="sms_gateway allow_user_sms">
																	<span class="slider"></span>
																</label>
															</div>
														</div>
														<div class="col-lg-12 sms_gateway_options {{ $plan->sms->is_allowed == true ? '' : 'd-none' }} ">
															<label for="credit" class="form-label">{{ translate('SMS Gateway Selection')}} <sup class="text--danger">*</sup></label>
															<div class="row g-3">
																<div class="col-md-9">
																	<select class="form-select" name="sms_gateway_select" id="sms_gateways">
																		<option value="" selected disabled>Select One</option>
																		@foreach($sms_credentials as $sms_credential)
																			<option value="{{$sms_credential}}">{{preg_replace('/[[:digit:]]/','', setInputLabel($sms_credential))}}</option>
																		@endforeach
																	</select>
																</div>
																
																<div class="col-md-3">
																	<a href="javascript:void(0)" class="i-btn primary--btn btn--md border-0 rounded newSmsdata"><i class="las la-plus"></i>  {{ translate('Add New')}}</a>
																</div>
															</div>
															<div class="oldSmsData">
																@if($sms_gateways && $plan->type == App\Models\PricingPlan::USER && $plan->sms->is_allowed == true)
																	@foreach($sms_gateways as $sms_key => $sms_value)
																		<div class="row newSmsdata mt-3">
																			<div class="mb-2 col-lg-5">
																				<input readonly="true" name="sms_gateways[]" class="form-control" value="{{ $sms_key }}" type="text" placeholder="{{ strtoupper($sms_key) }}">
																			</div>
																			<div class="mb-2 col-lg-5">
																				<input name="total_sms_gateway[]" class="form-control" value="{{ $sms_value }}" type="number" placeholder="{{ translate('Total Gateways')}}">
																			</div>
																			<div class="col-lg-2 text-end">
																				<span class="input-group-btn">
																					<button class="i-btn danger--btn btn--md text--light removeSmsBtn" type="button">
																						<i class="fa fa-times"></i>
																					</button>
																				</span>
																			</div>
																		</div>
																	@endforeach
																@endif
															</div>						
															<div class="newSmsDataAdd"></div>
															
														</div>
													</div>
												</div>
											</div>
											<div class="row">
												<div class="col-lg-12 mb-3 user-sms-credit {{ $plan->sms->is_allowed == false ? "d-none" : "" }}">
													<label for="sms_credit_gu" class="form-label">{{ translate('SMS Credit Limit')}} <sup class="text--danger">*</sup></label>
													<div class="input-group">
														<input value="{{ $plan->sms->credits }}" type="text" class="form-control" id="sms_credit_gu" name="sms_credit_gu" placeholder="{{ translate('Enter SMS Credit')}}" aria-label="Recipient's username" aria-describedby="basic-addon2" >
														<span class="input-group-text" id="basic-addon2">{{ translate('Credit')}}</span>
													</div>
												</div>
											</div>
										</div>
									</div>
									<div class="col-lg-12">
										<div class="row">
											<div class="col-lg-12 col-xxl-6">
												<div class="form-wrapper border--dark">
													<div class="form-wrapper-title">{{ translate("User's Email Section") }}</div>
													<div class="row">
														<div class="mb-3 col-lg-12">
															<div class="switch-container">
																<label class="form-check-label text-capitalize" for="multi_gateway">{{translate('Allow users to make multiple ')}}<strong>{{translate('Email Gateways')}}</strong>
																	<br>
																	<p class="relative-note"><sup class="text--danger">*</sup>{{translate("Choose the amount of gateways users can create from each Email gateway type")}}</p>
																</label>
																<label class="switch">
																	<input {{ $plan->email->is_allowed == true ? 'checked' : ' ' }} type="checkbox" value="true" name="mail_multi_gateway" id="multi_gateway" class="multiple_gateway allow_user_email">
																	<span class="slider"></span>
																</label>
															</div>
														</div>
														<div class="col-lg-12 email_gateway_options {{ $plan->email->is_allowed == true ? '' : 'd-none' }} mb-3">
															<label for="credit" class="form-label">{{ translate('Gateway Selection')}} <sup class="text--danger">*</sup></label>
															<div class="row g-3">
																<div class="col-md-9">
																	<select class="form-select" name="mail_gateway_select" id="mail_gateways">
																		<option value="" selected disabled>Select One</option>
																		@foreach($mail_credentials as $mail_credential)
																			<option value="{{strToLower($mail_credential)}}">{{strtoupper($mail_credential)}}</option>
																		@endforeach
																	</select>
																</div>
																	
																<div class="col-md-3">
																	<a href="javascript:void(0)" class="i-btn primary--btn btn--md border-0 rounded newEmailData"><i class="las la-plus"></i>  {{ translate('Add New')}}</a>
																</div>
															</div>		
															<div class="oldEmailData">
																@if($mail_gateways && $plan->type == App\Models\PricingPlan::USER && $plan->email->is_allowed == true)
																	@foreach($mail_gateways as $mail_key => $mail_value)
																		<div class="row newEmaildata mt-3">
																			<div class="mb-2 col-lg-5">
																				<input type="text"  name="mail_gateways[]" class="form-control" value="{{ $mail_key }}" placeholder="{{ strtoupper($mail_key) }}" readonly="true">
																			</div>
																			<div class="mb-2 col-lg-5">
																				<input name="total_mail_gateway[]" class="form-control" value="{{ $mail_value }}" type="number"  placeholder=" {{ translate('Total Gateways')}}">
																			</div>
																			<div class="col-lg-2 text-end">
																				<span class="input-group-btn">
																					<button class="i-btn danger--btn btn--md text--light removeEmailBtn" type="button">
																						<i class="fa fa-times"></i>
																					</button>
																				</span>
																			</div>
																		</div>
																	@endforeach
																@endif
															</div>						
															<div class="newEmailDataAdd"></div>
														</div>
													</div>
													<div class="row">
														<div class="col-lg-12 mb-3 user-email-credit {{ $plan->email->is_allowed == false ? "d-none" : "" }}">
															<label for="email_credit_gu" class="form-label">{{ translate('Email Credit Limit')}} <sup class="text--danger">*</sup></label>
															<div class="input-group">
																<input value="{{ $plan->email->credits }}" type="text" class="form-control" id="email_credit_gu" name="email_credit_gu" placeholder="{{ translate('Enter Email Credit')}}" aria-label="Recipient's username" aria-describedby="basic-addon2" >
																<span class="input-group-text" id="basic-addon2">{{ translate('Credit')}}</span>
															</div>
														</div>
													</div>
												</div>
											</div>
											<div class="col-lg-12 col-xxl-6">
												<div class="form-wrapper border--dark">
													<div class="form-wrapper-title">{{ translate("User's Whatsapp Section") }}</div>
													<div class="row">
														<div class="mb-3 col-lg-12 user_whatsapp">
															<div class="switch-container">
																<label class="form-check-label" for="allow_user_whatsapp">{{translate('Allow users to add ')}}<strong>{{translate('Whatsapp Devices')}}</strong>
																	<br>
																	<p class="relative-note"><sup class="text--danger">*</sup>{{ translate("Enable unlimited Devices if you set the value to '0'") }}</p>
																</label>
																<label class="switch">
																	<input {{ $plan->whatsapp->is_allowed == true ? "checked" : "" }} class="allow_user_whatsapp" type="checkbox" value="true" name="allow_user_whatsapp" type="checkbox" id="allow_user_whatsapp">
																	<span class="slider"></span>
																</label>
															</div>
														</div>
													</div>
													<div class="row">
														<div class="col-lg-12 mb-3 d-none">
															<label for="user_whatsapp_device_limit" class="form-label">{{ translate('Whatsapp Device Limit')}} <sup class="text--danger">*</sup></label>
															<div class="input-group">
																<input value="{{ $plan->whatsapp->gateway_limit }}" type="number" class="form-control" id="user_whatsapp_device_limit" name="user_whatsapp_device_limit" placeholder="{{ translate('Users can Add upto')}}" aria-label="Whatsapp Device Limit" aria-describedby="basic-addon2">
																<span class="input-group-text" id="basic-addon2">{{ translate('Whatsapp Device Limit')}}</span>
															</div>
														</div>
														<div class="col-lg-12 mb-3 user-whatsapp-credit {{ $plan->whatsapp->is_allowed == false ? "d-none" : "" }}">
															<label for="whatsapp_credit_gu" class="form-label">{{ translate('Whatsapp Credit Limit')}} <sup class="text--danger">*</sup></label>
															<div class="input-group">
																<input value="{{ $plan->whatsapp->credits ?? 0}}" type="text" class="form-control" id="whatsapp_credit_gu" name="whatsapp_credit_gu" placeholder="{{ translate('Enter Whatsapp Credit')}}" aria-label="Recipient's username" aria-describedby="basic-addon2" >
																<span class="input-group-text" id="basic-addon2">{{ translate('Credit')}}</span>
															</div>
														</div>
													</div>
												</div>
											</div>
										</div>
									</div>
								</div>
								
							</div>
						</div>
					</div>
					
					<button type="submit" class="i-btn primary--btn btn--md text--light"> {{ translate('Submit')}}</button>
				</form>
			</div>
		</div>
			
	</div>
</section>
@endsection

@push('script-push')
<script>
	(function($){
		"use strict";

			
		$('.newEmailData').on('click', function(){

			
			var mail_gateway = $('#mail_gateways').val();
			
			if ($('.newEmaildata input[value="' + mail_gateway + '"]').length > 0) {
				alert('Row with ' + mail_gateway + ' already exists. Please choose a different mail gateway.');
				return; 
			}
	        var html = `
				<div class="row newEmaildata mt-3">
					<div class="mb-2 col-lg-5">
						<input type="text"  name="mail_gateways[]" class="form-control" value="${mail_gateway}" required placeholder="${mail_gateway.toUpperCase()}" readonly="true">
					</div>
					<div class="mb-2 col-lg-5">
						<input name="total_mail_gateway[]" class="form-control" type="number" required placeholder=" {{ translate('Total Gateways')}}">
					</div>
		    		<div class="col-lg-2 text-end">
		                <span class="input-group-btn">
		                    <button class="i-btn danger--btn btn--md text--light removeEmailBtn" type="button">
		                        <i class="fa fa-times"></i>
		                    </button>
		                </span>
		            </div>
				</div>`;

	        $('.newEmailDataAdd').append(html);
	    });

		$(document).on('click', '.removeEmailBtn', function () {
	        $(this).closest('.newEmaildata').remove();
	    });


		$('.newSmsdata').on('click', function(){

			
			var sms_gateway = $('#sms_gateways').val().replace($('#sms_gateways').val().match(/(\d+)/g)[0], '').trim();  
			
			if ($('.newSmsdata input[value="' + sms_gateway + '"]').length > 0) {
				alert('Row with ' + sms_gateway + ' already exists. Please choose a different sms gateway.');
				return; 
			}
	        var html = `
				<div class="row newSmsdata mt-3">
					<div class="mb-2 col-lg-5">
						<input readonly="true" name="sms_gateways[]" class="form-control" value="${sms_gateway}" type="text" required placeholder="${sms_gateway.toUpperCase()}">
					</div>
					<div class="mb-2 col-lg-5">
						<input name="total_sms_gateway[]" class="form-control" type="number" required placeholder=" {{ translate('Total Gateways')}}">
					</div>
		    		<div class="col-lg-2 text-end">
		                <span class="input-group-btn">
		                    <button class="i-btn danger--btn btn--md text--light removeSmsBtn" type="button">
		                        <i class="fa fa-times"></i>
		                    </button>
		                </span>
		            </div>
				</div>`;
				
	        $('.newSmsDataAdd').append(html);
	    });

	  
	    $(document).on('click', '.removeSmsBtn', function () {
	        $(this).closest('.newSmsdata').remove();
	    });

		$('.select2').select2({
			tags: true,
			tokenSeparators: [',']
		});


		function showEmailGatewayOption(value) {
			value.is(":checked") ? $(".email_gateway_options").removeClass("d-none").addClass("d-block") : $(".email_gateway_options").removeClass("d-block").addClass("d-none");
			value.is(":checked") ? $(".info-email").removeClass("d-block").addClass("d-none") : $(".info-email").removeClass("d-none").addClass("d-block");
		}
		function showSmsGatewayOption(value) {
			value.is(":checked") ? $(".sms_gateway_options").removeClass("d-none").addClass("d-block") : $(".sms_gateway_options").removeClass("d-block").addClass("d-none");
			value.is(":checked") ? $(".info-sms").removeClass("d-block").addClass("d-none") : $(".info-sms").removeClass("d-none").addClass("d-block");
		}

		$(document).ready(function() {
			
			$(".multiple_gateway").change(function() {
				showEmailGatewayOption($(this));
			});
			
			$(".sms_gateway").change(function() {
				showSmsGatewayOption($(this));
			});
		});

		function toggleGatewayOptionVisibility(toggled) {
			const adminItems = $(".admin-items");
			const userItems = $(".user-items");

			const adminCheckboxes = [
				"#allow_admin_sms",
				"#allow_admin_email",
				"#allow_admin_android",
				"#allow_admin_whatsapp"
			];

			const userCheckboxes = [
				"#allow_user_android",
				"#allow_user_whatsapp",
				"#multi_gateway",
				"#sms_gateway"
			];

			if ($("#allow_admin_creds").is(":checked")) {
			
				if (toggled) {

					var android_permission  = {{$plan->sms->android->is_allowed == " " ? 1 : 0}};
					var sms_permission 	    = {{$plan->sms->is_allowed == " " ? 1 : 0}};
					var email_permission    = {{$plan->email->is_allowed == " " ? 1 : 0}};
					var whatsapp_permission = {{$plan->whatsapp->is_allowed == " " ? 1 : 0}};

					if(android_permission == 0 && sms_permission == 0) {

						$(".admin-sms-credit").addClass("d-none");
					}

					adminCheckboxes.forEach((checkbox) => {

						if(checkbox == "#allow_admin_android") {

							

							if(android_permission == 1) {
								$(checkbox).prop("checked", true);
							}
							
							
						}
						if(checkbox == "#allow_admin_sms") {

							
							if(sms_permission == 1) {
								$(checkbox).prop("checked", true);
							}
							
						}
						if(checkbox == "#allow_admin_email") {
							
							
							if(email_permission == 1) {
								$(checkbox).prop("checked", true);
							} else {
								$(".admin-email-credit").addClass("d-none");
							}
							
						}
						if(checkbox == "#allow_admin_whatsapp") {
							
							
							if(whatsapp_permission == 1) {
								$(checkbox).prop("checked", true);
							} else {
								$(".admin-whatsapp-credit").addClass("d-none");
							}
						}


					});
				}
				adminItems.removeClass("d-none").addClass("d-block");
				userItems.removeClass("d-block").addClass("d-none");
			} else {
				if (toggled) {
					userCheckboxes.forEach((checkbox) => {
						$(checkbox).prop("checked", true);
						if (checkbox === "#multi_gateway") {
							showEmailGatewayOption($(checkbox));
						} else if (checkbox === "#sms_gateway") {
							showSmsGatewayOption($(checkbox));
						}
					});
					adminCheckboxes.forEach((checkbox) => $(checkbox).prop("checked", false));
				}
				adminItems.removeClass("d-block").addClass("d-none");
				userItems.removeClass("d-none").addClass("d-block");
			}
		}
		
		$(document).ready(function() {
			
			var uwLimit = $("#user_whatsapp_device_limit").closest('.d-none');
			var uaLimit = $("#user_android_gateway_limit").closest('.d-none');
			var awLimit = $("#whatsapp_device_limit").closest('.d-none');

			toggleLimitVisibility($("#allow_user_android"), uaLimit, $(".user_android"))
			toggleLimitVisibility($("#allow_user_whatsapp"), uwLimit, $(".user_whatsapp"));
			toggleLimitVisibility($("#allow_admin_whatsapp"), awLimit, $(".admin_whatsapp"));
			toggleGatewayOptionVisibility(true);
			$("#allow_admin_creds").change(function() {

				toggleGatewayOptionVisibility(true);
				toggleLimitVisibility($("#allow_admin_whatsapp"), awLimit, $(".admin_whatsapp"));
			});
			$("#allow_admin_whatsapp").change(function() {

				toggleLimitVisibility($("#allow_admin_whatsapp"), awLimit, $(".admin_whatsapp"));
			});
			$("#allow_user_android").change(function() {

				toggleLimitVisibility($("#allow_user_android"), uaLimit, $(".user_android"));
			});
			$("#allow_user_whatsapp").change(function() {

				toggleLimitVisibility($("#allow_user_whatsapp"), uwLimit, $(".user_whatsapp"));
			});
		});

		function toggleLimitVisibility(accessToggle, closestLimitBox,boxSize) {
			
			if (accessToggle.is(":checked")) {

				if (closestLimitBox.length > 0) {

					closestLimitBox.removeClass("d-none");
				}
			} else {
				closestLimitBox.addClass("d-none");
			}
		}
		$(document).ready(function() {

			$(".allow_admin_sms").change(function() {

				if($(".allow_admin_sms").is(":checked")) {
					
					$(".admin-sms-credit").removeClass("d-none");
				} else {
					$(".admin-sms-credit").addClass("d-none");
				}
			});
			$(".allow_admin_email").change(function() {

				if($(".allow_admin_email").is(":checked")) {
					
					$(".admin-email-credit").removeClass("d-none");
				} else {
					$(".admin-email-credit").addClass("d-none");
				}
			});
			$(".allow_admin_whatsapp").change(function() {

				if($(".allow_admin_whatsapp").is(":checked")) {
					
					$(".admin-whatsapp-credit").removeClass("d-none");
				} else {
					$(".admin-whatsapp-credit").addClass("d-none");
				}
			});
		});

		$(document).ready(function() {
			
			$(".allow_user_sms").change(function() {

				if($(".allow_user_sms").is(":checked")) {
					
					$(".user-sms-credit").removeClass("d-none");
				} else {
					$(".user-sms-credit").addClass("d-none");
				}
			});
			$(".allow_user_email").change(function() {

				if($(".allow_user_email").is(":checked")) {
					
					$(".user-email-credit").removeClass("d-none");
				} else {
					$(".user-email-credit").addClass("d-none");
				}
			});
			$(".allow_user_whatsapp").change(function() {

				if($(".allow_user_whatsapp").is(":checked")) {
					
					$(".user-whatsapp-credit").removeClass("d-none");
				} else {
					$(".user-whatsapp-credit").addClass("d-none");
				}
			});
		});
	})(jQuery);
</script>
@endpush
