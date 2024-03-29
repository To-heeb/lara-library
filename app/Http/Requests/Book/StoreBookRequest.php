<?php

namespace App\Http\Requests\Book;

use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Http\FormRequest;

class StoreBookRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return Gate::allows('create', Book::class);
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
            'publisher_id' => 'required|integer|exists:publishers,id',
            'category_id' => 'required|integer|exists:categories,id',
            'author_id' => 'required|integer|exists:authors,id',
            'available_copies' =>  ['required', 'integer'],
            'total_copies' =>  ['required', 'integer'],
            'isbn' => ['required', 'string', 'unique:books,isbn'],
            'published_year' => ['required', 'date_format:Y'],
            'edition' => 'required'
        ];
    }
}
