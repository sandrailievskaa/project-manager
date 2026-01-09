<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateTaskRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title' => ['sometimes', 'required', 'string', 'max:255'],
            'description' => ['sometimes', 'required', 'string'],
            'user_id' => ['nullable', 'exists:users,id'],
            'status' => ['sometimes', 'required', 'string', 'in:to_do,in_progress,qa,done'],
        ];
    }
}
