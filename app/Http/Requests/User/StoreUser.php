<?php

namespace App\Http\Requests\User;

use Illuminate\Validation\Rules\Password;
use Illuminate\Foundation\Http\FormRequest;

class StoreUser extends FormRequest
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
            'first_name' => 'required',
            'last_name' => 'required',
            'user_name' => 'required|unique:users,name',
            'email' => 'email|required|unique:users,email',
            'password' =>  ['required', 'confirmed', Password::defaults()],
            'phone_number' => 'nullable'
        ];
    }
}
