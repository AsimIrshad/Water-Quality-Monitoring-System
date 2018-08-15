<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class update_sensor extends FormRequest
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
             //
            "name" =>'required|regex:/^[\pL\s\-]+$/u|max:25',
            "stability_criteria" =>'required|numeric',
            "start_range" =>'required|numeric',
            "end_range" =>'required|numeric',
            "status" =>'required|alpha|max:25',
            "station_id" =>'required|not_in:none',
            //
        ];
    }
}
