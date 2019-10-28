<?php

namespace App\Http\Requests\API;

use Illuminate\Foundation\Http\FormRequest;

class CompletePasswordResetRequest extends FormRequest
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
            'reset_code' => 'required',
            'password' => 'required'
        ];
    }
}
