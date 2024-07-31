<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreFileRequest extends FormRequest
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
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'file' => ['required', 'file', 'max:1000000'], // 10MB
            'name' => ['required', 'string', 'max:255'],
            'type' => ['required', 'max:255'],
            'key' => ['nullable', 'string', 'max:255'], // if null, then it's a custom key
            'checksum' => ['required', 'string'],
        ];
    }
}
