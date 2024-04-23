<?php

namespace App\Http\Controllers\User\Contact;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\ContactGroupRequest;
use App\Models\Contact;
use App\Models\GeneralSetting;
use App\Models\Group;
use App\Service\ContactService;
use Illuminate\View\View;

class ContactGroupController extends Controller
{
    public ContactService $contactService;
    private $general;
    public function __construct(ContactService $contactService) { 
        $this->contactService = $contactService;
        $this->general = GeneralSetting::first();
    }

    /** 
     * @param Request $request
     * Contact Group Search 
    */ 
    public function search(Request $request) {
            
        $search = $request->input('search');
        $status = $request->input('status');

        if (empty($search) && empty($status)) {
            $notify[] = ['error', 'Search data field empty'];
            return back()->withNotify($notify);
        }

        $contact_groups = $this->contactService->searchContactGroup($search, $status, auth()->user()->id);
        $contact_groups = $contact_groups->paginate(paginateNumber());
        $title          = 'Contact group Search - ' . $search.' '.$status;

        return view('user.contact.groups.index', compact('title', 'contact_groups', 'search', 'status'));
    }

    /** 
     * Contact Group List
     * @return View
    */ 
    public function index($id = null):View {
        
        $title          = "Manage Contact Groups";
        $contact_groups = $id ? Group::where("user_id", auth()->user()->id)->where("id",$id)->latest()->paginate(paginateNumber()) : Group::where("user_id", auth()->user()->id)->latest()->paginate(paginateNumber());
        return view('user.contact.groups.index', compact('title', 'contact_groups'));
    }

    /** 
     * Contact Group Store
     * @param ContactGroupRequest $request
    */ 
    public function store(ContactGroupRequest $request) {
        
        $data = [
            "name"    => $request->group_name,
            "status"  => $request->status,
            "user_id" => auth()->user()->id
        ];

        $data = $this->contactService->groupSave(null, $data);
        return back()->withNotify($data);
    }

    /** 
     * Contact Group update
     * @param ContactGroupRequest $request
    */ 
    public function update(ContactGroupRequest $request) {

        $data = [
            "name"    => $request->group_name,
            "status"  => $request->status,
            "user_id" => auth()->user()->id,
        ];

        $data = $this->contactService->groupSave($request->uid, $data);
        return back()->withNotify($data);
    }

    /** 
     * Contact Group delete
     * @param Request $request
    */ 
    public function delete(Request $request) {

        $group = Group::where("id", $request->id)->first();
        
        if(!$group) {

            $notify[] = ['error', "Selected Group Does Not Exist!"];
            return back()->withNotify($notify);
        } else {
            Contact::where('user_id', auth()->user()->id)->where('group_id', $group->id)->delete();
            $group->delete();
            $notify[] = ['success', "Group deleted along with the related contacts"];
            return back()->withNotify($notify);
        }
    }

    /** 
     * Contact Group Bulk Status Update
     * @param Request $request
    */ 
    public function bulkStatusUpdate(Request $request) {
        
        $request->validate([
            'id'     => 'nullable|exists:groups,id',
            'status' => 'required|in:1,2',
        ]);
        $groupNames = [];
        if($request->input('contactGroupUid') !== null){
            
            
            $contactGroupUids = array_filter(explode(",",$request->input('contactGroupUid')));
            if(!empty($contactGroupUids)){
            
                foreach($contactGroupUids as $groupUid) {
                    $contactGroup = Group::where("uid", $groupUid)->first();
                    $contactGroup->status = $request->input("status");
                    $contactGroup->update();

                    $groupNames[] = $contactGroup->name;
                }
            }

            $groupNames = implode(", ", $groupNames);
        }
      
        if($request->input('id') != null){
           
            $notify[] = ['info', 'Work In Progress'];
            return back()->withNotify($notify);
        }

        $notify[] = ['success', "Status has been updated for: $groupNames"];
        return back()->withNotify($notify);
    }

    public function fetch(Request $request, $type = null) {

        try {
            if ($type == "contact_attributes") {

                $groupIds = $request->input('group_ids');
                $channel = $request->input('channel');
               
                if($groupIds) {
                    $contacts = Contact::whereIn('group_id', $groupIds)
                    ->where($channel . '_contact', '!=', '')
                    ->get();
                }
                   
                if ($contacts->isNotEmpty()) {

                    $groupAttributes = Group::whereIn('id', $groupIds)
                        ->whereNotNull('contact_attributes')
                        ->pluck('contact_attributes');
        
                    $mergedAttributes = [];
        
                    foreach ($groupAttributes as $attributes) {
                        $decodedAttributes = json_decode($attributes, true);
        
                        foreach ($decodedAttributes as $key => $attribute) {

                            if ($attribute['status'] === true) {

                                if (!isset($mergedAttributes[$key]) || $mergedAttributes[$key] !== $attribute['type']) {
                                    $mergedAttributes[$key] = $attribute['type'];
                                }
                            }
                        }
                    }
                    return response()->json(['status' => true, 'merged_attributes' => $mergedAttributes]);
                } else {

                    return response()->json(['status' => false, 'message' => "No $channel contacts found for the selected groups"]);
                }
            }
        } catch (\Exception $e) {
           
            $notify[] = ['error', translate('Something Went Wrong')];
            return back()->withNotify($notify);
        }
        
    }
}
