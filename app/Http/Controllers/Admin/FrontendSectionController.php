<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\EmailTemplates;
use App\Models\FrontendSection;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;

class FrontendSectionController extends Controller
{

    public function index($section_key)
    {
       
        $sections = getFrontendSection();
        
        $sectionData = Arr::get($sections, $section_key, null);

        if (!isset($sectionData)) {
            abort(404);
        }

        $sectionFixedContent = FrontendSection::where('section_key', $section_key . '.fixed_content')->orderBy('id', 'desc')->first();
        $elementContents = FrontendSection::where('section_key', $section_key . '.element_content')->orderBy('id')->orderBy('id', 'desc')->get();

        $title = Arr::get($sectionData, 'name');

        return view('admin.frontend_section.index', compact('sectionData', 'sectionFixedContent', 'elementContents', 'section_key', 'title'));
    }

    public function saveFrontendSectionContent(Request $request, $key)
    {
       
 
        $valInputs = $request->except('_token', 'key', 'status', 'content_type', 'id', 'images');
        $inputContentValue = $this->sanitizeInputs($valInputs);

        $type = $request->input('content_type');
        if (!$type) {
            abort(404);
        }

        $imageJson = Arr::get(getFrontendSection(), "{$key}.{$type}.images", []);

        $validationRules = $this->generateValidationRules($request, $imageJson);
        $request->validate($validationRules, [], ['images' => 'image']);

        $inputvalue = $this->processImageInputs($request, $imageJson, $inputContentValue, $key);

        if($inputvalue  == false || !is_array($inputvalue)){
            $notify[] = ['error', 'Something is wrong'];
            return back()->withNotify($notify);
        }
        $frontendSection = $this->findOrCreateContent($request, $key, $type);
        $sectionValue = [];
        if(!is_null($frontendSection->section_value)){
            $sectionValue = $frontendSection->section_value;
        }
        $frontendSection->section_value = array_replace_recursive($sectionValue, $inputvalue);
        $frontendSection->save();

        $notify[] = ['success', 'Content has been updated.'];
        return redirect()->route('admin.frontend.sections.index', $key)->withNotify($notify);
    }


    private function processImageInputs(Request $request, array $imgJson, array $inputContentValue, string  $key)
    {
        if ($imgJson) {
            foreach ($imgJson as $imageKey => $imgValue) {
                $file = $request->file("images.{$imageKey}");

                if (is_file($file)) {
                    try {
                        $setImageStoreValue = $this->storeImage($imgJson,$request->input('content_type'),$key,$file,$imageKey);

                        if($setImageStoreValue == false){
                            return false;
                        }

                        Arr::set($inputContentValue, $imageKey, $setImageStoreValue);

                    } catch (\Exception $exp) {
        
                        return false;
                    }
                }
            }

            return $inputContentValue;
        }

        return $inputContentValue;

    }

    protected function storeImage($imgJson,$type,$key,$file,$imgKey): string
    {
        try {
            $path = filePath()['frontend']['path'];
            if ($type == 'fixed_content' || $type == 'element_content') {
                $size = Arr::get($imgJson, "{$imgKey}.size", );
            }else{
                $path = filePath()[$key]['path'];
                $size = filePath()[$key]['size'];
            }
            return StoreImage($file, $path, $size);

        }catch (\Exception $exception){
            return false;
        }
    }

    public function getFrontendSectionElement($section_key, $id = null)
    {
        $section = getFrontendSection();
        $sectionData = $section[$section_key] ?? null;

        if (!$sectionData) {
            abort(404);
        }

        $title = $sectionData['name'] . ' elements';

        if ($id) {
            $frontendSectionElement = FrontendSection::findOrFail($id);
            return view('admin.frontend_section.element', compact('section','sectionData','section_key', 'title', 'frontendSectionElement'));
        }
        return view('admin.frontend_section.element', compact('section','sectionData', 'section_key', 'title'));
    }


    public function delete(Request $request)
    {
        $request->validate(['element_id' => 'required']);
        $frontendSectionElement = FrontendSection::findOrFail($request->input('element_id'));
        $frontendSectionElement->delete();

        $notify[] = ['success', 'Section element content has been removed.'];
        return back()->withNotify($notify);
    }



    private function sanitizeInputs(array $inputs): array
    {
        $purifier = new \HTMLPurifier();
        $sanitizedInputs = [];
        foreach ($inputs as $keyName => $input) {
            if (is_array($input)) {
                $sanitizedInputs[$keyName] = $input;
            } else {
                $sanitizedInputs[$keyName] = $purifier->purify($input);
            }
        }

        return $sanitizedInputs;
    }

    private function generateValidationRules(Request $request, ?array $imgJson): array
    {
        $validationRules = [];
        foreach ($request->except('_token') as $input => $val) {
            if ($input == "images" && $imgJson) {
                foreach ($imgJson as $key => $imageValue) {
                    $validationRules["images.{$key}"] = ['nullable', 'image'];
                }
            }else {
                $validationRules[$input] = 'required';
            }
        }
        return $validationRules;
    }

    private function findOrCreateContent(Request $request, $key, $type)
    {
        if ($request->has('id')) {
            return FrontendSection::findOrFail($request->input('id'));
        }else{
            $frontendSection = FrontendSection::where('section_key', "{$key}.{$type}")->first();
            if (!$frontendSection || $type == 'element_content') {
                $frontendSection = new FrontendSection();
                $frontendSection->section_key = "{$key}.{$type}";
                $frontendSection->save();
            }
        }
        return $frontendSection;
    }


}
