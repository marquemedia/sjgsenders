<?php

namespace App\Service;

use App\Models\Contact;
use App\Models\GeneralSetting;
use App\Models\Group;
use Carbon\Carbon;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use App\Exports\ContactDemoExport;
use App\Jobs\ImportJob;
use App\Models\Import;
use Maatwebsite\Excel\Facades\Excel;
use Shuchkin\SimpleXLSX;
use Shuchkin\SimpleXLSXGen;
use App\Imports\ContactImport;
use App\Models\User;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;


class ContactService
{ 
    //Contact 
        public function contactSave($general, $data, $user_id = null) {

            try {

                if($data["single_contact"] == "true") {
                
                    $data["user_id"] = $user_id;
                    if(isset($data["attributes"])) {
                        $refinedAttribute = $data["attributes"];
                        
                        foreach($data["attributes"] as $key => $value) {
                            if($data["attributes"][$key] != null) {
                            
                                $refinedAttribute[explode("::", $key)[0]] = [
                                    "value" => $value,
                                    "type"  => explode("::", $key)[1]
                                ];
                                unset($refinedAttribute[$key]);
                            } else {
                                unset($refinedAttribute[$key]);
                            }
                        }
                        $data["attributes"] = $refinedAttribute;
                    }
                    unset($data["single_contact"]);
                    
                    Contact::updateOrCreate([
                        
                        "uid" => $data["uid"] ?? null

                    ], $data);

                    if(isset($data["attributes"])) {
                        $attributes = $data["attributes"];

                        foreach($attributes as &$attribute_values) {

                            foreach($attribute_values as $attribute_key => $attribute_value) {

                                if($attribute_key == "value") {

                                    $attribute_values["status"] = true;
                                }
                                unset($attribute_values["value"]);
                            }
                            unset($attribute_values);
                        }
                    
                        $group             = Group::find($data["group_id"]);
                        $currentAttributes = json_decode($group->contact_attributes, true);
                        $mergedAttributes  = $currentAttributes ? array_merge($currentAttributes, $attributes) : $attributes;
                        $newAttributes     = json_encode($mergedAttributes);
                        $group->contact_attributes = $newAttributes;
                        $group->save();
                    }
                    $notify[] = ['success', "Contact Saved Successfully"];

                } else {
                    
                    $notify          = [];
                    $locationKeys    = explode(",", $data["location"][0]);
                    $values          = explode(",", $data["value"][0]);
                    $mappedDataInput = [];
                    $i               = 0;

                    foreach ($locationKeys as $index => $key) {

                        $mappedDataInput[$key] = $values[$i];
                        $i++;
                    }
                    $data["mappedDataInput"] = $mappedDataInput;
                    unset($data["single_contact"], $data["file"], $data["import_contact"], $data["location"], $data["value"]);
                    try {

                        $filePath = filePath()["contact"]["path"];
                        $mime = explode(".", $data["file__name"])[1];
                        
                        $imported = $this->save($this->prepParams($filePath, $mime, $user_id, null, $data));
                        
                        ImportJob::dispatch($imported->id);
                    } catch (\Exception) {
            
                        $notify[] = ['error', "There's something wrong. Please check your directory permission"];
                        return back()->withNotify($notify);
                    }
                
                    $notify[] = ['success', "Contacts will be imported shortly."];
                }

            } catch(\Exception $e) {
                
                $notify[] = ['error', "Something went wrong"];
            }
            return $notify;
        }

        /**
         * @param array $row
         * @return Import
         */
        public function save(array $row): Import {
            return Import::create($row);
        }
        /**
         * @param string $name
         * @param string $path
         * @param string $mime
         * @param int|null $userId
         * @param string|null $type
         * @param int $groupId
         * @param array $contact_structure
         * @return array
         */
        public function prepParams(string $path, string $mime, ?int $userId, ?string $type, array $contact_structure): array{

            $data = $contact_structure;
            unset($contact_structure["file__name"], $contact_structure["group_id"]);
            
            return [
                'user_id'           => $userId,
                'name'              => $data["file__name"],
                'path'              => $path,
                'mime'              => $mime,
                'group_id'          => $data["group_id"],
                'type'              => $type,
                'contact_structure' => $contact_structure,
            ];
        }
            
        public function importContactFormFile($name, $filePath, $data, $group_id, $user_id = null) {

            $file = storage_path("../../$filePath/$name");
            $row_data = Excel::toArray(new ContactImport, $file);
            $data["user_id"] = $user_id;
            if($data["new_row"] == "true") {

                unset($data["new_row"], $data["file__name"]);
                $updated_column = $this->transformColumns($data["mappedDataInput"]);
                $row_data = $this->transformRowData($row_data, $updated_column);
              
                unset($data["mappedDataInput"]);
                
                $new_row_data = [];
                $keys = array_map(function ($key) {
                    return strtolower(str_replace(' ', '_', $key)); 
                }, $row_data[0][0]);
                
                foreach ($row_data[0] as $index => $v) {
                    
                    $new_row_data[$index] = array_combine($keys, $v);
                }
                $data["attributes"] = [];
                $attributes = [];
                
                foreach(array_chunk($new_row_data, 200) as $chunks) {

                    foreach($chunks as $values) {
                    
                        $i = 0;
                        $attributes = []; 
                        foreach($values as $column_key => $column_value) {
                            
                            try{
                                if(array_key_exists($column_key, $data)) {
                                    
                                    $data[$column_key] = strtolower($column_value);
                                    
                                   
                                } else {
                                   
                                    if(isset(array_values($updated_column)[$i][$column_key])) {
                                        
                                        $attributes += [
                                            $column_key => [
                                                "value" => strtolower($column_value),
                                                "type"  => array_values($updated_column)[$i][$column_key]["type"]
                                            ],
                                        ];
                                    }
                                }
                                $i++;
                            } catch(\Exception $e){
                                
                            }
                        } 
                        $data["attributes"] = $attributes;  
                        $data["group_id"] = $group_id;  
                       
                        Contact::create($data);
                    }
                }
                
                unlink($file);
            } else {
               
                unset($data["new_row"], $data["file__name"]);
                
                $updated_column = $this->transformColumns($data["mappedDataInput"]);
                $row_data = $this->transformRowData($row_data, $updated_column);
              
                unset($data["mappedDataInput"]);
                
                $new_row_data = [];
                $keys = array_map(function ($key) {
                    return strtolower(str_replace(' ', '_', $key)); 
                }, $row_data[0][0]);
                
                foreach ($row_data[0] as $index => $v) {
                    
                    $new_row_data[$index] = array_combine($keys, $v);
                }
                
                array_splice($new_row_data, 0, 1);
                $data["attributes"] = [];
                $attributes = [];
                
                foreach($new_row_data as $values) {
                    
                    $i = 0;
                    $attributes = []; 
                    foreach($values as $column_key => $column_value) {
                        
                        try{
                            if(array_key_exists($column_key, $data)) {
                                
                                $data[$column_key] = strtolower($column_value);
                                
                               
                            } else {
                               
                                if(isset(array_values($updated_column)[$i][$column_key])) {
                                    
                                    $attributes += [
                                        $column_key => [
                                            "value" => strtolower($column_value),
                                            "type"  => array_values($updated_column)[$i][$column_key]["type"]
                                        ],
                                    ];
                                }
                            }
                            $i++;
                        } catch(\Exception $e){
                            
                        }
                    } 
                    $data["attributes"] = $attributes;  
                    $data["group_id"] = $group_id;  
                    Contact::create($data);
                }

                unlink($file);
            }
        }
        
        public function transformRowData($row_data, $updated_column) {
            
            $headers = $row_data[0][0];
            $column_mapping = [];
            foreach ($updated_column as $original_column => $updated_column_data) {
                foreach ($updated_column_data as $updated_name => $config) {
                    $column_mapping[$original_column] = $updated_name;
                }
            }
            
            
            $transformed_headers = array_map(function($header) use ($column_mapping) {
                
                return $column_mapping[strtolower(str_replace(' ', '_', str_replace(['(', ')', '?','/'], '', rtrim($header))))] ?? strtolower(str_replace(' ', '_', str_replace(['(', ')', '?','/'], '', rtrim($header))));
            }, $headers);
            $row_data[0][0] = $transformed_headers;
            
            
            
            foreach ($row_data[0] as $index => $data_row) {
                
                $transformed_data = [];
                foreach ($data_row as $key => $value) {
                    
                    $original_column = $headers[$key];
                    
                    $updated_column_name = $column_mapping[$original_column] ?? $original_column;
                    
                    if (isset($updated_column[strtolower(str_replace(' ', '_', str_replace(['(', ')', '?','/'], '', rtrim($original_column))))])) {
                        
                        $transformed_data[] = $value;
                        
                    }
                }
                $row_data[0][$index] = $transformed_data;
                
            }
            
            
            return $row_data;
        }
            
        public function transformColumns($columns) {
            
            $transformedColumns = [];
            
            foreach ($columns as $key => $value) {

                $parts = explode("::", $value);
                $field = $parts[0];
                $type = isset($parts[1]) ? intval($parts[1]) : null;
                $transformedColumns[$key] = [
                    $field => [
                        "status" => true,
                        "type" => $type,
                    ]
                ];
            }

            return $transformedColumns;
        }

        public function searchContact($contacts, $data) {
            
            if(isset($data["search"]) && $data["search"] != null) {
                $search = $data["search"];
                $contacts->where('first_name', 'like', "%$search%")->orWhere("last_name", "like", "%$search%")->orWhere("whatsapp_contact", "like", "%$search%")->orWhere("email_contact", "like", "%$search%")->orWhere("sms_contact", "like", "%$search%");
            }
            if(isset($data["status"]) && $data["status"] != null  && $data["status"] !== 'all') {

                $status = $data["status"];
            
                $contacts->where('status', $status);
            }
            if(isset($data["group"]) && $data["group"] != null  && $data["group"] !== 'all') {

                $group = $data["group"];
                $contacts->where('group_id', $group);
            }
            if(isset($data["contact_type"]) && $data["contact_type"] != null  && $data["contact_type"] !== 'all') {

                $contact_type = $data["contact_type"];
                
                if($data["contact_type"] == 'none') {
                    $contacts->whereNull("whatsapp_contact")->whereNull("sms_contact")->whereNull("email_contact");
                }else {
                    $contacts->whereNotNull("$contact_type");
                }
            }

            return $contacts;
        }
    //Contact 

    //Contact Group

        public function groupSave($uid = null, $data) {
            try {
                
                Group::updateOrCreate([ 
                    "uid" => $uid 
                ],$data);
                $notify[] = ['success', "Group has been saved"];
            
            } catch(\Exception $e) {
                
                $notify[] = ['error', "Something went wrong"];
                
            }
            return $notify;
        }

        public function searchContactGroup($search, $status, $user_id = null) {
            $contactgroup = $user_id ? Group::where("user_id", auth()->user()->id) : Group::whereNull("user_id");
            
            if (!empty($search)) {
                $contactgroup->where('name', 'like', "%$search%");
            }

            if (!empty($status) && $status !== 'all') {
                $statusValues = match ($status) {
                    'active' => [1],
                    'inactive' => [2],
                    default => [1, 2],
                };
                $contactgroup->whereIn('status', $statusValues);
            }

            return $contactgroup;
        }

    //Contact Group 

    //Contact Settings 

        public function settingsSave($attributes, $general, $data, $user_id = null) {

            try { 
                
                if(array_key_exists('attribute_name', $data) || array_key_exists("attribute_type", $data)) {

                    if (isset($attributes[$data['oldKey']])) {

                        $oldValue = $attributes[$data['oldKey']];
                        unset($attributes[$data['oldKey']]);
                        $attributes[strtolower(str_replace(' ', '_', $data['attribute_name']))] = [
                            'type' => $data['attribute_type'],
                            'status' => $oldValue['status'],
                        ];
                        $updatedAttributes = json_encode($attributes);
                        if($user_id) {
                           $user = User::where("user_id", $user_id);
                           $user->contact_attributes = $updatedAttributes;
                           $user->save();
                        } else {
                            $general->contact_attributes = $updatedAttributes;
                            $general->save();
                        }
                       
                        $notify[] = ['success', "Attribute has been saved"];
                    } else {

                        $notify[] = ['error', "This attribute does not exist"];
                    }
                } else {
                    
                    $newAttributeKey = key($data);
                    
                    if (!isset($attributes[$newAttributeKey])) {

                        $attributes += $data;
                        $updatedAttributes = json_encode($attributes);
                        $general->contact_attributes = $updatedAttributes;
                        $general->save();
                        $notify[] = ['success', "Attribute has been saved"];

                    } else {
                        $notify[] = ['error', "Attribute Already Exists."];
                    }
                }
            } catch(\Exception $e) {
                
                $notify[] = ['error', "Something went wrong"];
                
            }
            return $notify;
        }

        public function searchContactAttribute($contact_attributes, $search, $status, $general) {

            $filteredAttributes = array_filter($contact_attributes, function ($attribute, $key) use ($search, $status) {

                $keyMatches = str_contains(strtolower(textFormat(["_"], $key)), strtolower($search));
                
                if ($status == 'all') {

                    return $keyMatches;
                } elseif ($status == 'inactive') {
                    
                    return $keyMatches && isset($attribute['status']) && $attribute['status'] === false;
                } elseif ($status == 'active') {
                    
                    return $keyMatches && isset($attribute['status']) && $attribute['status'] === true;
                }
        
                return false;
            }, ARRAY_FILTER_USE_BOTH);
        
            $paginatedAttributes = slice_array_pagination($filteredAttributes);
        
            return $paginatedAttributes;
        }

    //Contact Settings

    // Mass Contact functions Start
       

        public function getFilePath($folder = "default") {
            $directory = public_path("../../assets/file/contact/$folder");

            if (!File::isDirectory($directory)) {
                File::makeDirectory($directory, 0755, true, true);
            }

            return $directory;
        }
    //Mass Contact functions End
}
