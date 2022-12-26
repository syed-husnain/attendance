<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreUserRequest extends FormRequest
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
            'name'                          => 'required|string',
            'cnic'                          => 'nullable|integer|digits_between:13,13',
            'phone'                         => 'required|string',
            'email'                         => 'required|email|unique:users,email',
            'dob'                           => 'required',
            'designation'                   => 'required',
            'member_since'                  => 'required',
            'basic_salary'                  => 'required',

        ];
    }
}
