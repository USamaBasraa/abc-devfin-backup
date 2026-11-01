<?php

namespace App\Http\Requests;
use Illuminate\Foundation\Http\FormRequest;

class StoreAttendanceManagement extends FormRequest
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
        $this->sanitize();
        return [
            'ca_hod_attendance' => 'required',
            'ca_hod_remarks'    => 'required|min:5|max:50',
        ];
    }

    public function sanitize()
    {
        $input = $this->all();
        $input['ca_hod_remarks'] = filter_var($input['ca_hod_remarks'], FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_HIGH);
        $this->replace($input);
    }
}
