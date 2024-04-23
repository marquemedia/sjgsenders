@extends('admin.layouts.app')
@section('panel')
<section>
	<div class="card">
        <div class="card-header">
            <h4 class="card-title">{{ucfirst($paymentMethod->name)}} {{translate($title)}}</h4>
        </div>
        <div class="card-body">
            <form action="{{route('admin.payment.method.update', $paymentMethod->id)}}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="shadow-sm p-3 bg-body rounded">
                    <div class="row">
                        <div class="mb-3 col-lg-6 col-md-12">
                            <label for="currency_id" class="form-label"> {{ translate('Select Currency')}} <sup class="text--danger">*</sup></label>
                            <select class="form-select" name="currency_id" id="currency_id" required>
                                <option value=""> {{ translate('Select One')}}</option>
                                @foreach($currencies as $currency)
                                    <option value="{{$currency->id}}" data-rate_value="{{shortAmount($currency->rate)}}" @if($paymentMethod->currency_id == $currency->id) selected @endif>{{$currency->name}}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mb-3 col-lg-6 col-md-12">
                            <label for="image" class="form-label"> {{ translate('Image')}} <sup class="text--danger">*</sup></label>
                            <input type="file" name="image" id="image" class="form-control">
                        </div>


                        <div class="mb-3 col-lg-6 col-md-12">
                            <label for="search_min" class="form-label"> {{ translate('Percent Charge')}} <sup class="text--danger">*</sup></label>
                            <div class="input-group">
                                <input type="text" class="form-control" id="percent_charge" name="percent_charge" value="{{shortAmount($paymentMethod->percent_charge)}}" placeholder="{{ translate('Enter Number')}}" aria-describedby="basic-addon2">
                                <span class="input-group-text" id="basic-addon2">%</span>
                            </div>
                        </div>

                        <div class="mb-3 col-lg-6 col-md-12">
                            <label for="rate" class="form-label"> {{ translate('Currency Rate')}} <sup class="text--danger">*</sup></label>
                            <div class="input-group mb-3">
                                <span class="input-group-text">1 {{$general->currency_name}} = </span>
                                <input type="text" name="rate" value="{{shortAmount($paymentMethod->rate)}}" class="method-rate form-control" aria-label="Amount (to the nearest dollar)">
                                <span class="input-group-text limittext"></span>
                            </div>
                        </div>

                        @foreach($paymentMethod->payment_parameter as $key => $parameter)
                            <div class="mb-3 col-lg-12">
                                <label for="{{$key}}" class="form-label">{{ucwords(str_replace('_', ' ', $key))}} <sup class="text--danger">*</sup></label>
                                <input type="text" name="method[{{$key}}]" id="{{$key}}" value="{{$parameter}}" class="form-control" placeholder=" {{ translate('Give Valid Data')}}" required>
                            </div>
                        @endforeach


                        <div class="mb-3 col-lg-12 col-md-12">
                            <label for="status" class="form-label"> {{ translate('Status')}} <sup class="text--danger">*</sup></label>
                            <select class="form-control" name="status" id="status" required>
                                <option value="1" @if($paymentMethod->status == 1) selected @endif> {{ translate('Active')}}</option>
                                <option value="2" @if($paymentMethod->status == 2) selected @endif> {{ translate('Inactive')}}</option>
                            </select>
                        </div>
                    </div>
                </div>
                <button type="submit" class="i-btn primary--btn btn--md mt-3"> {{ translate('Submit')}}</button>
            </form>
        </div>
	</div>

</section>
@endsection

@push('script-push')
<script>
	(function($){
		"use strict";
		$("#currency_id").on('change', function(){
			var value = $(this).find("option:selected").text();
			$(".limittext").text(value);
			$(".method-rate").val($('select[name=currency_id] :selected').data('rate_value'));
		}).change();
	})(jQuery);
</script>
@endpush
