<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreTaskRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string'],
            'project_id' => ['required', 'exists:projects,id'],
            'user_id' => ['nullable', 'exists:users,id'],
            'status' => ['required', 'string', 'in:to_do,in_progress,qa,done'],
        ];
    }
}
