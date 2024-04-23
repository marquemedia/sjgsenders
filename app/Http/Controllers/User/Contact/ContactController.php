<?php

namespace App\Http\Controllers\User\Contact;

use App\Http\Controllers\Controller;
use App\Http\Requests\ContactRequest;
use App\Imports\UserContactImport;
use App\Models\Contact;
use App\Models\GeneralSetting;
use App\Models\Group;
use App\Service\ContactService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\View\View;
use Maatwebsite\Excel\Facades\Excel;
use Maatwebsite\Excel\HeadingRowImport;

class ContactController extends Controller
{
    
    
    public ContactService $contactService;
    private $general;
    public function __construct(ContactService $contactService) { 

        $this->contactService = $contactService;
        $this->general        = GeneralSetting::first();
    }

    /** 
     * @param Request $request
     * Contact Search 
    */ 

    public function search(Request $request) {
        
        $data = $request->all();
        if (count(array_filter($data, function ($value) {
            return $value !== null;
        })) === 0) {
            $notify[] = ['error', 'All search data fields are empty'];
            return back()->withNotify($notify);
        }

        $contacts           = Contact::where("user_id", auth()->user()->id);
        $groups             = Group::where("user_id", auth()->user()->id)->where("status", Group::ACTIVE)->pluck("name", "id")->toArray();
        $attributes         = json_decode(auth()->user()->contact_attributes);
        $filteredAttributes = collect($attributes)->filter(function ($attribute) {
            return $attribute->status === true;
        })->toArray();

        $contacts          = $this->contactService->searchContact($contacts, $data);
        $contacts          = $contacts->paginate(paginateNumber());
        $contactAttributes = auth()->user()->contact_attributes;
        $title             = 'Contact Search';
        return view('user.contact.index', compact('title', 'contacts', 'data', 'groups', 'contactAttributes', 'filteredAttributes'));
    }

    /** 
     * Contact List
     * @return View
    */ 

    public function index($id = null): View {
        
        $title              = "Manage Contacts";
        $contacts           = $id ? Contact::where("user_id", auth()->user()->id)->where("group_id",$id)->latest()->paginate(paginateNumber()) : Contact::where("user_id", auth()->user()->id)->latest()->paginate(paginateNumber());
        $contactAttributes  = auth()->user()->contact_attributes;
        $attributes         = json_decode(auth()->user()->contact_attributes);
        $filteredAttributes = collect($attributes)->filter(function ($attribute) {
            return $attribute->status === true;
        })->toArray();
        
        $groups             = Group::where("user_id", auth()->user()->id)->where("status", Group::ACTIVE)->pluck("name", "id")->toArray();
        return view('user.contact.index', compact('title', 'contacts', 'filteredAttributes', 'groups', 'contactAttributes'));
    }

    /**
     * Contact Create
     * @return View
     */
    public function create():View {

        $title              = "Add Contacts";
        $contactAttributes  = auth()->user()->contact_attributes;
        $attributes         = json_decode(auth()->user()->contact_attributes);
        $filteredAttributes = collect($attributes)->filter(function ($attribute) {
            return $attribute->status === true;
        })->toArray();
       
        $groups             = Group::where("user_id", auth()->user()->id)->where("status", Group::ACTIVE)->pluck("name", "id")->toArray();
        return view('user.contact.create', compact('title', 'filteredAttributes', 'groups', 'contactAttributes'));
    }

    /** 
     * Contact Store
     * @param ContactRequest $request
    */ 
    public function store(ContactRequest $request) {
       
        $data = $request->all();
        unset($data["_token"]);
        $data = $this->contactService->contactSave($this->general, $data, auth()->user()->id);
        return back()->withNotify($data);
    }

    /** 
     * Contact update
     * @param ContactRequest $request
    */ 
    public function update(ContactRequest $request) {

        $data = $request->all();
        unset($data["_token"]);
        $data = $this->contactService->contactSave($this->general, $data, auth()->user()->id);
        return back()->withNotify($data);
    }

    /** 
     * Contact delete
     * @param Request $request
    */ 
    public function delete(Request $request) {
    
        $contact      = Contact::where("uid", $request->input("uid"))->first();
        if(!$contact) {
            $notify[] = ['error', "Contact Does Not Exist"];
            return back()->withNotify($notify);
        }
        $contact->delete();
        $notify[]     = ['success', "Contact $contact->first_name $contact->last_name deleted permanently"];
        return back()->withNotify($notify);
    }

    /** 
     * Contact Bulk Status Update
     * @param Request $request
    */ 

    public function bulkStatusUpdate(Request $request) {
        
        $request->validate([
            'id'     => 'nullable|exists:contacts,id',
            'status' => 'required|in:1,2',
        ]);
        $groupNames = [];
        if($request->input('contactUid') !== null){
            
            
            $contactUids = array_filter(explode(",",$request->input('contactUid')));
            if(!empty($contactUids)){
            
                foreach($contactUids as $contactUid) {
                    $contact = Contact::where("uid", $contactUid)->first();
                    $contact->status = $request->input("status");
                    $contact->update();

                    $contactNames[] = $contact->first_name;
                }
            }

            $contactNames = implode(", ", $contactNames);
        }
    
        if($request->input('id') != null){
        
            $notify[] = ['info', 'Work In Progress'];
            return back()->withNotify($notify);
        }

        $notify[] = ['success', "Status has been updated for: $contactNames"];
        return back()->withNotify($notify);
    }

    /** 
     * Contact generate Demo File
    */ 

    public function demoFile($type = null) {

        try {

            $filePath = generateDemoFile($type, $this->general, [], true);
            return response()->download($filePath);

        } catch (\Exception $e) {
            
            $notify[] = ['error', 'Something went wrong, File could not generate'];
            return back()->withNotify($notify);
        }
    }

    /** 
     * Key Mapping upload 
    */ 

    public function uploadFile(Request $request) {

        try {

            $file = $request->file('file');
            $extension = $file->getClientOriginalExtension();
            $directory = public_path("../../assets/file/contact/temporary");
           
            if (!File::isDirectory($directory)) {
                File::makeDirectory($directory, 0755, true, true);
            }
    
            $fileName = 'temp_' . time() . '.' . $extension;
            $filePath = $directory . '/' . $fileName;
            
            $file->move($directory, $fileName);
            $filePath = public_path("../../assets/file/contact/temporary/{$fileName}");
            return response()->json(["status" => true, "file_name" => $fileName, "file_path" => $filePath]);

        } catch (\Exception $e) {

            return response()->json(["status" => false]);
        }
    }

    /** 
     * Key Mapping file remove
    */ 

    public function deleteFile(Request $request) {

        $fileName = $request->input('file_name');
        $filePath = public_path("../../assets/file/contact/temporary/{$fileName}");

        try {
            if (File::exists($filePath)) {
                File::delete($filePath);
                return response()->json(['status' => true]);
            } else {
                return response()->json(['status' => false, 'message' => 'File not found.']);
            }
        } catch (\Exception $e) {
            return response()->json(['status' => false, 'message' => 'Error deleting file.']);
        }
    }

    /** 
     * Key Mapping parsing
    */ 
    public function parseFile(Request $request) {
        try {
            $filePath = $request->input('filePath');
            $parsedData = $this->parseData($filePath);
            return response()->json(["data" => $parsedData]);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to parse file.']);
        }
    }

    /** 
     * Column Data Extract
    */ 
    private function parseData($filePath): array {

        $data = (new HeadingRowImport)->toArray($filePath);
        $headerRow = $data[0][0];
        $headers = array_combine(array_map([$this, 'getExcelColumnName'], range(1, count($headerRow))), $headerRow);
        
        return $headers;
    }

    private function getExcelColumnName(int $columnNumber): string {

        $dividend = $columnNumber;
        $columnName = '';
       
        while ($dividend > 0) {
           
            $modulo = ($dividend - 1) % 26;
            $columnName = chr(65 + $modulo) . $columnName;
            $dividend = (int)(($dividend - $modulo) / 26);
        }

        return $columnName . '1';
    }
}
