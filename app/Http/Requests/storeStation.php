<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class storeStation extends FormRequest
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
            "station_name" =>'required|regex:/^[\pL\s\-]+$/u|max:100',
            "address" =>'required',
            //
        ];
    }
}
