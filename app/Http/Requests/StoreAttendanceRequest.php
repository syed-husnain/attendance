<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreAttendanceRequest extends FormRequest
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
            'user_id'         => 'required',
            'check_in'        => 'required|date_format:H:i',
            'check_out'       => 'date_format:H:i|after:check_in',
        ];
    }
    public function messages()
    {
        return [
            'check_in.required' => 'Sign in is required',
            'check_out.after' => 'The Sign out must be a time after Sign in.',
        ];
    }
}
