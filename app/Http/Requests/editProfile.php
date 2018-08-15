<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class editProfile extends FormRequest
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
            "first_name" =>'required|regex:/^[\pL\s\-]+$/u|max:25',
            "last_name" =>'required|regex:/^[\pL\s\-]+$/u|max:25',
            "user_name" =>'required|regex:/^[\pL\s\-]+$/u|max:25',
            "employee_no" =>'required|alpha-num|max:25',
            "department_name" =>'required|regex:/^[\pL\s\-]+$/u|max:55',
            "mobile_no" =>'required|alpha-num|max:25',
            "cnic" =>'required|alpha-num|max:25',
            
            //
        ];
    }
}
