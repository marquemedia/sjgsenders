<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Language;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\File;
use Illuminate\Validation\ValidationException;

class LanguageController extends Controller
{

    public function index()
    {
        $title = "Manage language";
        $languages = Language::latest()->get();
        $countries = json_decode(file_get_contents(resource_path('views/partials/country_file.json')));
        return view('admin.language.index', compact('title', 'languages','countries'));
    }

    /**
     * @throws ValidationException
     */
    public function store(Request $request)
    {
        $this->validate($request,[
            'flag' => 'required|max:255|unique:languages,flag,'.$request->input('id'),
            'name' => 'required|max:255|unique:languages,name,'.$request->input('id'),
            'code' => 'required|max:255|unique:languages,code'.$request->input('id'),
        ]);
        $json_data = file_get_contents(resource_path('lang/') . 'en.json');
        $file = strtolower($request->input('code')) . '.json';
        $path = resource_path('lang/') . $file;
        File::put($path, $json_data);

        Language::create([
            'flag' => Str::lower($request->input('flag')),
            'name' => $request->input('name'),
            'code' => strtolower($request->input('code')),
            'is_default' => 0
        ]);

        $notify[] = ['success', 'Language has been created'];
        return back()->withNotify($notify);
    }

    /**
     * @throws ValidationException
     */
    public function update(Request $request)
    {
        $this->validate($request,[
            'name' => 'required|max:255|unique:languages,name,'.$request->input('id')
        ]);

        $language = Language::findOrFail($request->input('id'));
        $language->update([
            'name' => $request->input('name')
        ]);

        $notify[] = ['success', 'Language has been updated'];
        return back()->withNotify($notify);
    }

    /**
     * @throws ValidationException
     */
    public function setDefaultLang(Request $request)
    {
        $this->validate($request,[
            'id' => 'required'
        ]);
        Language::where('is_default', 1)->update([
            'is_default' => 0
        ]);

        $language = Language::findOrFail($request->id);

        $language->update([
            'is_default' => 1
        ]);
        session()->put('flag', $language->flag);
        session()->put('lang', $language->code);

        $notify[] = ['success', 'Default language has been set to '.$language->name];
        return back()->withNotify($notify);
    }


    public function translate($code)
    {
        $language = Language::where('code',$code)->first();
        $title = "Language update " . $language->name . " Keywords";
        $data = file_get_contents(resource_path('lang/') . $language->code . '.json');
        $languages = Language::get();

        if (empty($data)) {
            $notify[] = ['error', 'This language File not found'];
            return back()->withNotify($notify);
        }

        $datas = json_decode($data);
        return view('admin.language.edit', compact('title', 'datas', 'language', 'languages'));
    }


    /**
     * @throws ValidationException
     */
    public function languageDataStore(Request $request)
    {
        $this->validate($request, [
            'key' => 'required',
            'value' => 'required'
        ]);

        $language = Language::findOrFail($request->input('id'));
        $key = trim($request->input('key'));
        $data = file_get_contents(resource_path('lang/') . $language->code . '.json');

        if(array_key_exists($key, json_decode($data, true))) {
            $notify[] = ['error', "$key Already exist"];
        }else {
            $array[$key] = trim($request->input('value'));
            $dataItems = json_decode($data, true);
            $arrayMerge = array_merge($dataItems, $array);

            file_put_contents(resource_path('lang/') . $language->code . '.json', json_encode($arrayMerge));
            $notify[] = ['success', $key." has been added"];
        }

        return back()->withNotify($notify);
    }

    public function languageDataUpdate(Request $request): bool|string
    {
        $data = file_get_contents(resource_path('lang/') . $request->input('data.code') . '.json');
        $dataItems = json_decode($data, true);
        $dataItems[$request->input('data.key')] = trim($request->input('data.keyValue'));

        file_put_contents(resource_path('lang/'). $request->input('data.code') . '.json', json_encode($dataItems));

        return json_encode([
            'status' => 200,
            'message' =>'Language key has been updated'
        ]);
    }

    /**
     * @throws ValidationException
     */
    public function languageDelete(Request $request)
    {
        $this->validate($request, [
            'id' => 'required'
        ]);

        $language = Language::findOrFail($request->input('id'));

        if ($language->is_default == 1) {
            $notify[] = ['error', "You can not remove default language"];
            return back()->withNotify($notify);
        }

        unlink(resource_path('lang/') . $language->code . '.json');
        $language->delete();

        $notify[] = ['success', "Language Data Removed"];
        return back()->withNotify($notify);
    }

    /**
     * @throws ValidationException
     */
    public function languageDataUpDelete(Request $request)
    {
        $this->validate($request, [
            'key' => 'required'
        ]);

        $language = Language::findOrFail($request->input('id'));
        $key = trim($request->input('key'));

        $data = file_get_contents(resource_path('lang/') . $language->code . '.json');
        $dataItems = json_decode($data, true);

        unset($dataItems[$key]);
        file_put_contents(resource_path('lang/'). $language->code . '.json', json_encode($dataItems));

        $notify[] = ['success', trim($key)." has been removed"];
        return back()->withNotify($notify);
    }
}
