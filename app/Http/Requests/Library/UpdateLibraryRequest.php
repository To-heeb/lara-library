<?php

namespace App\Http\Requests\Library;

use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Http\FormRequest;

class UpdateLibraryRequest extends FormRequest
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
            //
            'name' => 'required',
            'subdomain' => 'required|string',
            'address' => 'required|string',
            'email' => ['email', 'required', 'unique:libraries,email,' . Auth::user()->library_id],
            'book_issue_duration_in_days' =>  ['required', 'integer'],
            'max_issue_extentions' =>  ['required', 'integer'],
            'phone_number' => 'nullable'
        ];
    }
}
