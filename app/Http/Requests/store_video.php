<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class store_video extends FormRequest
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
            "file.*"=>'required|mimes:image/jpg,image/png,image/gif,video/webm,video/mp4,audio/mpeg',
            //
        ];
    }
}
