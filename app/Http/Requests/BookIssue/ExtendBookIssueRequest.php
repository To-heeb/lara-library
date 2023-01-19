<?php

namespace App\Http\Requests\BookIssue;

use Illuminate\Http\Request;
use App\Rules\ExtendReturnDateRule;
use Illuminate\Foundation\Http\FormRequest;


class ExtendBookIssueRequest extends FormRequest
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
            'user_id' => 'required|integer|exists:users,id',
            'book_id' => 'required|integer|exists:books,id',
            'return_date' =>  ['required', 'date_format:Y-m-d', new ExtendReturnDateRule],
        ];
    }
}
