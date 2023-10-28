<?php

namespace App\Http\Requests\Profile;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class EditUserRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     * @return array<string, Rule|array|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['required'],
            'avatar' => ['nullable', 'image', 'max:1024'],
            'type' => ['required', 'in:normal,gold,silver'],
        ];
    }


    /**
     * Determine if the user is authorized to make this request.
     * @return bool
     */
    public function authorize(): bool
    {
        return true;
    }


    /**
     * Get the validation messages that apply to the request.
     * @return array<string, string>
     */
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
