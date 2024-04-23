<?php

namespace App\Http\Controllers\User\Contact;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\ContactSettingsRequest;
use App\Models\Contact;
use App\Models\GeneralSetting;
use App\Models\Group;
use App\Models\User;
use App\Service\ContactService;
use Illuminate\Support\Facades\Auth;

class ContactSettingsController extends Controller
{
    public ContactService $contactService;
    private $user;
    public function __construct(ContactService $contactService) { 
        $this->contactService = $contactService;
        
    }

    /** 
     * @param Request $request
     * Contact Settings->Attributes Search 
    */ 
    public function attributeSearch(Request $request) {
        $search = $request->input('search');
        $status = $request->input('status');    
        

        if (empty($search) && empty($status)) {
            $notify[] = ['error', 'Search data field empty'];
            return back()->withNotify($notify);
        }
        $contact_attributes = json_decode(auth()->user()->contact_attributes, true);
        $contact_attributes = $this->contactService->searchContactAttribute($contact_attributes, $search, $status, auth()->user());
        
        $title = 'Contact Attribute Search - ' . $search.' '.$status;
        return view('user.contact.settings.index', compact('title', 'contact_attributes', 'search', 'status'));
    }

    /** 
     * Contact Settings List
     * @return View
    */ 
    public function settings() {
        $title   = "Manage Contact Settings";
        $attributes = json_decode(auth()->user()->contact_attributes, true);
        $contact_attributes = slice_array_pagination($attributes);

        return view('user.contact.settings.index', compact('title', 'contact_attributes'));
    }

    /** 
     * Contact Settings->Attributes Store
     * @param ContactSettingsRequest $request
    */ 
    public function attributeStore(ContactSettingsRequest $request) {

        $data = [
            
            strtolower(str_replace(' ', '_', $request->input("attribute_name"))) => [
                "type"   => $request->input("attribute_type"),
                "status" => (boolean)$request->input("status"),
            ]
        ];
        $attributes = json_decode(auth()->user()->contact_attributes, true);
        $notify = $this->contactService->settingsSave($attributes, auth()->user(), $data);

        return back()->withNotify($notify);
    }

    /** 
     * Contact Settings->Attributes update
     * @param ContactSettingsRequest $request
    */ 
    public function attributeUpdate(ContactSettingsRequest $request) {

        $data = $request->all();
        $attributes = json_decode(auth()->user()->contact_attributes, true);
        $data = $this->contactService->settingsSave($attributes, auth()->user(), $data);
        return back()->withNotify($data);
    }

    /** 
     * Contact Settings->Attributes delete
     * @param Request $request
    */ 
    public function attributeDelete(Request $request) {
        
        $attribute = $request->attribute_name;
        $setting_attributes = auth()->user()->contact_attributes;
        
        $settingArray = json_decode($setting_attributes, true);

        if (isset($settingArray[$attribute])) {
            unset($settingArray[$attribute]);
        }
        
        $setting_attributes = json_encode($settingArray);
        auth()->user()->contact_attributes = $setting_attributes;
        auth()->user()->save();
        $notify[] = ['success', "Settings Attribute deleted successfully"];
        return back()->withNotify($notify);
    }

    /** 
     * Contact Settings->Attributes Bulk Status Update
     * @param Request $request
    */
    public function attributeStatusUpdate(Request $request) {
        
        $name       = $request->input("name");
        $status     = $request->input("status") == "true" ? true : false;
        $attributes = json_decode(auth()->user()->contact_attributes, true);

        if (isset($attributes[$name])) {

            $attributes[$name]['status'] = $status;
            $updatedAttributes = json_encode($attributes);
            auth()->user()->contact_attributes = $updatedAttributes;
            auth()->user()->save();
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
    }
}
