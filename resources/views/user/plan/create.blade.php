@extends('user.layouts.app')
@section('panel')
<section>
	<div class="pricing-plan-banner">
		<span>
			{{ translate('Our Pricing Plan')}}
		</span>
		<h2>
			{{ translate('Choose the plan that fits
			your needs.')}}
		</h2>
		<p class="mt-2">{{translate('Our pricing plans are designed to be affordable, flexible, and
                tailored to your unique needs')}}.</p>
	</div>

	<div class="pricing-container">
		<div class="row justify-content-center">
			<div class="col-xl-10 mx-auto ">
				<div class="row g-4">
					@foreach($plans as $plan)
						
						<div class="col-xl-4 col-sm-6">
							<div class="card pricingTable">
								<div class="pricingTable-header">
                                    @if($plan->recommended_status==1)
									    <h6 class="price-ribbon">{{translate('Recommended')}}</h6>
                                    @endif
									<div class="d-flex align-items-center gap-3">
										<div class="price-icon">
											<svg xmlns="http://www.w3.org/2000/svg" version="1.1" x="0" y="0" viewBox="0 0 512 512" style="enable-background:new 0 0 512 512" xml:space="preserve"><g><path d="M511.892 157.407c-.004-8.755-3.447-16.476-9.957-22.326l-77.824-69.956c-9.68-8.699-21.885-17.59-39.045-17.59h-.018c-17.171.006-29.377 8.909-39.052 17.618l-77.774 70.009c-6.507 5.856-9.944 13.58-9.941 22.335l.036 89.85h-51.651c9.348-19.413 15.859-50.079 8.547-73.042-5.146-16.161-16.479-26.815-32.773-30.811a8.001 8.001 0 0 0-9.701 5.974c-.887 3.853-1.708 7.465-2.479 10.86-9.609 42.314-10.844 47.752-37.385 65.099-18.75 12.255-20.081 13.837-31.742 30.849a1681.377 1681.377 0 0 1-3.976 5.782c-4.722 6.83-10.763 9.91-19.542 9.987l-.677.002v-1.916c0-9.964-8.106-18.07-18.071-18.07H18.07c-9.964 0-18.07 8.106-18.07 18.07v176.263c0 9.965 8.106 18.071 18.07 18.071h40.798c9.964 0 18.071-8.106 18.071-18.071v-8.2c3.606 1.258 7.211 2.6 10.923 3.986 18.175 6.788 38.774 14.482 69.359 14.482h106.196c4.527 0 8.835-.945 12.746-2.64 3.84 1.727 8 2.64 12.265 2.64h.007l193.54-.067c16.562-.007 30.031-13.486 30.024-30.048zM60.939 446.394c0 1.123-.948 2.071-2.071 2.071H18.07c-1.122 0-2.07-.948-2.07-2.071V270.131c0-1.122.948-2.07 2.07-2.07h40.798c1.123 0 2.071.948 2.071 2.07zm96.282-5.732c-27.694 0-46.029-6.849-63.76-13.471-5.353-1.999-10.842-4.049-16.521-5.879V288.047l.771-.002c13.975-.122 24.947-5.805 32.609-16.89 1.484-2.146 2.809-4.079 4.011-5.833 10.754-15.689 10.754-15.689 27.299-26.503 31.957-20.887 34.313-31.266 44.233-74.948l.447-1.971c6.593 3.5 11.092 9.202 13.658 17.259 7.548 23.704-4.355 60.197-13.668 70.95a8.001 8.001 0 0 0 6.048 13.238h91.46c8.913 0 16.164 7.251 16.164 16.164s-7.251 16.165-16.164 16.165h-63.055a8 8 0 0 0 0 16h76.269c8.913 0 16.164 7.251 16.164 16.164s-7.251 16.165-16.164 16.165h-76.269a8 8 0 0 0 0 16h67.963c8.913 0 16.164 7.251 16.164 16.164s-7.251 16.164-16.164 16.164h-67.963a8 8 0 0 0 0 16h42.665c8.913 0 16.165 7.252 16.165 16.165s-7.251 16.164-16.165 16.164zm324.748-.067-190.761.066a31.957 31.957 0 0 0 4.374-16.163 31.971 31.971 0 0 0-4.418-16.257c16.596-1.256 29.716-15.161 29.716-32.072a32.02 32.02 0 0 0-7.511-20.636c9.46-5.606 15.817-15.922 15.817-27.693 0-13.173-7.961-24.521-19.323-29.489a31.991 31.991 0 0 0 6.109-18.84c0-17.735-14.429-32.164-32.164-32.164h-9.49l-.036-89.855c-.001-4.208 1.518-7.622 4.645-10.437l77.774-70.01c10.657-9.593 18.872-13.506 28.354-13.51h.012c9.474 0 17.69 3.909 28.35 13.489l77.824 69.956c3.129 2.813 4.651 6.226 4.653 10.434L496 426.553c.003 7.74-6.291 14.039-14.031 14.042zm-48.856-108.263c1.172 9.655-1.416 18.467-7.483 25.482-7.136 8.25-18.994 13.781-32.489 15.333v10.176a8 8 0 0 1-16 0v-10.292c-19.984-2.746-35.518-15.382-40.015-33.42a8 8 0 0 1 15.525-3.871c4.144 16.619 20.868 22.164 33.938 21.838 11.222-.267 21.796-4.282 26.94-10.23 3.122-3.609 4.333-7.891 3.702-13.088-1.493-12.3-11.754-19.36-33.271-22.893-34.244-5.623-42.218-22.99-42.876-36.568-.914-18.815 12.258-34.585 32.777-39.239a50.745 50.745 0 0 1 3.282-.629v-10.405a8 8 0 0 1 16 0v10.337c14.647 2.169 29.096 10.654 36.315 27.849a8 8 0 0 1-4.279 10.473 7.998 7.998 0 0 1-10.473-4.279c-6.939-16.526-23.736-20.818-37.306-17.74-10.254 2.325-20.981 9.552-20.334 22.859.202 4.167.817 16.848 29.487 21.555 10.622 1.742 42.955 7.051 46.56 36.752zm-48.038-177.524c15.709 0 28.49-12.78 28.49-28.49s-12.78-28.49-28.49-28.49-28.491 12.78-28.491 28.49 12.781 28.49 28.491 28.49zm0-40.981c6.887 0 12.49 5.604 12.49 12.49s-5.603 12.49-12.49 12.49-12.491-5.604-12.491-12.49 5.604-12.49 12.491-12.49z" opacity="1" data-original="#000000" class=""></path></g></svg>
										</div>
										<h3 class="heading">{{ucfirst($plan->name)}}</h3>
										
									</div>
									<p class="w-100">{{ucfirst($plan->description)}}</p>
									<div class="price-value">
										<h2>{{$general->currency_symbol}}{{shortAmount($plan->amount)}}</h2>
										<span class="month"> /{{$plan->duration}} {{ translate('Days')}}</span>
									</div>

									<div class="plan-bg">
										<svg xmlns="http://www.w3.org/2000/svg" version="1.1"x="0" y="0" viewBox="0 0 682.667 682.667" style="enable-background:new 0 0 512 512" xml:space="preserve" class=""><g><defs><clipPath id="b" clipPathUnits="userSpaceOnUse"><path d="M0 512h512V0H0Z" fill="#000000" opacity="1" data-original="#000000"></path></clipPath></defs><mask id="a"><rect width="100%" height="100%" fill="#ffffff" opacity="1" data-original="#ffffff"></rect></mask><g mask="url(#a)"><path d="M0 0v180.477" style="stroke-width:15;stroke-linecap:round;stroke-linejoin:round;stroke-miterlimit:10;stroke-dasharray:none;stroke-opacity:1" transform="matrix(1.33333 0 0 -1.33333 639.24 483.651)" fill="none" stroke="#000000" stroke-width="15" stroke-linecap="round" stroke-linejoin="round" stroke-miterlimit="10" stroke-dasharray="none" stroke-opacity="" data-original="#000000" class=""></path><g clip-path="url(#b)" transform="matrix(1.33333 0 0 -1.33333 0 682.667)"><path d="M0 0h299.761c26.525 0 48.028 21.503 48.028 48.028v59.21" style="stroke-width:15;stroke-linecap:round;stroke-linejoin:round;stroke-miterlimit:10;stroke-dasharray:none;stroke-opacity:1" transform="translate(131.64 7.5)" fill="none" stroke="#000000" stroke-width="15" stroke-linecap="round" stroke-linejoin="round" stroke-miterlimit="10" stroke-dasharray="none" stroke-opacity="" data-original="#000000" class=""></path><path d="M0 0v123.677c0 9.146-7.415 16.561-16.562 16.561h-314.666c-9.146 0-16.561-7.415-16.561-16.561v-392.663" style="stroke-width:15;stroke-linecap:round;stroke-linejoin:round;stroke-miterlimit:10;stroke-dasharray:none;stroke-opacity:1" transform="translate(479.43 364.262)" fill="none" stroke="#000000" stroke-width="15" stroke-linecap="round" stroke-linejoin="round" stroke-miterlimit="10" stroke-dasharray="none" stroke-opacity="" data-original="#000000" class=""></path><path d="M0 0h-19.606c-9.147 0-16.562-7.415-16.562-16.562v-23.185c0-26.525 21.503-48.028 48.028-48.028h348.542c-26.525 0-48.028 21.503-48.028 48.028v23.185C312.374-7.415 304.959 0 295.812 0H34.523" style="stroke-width:15;stroke-linecap:round;stroke-linejoin:round;stroke-miterlimit:10;stroke-dasharray:none;stroke-opacity:1" transform="translate(68.738 95.275)" fill="none" stroke="#000000" stroke-width="15" stroke-linecap="round" stroke-linejoin="round" stroke-miterlimit="10" stroke-dasharray="none" stroke-opacity="" data-original="#000000" class=""></path><path d="M0 0h-49.503a6 6 0 0 0-6 6v49.502a6 6 0 0 0 6 6H0a6 6 0 0 0 6-6V6a6 6 0 0 0-6-6Z" style="stroke-width:15;stroke-linecap:round;stroke-linejoin:round;stroke-miterlimit:10;stroke-dasharray:none;stroke-opacity:1" transform="translate(242.67 369.503)" fill="none" stroke="#000000" stroke-width="15" stroke-linecap="round" stroke-linejoin="round" stroke-miterlimit="10" stroke-dasharray="none" stroke-opacity="" data-original="#000000" class=""></path><path d="m0 0 130.146-.316" style="stroke-width:15;stroke-linecap:round;stroke-linejoin:round;stroke-miterlimit:10;stroke-dasharray:none;stroke-opacity:1" transform="translate(293.696 386.146)" fill="none" stroke="#000000" stroke-width="15" stroke-linecap="round" stroke-linejoin="round" stroke-miterlimit="10" stroke-dasharray="none" stroke-opacity="" data-original="#000000" class=""></path><path d="m0 0 85.893-.192" style="stroke-width:15;stroke-linecap:round;stroke-linejoin:round;stroke-miterlimit:10;stroke-dasharray:none;stroke-opacity:1" transform="translate(338.01 414.555)" fill="none" stroke="#000000" stroke-width="15" stroke-linecap="round" stroke-linejoin="round" stroke-miterlimit="10" stroke-dasharray="none" stroke-opacity="" data-original="#000000" class=""></path><path d="m0 0 17.476-.067" style="stroke-width:15;stroke-linecap:round;stroke-linejoin:round;stroke-miterlimit:10;stroke-dasharray:none;stroke-opacity:1" transform="translate(293.756 414.679)" fill="none" stroke="#000000" stroke-width="15" stroke-linecap="round" stroke-linejoin="round" stroke-miterlimit="10" stroke-dasharray="none" stroke-opacity="" data-original="#000000" class=""></path><path d="M0 0h-49.503a6 6 0 0 0-6 6v49.503a6 6 0 0 0 6 6H0a6 6 0 0 0 6-6V6a6 6 0 0 0-6-6Z" style="stroke-width:15;stroke-linecap:round;stroke-linejoin:round;stroke-miterlimit:10;stroke-dasharray:none;stroke-opacity:1" transform="translate(242.67 259.541)" fill="none" stroke="#000000" stroke-width="15" stroke-linecap="round" stroke-linejoin="round" stroke-miterlimit="10" stroke-dasharray="none" stroke-opacity="" data-original="#000000" class=""></path><path d="m0 0 130.146-.316" style="stroke-width:15;stroke-linecap:round;stroke-linejoin:round;stroke-miterlimit:10;stroke-dasharray:none;stroke-opacity:1" transform="translate(293.696 276.184)" fill="none" stroke="#000000" stroke-width="15" stroke-linecap="round" stroke-linejoin="round" stroke-miterlimit="10" stroke-dasharray="none" stroke-opacity="" data-original="#000000" class=""></path><path d="m0 0 85.893-.193" style="stroke-width:15;stroke-linecap:round;stroke-linejoin:round;stroke-miterlimit:10;stroke-dasharray:none;stroke-opacity:1" transform="translate(338.01 304.594)" fill="none" stroke="#000000" stroke-width="15" stroke-linecap="round" stroke-linejoin="round" stroke-miterlimit="10" stroke-dasharray="none" stroke-opacity="" data-original="#000000" class=""></path><path d="m0 0 17.476-.068" style="stroke-width:15;stroke-linecap:round;stroke-linejoin:round;stroke-miterlimit:10;stroke-dasharray:none;stroke-opacity:1" transform="translate(293.756 304.718)" fill="none" stroke="#000000" stroke-width="15" stroke-linecap="round" stroke-linejoin="round" stroke-miterlimit="10" stroke-dasharray="none" stroke-opacity="" data-original="#000000" class=""></path><path d="m0 0 10.015-10.014 25.888 25.889" style="stroke-width:15;stroke-linecap:round;stroke-linejoin:round;stroke-miterlimit:10;stroke-dasharray:none;stroke-opacity:1" transform="translate(211 293.292)" fill="none" stroke="#000000" stroke-width="15" stroke-linecap="round" stroke-linejoin="round" stroke-miterlimit="10" stroke-dasharray="none" stroke-opacity="" data-original="#000000" class=""></path><path d="M0 0h-49.503a6 6 0 0 0-6 6v49.503a6 6 0 0 0 6 6H0a6 6 0 0 0 6-6V6a6 6 0 0 0-6-6Z" style="stroke-width:15;stroke-linecap:round;stroke-linejoin:round;stroke-miterlimit:10;stroke-dasharray:none;stroke-opacity:1" transform="translate(242.67 146.58)" fill="none" stroke="#000000" stroke-width="15" stroke-linecap="round" stroke-linejoin="round" stroke-miterlimit="10" stroke-dasharray="none" stroke-opacity="" data-original="#000000" class=""></path><path d="m0 0 130.146-.317" style="stroke-width:15;stroke-linecap:round;stroke-linejoin:round;stroke-miterlimit:10;stroke-dasharray:none;stroke-opacity:1" transform="translate(293.696 163.223)" fill="none" stroke="#000000" stroke-width="15" stroke-linecap="round" stroke-linejoin="round" stroke-miterlimit="10" stroke-dasharray="none" stroke-opacity="" data-original="#000000" class=""></path><path d="m0 0 85.893-.192" style="stroke-width:15;stroke-linecap:round;stroke-linejoin:round;stroke-miterlimit:10;stroke-dasharray:none;stroke-opacity:1" transform="translate(338.01 191.632)" fill="none" stroke="#000000" stroke-width="15" stroke-linecap="round" stroke-linejoin="round" stroke-miterlimit="10" stroke-dasharray="none" stroke-opacity="" data-original="#000000" class=""></path><path d="m0 0 17.476-.068" style="stroke-width:15;stroke-linecap:round;stroke-linejoin:round;stroke-miterlimit:10;stroke-dasharray:none;stroke-opacity:1" transform="translate(293.756 191.756)" fill="none" stroke="#000000" stroke-width="15" stroke-linecap="round" stroke-linejoin="round" stroke-miterlimit="10" stroke-dasharray="none" stroke-opacity="" data-original="#000000" class=""></path><path d="m0 0 10.015-10.015 25.888 25.889" style="stroke-width:15;stroke-linecap:round;stroke-linejoin:round;stroke-miterlimit:10;stroke-dasharray:none;stroke-opacity:1" transform="translate(211 180.331)" fill="none" stroke="#000000" stroke-width="15" stroke-linecap="round" stroke-linejoin="round" stroke-miterlimit="10" stroke-dasharray="none" stroke-opacity="" data-original="#000000" class=""></path></g></g></g></svg>
									</div>
								</div>

								<div class="pricing-content">
									<h6 class="mb-3">{{ translate('We provide this feature')}} :</h6>
									<ul>
										@if($plan->carry_forward == App\Models\PricingPlan::ENABLED)
											<li><b>{{ translate("Credit carry forward when renewed") }}</b></li>
										@endif
										@if($plan->sms->android->is_allowed == true || $plan->whatsapp->is_allowed == true || $plan->sms->is_allowed == true || $plan->email->is_allowed == true)
											
											@if($plan->sms->android->is_allowed == true && $plan->whatsapp->is_allowed == true)
												@if($plan->type == App\Models\PricingPlan::USER && $plan->sms->android->is_allowed == true && $plan->whatsapp->is_allowed == true)
												
													@if($plan->sms->android->is_allowed == true)
														<li>{{ translate('Add ')}} <b class="{{ $plan->sms->android->gateway_limit == 0 ? "text--primary" : "" }}">{{ $plan->sms->android->gateway_limit == 0 ? "unlimited" : $plan->sms->android->gateway_limit }}</b> <b>{{ translate(" Android Gateways")}}</b></li>
													@endif
													@if($plan->whatsapp->is_allowed == true)
														<li>{{ translate('Add ')}} <b class="{{ $plan->whatsapp->gateway_limit == 0 ? "text--primary" : "" }}">{{ $plan->whatsapp->gateway_limit == 0 ? "unlimited" : $plan->whatsapp->gateway_limit }}</b> <b>{{translate(" Whatsapp devices")}}</b></li>
													@endif
												
												@elseif($plan->type == App\Models\PricingPlan::ADMIN && $plan->sms->android->is_allowed == true && $plan->whatsapp->is_allowed == true)
													
													<li>{{ translate("Use Admin's Gateways for: ")}}<b>
														@if($plan->sms->is_allowed == true) {{ translate(" SMS ") }} @endif
														@if($plan->sms->android->is_allowed == true) {{ translate(" Android ") }} @endif
														@if($plan->email->is_allowed == true) {{ translate(" Email ") }} @endif
													</b></li>
													
												@endif
											@endif

											@if($plan->type == App\Models\PricingPlan::ADMIN)
												@if($plan->whatsapp->is_allowed == true)
													<li>{{ translate('Add ')}} <b class="{{ $plan->whatsapp->gateway_limit == 0 ? "text--primary" : "" }}">{{ $plan->whatsapp->gateway_limit == 0 ? "unlimited" : $plan->whatsapp->gateway_limit }}</b> <b>{{translate(" Whatsapp devices")}}</b></li>
												@endif
											
											@elseif($plan->type == App\Models\PricingPlan::USER)
												@if($plan->email->is_allowed == true)
													@php 
														$gateway_mail 		= (array)@$plan->email->allowed_gateways;
														$total_mail_gateway = 0; 
														foreach ($gateway_mail as $email_value) { $total_mail_gateway += $email_value; }
													@endphp
													<li>{{ translate('Add up To') }} <b>{{ $total_mail_gateway }}</b> {{ translate("Mail Gateways") }}</li>
												@endif
												@if($plan->sms->is_allowed == true)
													@php 
														$gateway_sms 	   = (array)@$plan->sms->allowed_gateways; 
														$total_sms_gateway = 0;
														foreach ($gateway_sms as $sms_value) { $total_sms_gateway += $sms_value; }
														
													@endphp
													<li>{{ translate('Add Up To') }} <b>{{ $total_sms_gateway }}</b> {{ translate("SMS Gateways") }}</li>
												@endif
											@endif
										@endif

										@if(!is_null($plan->sms_gateways))

											<li>{{ translate('Add') }} <b>{{ $plan->total_sms_gateway }}</b> {{ translate("gateways from") }}<br/><b>{{strToUpper(implode(", ",($plan->sms_gateways)))}}</b></li>
										@endif
										
										<li><b>{{$plan->sms->credits}}</b> {{ translate('SMS Credit') }}</li>
										<li><b>{{$plan->email->credits}}</b> {{ translate('Email Credit') }}</li>
										<li><b>{{$plan->whatsapp->credits}}</b> {{ translate('WhatsApp Credit') }}</li>
										<li>{{ translate('1 Credit for '.$general->sms_word_text_count.' plain word')}}</li>
										<li>{{ translate('1 Credit for '.$general->sms_word_unicode_count.' unicode word')}}</li>
										<li>{{ translate('1 Credit for '.$general->whatsapp_word_count.' word')}}</li>
										<li>{{ translate('1 Credit for per Email')}}</li>
									</ul>

									<a href="javascript:void(0)" class="{{ $plan->id == @$subscription->plan_id  && @ ($subscription->status == App\Models\Subscription::RUNNING || $subscription->status == App\Models\Subscription::RENEWED) ? 'i-btn danger--btn btn--lg subscription-btn-disable' : 'i-btn subscription primary--btn btn--lg radius' }} w-100"  data-bs-toggle="modal" data-bs-target="{{$plan->id == @$subscription->plan_id && @$subscription->status == 1 ? ' ' : '#purchase' }}" data-id="{{$plan->id}}" data-status="{{ App\Models\Subscription::REQUESTED }}">
										@if($subscription)
											@if($plan->id == $subscription->plan_id && $subscription->status != 3)
												@if((Carbon\Carbon::now()->toDateTimeString() > $subscription->expired_date)  && $subscription->status == 2)
													{{ translate("Renew") }}
													
												@else
													{{ translate('Current Plan')}}
												@endif
											@else
											{{ translate('Upgrade Plan')}}
											@endif
										@else
											{{ translate('Purchase Now')}}
										@endif
									</a>
								</div>
							</div>
						</div>
					@endforeach
				</div>
			</div>
		</div>
	</div>
</section>

<div class="modal fade" id="purchase" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog nafiz modal-lg ">
        <div class="modal-content">
        	<div class="modal-header">
		        <h5 class="modal-title">{{ translate('Payment Method')}}</h5>
		        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
		     </div>
        	<form action="{{route('user.plan.store')}}" method="POST">
        		@csrf
        		<input type="hidden" name="id">
        		<input type="hidden" name="status">
        		<input type="hidden" name="payment_gateway">
	            <div class="modal_body">
	            	<div class="container px-0">
						<div class="card mb-4">
							<div class="card-header">
								<h4 class="card-title">{{ translate('Automatic Payment Method')}}</h4>
							</div>
							<div class="card-body">
								<div class="row g-3">
									@foreach($paymentMethods as $paymentMethod)
										@if(strpos($paymentMethod->unique_code, 'MANUAL') === false)
											<div class="col-xl-3 col-md-4 col-6">
												<div class="payment-item" data-payment_gateway="{{$paymentMethod->id}}">
													<div class="payment-item-img">
														<img src="{{showImage(filePath()['payment_method']['path'].'/'.$paymentMethod->image,filePath()['payment_method']['size'])}}" alt="{{$paymentMethod->name}}">
													</div>
													<h4 class="payment-item-title">
													{{$paymentMethod->name}}
													</h4>
													<div class="payment-overlay">
														<button type="submit" class="i-btn primary--btn btn--sm">{{ translate('Process')}}</button>
													</div>
												</div>
											</div>
										@endif
									@endforeach
								</div>
							</div>
						</div>

						<div class="card">
							<div class="card-header">
								<h4 class="card-title">{{ translate('Manual Payment Method')}}</h4>
							</div>

							<div class="card-body">
								<div class="row g-3">
									@foreach($paymentMethods as $paymentMethod)
										@if(strpos($paymentMethod->unique_code, 'MANUAL') !== false)
										<div class="col-xl-3 col-md-4 col-6">
											<div class="payment-item" data-payment_gateway="{{$paymentMethod->id}}">
												<div class="payment-item-img">
													<img src="{{showImage(filePath()['payment_method']['path'].'/'.$paymentMethod->image,filePath()['payment_method']['size'])}}" alt="{{$paymentMethod->name}}">
													</div>
													<h4 class="payment-item-title">
													{{$paymentMethod->name}}
													</h4>
													<div class="payment-overlay">
													<button type="submit" class="i-btn primary--btn btn--sm">{{ translate('Process')}}</button>
												</div>
											</div>
										</div>
										@endif
									@endforeach
								</div>
							</div>
						</div>
	                </div>
	            </div>
        	</form>
    	</div>
	</div>
</div>
@endsection


@push('style-push')
<style>
	.subscription-btn-disable {
		pointer-events: none;
	}
</style>
@endpush

@push('script-push')
<script>
	(function($){
		"use strict";
		$(".subscription").on('click', function(){
			
			var modal = $('#purchase');
			modal.find('input[name=id]').val($(this).data("id"));
			modal.find('input[name=status]').val($(this).data("status"));
			
			modal.modal('show');
		});

		$(".payment-item").on('click', function(){
			var modal = $('#purchase');
			modal.find('input[name=payment_gateway]').val($(this).data("payment_gateway"));
		});
	})(jQuery);
</script>
@endpush
