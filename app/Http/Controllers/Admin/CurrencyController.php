<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Currency;
use Illuminate\Validation\ValidationException;

class CurrencyController extends Controller
{

    public function index()
    {
        $title = "Manage currencies";
        $currencies = Currency::latest()->get();

        return view('admin.setting.currency', compact('title', 'currencies'));
    }

    /**
     * @throws ValidationException
     */
    public function store(Request $request)
    {
        $data = $this->validate($request, [
            'name' => 'required|max:50',
            'symbol' => 'required|max:10',
            'rate' => 'required|numeric|gt:0',
            'status' => 'required|in:1,2',
        ]);

        Currency::create($data);

        $notify[] = ['success', 'Currency has been created'];
        return back()->withNotify($notify);
    }

    /**
     * @throws ValidationException
     */
    public function update(Request $request)
    {
        $data = $this->validate($request, [
            'name' => 'required|max:50',
            'symbol' => 'required|max:10',
            'rate' => 'required|numeric|gt:0',
            'status' => 'required|in:1,2',
        ]);

        $currency = Currency::findOrFail($request->input('id'));
        $currency->update($data);

        $notify[] = ['success', 'Currency has been updated'];
        return back()->withNotify($notify);
    }
}
