<?php

namespace App\Http\Requests\Product;

use Illuminate\Foundation\Http\FormRequest;

class CreateProductRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'name' => ['required'],
            'description' => ['nullable'],
            'image' => ['nullable', 'image', 'max:1024'],
            'price' => ['required', 'numeric'],
            'is_active'=> ['nullable', 'boolean'],
        ];
    }

    public function authorize(): bool
    {
        return true;
    }

    public function messages()
    {
        return [
            'name.required' => 'Name is required',
            'image.image' => 'Image must be an image',
            'image.max' => 'Image size must be less than 1MB',
            'price.required' => 'Price is required',
            'price.numeric' => 'Price must be a number',
            'is_active.boolean' => 'Is Active must be a boolean',
        ];
    }
}
