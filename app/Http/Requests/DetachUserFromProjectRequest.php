<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class DetachUserFromProjectRequest extends FormRequest
{
    public function authorize(): bool
    {
        $project = $this->route('project');

        return $this->user()->can('detachUser', $project);
    }

    public function rules(): array
    {
        return [];
    }
}
