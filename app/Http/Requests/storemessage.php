<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class storemessage extends FormRequest
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
        return [
            "category" =>'required|not_in:none',
            "receiver" =>'required|not_in:none',
            "file"=>'nullable|file|image',
            "body"=>'required',
            "subject"=>'required|regex:/^[\pL\s\-]+$/u'
            //
        ];
    }
}
