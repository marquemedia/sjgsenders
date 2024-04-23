<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Template;
use App\Models\WhatsappDevice;
use App\Models\WhatsappTemplate;
use Illuminate\View\View;
use Illuminate\Support\Facades\Http;

class TemplateController extends Controller
{
	public function index(Request $request, $type = null, $id = null) {

		$templates = [];
		$title 	   = strtoupper(@$type)." ".translate("Template List");
		if($type = "whatsapp") {
			$templates = $id ? WhatsappTemplate::whereNull("user_id")->where("cloud_id", $id)->latest()->paginate(paginateNumber()) :  WhatsappTemplate::whereNull("user_id")->latest()->paginate(paginateNumber());
			return view('admin.template.whatsapp.index', compact('title', 'templates'));

		}
	}
	public function fetch(Request $request, $type = null) {
		
		$templates = WhatsappTemplate::whereNull("user_id")->where("cloud_id", $request->input("cloud_id"))
                              ->whereIn("status", ["APPROVED"])
                              ->get();
		return response()->json(['templates' => $templates]);
	
	}
    public function userTemplate() {

        $view = 'admin_view';
    	$title = "User Template List";
        $userTemplates = Template::whereNotNull('user_id')->paginate(paginateNumber());
    	return view('admin.template.tabs.user', compact('title','view', 'userTemplates'));
    }
    public function adminTemplate() {

        $view = 'admin_view';
    	$title = "Admin Template List";
    	$templates = Template::whereNull('user_id')->paginate(paginateNumber());
    	return view('admin.template.tabs.admin', compact('title', 'templates','view'));
    }

    public function store(Request $request)
    {
    	$request->validate([
    		'name' => 'required|max:255',
    		'message' => 'required',
    	]);

        $message = '';
    	Template::create([
			'name' => $request->input('name'),
			'message' => offensiveMsgBlock($request->input('message')),
		]);

        if (offensiveMsgBlock($request->input('message')) != $request->input('message') ){
            $message = session()->get('offsensiveNotify') ;
        }

    	$notify[] = ['success', 'Template has been created'.$message];
    	return back()->withNotify($notify);
    }

    public function update(Request $request)
    {
        $message = '';
    	$request->validate([
    		'name' => 'required|max:255',
    		'message' => 'required',
    	]);

    	$template = Template::whereNull('user_id')->where('id', $request->input('id'))->firstOrFail();
    	$template->update([
			'name' => $request->input('name'),
			'message' => offensiveMsgBlock($request->input('message')),
		]);

        if (offensiveMsgBlock($request->input('message')) != $request->input('message') ){
            $message = session()->get('offsensiveNotify') ;
        }

    	$notify[] = ['success','Template has been updated'.$message];
    	return back()->withNotify($notify);
    }

    public function delete(Request $request)
    {
    	$template = Template::where('id', $request->input('id'))->firstOrFail();
        $template->delete();

    	$notify[] = ['success', 'Template has been deleted'];
    	return back()->withNotify($notify);
    }


    /**
     * @return View
     */
    public function userIndex(): View
    {
    	$title = "Manage User Template List";
		$view = 'user_view';

    	$templates = Template::whereNotNull('user_id')->paginate(paginateNumber());
    	return view('admin.template.index', compact('title', 'templates', 'view'));
    }

    public function updateStatus(Request $request)
	{
		$request->validate([
			'id' => 'required|exists:templates,id',
			'status' => 'required|in:1,2,3'
		]);

		$template = Template::where('id', $request->input('id'))->first();
		$template->status = $request->input('status');
		$template->save();

		$notify[] = ['success', 'Status Updated Successfully'];
    	return back()->withNotify($notify);
	}
	
	/**
     * whatsapp cloud api templates
     * @param Request $request
     * @return view
     */

	public function whatsAppRefresh(Request $request) {
		
		try {

			$itemId = $request->input("itemId");
			WhatsappTemplate::where('cloud_id', $itemId)->delete();
			$whatsapp_business_account = WhatsappDevice::find($itemId);
			$credentials 			   = $whatsapp_business_account->credentials;
			$token 					   = $credentials['user_access_token'];
			$waba_id 				   = $credentials['whatsapp_business_account_id'];
			$url 					   = "https://graph.facebook.com/v19.0/$waba_id/message_templates";

			$queryParams = [
				'fields' => 'name,category,language,quality_score,components,status',
				'limit'  => 100
			];

			$headers = [
				'Authorization' => "Bearer $token"
			];

			$response 	  = Http::withHeaders($headers)->get($url, $queryParams);
			$responseData = $response->json();

			if (array_key_exists("data", $responseData)) {
				
				foreach ($responseData["data"] as $template) {
					
					$template_data = [
						'cloud_id'      	   => $itemId,
						'user_id'       	   => null,
						'language_code' 	   => $template["language"],
						'name' 	   			   => $template["name"],
						'category'	    	   => $template["category"],
						'status'        	   => $template["status"],
						'template_information' => $template["components"]
					];

					WhatsappTemplate::create($template_data);
				}

				return json_encode([
					'reload' => true,
					'status' => true,
				]);
				
			} else {

				return json_encode([
					'reload' => true,
					'status' => false,
				]);
			}

		} catch (\Exception $e) {

			return json_encode([
				'reload' => true,
				'status' => false,
			]);
		}
	}
}
