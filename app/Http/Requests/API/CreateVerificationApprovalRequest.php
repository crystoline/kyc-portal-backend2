<?php

namespace App\Http\Requests\API;

use App\Models\User;
use App\Models\VerificationApproval;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use InfyOm\Generator\Request\APIRequest;

class CreateVerificationApprovalRequest extends  APIRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        /** @var User $user */
        $user =  Auth::user();
        if($user === null) {
            return false;
        }
        $this->merge(['user_id' => $user->id]);
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return VerificationApproval::$rules;
    }
}
