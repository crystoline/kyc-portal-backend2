<?php

namespace App\Http\Requests\API;

use App\Models\Document;
use Illuminate\Support\Facades\Log;
use InfyOm\Generator\Request\APIRequest;

class CreateDocumentAPIRequest extends APIRequest
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
        //Log::info(json_encode($this->all()));
        //Log::info( file_get_contents('php://input'));
       // print file_get_contents('php://input');
        //print '*********************';
        //die($this->all());
        return Document::$rules;
    }
}
