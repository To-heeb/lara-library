<?php

namespace App\Http\Requests\BookIssue;

use Illuminate\Foundation\Http\FormRequest;

class StoreBookIssueRequest extends FormRequest
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
            'name' => 'required|string',
            'user_id' => 'required|integer|exists:users,id',
            'book_id' => 'required|integer|exists:books,id',
            'issue_date' => ['required', 'date_format:Y-m-d'],
            'return_date' =>  ['required', 'date_format:Y-m-d'],
            'due_date' =>  ['required', 'date_format:Y-m-d'],
            'extention_num' => ['required', 'integer'],
        ];
    }
}
