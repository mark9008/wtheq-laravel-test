<?php

namespace App\Http\Requests\Profile;

use Illuminate\Foundation\Http\FormRequest;

class EditUserRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'name' => ['required'],
            'avatar' => ['nullable', 'image', 'max:1024'],
            'type' => ['required', 'in:normal,gold,silver'],
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
            'avatar.image' => 'Avatar must be an image',
            'avatar.max' => 'Avatar size must be less than 1MB',
            'type.required' => 'Type is required',
            'type.in' => 'Type must be one of the following: normal, gold, silver',
        ];
    }
}
