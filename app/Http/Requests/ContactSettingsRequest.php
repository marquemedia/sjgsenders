<?php

namespace App\Http\Requests;

use App\Models\GeneralSetting;
use Illuminate\Foundation\Http\FormRequest;

class ContactSettingsRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $date = GeneralSetting::DATE;
        $boolean = GeneralSetting::BOOLEAN;
        $text = GeneralSetting::TEXT;
        $number = GeneralSetting::NUMBER;

        $rules = [
            
            "attribute_name" => "required",
            "attribute_type" => ["required", "in:$date,$boolean,$text,$number"],
            "status"        => ["in:true,false"]
        ];
       
        return $rules;
    }
}
