<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreSalaryRequest extends FormRequest
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
            'user_id'               => 'required',
            'basic_salary'          => 'required|numeric|gt:0',
            'travel_allowance'      => 'required|numeric|gt:0',
            'medical_allowance'     => 'required|numeric|gt:0',
            'bonus'                 => 'required|numeric|gt:0',
            'working_days'          => 'required|numeric|gt:0',
            'working_hours'         => 'required|date_format:H:i:s',
            'late'                  => 'required|numeric',
            'salary'                => 'required|numeric|gt:0',

        ];
    }
}
