<?php

namespace App\Http\Requests\BookIssue;

use Illuminate\Foundation\Http\FormRequest;

class ReturnBookIssueRequest extends FormRequest
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
            'user_id' => 'required|integer|exists:book_issues,user_id',
            'book_id' => 'required|integer|exists:book_issues,book_id',
        ];
    }
}
