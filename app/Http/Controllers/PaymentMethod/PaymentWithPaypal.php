<?php

namespace App\Http\Controllers\PaymentMethod;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\PaymentMethod;
use App\Models\PaymentLog;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Redirect;

use PayPal\Rest\ApiContext;
use PayPal\Auth\OAuthTokenCredential;
use PayPal\Api\Amount;
use PayPal\Api\Details;
use PayPal\Api\Item;
use PayPal\Api\ItemList;
use PayPal\Api\Payer;
use PayPal\Api\Payment;
use PayPal\Api\RedirectUrls;
use PayPal\Api\ExecutePayment;
use PayPal\Api\PaymentExecution;
use PayPal\Api\Transaction;
use App\Http\Utility\PaymentInsert;
use App\Http\Controllers\PaymentMethod\PaymentController;

class PaymentWithPaypal extends Controller
{
    private $_api_context;
    
    public function __construct()
    {
        $paypal_configuration = Config::get('paypal');
        $this->_api_context = new ApiContext(new OAuthTokenCredential($paypal_configuration['client_id'], $paypal_configuration['secret']));
        $this->_api_context->setConfig($paypal_configuration['settings']);
    }

    public function getPaymentStatus(Request $request, ? string $trx_code = null, ? string $type = null )
    {        
       
        $paymentLog    = PaymentLog::where('status', 0)->where('trx_number', $trx_code)->first();
        $paymentMethod = PaymentMethod::where('unique_code', "PAYPAL102")->first();
        if(!$paymentLog){
           abort(404);
        }
        $url         = "https://api.paypal.com/v2/checkout/orders/{$type}";
        $client_id   = $paymentMethod->payment_parameter->client_id;
        $secret      = $paymentMethod->payment_parameter->secret;
        $headers = [
            'Content-Type:application/json',
            'Authorization:Basic ' . base64_encode("{$client_id}:{$secret}")
        ];
        $response     = $this->curlGetRequestWithHeaders($url, $headers);
        $paymentData  = json_decode($response, true);

        if (isset($paymentData['status']) && $paymentData['status'] == 'COMPLETED') {

            $paymentTrackNumber = session()->get('payment_track');
            $paymentLog = PaymentLog::where('trx_number', $paymentTrackNumber)->first();       
            PaymentController::paymentUpdate($paymentLog->trx_number);
            $notify[] = ['success', 'Payment successful!'];
            return redirect()->route('user.dashboard')->withNotify($notify);
           
        } else {
            $notify[] = ['error', 'Payment failed !!'];
            return redirect()->route('user.dashboard')->withNotify($notify);
        }

    }
}
