<?php

namespace App\Http\Requests\Publisher;

use App\Models\Publisher;
use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Http\FormRequest;

class StorePublisherRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return Gate::allows('create', Publisher::class);
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
        ];
    }
}
