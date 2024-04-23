@extends('user.layouts.app')
@section('panel')
<section class="mt-3 rounded_box">
    <div class="container-fluid p-0 mb-3 pb-2">
        <div class="row d-flex align--center rounded">
            <div class="col-xl-12">
                <div class="card">
                    <div class="card-header bg--lite--violet">
                        <h6 class="card-title text-center text-light">{{ translate('Payment with PayPal')}}</h6>
                    </div>
                    <div class="card-body text-center mx-auto">
                        <h6 class="mb-3">{{ translate("Amount: ") }}{{round($paymentLog->final_amount)}} {{$paymentLog->paymentGateway->currency->name}}</h6>

                         
                        <div id="paypal-button-container"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
@push('script-push')

    <script src="https://www.paypal.com/sdk/js?client-id={{$data->client_id}}"> </script>
    <script>
        "use strict";
            paypal.Buttons({
            createOrder: function (data, actions) {
                return actions.order.create({
                    purchase_units: [
                        {
                            description: "{{ $data->description }}",
                            custom_id: "{{$data->custom_id}}",
                            amount: {
                                currency_code: "{{$data->currency}}",
                                value: "{{$data->amount}}",
                                breakdown: {
                                    item_total: {
                                        currency_code: "{{$data->currency}}",
                                        value: "{{$data->amount}}"
                                    }
                                }
                            }
                        }
                    ]
                });
            },

            onApprove: function (data, actions) {

                return actions.order.capture().then(function (details) {

                    var trx = "{{$data->custom_id}}";
                    window.location = '{{ url('user/ipn/paypal/status')}}/' + trx + '/' + details.id
                });
            }
        }).render('#paypal-button-container');
    </script>
@endpush
