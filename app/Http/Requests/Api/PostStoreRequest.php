<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class PostStoreRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'image' => 'mimes:jpg,jpeg,png',
            'category_id' => 'required|integer',
            'title' => 'required|max:255',
            'slug' => 'required|max:200',
            'detail' => 'required',
            'status' => 'required|max:20'
        ];
    }
}
