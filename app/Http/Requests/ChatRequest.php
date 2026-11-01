<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Factory as ValidationFactory;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Validation\ValidationException;



class ChatRequest extends FormRequest
{


    protected $stopOnFirstFailure = true;


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
            'message'=> 'required|max:1200',
            'co_id' => 'required'
        ];
    }


    public function withValidator($validator){

        $validator->after(function ($validator) {
            //if ($this->somethingElseIsInvalid()) {
                if( count($validator->errors()->all()) > 0 ){
                    $validator->errors()->add('api_error', $validator->errors()->first());
                }
                
            // }
        });

    }


}
