<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreAssessmentMarks extends FormRequest
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
        $rules = [
            'total_marks'   => 'required|integer|min:1',
            'cam_details'   => 'nullable|max:255',
            'cam_date'      => 'nullable|date',
        ];
        foreach($this->request->get('obt_marks') as $key => $val) {
            $rules['obt_marks.'.$key] = 'numeric|nullable';
        }
        return $rules;
    }

}
