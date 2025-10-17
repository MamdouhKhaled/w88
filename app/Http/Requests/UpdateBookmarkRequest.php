<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateBookmarkRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            "url" => "required|url|max:255|min:13",
            "title" => "nullable|string|max:255|min:3",
            "note" => "nullable|string|max:255|min:3",
            'tags' => 'nullable|array',
            'tags.*' => 'nullable|string'
        ];
    }
}
