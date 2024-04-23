<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\PaymentMethod;
use App\Models\Currency;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class ManualPaymentGatewayController extends Controller
{

    /**
     * @return View
     */
    public function index(): View
    {
        $title = "Manual Payment methods";
        $manualPayments = PaymentMethod::manualMethod()->orderBy('id','ASC')->with('currency')->paginate(paginateNumber());

        return view('admin.manual_payment.index', compact('title', 'manualPayments'));
    }

    /**
     * @return View
     */
    public function create(): View
    {
        $title = "Manual payment method create";
        $currencies = Currency::latest()->get();

        return view('admin.manual_payment.create', compact('title', 'currencies'));
    }

    /**
     * @throws ValidationException
     */
    public function store(Request $request)
    {
       
        $this->validate($request, [
            'status'            => 'required|in:1,2',
            'image'             => 'required|image|mimes:jpg,png,jpeg',
            'name'              => 'required',
            'percent_charge'    => 'required|numeric',
            'rate'              => 'required|numeric',
            'field_name'        => 'required',
        ]);

        $new_code = "500";
        $paymentMethodLog = PaymentMethod::manualMethod()->orderBy('unique_code','DESC')->limit(1)->first();

        if ($paymentMethodLog!=null) {
            $new_code = intval(substr($paymentMethodLog->unique_code, 6, 3)) + 1;
        }

        $paymentMethod = new PaymentMethod();
        $paymentMethod->name = $request->input('name');
        $paymentMethod->currency_id = $request->input('currency_id');
        $paymentMethod->percent_charge = $request->input('percent_charge');
        $paymentMethod->rate = $request->input('rate');
        $paymentMethod->status = $request->input('status');
        $parameter = [];

        if($request->has('field_name')){
            for($i=0; $i<count($request->input('field_name')); $i++){
                $parameter = $this->getArr($request, $i, $parameter);
            }
        }   
        $array_push = [];
        $array_push['payment_gw_info'] = $request->has('payment_gw_info') ? $request->input('payment_gw_info') : "";
        $parameter[] = $array_push;

        $paymentMethod->payment_parameter = $parameter;

        if($request->hasFile('image')){
            try {
                $paymentMethod->image = StoreImage($request->file('image'), filePath()['payment_method']['path'], filePath()['payment_method']['size'], $paymentMethod->image ?: null);
            }catch (\Exception) {
                $notify[] = ['error', 'Image could not be uploaded.'];
                return back()->withNotify($notify);
            }
        }

        $paymentMethod->unique_code = "MANUAL".$new_code;
        $paymentMethod->save();

        $notify[] = ['success', 'Manual payment method has been create'];
        return back()->withNotify($notify);
    }


    public function edit($id)
    {
        $title = "Manual payment method update";
        $currencies = Currency::latest()->get();
        $manualPayment = PaymentMethod::manualMethod()->where('id', $id)->firstOrFail();

        return view('admin.manual_payment.edit', compact('title', 'currencies', 'manualPayment'));
    }

    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'status'            => 'required|in:1,2',
            'name'              => 'required',
            'percent_charge'    => 'required|numeric',
            'rate'              => 'required|numeric',
            'field_name'        => 'required',
        ]);

        $paymentMethod =  PaymentMethod::findOrFail($id);
        $paymentMethod->name = $request->input('name');
        $paymentMethod->currency_id = $request->input('currency_id');
        $paymentMethod->percent_charge = $request->input('percent_charge');
        $paymentMethod->rate = $request->input('rate');
        $paymentMethod->status = $request->input('status');
        $parameter = [];

        if($request->has('field_name')){
            for($i=0; $i<count($request->input('field_name')); $i++){
                $parameter = $this->getArr($request, $i, $parameter);
            }
        }

        $array_push = [];
        $array_push['payment_gw_info'] = $request->has('payment_gw_info') ? $request->input('payment_gw_info') : "";
        $parameter[] = $array_push;
        $paymentMethod->payment_parameter = $parameter;

        if($request->hasFile('image')){
            try {
                $paymentMethod->image = StoreImage($request->image, filePath()['payment_method']['path'], filePath()['payment_method']['size'], $paymentMethod->image ?: null);
            }catch (\Exception $exp) {
                $notify[] = ['error', 'Image could not be uploaded.'];
                return back()->withNotify($notify);
            }
        }

        $paymentMethod->save();
        $notify[] = ['success', 'Manual payment method has been create'];
        return back()->withNotify($notify);
    }

    /**
     * @throws ValidationException
     */
    public function delete(Request $request)
    {
        $this->validate($request, [
            'id' => 'required'
        ]);
        $del_method = PaymentMethod::findOrFail($request->id);
        unlink(filePath()['payment_method']['path']."/".$del_method->image);
        $del_method->delete();

        $notify[] = ['success', "Manual Payment Method Data Removed"];
        return back()->withNotify($notify);
    }

    /**
     * @param Request $request
     * @param int $i
     * @param array $parameter
     * @return array
     */
    public function getArr(Request $request, int $i, array $parameter): array
    {
        $array = [];
        $array['field_label'] = $request->input("field_name.{$i}");
        $array['field_name'] = strtolower(str_replace(' ', '_', $request->input("field_name.{$i}")));
        $array['field_type'] = $request->input("field_type.{$i}");
        $parameter[$array['field_name']] = $array;
        return $parameter;
    }
}
